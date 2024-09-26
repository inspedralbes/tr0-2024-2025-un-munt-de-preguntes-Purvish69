let preguntas = [];
let indiceActual = 0;
let respuestasCorrectas = 0;

// Función para cargar las preguntas del backend
function cargarPreguntas() {
    fetch('http://localhost:8800/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/getPreguntes.php')
        .then(response => response.json())
        .then(data => {
            preguntas = data;
            mostrarPregunta();
        })
        .catch(error => console.error('Error al cargar las preguntas:', error));
}

// Función para mostrar preguntas con las opciones de respuesta
function mostrarPregunta() {
    if (indiceActual >= preguntas.length) {
        // muestro resultado si han respondido todas las respuestas
        mostrarResultados(); 
        return;
    }

    //Cargar Las preguntas, imagen y sus opciones 
    const pregunta = preguntas[indiceActual];
    let htmlString = "";
    htmlString += `<h3>${indiceActual + 1}) ${pregunta.pregunta}</h3>`;
    htmlString += `<img src="${pregunta.imatge}" alt="Imagen de la pregunta" style="width: 200px; height: auto;"><br><br>`;

    const letras = ["A", "B", "C", "D"];

    pregunta.respostes.forEach((respuesta, index) => {
        htmlString += `
            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                <button type="button" class="btn-respuesta" data-id="${respuesta.id}" data-opcion="${letras[index]}" style="margin-right: 10px;">${letras[index]}</button>
                <span>${respuesta.resposta}</span>
            </div>
        `;
    });

    htmlString += `<br><button id="button-siguiente">Siguiente</button>`;
    htmlString += `<div id="resultado"></div>`;  // Contenedor para mostrar si es correcta o incorrecta

    // Pintar el contenido en el contenedor "pintaPreguntes"
    document.getElementById("pintaPreguntes").innerHTML = htmlString;

    // Añadir los event listeners para los botones de respuesta
    document.querySelectorAll('.btn-respuesta').forEach(btn => {
        btn.addEventListener('click', seleccionarRespuesta);
    });

    // Añadir el event listener para el botón 'Siguiente'
    document.getElementById('button-siguiente').addEventListener('click', siguientePregunta);
}

// Función para manejar la selección de una respuesta
function seleccionarRespuesta(event) {
    // Eliminar la clase 'selected' de todos los botones
    document.querySelectorAll('.btn-respuesta').forEach(btn => {
        btn.classList.remove('selected');
    });

    // Marcar el botón seleccionado
    event.target.classList.add('selected');
    // Guardar el id de la respuesta seleccionada en el botón
    document.querySelectorAll('.btn-respuesta').forEach(btn => {
        btn.dataset.selected = btn === event.target;
    });
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
    // Obtener el botón seleccionado
    const seleccionado = document.querySelector('.btn-respuesta.selected');
    if (seleccionado) {
        const respuestaId = seleccionado.dataset.id;
        const opcionSeleccionada = seleccionado.dataset.opcion;  // Obtener la opción seleccionada (A, B, C, D)
        const preguntaActual = preguntas[indiceActual];

        // Verificar si la respuesta seleccionada es correcta
        const respuestaCorrecta = preguntaActual.respostes.find(r => r.correcta).id;

        // Mostrar si es correcta o incorrecta
        const contenedorResultado = document.getElementById('resultado');
        if (parseInt(respuestaId) === respuestaCorrecta) {
            contenedorResultado.innerHTML = `<p>¡Respuesta correcta!</p>`;
            respuestasCorrectas++;
        } else {
            contenedorResultado.innerHTML = `<p>Respuesta incorrecta.</p>`;
        }

        // Mostrar el número de pregunta y opción seleccionada en la consola
        console.log(`Pregunta ${indiceActual + 1}`);
        console.log(`Opción seleccionada: ${opcionSeleccionada}`);

        // Deshabilitar los botones de respuesta para que no se pueda cambiar la selección
        document.querySelectorAll('.btn-respuesta').forEach(btn => {
            btn.disabled = true;
        });

        // Esperar unos segundos antes de mostrar la siguiente pregunta
        setTimeout(() => {
            indiceActual++;
            mostrarPregunta();
        }, 1000); 
    } else {
        alert('Por favor, selecciona una respuesta antes de continuar.');
    }
}

// Función para reiniciar el cuestionario
function reiniciarCuestionario() {
    indiceActual = 0; 
    respuestasCorrectas = 0; 
    cargarPreguntas(); 
}

// Cargar las preguntas al cargar la página
window.onload = function() {
    cargarPreguntas();
};
