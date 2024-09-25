<?php
// Iniciar la sesión
session_start();

// Leer el archivo JSON
$jsonData = file_get_contents('data.json');
$preguntas = json_decode($jsonData, true)['preguntes'];

// Si no hay preguntas guardadas en la sesión, seleccionamos 10 preguntas aleatorias
if (!isset($_SESSION['preguntas'])) {
    // Mezclar las preguntas y seleccionar 10 aleatorias
    shuffle($preguntas);
    $preguntasSeleccionadas = array_slice($preguntas, 0, 10);

    // Guardar las preguntas en la sesión
    $_SESSION['preguntas'] = $preguntasSeleccionadas;
    $_SESSION['respuestas'] = []; // Inicializar el array de respuestas
} else {
    // Recuperar preguntas ya seleccionadas
    $preguntasSeleccionadas = $_SESSION['preguntas'];
}

// Verificar si se han enviado respuestas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar las respuestas enviadas
    $respuestasUsuario = $_POST['respuestas'] ?? [];
    
    // Guardar las respuestas en la sesión
    $_SESSION['respuestas'] = $respuestasUsuario;
    
    // Enviar una respuesta de éxito al frontend
    echo json_encode(['status' => 'success']);
    exit;
}

// Enviar las preguntas como respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($preguntasSeleccionadas);
?>
