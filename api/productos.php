<?php
require 'config.php';


    if($_SERVER["REQUEST_METHOD"] !== "GET"){
      http_response_code(405);
      json_encode(['error' => "El método no es GET"]);
      exit;
    }

 
    $sql = "SELECT * FROM productos";
    $stm= $pdo->prepare($sql);
    $stm->execute();
    $productos_encontrados = $stm->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode(['productos' =>$productos_encontrados]);
 
   ?>