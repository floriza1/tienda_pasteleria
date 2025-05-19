<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Asegura que la respuesta sea JSON
session_set_cookie_params([
    'lifetime' => 3600,
    'path'  => '/',
    'secure' => false, // Cambiar a true si usas HTTPS
    'httponly' => true,
]);
session_start();

require 'config.php';

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(405);//no es compatible con el método de solicitud
        echo json_encode(["error" => "Solo se aceptan soclicitudes POST"]);
         exit;
    }
  //Recibe los datos del registro en formato json
$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['email']) || !isset($data['contrasenia']) ){
    echo json_encode(["error" => "Los campos obligarios incorrectos"]);
    exit;
}

$email = $data['email'];
$contrasenia = $data['contrasenia'];

 try{
    $sql = "SELECT id_cliente, nombre, telefono, email, contrasenia, rol FROM clientes WHERE email = ?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$email]);
    $user = $stm->fetch(); //me devuelve una fila con array asociativo
    
    if(!$user){
        echo json_encode(['error' => "El usuario no existe"]);
        exit;
    }
     //Si el el usuario con la contraseña correcta se verifica
     if(!password_verify($contrasenia, $user['contrasenia'])){
        http_response_code(401);
        echo json_encode(['error' => 'Contraseña incorrecta']);
        exit;
    }
  //Iniciar session
  $_SESSION['id_cliente'] = $user['id_cliente'];
  $_SESSION['rol'] = $user['rol'];
  $_SESSION['ultima_actividad'] = time(); //Guardo la hora
  
  echo json_encode(["message" => 'Sesión éxitoso']);
  echo json_encode([
    "message" => "Bienvenido, {$user['nombre']}"
  ]);

   }catch(PDOException $e){
    echo json_encode(['error'=> 'Credenciales incorrectas']);
   }
?>