<?php

session_set_cookie_params([
    'lifetime' => 3600,
    'path'  => '/',
    'secure' => false, //Cambiar a true si usas https
    'httponly' => true,
]);

session_start();

require 'config.php';

header('Content-Type: application/json');

// 1. Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Solo se aceptan solicitudes POST"]);
    exit;
}

// 2. Validar sesión
if (!isset($_SESSION['id_cliente'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Debe iniciar sesión']);
    exit;
}
 $id_cliente = $_SESSION['id_cliente'];
 switch ($_SERVER['REQUEST_METHOD']){
     case 'POST':
        try {
            // 3. Leer y validar datos
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (empty($data['productos']) || !is_array($data['productos']) || 
                empty($data['fecha_entrega']) || 
                empty($data['metodo_pago']) || 
                empty($data['telefono']) || 
                empty($data['direccion_entrega'])) {
                http_response_code(400);
                echo json_encode(["error" => "Faltan campos obligatorios"]);
                exit;
            }

            // 5. Iniciar transacción
            $pdo->beginTransaction();

            // 6. Calcular total y preparar detalles
            $precioTotal = 0;
            $detalles = [];
            
            foreach ($data['productos'] as $item) {
                // Obtener datos del producto
                $stmt = $pdo->prepare("SELECT tipo_precio, precio_unitario, precio_mediano, precio_grande 
                                    FROM productos WHERE id_producto = ?");
                $stmt->execute([$item['id_producto']]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$producto) {
                    throw new Exception("Producto no encontrado: ID {$item['id_producto']}");
                }

                // Calcular precio según tipo
                switch ($producto['tipo_precio']) {
                    case 'unitario':
                        $precio = $producto['precio_unitario'];
                        break;
                    case 'mediano':
                        $precio = $producto['precio_mediano'];
                        break;
                    case 'grande':
                        $precio = $producto['precio_grande'];
                        break;
                    default:
                        throw new Exception("Tipo de precio no válido");
                }

                $subtotal = $precio * $item['cantidad'];
                $precioTotal += $subtotal;
            }

            // 7. Insertar pedido principal
            $stmt = $pdo->prepare("INSERT INTO pedidos 
                                (id_cliente, precio_total, fecha_entrega, metodo_pago, telefono, direccion_entrega) 
                                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $id_cliente,
                $precioTotal,
                $data['fecha_entrega'],
                $data['metodo_pago'],
                $data['telefono'],
                $data['direccion_entrega']
            ]);
            $id_pedido = $pdo->lastInsertId();

            // 8. Insertar productos del pedido
            $stmt = $pdo->prepare("INSERT INTO pedidos_productos 
                                (id_pedido, id_producto, cantidad, precio_unitario, subtotal) 
                                VALUES (?, ?, ?, ?, ?)");
            

                foreach ($data['productos'] as $producto){
                        $stmt->execute([
                        $id_pedido,
                        $producto['id_producto'],
                        $producto['cantidad'],
                        $precio,
                        $subtotal
                        ]);
                        
                    }

            // 9. Confirmar transacción
            $pdo->commit();

            echo json_encode([
                "success" => true,
                "id_pedido" => $id_pedido,
                "total" => $precioTotal,
                 "id_cliente" => $id_cliente
            ]);

        } catch (PDOException $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;
      
}

 
