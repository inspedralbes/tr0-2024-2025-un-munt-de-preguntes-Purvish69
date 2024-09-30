<?php

session_start();
$jsonFile = file_get_contents('data.json');
$datos = json_decode($jsonFile,true);
$preguntes = $datos['preguntes'];

shuffle($preguntes);

if(!isset($_SESSION['preguntesSeleccionades'])){
    $preguntasSeleccionadas = [];

    while(count($preguntasSeleccionadas)< 10){
        $preAleatoria = rand(0,count($preguntes)-1);
        if (!in_array($preguntes[$preAleatoria], $preguntasSeleccionadas)){
            $preguntasSeleccionadas[] = $preguntes[$preAleatoria];
        }
    }
    $_SESSION['preguntasSeleccionadas'] = $preguntasSeleccionadas;
}else{
    $preguntasSeleccionadas = $_SESSION['preguntasSeleccionadas'];
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
