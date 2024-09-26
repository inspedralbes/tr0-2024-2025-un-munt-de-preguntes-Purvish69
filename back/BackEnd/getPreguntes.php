<?php

session_start();

$numPreguntas = 10;

if (isset($_GET['numPreguntas'])) {
    $numPreguntas = intval($_GET['numPreguntas']);
}

// Leer el archivo json
$jsonData = file_get_contents('data.json');
$preguntas = json_decode($jsonData, true)['preguntes'];

// Esta bucle de if es para guardar las preguntas si no hay preguntas guardadas en la sesion, y selecciona las preguntas aleatorias
if (!isset($_SESSION['preguntas'])) {
    shuffle($preguntas);
    $preguntasSeleccionadas = array_splice($preguntas, 0, $numPreguntas);
    $_SESSION['preguntas'] = $preguntasSeleccionadas;
} else {
    $preguntasSeleccionadas = $_SESSION['preguntas'];
}

//Eliminar el indice que marca la respuesta correcta
for ($i = 0; $i < count($preguntasSeleccionadas); $i++) {
    for ($j = 0; $j < count($preguntasSeleccionadas[$i]['respostes']); $j++) {
        unset($preguntasSeleccionadas[$i]['respostes'][$j]['correcta']);
    }
}

// Enviar las preguntas seleccionadas al FrontEnd
echo json_encode($preguntasSeleccionadas);

?>