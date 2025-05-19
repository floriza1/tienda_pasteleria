<?php
include 'config.php';
//Configuración de errores
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errors.log');

header('Content-Type: application/json'); // Para respuestas JSON

//1 Verificar que la petición sea POST
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode(['error' => 'Solo se aceptan solicitudes POST']);
    exit;
}

//2. Obtener los datos del cuerpo de la petición (JSON)
$data = json_decode(file_get_contents("php://input"), true);

 if(!isset($data['nombre'] )|| !isset($data['apellidos']) || !isset($data['telefono'])|| !isset($data['email'])|| !isset($data['contrasenia'])){
    echo json_encode(['error' => 'Faltan campos obligatorios']);
    exit;
 }
 //Guardamos los datos recibidos en las varibles para ejecurarlas
 $nombre = $data['nombre'];
 $apellidos = $data['apellidos'];
 $telefono = $data['telefono'];
 $email = $data['email'];
 $contrasenia = $data['contrasenia'];
  
 // Verificar si se pasó el campo 'rol', si no, asignar 'cliente' por defecto
$rol = isset($data['rol']) && $data['rol'] === 'ADMIN' ? 'ADMIN' : 'CLIENTE';

 try{
    //verificar si el emil ya existe
    $sql = "SELECT COUNT(*) FROM clientes WHERE email = ?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$email]);
    //Si en la columna hay mayor que 0 existe
    $emailExist = $stm->fetchColumn() > 0;
    
    if($emailExist){
        echo json_encode(['error' => "El email ya existe"]);
    }else{
        //Se hace la inserción del registro
        $sql = "INSERT INTO clientes (nombre, apellidos, telefono, email, contrasenia, rol)VALUES ( ?,?,?,?,?,?)";
        $stm = $pdo->prepare($sql);
        //Hasheamos la contraseña antes de guardarla
        $hashContasenia =  password_hash($contrasenia, PASSWORD_DEFAULT);
        $stm->execute([$nombre, $apellidos, $telefono, $email, $hashContasenia, $rol]);
        echo json_encode(['message' => 'Registro exitoso']);
    }   

 }catch(PDOException $e){
  echo json_encode(['error' => 'Las credenciales son incorrectos']);
 }
?>