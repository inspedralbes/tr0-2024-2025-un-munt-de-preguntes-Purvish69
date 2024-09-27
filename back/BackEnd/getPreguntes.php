<?php
session_start();

// Leer el número de preguntas a devolver desde el parámetro GET
$numPreguntas = isset($_GET['numPreguntas']) ? (int)$_GET['numPreguntas'] : 10;

// Verificar si ya hay preguntas seleccionadas en la sesión
if (!isset($_SESSION['preguntas']) || count($_SESSION['preguntas']) != $numPreguntas) {
    // Leer el archivo JSON de preguntas
    $jsonData = file_get_contents('data.json');
    $preguntas = json_decode($jsonData, true)['preguntes'];

    // Barajar las preguntas y seleccionar el número deseado
    shuffle($preguntas);
    $preguntasSeleccionadas = array_slice($preguntas, 0, $numPreguntas);

    // Guardar las preguntas seleccionadas en la sesión
    $_SESSION['preguntas'] = $preguntasSeleccionadas;
} else {
    // Usar las preguntas que ya están en la sesión
    $preguntasSeleccionadas = $_SESSION['preguntas'];
}

// Eliminar el índice que marca la respuesta correcta antes de enviar al frontend
foreach ($preguntasSeleccionadas as &$pregunta) {
    foreach ($pregunta['respostes'] as &$respuesta) {
        unset($respuesta['correcta']);
    }
}

// Enviar las preguntas seleccionadas al frontend
echo json_encode($preguntasSeleccionadas);
?>
