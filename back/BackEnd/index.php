<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuestionario</title>
   <style>
        .hidden {
            display: none;
        }

        .result-message {
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style> 
</head>
<body>

    <?php

    // Iniciar la sesión para poder almacenar y acceder a los datos en $_SESSION
    session_start(); 

    // Cargar el archivo JSON que contiene las preguntas
    $jsonFile = 'data.json';
    $jsonData = file_get_contents($jsonFile);
    $data = json_decode($jsonData, true); 


    if (!isset($_SESSION['preguntas_seleccionadas'])) {
        $preguntas = $data['preguntes']; 
        shuffle($preguntas); 
        $preguntasSeleccionadas = array_slice($preguntas, 0, 10); 
        $_SESSION['preguntas_seleccionadas'] = $preguntasSeleccionadas; 
        $_SESSION['preguntaActual'] = 0; 
        $_SESSION['respuestas'] = [];
        $_SESSION['mensajeDeResultado'] = ''; 
    } else {
        $preguntasSeleccionadas = $_SESSION['preguntas_seleccionadas']; 
    }

    $preActual = $_SESSION['preguntaActual']; 
    $pregunta = isset($preguntasSeleccionadas[$preActual]) ? $preguntasSeleccionadas[$preActual] : null; 

    // Si no hay más preguntas, mostrar la puntuación y un botón para reiniciar el cuestionario
    if ($pregunta === null) {
        $totalPreguntas = count($_SESSION['preguntas_seleccionadas']); 
        $respuestasCorrectas = 0;

        // Contar las respuestas correctas
        for ($index = 0; $index < count($_SESSION['respuestas']); $index++) {
            $respuestaId = $_SESSION['respuestas'][$index]; 

            // Verificar si la respuesta es correcta
            for ($j = 0; $j < count($_SESSION['preguntas_seleccionadas'][$index]['respostes']); $j++) {
                $respuesta = $_SESSION['preguntas_seleccionadas'][$index]['respostes'][$j];
                if ($respuesta['id'] == $respuestaId && $respuesta['correcta']) {
                    $respuestasCorrectas++; 
                }
            }
        }

        // Mostrar la puntuación final
        echo "<h1>Cuestionario completado</h1>";
        echo "<b><p>Tu puntuación es: $respuestasCorrectas/$totalPreguntas</p></b>";
        echo "<form method='POST'>";
        echo "<button type='submit' name='action' value='restart'>Iniciar cuestionario</button>";
        echo "</form>";

        // Reiniciar el cuestionario si el usuario hace clic en el botón de reinicio
        if (isset($_POST['action']) && $_POST['action'] == 'restart') {
            session_unset();
            session_destroy(); 
            header('Location: ' . $_SERVER['PHP_SELF']); 
            exit();
        }
    } else {
        ?>

        <h1>Cuestionario</h1><hr>

        <?php
        // Mostrar el resultado de la respuesta anterior (si existe)
        if (isset($_SESSION['resultado']) && $_SESSION['resultado'] != '') {
            echo "<div class='mensajeDeRes'>" . $_SESSION['resultado'] . "</div>";
            $_SESSION['resultado'] = ''; 
        }
        ?>

        <form id="preActual" method="POST">
            <div id="contenedorPregunta">
                <?php
                // Mostrar la pregunta actual y sus respuestas
                echo "<h3>" . ($preActual + 1) . ". " . $pregunta['pregunta'] . "</h3>";
                echo "<img src='" . $pregunta['imatge'] . "' alt='Imagen de la pregunta' style='max-width:300px;'><br><br>";

                // Recorrer y mostrar las posibles respuestas de la pregunta
                for ($i = 0; $i < count($pregunta['respostes']); $i++) {
                    $respuesta = $pregunta['respostes'][$i];
                    echo "<input type='radio' name='respuesta' value='" . $respuesta['id'] . "'>";
                    echo $respuesta['resposta'] . "<br><br>";
                }
                ?>
            </div>
            <button type="submit" name="action" value="next">Siguiente</button>
        </form>

        <?php
    }

    // Procesar la acción del formulario (siguiente pregunta o reinicio)
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'next') {
            // Guardar la respuesta seleccionada por el usuario
            if (isset($_POST['respuesta'])) {
                $idRespuesta = $_POST['respuesta'];
                $_SESSION['respuestas'][$preActual] = $idRespuesta; // aqui lo voy a guardar las respuestas de SESSION

                // Verificar si la respuesta es correcta
                $respuestaCorrecta = false;
                for ($i = 0; $i < count($pregunta['respostes']); $i++) {
                    $respuesta = $pregunta['respostes'][$i];
                    if ($respuesta['id'] == $idRespuesta && $respuesta['correcta']) {
                        $respuestaCorrecta = true;
                        break;
                    }
                }

                // Almacenar el resultado de la verificación (correcto o incorrecto)
                $_SESSION['resultado'] = $respuestaCorrecta ? "Respuesta correcta." : "Respuesta incorrecta.";

                // Avanzar a la siguiente pregunta
                $_SESSION['preguntaActual']++;

                // Verificar si se han respondido todas las preguntas
                if ($_SESSION['preguntaActual'] >= count($_SESSION['preguntas_seleccionadas'])) {
                    // Finalizar el cuestionario y mostrar la puntuación
                    $_SESSION['preguntaActual'] = count($_SESSION['preguntas_seleccionadas']); 
                    header('Location: ' . $_SERVER['PHP_SELF']); 
                    exit();
                } else {
                    // Recargar la página para mostrar la siguiente pregunta
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
            }
        }
    }
    ?>

</body>
</html>
