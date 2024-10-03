<?php


$host = "localhost";
$usuario = "root";
$password = "";
$nombreBD = "autoescuela";

// Crear conexion
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
$crearTabla = "CREATE TABLE IF NOT EXISTS preguntes (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pregunta VARCHAR(255) NOT NULL,
    imagen VARCHAR(255),
    respuesta_correcta INT(1) NOT NULL
)";

// verificar la tabla de preguntes
if ($conn->query($crearTabla) === TRUE) {
    echo "Tabla 'preguntes' creada.<br>";
} else {
    echo "Error creando la tabla: " . $conn->error;
}

// Crear tabla 'respostes'
$crearTablaRespostes = "CREATE TABLE IF NOT EXISTS respostes (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pregunta_id INT(6) UNSIGNED NOT NULL ,
    respuesta VARCHAR(255) NOT NULL,
    correcta BOOLEAN NOT NULL,
    FOREIGN KEY (pregunta_id) REFERENCES preguntes(id)
)";

// verificar la tabla de respostes
if ($conn->query($crearTablaRespostes) === TRUE) {
    echo "Tabla 'respostes' creada<br>";
} else {
    echo "Error creando la tabla: " . $conn->error;
}

// Leer el archivo JSON
$jsonData = file_get_contents('data.json');
$preguntas = json_decode($jsonData,true);

// Insertar sobre las preguntas y respuestas
foreach($preguntas['preguntes'] as $pregunta){
    $preguntaTexto = $pregunta['pregunta'];
    $imagen = $pregunta['imatge'];

    //Insertar la pregunta
    $stmt = $conn->prepare("INSERT INTO preguntes (pregunta, imagen, respuesta_correcta) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $preguntaTexto,$imagen,$respuestaCorrecta);

    //Encontrar la respuesta correcta 
    foreach ($pregunta['respostes'] as $respuesta) {
        if($respuesta['correcta']) {
            $respuestaCorrecta = $respuesta['id'];
        }
    }
    $stmt->execute();
    $preguntaID = $conn->insert_id; // obtener el ID de la preguntaa recien insertada

    // Insertar las preguntas
    foreach($pregunta['respostes'] as $respuesta){
        $respuestaTexto = $respuesta['resposta'];
        $correcta = $respuesta['correcta'] ? 1 : 0; // Convertir boolean a 1 o 0

        $stmt = $conn->prepare("INSERT INTO respostes (pregunta_id, respuesta, correcta) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $preguntaID,$respuestaTexto,$correcta);
        $stmt->execute();
    }
}

echo "Datos insertados correctamente.";
$stmt->close();
$conn->close();

?>