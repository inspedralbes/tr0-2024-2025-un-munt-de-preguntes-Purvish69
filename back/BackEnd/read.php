<?php

// read.php
session_start();
$host = "localhost";
$usuario = "root";
$password = "";
$nombreBD = "autoescuela";

$conn = new mysqli($host, $usuario, $password, $nombreBD);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT * FROM preguntes";
$result = $conn->query($sql);
$preguntas = [];

while ($pregunta = $result->fetch_assoc()) {
    $preguntaId = $pregunta['id'];
    
    // Obtener las respuestas asociadas a la pregunta
    $sqlRespuestas = "SELECT * FROM respostes WHERE pregunta_id = ?";
    $stmt = $conn->prepare($sqlRespuestas);
    $stmt->bind_param('i', $preguntaId);
    $stmt->execute();
    $respuestasResult = $stmt->get_result();
    
    $respuestas = [];
    while ($respuesta = $respuestasResult->fetch_assoc()) {
        $respuestas[] = $respuesta;
    }
    
    $pregunta['respuestas'] = $respuestas; // Añadir respuestas a la pregunta
    $preguntas[] = $pregunta;
}

echo json_encode($preguntas);
$conn->close();
?>