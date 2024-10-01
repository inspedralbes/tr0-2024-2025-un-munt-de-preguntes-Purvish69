<?php
// Parámetros de conexión
$host = "localhost";
$usuario = "root";
$password = "";
$nombreBD = "autoescuela";

$conn = new mysqli($host, $usuario, $password);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS $nombreBD";
if($conn->query($sql) === TRUE){
    echo "Base de datos creado.<br>";
}else{
    echo "Error en crear la base de datos: " . $conn->error;
}

$conn->select_db($nombreBD);

// Crear tabla 'preguntes'
$crearTabla = "
CREATE TABLE IF NOT EXISTS preguntes (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pregunta VARCHAR(255) NOT NULL,
    imagen VARCHAR(255),
    respuesta_correcta INT(1) NOT NULL
)";

if ($conn->query($crearTabla) === TRUE) {
    echo "Tabla 'preguntes' creada.<br>";
} else {
    echo "Error creando la tabla: " . $conn->error;
}

// Crear tabla 'respostes'
$crearTablaRespostes = "
CREATE TABLE IF NOT EXISTS respostes (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pregunta_id INT(6) UNSIGNED NOT NULL ,
    respuesta VARCHAR(255) NOT NULL,
    correcta BOOLEAN NOT NULL,
    FOREIGN KEY (pregunta_id) REFERENCES preguntes(id)
)";

if ($conn->query($crearTablaRespostes) === TRUE) {
    echo "Tabla 'respostes' creada<br>";
} else {
    echo "Error creando la tabla: " . $conn->error;
}


?>
