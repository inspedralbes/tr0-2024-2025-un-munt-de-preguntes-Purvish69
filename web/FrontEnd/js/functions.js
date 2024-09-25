// Variables globales para las preguntas, el índice actual y el contador de respuestas correctas
let preguntas = [];
let indiceActual = 0;
let respuestasCorrectas = 0;

// Función para cargar las preguntas del backend
function cargarPreguntas() {
    fetch('http://localhost:8800/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/index.php')
        .then(response => response.json())
        .then(data => {
            preguntas = data;
            mostrarPregunta();
        })
        .catch(error => console.error('Error al cargar las preguntas:', error));
}

// Función para mostrar una pregunta con las opciones de respuesta
function mostrarPregunta() {
    if (indiceActual >= preguntas.length) {
        mostrarResultados(); // Mostrar resultados si se han respondido todas las preguntas
        return;
    }

    const pregunta = preguntas[indiceActual];
    let htmlString = "";

    // Generar el HTML de la pregunta y las opciones
    htmlString += `<h3>${indiceActual + 1}) ${pregunta.pregunta}</h3>`;
    
    // Mostrar la imagen de la pregunta si existe
    if (pregunta.imatge) {
        htmlString += `<img src="${pregunta.imatge}" alt="Imagen de la pregunta" style="width: 200px; height: auto;"><br><br>`;
    }

    // Generar las opciones de respuesta
    pregunta.respostes.forEach((respuesta, index) => {
        htmlString += `
            <div>
                <input type="radio" name="respuesta" id="respuesta${index}" value="${respuesta.id}">
                <label for="respuesta${index}">${respuesta.resposta}</label>
            </div>
        `;
    });

   
    htmlString += `<br><button id="button-siguiente">Siguiente</button>`;

    // Pintar el contenido en el contenedor "pintaPreguntes"
    document.getElementById("pintaPreguntes").innerHTML = htmlString;

    // Añadir el event listener para el botón "Siguiente"
    document.getElementById('button-siguiente').addEventListener('click', siguientePregunta);
}

// Función para mostrar el resultado final
function mostrarResultados() {
    let htmlString = `<h3>Has completado el cuestionario.</h3>`;
    htmlString += `<p>Has acertado ${respuestasCorrectas} de ${preguntas.length} preguntas.</p>`;
    
    // Botón para reiniciar el cuestionario
    htmlString += `<button id="reiniciar-btn">Reiniciar cuestionario</button>`;
    
    // Pintar el resultado final
    document.getElementById("pintaPreguntes").innerHTML = htmlString;
    
    // Añadir el event listener para reiniciar el cuestionario
    document.getElementById("reiniciar-btn").addEventListener('click', reiniciarCuestionario);
}

// Función para pasar a la siguiente pregunta
function siguientePregunta() {
    // Obtener la respuesta seleccionada
    const seleccionada = document.querySelector('input[name="respuesta"]:checked');
    if (seleccionada) {
        const respuestaId = seleccionada.value;
        const preguntaActual = preguntas[indiceActual];

        // Verificar si la respuesta seleccionada es correcta
        const respuestaCorrecta = preguntaActual.respostes.find(r => r.correcta).id;
        if (parseInt(respuestaId) === respuestaCorrecta) {
            respuestasCorrectas++; // Incrementar el contador de respuestas correctas
        }

        console.log('Pregunta actual:', indiceActual + 1);
        console.log('Respuesta seleccionada:', respuestaId);

        // Avanzar al siguiente índice
        indiceActual++;
        mostrarPregunta();
    } else {
        alert('Por favor, selecciona una respuesta antes de continuar.');
    }
}

// Función para reiniciar el cuestionario
function reiniciarCuestionario() {
    indiceActual = 0; // Reiniciar el índice
    respuestasCorrectas = 0; // Reiniciar el contador de respuestas correctas
    cargarPreguntas(); // Cargar las preguntas de nuevo
}

// Cargar las preguntas al cargar la página
window.onload = function() {
    cargarPreguntas();
};