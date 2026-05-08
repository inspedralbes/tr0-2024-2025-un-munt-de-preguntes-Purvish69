<?php
session_start();

include 'cone.php';

// Verificar si se ha recibido el ID de la pregunta
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $preguntaId = $_GET['id'];

    // Eliminar las respuestas asociadas a la pregunta
    $sqlEliminarRespuestas = "DELETE FROM respostes WHERE pregunta_id = ?";
    $stmtRespuestas = $conn->prepare($sqlEliminarRespuestas);
    $stmtRespuestas->bind_param('i', $preguntaId);
    $stmtRespuestas->execute();

    // Eliminar la pregunta
    $sqlEliminarPregunta = "DELETE FROM preguntes WHERE id = ?";
    $stmtPregunta = $conn->prepare($sqlEliminarPregunta);
    $stmtPregunta->bind_param('i', $preguntaId);
    $stmtPregunta->execute();

    // Verificar si se eliminaron correctamente
    if ($stmtPregunta->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la pregunta']);
    }

    $stmtPregunta->close();
    $stmtRespuestas->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID de pregunta no proporcionado']);
}

$conn->close();
?>
