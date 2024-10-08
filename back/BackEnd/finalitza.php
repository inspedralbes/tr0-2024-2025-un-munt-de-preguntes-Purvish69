<?php

session_start();

include 'cone.php';

// Obtener las respuestas del usuario
$datosRecibidos = file_get_contents('php://input');
$respuestasDeUsuario = json_decode($datosRecibidos, true);

// Verificar que las respuestas del usuario se recibieron correctamente
if (!$respuestasDeUsuario) {
    echo json_encode([
        "error" => "No se recibieron las respuestas del usuario."
    ]);
    exit();
}

$preguntasSeleccionadas = $_SESSION['preguntasSeleccionadas']; // Las preguntas seleccionadas almacenadas en la sesión
$respuestasCorrectas = 0;
$totalPreguntas = count($preguntasSeleccionadas);

for ($i = 0; $i < $totalPreguntas; $i++) {
    $pregunta = $preguntasSeleccionadas[$i];
    $preguntaId = $pregunta['id'];
    $respuestaUsuario = $respuestasDeUsuario[$i]; // Este es el índice seleccionado por el usuario

    // Consulta para obtener la respuesta correcta de la base de datos
    $sql = "SELECT id FROM respostes WHERE pregunta_id = ? AND correcta = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $preguntaId);
    $stmt->execute();
    $stmt->bind_result($idRespuestaCorrecta);
    $stmt->fetch();
    $stmt->close();

    // Aquí debes comparar el ID de la respuesta seleccionada con el ID de la respuesta correcta
    if ($pregunta['respostes'][$respuestaUsuario]['id'] == $idRespuestaCorrecta) {
        $respuestasCorrectas++;
    }
}


$resultado = [
    'totalPreguntas' => $totalPreguntas,
    'respuestasCorrectas' => $respuestasCorrectas // Corrige el nombre de la clave aquí
];

header('Content-Type: application/json');
echo json_encode($resultado);

$conn->close();
?>
