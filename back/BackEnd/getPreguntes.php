<?php
session_start();
$host = "localhost";
$usuario = "root";
$password = "";
$nombreBD = "autoescuela";

// Crear conexión a la base de datos
$conn = new mysqli($host, $usuario, $password, $nombreBD);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener 10 preguntas aleatorias
$sqlPreguntas = "SELECT id AS pregunta_id, pregunta, imagen FROM preguntes ORDER BY RAND() LIMIT 10";
$resultPreguntas = $conn->query($sqlPreguntas);

$preguntas = [];
if ($resultPreguntas->num_rows > 0) {
    while ($row = $resultPreguntas->fetch_assoc()) {
        $preguntaId = $row['pregunta_id'];
        
        // Añadir la pregunta al array
        $preguntas[$preguntaId] = [
            'id' => $preguntaId,
            'pregunta' => $row['pregunta'],
            'imatge' => $row['imagen'],
            'respostes' => []
        ];
    }
}

// Ahora obtenemos las respuestas para las preguntas seleccionadas
if (!empty($preguntas)) {
    // Obtener IDs de las preguntas seleccionadas
    $preguntaIds = implode(',', array_keys($preguntas));

    // Consulta para obtener las respuestas correspondientes
    $sqlRespuestas = "SELECT r.id AS respuesta_id, r.respuesta, r.correcta, r.pregunta_id 
                      FROM respostes r 
                      WHERE r.pregunta_id IN ($preguntaIds)";
    
    $resultRespuestas = $conn->query($sqlRespuestas);
    
    // Agrupar las respuestas por pregunta
    while ($row = $resultRespuestas->fetch_assoc()) {
        $preguntaId = $row['pregunta_id'];
        if (isset($preguntas[$preguntaId])) {
            $preguntas[$preguntaId]['respostes'][] = [
                'id' => $row['respuesta_id'],
                'resposta' => $row['respuesta'],
                'correcta' => $row['correcta']
            ];
        }
    }
}

// Convertir el array con IDs en un array indexado
$preguntasArray = array_values($preguntas); 

// Eliminar el índice que marca la respuesta correcta antes de enviar al frontend
foreach ($preguntasArray as &$pregunta) {
    foreach ($pregunta['respostes'] as &$respuesta) {
        unset($respuesta['correcta']);
    }
}

// Guardar las preguntas seleccionadas en la sesión
$_SESSION['preguntasSeleccionadas'] = $preguntasArray;

// Enviar las preguntas seleccionadas al frontend en formato JSON
echo json_encode($preguntasArray);

$conn->close();
?>
