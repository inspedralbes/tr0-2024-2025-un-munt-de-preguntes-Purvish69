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

// Consulta para obtener todas las preguntas y sus respuestas
$sql = "SELECT p.id AS pregunta_id, p.pregunta, p.imagen, r.id AS respuesta_id, r.respuesta, r.correcta 
        FROM preguntes p 
        LEFT JOIN respostes r ON p.id = r.pregunta_id 
        ORDER BY p.id, r.id";

$result = $conn->query($sql);

$preguntas = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $preguntaId = $row['pregunta_id'];
        if (!isset($preguntas[$preguntaId])) {
            // Si no existe la pregunta aún en el array, la agregamos
            $preguntas[$preguntaId] = [
                'id' => $preguntaId,
                'pregunta' => $row['pregunta'],
                'imagen' => $row['imagen'],
                'respuestas' => [],
            ];
        }
        // Añadir las respuestas a la pregunta
        $preguntas[$preguntaId]['respuestas'][] = [
            'id' => $row['respuesta_id'],
            'respuesta' => $row['respuesta'],
            'correcta' => $row['correcta'],
        ];
    }
}

// Cambiar el array para que cada elemento tenga un número como índice."
$preguntasArray = array_values($preguntas);

// Enviar el resultado en formato JSON
header('Content-Type: application/json');
echo json_encode($preguntasArray);

$conn->close();
?>
