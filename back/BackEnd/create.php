<?php
session_start();
include 'cone.php';

try {
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
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    $stmt->bind_param("ss", $pregunta, $imagen);
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    $preguntaId = $stmt->insert_id;

    // Insertar las respuestas
    foreach ($respuestas as $index => $respuesta) {
        $correcta = ($index + 1 == $respuestaCorrecta) ? 1 : 0;
        $stmt = $conn->prepare("INSERT INTO respostes (pregunta_id, respuesta, correcta) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta para respuesta: " . $conn->error);
        }
        $stmt->bind_param("isi", $preguntaId, $respuesta, $correcta);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta para respuesta: " . $stmt->error);
        }
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>
