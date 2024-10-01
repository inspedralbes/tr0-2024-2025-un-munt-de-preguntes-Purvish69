<?php
session_start();

$datosRecibidos = file_get_contents('php://input');
$respuestasDeUsuario = json_decode($datosRecibidos, true);

$preguntasSeleccionadas = $_SESSION['preguntasSeleccionadas'];

$respuestasCorrectas = 0;
$totalPreguntas = count($preguntasSeleccionadas);

// Comparar las respuestas del usuario con las correctas
for ($i = 0; $i < $totalPreguntas; $i++) {
    $pregunta = $preguntasSeleccionadas[$i];

    // Encontrar el índice de la respuesta correcta en el array de respuestas
    $correctIndex = array_search(true, array_column($pregunta['respostes'], 'correcta'));

    // Verificar si el usuario seleccionó la respuesta correcta
    if (isset($respuestasDeUsuario[$i]) && $respuestasDeUsuario[$i] == $correctIndex) {
        $respuestasCorrectas++;
    }
}

// Enviar el resultado al cliente
$resultado = ['totalPreguntas' => $totalPreguntas, 'respuestasCorrectas' => $respuestasCorrectas];
echo json_encode($resultado);
?> 
