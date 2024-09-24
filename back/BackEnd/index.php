<?php
session_start();

// Esto permite acceder CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Conectar con el archivo JSON y decodificarlo
$jsonFile = 'data.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Cargar preguntas si no están en sesión
    if (!isset($_SESSION['preguntas_seleccionadas'])) {
        $preguntas = $data['preguntes'];
        shuffle($preguntas);
        $_SESSION['preguntas_seleccionadas'] = array_slice($preguntas, 0, 10);
        $_SESSION['preguntaActual'] = 0;
        $_SESSION['respuestas'] = [];
    }
    echo json_encode($_SESSION['preguntas_seleccionadas']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'next') {
        $respuestaId = $_POST['respuesta'] ?? null;
        $preguntasSeleccionadas = $_SESSION['preguntas_seleccionadas'];
        $preActual = $_SESSION['preguntaActual'];

        // Guardar la respuesta seleccionada
        $esCorrecta = false;
        if ($respuestaId !== null) {
            $_SESSION['respuestas'][$preActual] = $respuestaId;

            // Verificar si la respuesta es correcta
            foreach ($preguntasSeleccionadas[$preActual]['respostes'] as $respuesta) {
                if ($respuesta['id'] == $respuestaId && $respuesta['correcta']) {
                    $esCorrecta = true;
                    break;
                }
            }
        }

        // Avanzar a la siguiente pregunta
        $_SESSION['preguntaActual']++;

        // Verificar si se han respondido todas las preguntas
        if ($_SESSION['preguntaActual'] >= count($preguntasSeleccionadas)) {
            // Calcular puntuación final
            $totalPreguntas = count($preguntasSeleccionadas);
            $respuestasCorrectas = 0;
            foreach ($_SESSION['respuestas'] as $index => $respuestaId) {
                foreach ($preguntasSeleccionadas[$index]['respostes'] as $respuesta) {
                    if ($respuesta['id'] == $respuestaId && $respuesta['correcta']) {
                        $respuestasCorrectas++;
                    }
                }
            }
            $_SESSION['resultado'] = "Tu puntuación es: $respuestasCorrectas/$totalPreguntas";
            echo json_encode(['finished' => true, 'result' => $_SESSION['resultado']]);
            session_unset(); // Reiniciar sesión para permitir un nuevo cuestionario
            session_destroy();
        } else {
            echo json_encode(['finished' => false, 'correcta' => $esCorrecta]);
        }
    }

    if ($action === 'restart') {
        // Reiniciar cuestionario
        session_unset();
        session_destroy();
        echo json_encode(['status' => 'restart']);
    }
}
?>
