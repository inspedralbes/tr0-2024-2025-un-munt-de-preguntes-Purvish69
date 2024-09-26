<?php
/*
session_start();

$jsonData = file_get_contents('data.json');
$preguntas = json_decode($jsonData, true)['preguntes'];

// Si no hay preguntas guardadas en la sesión, seleccionamos 10 preguntas aleatorias desde json
if (!isset($_SESSION['preguntas'])) {
  
    shuffle($preguntas);
    $preguntasSeleccionadas = array_slice($preguntas, 0, 10);

    // Aqui guardo las preguntas en la sesión
    $_SESSION['preguntas'] = $preguntasSeleccionadas;
    $_SESSION['respuestas'] = []; 
} else {
    // Recuperar preguntas que ya esta seleccionadas
    $preguntasSeleccionadas = $_SESSION['preguntas'];
}

// Esto verificar si se han enviado respuestas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $respuestasUsuario = $_POST['respuestas'] ?? [];

    $_SESSION['respuestas'] = $respuestasUsuario;
    
    echo json_encode(['status' => 'success']);
    exit;
}

// Enviar las preguntas como respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($preguntasSeleccionadas);
*/
?>
