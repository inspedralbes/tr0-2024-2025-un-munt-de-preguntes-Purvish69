<?php
session_start();
$host = "localhost";
$usuario = "root";
$password = "";
$nombreBD = "autoescuela";

// Crear conexión a la base de datos
$conn = new mysqli($host, $usuario, $password, $nombreBD);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se han recibido los datos de la pregunta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $preguntaId = $_POST['id'];
    $pregunta = $_POST['pregunta'];
    $imagen = $_POST['imagen'];
    $opciones = [
        $_POST['opcion1'],
        $_POST['opcion2'],
        $_POST['opcion3'],
        $_POST['opcion4']
    ];
    $respuestaCorrecta = $_POST['respuesta_correcta'];

    // Actualizar la pregunta
    $sqlActualizarPregunta = "UPDATE preguntes SET pregunta = ?, imagen = ? WHERE id = ?";
    $stmtPregunta = $conn->prepare($sqlActualizarPregunta);
    $stmtPregunta->bind_param('ssi', $pregunta, $imagen, $preguntaId);
    $stmtPregunta->execute();

    // Actualizar las respuestas
    foreach ($opciones as $index => $opcion) {
        $sqlActualizarRespuesta = "UPDATE respostes SET respuesta = ?, correcta = ? WHERE pregunta_id = ? AND id = ?";
        $correcta = ($index + 1 == $respuestaCorrecta) ? 1 : 0;
        $stmtRespuesta = $conn->prepare($sqlActualizarRespuesta);
        $stmtRespuesta->bind_param('siii', $opcion, $correcta, $preguntaId, $index + 1);
        $stmtRespuesta->execute();
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'ID de pregunta no proporcionado']);
}

$conn->close();
?>
