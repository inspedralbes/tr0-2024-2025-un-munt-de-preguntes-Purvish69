<?php

session_start();

// Recibir los datos enviados desde el cliente
$datosRecebidos = file_get_contents('php://input');
$respuestasDeUsuario = json_decode($datosRecebidos,true);

$preguntasSeleccionadas = $_SESSION['preguntas'];

//Inicializar variable
$respuestasCorrectas = 0;
$totalpregunta = count($preguntasSeleccionadas);

// Comparar lasa respuestas del usuario con las correctas

for($i = 0; $i< count($preguntasSeleccionadas); $i++){
  $pregunta = $preguntasSeleccionadas[$i];

  $respuestasCorrectasID = array_search(true,array_column($preguntasSeleccionadas[$i]['respostes'],'correcta'));

  if (isset($respuestasDeUsuario[$i])&& $respuestasDeUsuario[$i] == $respuestasCorrectasID){
   $respuestasCorrectas++; 
  }
}

//ENviar el resultado al cliente

$resultado = ['totalPreguntas' => $totalpregunta,'respuestasCorrectas' => $respuestasCorrectas ];

echo json_encode($resultado);

?>