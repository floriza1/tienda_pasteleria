<?php
require 'config.php';

session_start();
session_destroy();
echo json_encode(["message" => "La sesión ha sido cerrada"]);

?>