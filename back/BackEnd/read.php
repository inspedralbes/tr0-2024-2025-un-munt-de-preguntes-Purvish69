<?php


session_start();

include 'cone.php';
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