<?php
session_start();

include 'cone.php';

// Obtener los datos del formulario
$pregunta = $_POST['pregunta'];
$imagen = $_POST['imagen'];
$respuestas = [
    $_POST['opcion1'],
    $_POST['opcion2'],
    $_POST['opcion3'],
    $_POST['opcion4']
];
$respuestaCorrecta = $_POST['respuesta_correcta'];

// Insertar la pregunta en la base de datos
$stmt = $conn->prepare("INSERT INTO preguntes (pregunta, imagen) VALUES (?, ?)");
$stmt->bind_param("ss", $pregunta, $imagen);
$stmt->execute();
$preguntaId = $stmt->insert_id; // Obtener el ID de la pregunta insertada

// Insertar las respuestas
foreach ($respuestas as $index => $respuesta) {
    $correcta = ($index + 1 == $respuestaCorrecta) ? 1 : 0; // Determinar si es correcta
    $stmt = $conn->prepare("INSERT INTO respostes (pregunta_id, respuesta, correcta) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $preguntaId, $respuesta, $correcta);
    $stmt->execute();
}

echo json_encode(['success' => true]);

$stmt->close();
$conn->close();
?>