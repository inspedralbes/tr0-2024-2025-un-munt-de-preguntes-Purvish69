<?php
session_start();
$host = "localhost";
$usuario = "root";
$password = "";
$nombreBD = "autoescuela";

// Crear conexión a la base de datos
$conn = new mysqli($host, $usuario, $password, $nombreBD);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

// Obtener los datos de la pregunta para la edición
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $preguntaId = $_GET['id'];

    // Consulta para obtener la pregunta y sus respuestas
    $sqlPregunta = "SELECT p.id, p.pregunta, p.imagen, r.id AS respuesta_id, r.respuesta, r.correcta 
                    FROM preguntes p 
                    LEFT JOIN respostes r ON p.id = r.pregunta_id 
                    WHERE p.id = ?";
    $stmt = $conn->prepare($sqlPregunta);
    $stmt->bind_param('i', $preguntaId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $preguntaData = [];
        while ($row = $result->fetch_assoc()) {
            $preguntaData['pregunta'] = $row['pregunta'];
            $preguntaData['imagen'] = $row['imagen'];
            $preguntaData['respuestas'][] = [
                'respuesta_id' => $row['respuesta_id'],
                'respuesta' => $row['respuesta'],
                'correcta' => $row['correcta']
            ];
        }
        echo json_encode(['success' => true, 'data' => $preguntaData]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Pregunta no encontrada']);
    }
}

// Actualizar la pregunta y las respuestas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $preguntaId = $_POST['id'];
    $pregunta = $_POST['pregunta'];
    $imagen = $_POST['imagen'];
    $opciones = [
        $_POST['opcion1'],
        $_POST['opcion2'],
        $_POST['opcion3'],
        $_POST['opcion4']
    ];
    $respuestaCorrecta = $_POST['respuesta_correcta'];

    $conn->begin_transaction(); 
    try {
        // Actualizar la pregunta
        $sqlActualizarPregunta = "UPDATE preguntes SET pregunta = ?, imagen = ? WHERE id = ?";
        $stmtPregunta = $conn->prepare($sqlActualizarPregunta);
        $stmtPregunta->bind_param('ssi', $pregunta, $imagen, $preguntaId);
        $stmtPregunta->execute();

        // Actualizar las respuestas
        foreach ($opciones as $index => $opcion) {
            $respuestaId = $_POST['respuesta_id_' . ($index + 1)];
            $sqlActualizarRespuesta = "UPDATE respostes SET respuesta = ?, correcta = ? WHERE id = ?";
            $correcta = ($index + 1 == $respuestaCorrecta) ? 1 : 0;
            $stmtRespuesta = $conn->prepare($sqlActualizarRespuesta);
            $stmtRespuesta->bind_param('sii', $opcion, $correcta, $respuestaId);
            $stmtRespuesta->execute();
        }

        $conn->commit(); // Confirmar transacción
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback(); // Revertir transacción en caso de error
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la pregunta: ' . $e->getMessage()]);
    }
}

$conn->close();
?>
