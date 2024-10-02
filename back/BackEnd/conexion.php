<?php

$host = "localhost";
$usuario = "root";
$password = "";
$nombreBD = "autoescuela";

// Crear conexion
$conn = new mysqli($host, $usuario, $password);

if ($conn->connect_error) { //verificar la conexion
    die("Conexión fallida: " . $conn->connect_error);
}

?>