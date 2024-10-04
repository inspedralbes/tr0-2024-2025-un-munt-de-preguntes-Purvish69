<?php
// Activar la visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'cone.php'; // Archivo de conexión a la base de datos

// Verificar si hay conexión a la base de datos
if (!$conn) {
    echo json_encode(['error' => 'Error al conectar a la base de datos.']);
    exit();
}

// Obtener las respuestas del usuario desde el cuerpo de la solicitud
$datosRecibidos = file_get_contents('php://input');
$respuestasDeUsuario = json_decode($datosRecibidos, true);

// Verificar que las respuestas fueron recibidas correctamente
if (!$respuestasDeUsuario) {
    echo json_encode(['error' => 'No se recibieron las respuestas del usuario o el formato no es válido.']);
    exit();
}

// Verificar que las preguntas seleccionadas están en la sesión
$preguntasSeleccionadas = isset($_SESSION['preguntasSeleccionadas']) ? $_SESSION['preguntasSeleccionadas'] : [];
if (empty($preguntasSeleccionadas)) {
    echo json_encode(['error' => 'No se encontraron preguntas en la sesión.']);
    exit();
}

$respuestasCorrectas = 0;
$totalPreguntas = count($preguntasSeleccionadas);

// Procesar cada pregunta y verificar si la respuesta es correcta
for ($i = 0; $i < $totalPreguntas; $i++) {
    $pregunta = $preguntasSeleccionadas[$i];
    $preguntaId = $pregunta['id'];
    $respuestaUsuario = $respuestasDeUsuario[$i]; // Este es el índice de la respuesta seleccionada por el usuario

    // Consulta para obtener la respuesta correcta de la base de datos
    $sql = "SELECT id FROM respostes WHERE pregunta_id = ? AND correcta = 1";
    $stmt = $conn->prepare($sql);

    // Verificar si la consulta fue preparada correctamente
    if ($stmt === false) {
        echo json_encode(['error' => 'Error preparando la consulta SQL.']);
        exit();
    }

    $stmt->bind_param("i", $preguntaId);
    $stmt->execute();
    $stmt->bind_result($idRespuestaCorrecta);
    $stmt->fetch();
    $stmt->close();

    // Verificar si se obtuvo una respuesta correcta
    if (!$idRespuestaCorrecta) {
        echo json_encode(['error' => 'No se encontró la respuesta correcta en la base de datos para la pregunta ID: ' . $preguntaId]);
        exit();
    }

    // Comparar la respuesta seleccionada por el usuario con la respuesta correcta
    if ($pregunta['respostes'][$respuestaUsuario]['id'] == $idRespuestaCorrecta) {
        $respuestasCorrectas++;
    }
}

// Preparar el resultado para enviar de vuelta al cliente
$resultado = [
    'totalPreguntas' => $totalPreguntas,
    'respuestasCorrectas' => $respuestasCorrectas
];

header('Content-Type: application/json'); // Asegurarse de que el encabezado indique JSON
echo json_encode($resultado);

$conn->close();
