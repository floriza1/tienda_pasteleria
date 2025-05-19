<?php

session_set_cookie_params([
    'lifetime' => 3600,
    'path'  => '/',
    'secure' => false, // Cambiar a true si usas https
    'httponly' => true,
]);

session_start();

require 'config.php';

header('Content-Type: application/json');

// Validar sesión
if (!isset($_SESSION['id_cliente']) ||
    !isset($_SESSION['rol']) ||
    !isset($_SESSION['ultima_actividad']) ||
    (time() - $_SESSION['ultima_actividad'] > 1800)) {

    http_response_code(401);
    echo json_encode(['error'=> "Sesión expirada o no válida"]);
    exit;
}

$_SESSION['ultima_actividad'] = time();

$id_cliente = $_SESSION['id_cliente'];
$rol = $_SESSION['rol'];

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'POST':
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['productos']) || !is_array($data['productos']) ||
                !isset($data['fecha_entrega']) ||
                !isset($data['metodo_pago']) ||
                !isset($data['telefono']) ||
                !isset($data['direccion_entrega'])) {
                
                echo json_encode(['error' => 'Faltan campos obligatorios']);
                exit;
            }

            $productos = $data['productos'];
            $fecha_entrega = $data['fecha_entrega'];
            $metodo_pago = $data['metodo_pago'];
            $telefono = $data['telefono'];
            $direccion_entrega = $data['direccion_entrega'];

            $pdo->beginTransaction();

            $precio_total = 0;
            $detalles_productos = [];

            // Recorremos los productos para calcular total y preparar datos individuales
            foreach ($productos as $producto) {
                $stm = $pdo->prepare("SELECT tipo_precio, precio_unitario, precio_mediano, precio_grande FROM productos WHERE id_producto = ?");
                $stm->execute([$producto['id_producto']]);
                $datos_producto = $stm->fetch(PDO::FETCH_ASSOC);

                if (!$datos_producto) {
                    throw new Exception("Producto no encontrado ID {$producto['id_producto']}");
                }

                switch ($datos_producto['tipo_precio']) {
                    case 'unitario':
                        $precio = $datos_producto['precio_unitario']; break;
                    case 'mediano':
                        $precio = $datos_producto['precio_mediano']; break;
                    case 'grande':
                        $precio = $datos_producto['precio_grande']; break;
                    default:
                        throw new Exception("Tipo de precio no válido");
                }

                $sub_total = $precio * $producto['cantidad'];
                $precio_total += $sub_total;

                // Guardamos datos individuales para usar después
                $detalles_productos[] = [
                    'id_producto' => $producto['id_producto'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $precio,
                    'subtotal' => $sub_total
                ];
            }

            // Insertar en pedidos
            $stm = $pdo->prepare("INSERT INTO pedidos (
                id_cliente, precio_total, fecha_entrega,
                metodo_pago, telefono, direccion_entrega
            ) VALUES (?, ?, ?, ?, ?, ?)");
            $stm->execute([
                $id_cliente,
                $precio_total,
                $fecha_entrega,
                $metodo_pago,
                $telefono,
                $direccion_entrega
                //estado
            ]);

            $id_pedido = $pdo->lastInsertId();

            // Insertar productos del pedido
            $stm = $pdo->prepare("INSERT INTO pedidos_productos (
                id_pedido, id_producto, cantidad, precio_unitario, subtotal
            ) VALUES (?, ?, ?, ?, ?)");

            foreach ($detalles_productos as $detalle) {
                $stm->execute([
                    $id_pedido,
                    $detalle['id_producto'],
                    $detalle['cantidad'],
                    $detalle['precio_unitario'],
                    $detalle['subtotal']
                ]);
            }

            $pdo->commit();
            echo json_encode([
                'message' => 'Pedido registrado',
                'id_pedido' => $id_pedido,
                'id_cliente' => $id_cliente
            ]);
        } catch (PDOException $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }

        break;
     //Para actualizar el estado del pedido
    case 'PUT':
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            $id_pedido = $data['id_pedido'];
            $estado_pedido = $data[' estado_pedido'];

            $sql = "UPDATE pedidos SET estado_pedido= ? WHERE = id_pedido AND id_cliente = ? ";
            $stm = $pdo->prepare($sql);
            $stm->execute([$estado_pedido,$id_pedido, $id_cliente ]);
            echo json_encode(["message" => "Estado del pedido actualizado"]);

         } catch(PDOException $e){
          http_response_code(500);
          echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'GET'  :
        try{
          //leer y validar datos
           //$data = json_decode(file_get_contents("php://input"), true);
          if($rol === "CLIENTE"){
            //Preparo la consulta
            $stm = $pdo->prepare(" SELECT  c.id_cliente, p.id_pedido, p.fecha_pedido,pro.id_producto,
            pp.cantidad, pp.precio_unitario, p.precio_total
            FROM clientes c
            JOIN pedidos p ON c.id_cliente = p.id_cliente
            JOIN pedidos_productos pp ON p.id_pedido = pp.id_pedido
            JOIN productos pro ON pp.id_producto = pro.id_producto
            WHERE  p.id_cliente = ? 
            order by p.fecha_pedido DESC
            limit 1;
               ");
            //Lo ejecuto
            $stm->execute([$id_cliente]);
            //recupero lo que me da la consulta
            $pedido = $stm->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['pedido' => $pedido]);
           }

           if($rol === "ADMIN"){
                if(isset($_GET['id_cliente'])){
                   // echo json_encode(["error" => "El id_cliente no es correcto"]);
               
                    // si es ADMIM puede ver un pedido 
                    $stm = $pdo->prepare("SELECT  c.id_cliente, p.id_pedido, p.fecha_pedido,pro.id_producto, pro.nombre as nombre_producto,
                    pp.cantidad, pp.precio_unitario, p.precio_total
                    FROM clientes c
                    JOIN pedidos p ON c.id_cliente = p.id_cliente
                    JOIN pedidos_productos pp ON p.id_pedido = pp.id_pedido
                    JOIN productos pro ON pp.id_producto = pro.id_producto
                    WHERE c.id_cliente = ?
                    order by p.fecha_pedido DESC");
                    //execute devuelve true o false
                     $stm->execute([$_GET['id_cliente']]);
                     //fetchAll trae los datos
                     $resultado = $stm->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['message' => $resultado]);
                }
                  
                if( isset($_GET['id_pedido'])){
                    // si es ADMIM puede ver un pedido 
                    $stm = $pdo->prepare("SELECT  c.id_cliente, p.id_pedido, p.fecha_pedido,pro.id_producto, pro.nombre as nombre_producto,
                    pp.cantidad, pp.precio_unitario, p.precio_total
                    FROM clientes c
                    JOIN pedidos p ON c.id_cliente = p.id_cliente
                    JOIN pedidos_productos pp ON p.id_pedido = pp.id_pedido
                    JOIN productos pro ON pp.id_producto = pro.id_producto
                    WHERE p.id_pedido = ?
                    order by p.fecha_pedido DESC");

                    $stm->execute([$_GET['id_pedido']]);
                    $pedido= $stm->fetch();
                    echo json_encode(['message' => $pedido]);
                }

                //Si la URL  no recibe estos parámetros entonces se ejecuta la consulta dentro de ese bloque
                 if(!isset($_GET['id_cliente']) && !isset($_GET['id_pedido'])){
                    //Mostrar todos los pedidos
                    $stm = $pdo->prepare("SELECT  c.id_cliente, p.id_pedido, p.fecha_pedido,pro.id_producto, pro.nombre as nombre_producto,
                    pp.cantidad, pp.precio_unitario, p.precio_total
                    FROM clientes c
                    JOIN pedidos p ON c.id_cliente = p.id_cliente
                    JOIN pedidos_productos pp ON p.id_pedido = pp.id_pedido
                    JOIN productos pro ON pp.id_producto = pro.id_producto
                    
                    order by p.fecha_pedido DESC");
                    $stm->execute();
                    $resultados = $stm->fetchAll(PDO::FETCH_ASSOC);
                     echo json_encode(['message' => $resultados]);
                }
            }
        }catch(PDOException $e){

        }
  }

    ?>