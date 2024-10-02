let preguntas = [];
let indiceActual = 0;
let respuestasUsuario = [];

let nombreUsuario = localStorage.getItem("nombreUsuario") || "";

function mostrarPaginaInicial() {
  const paginaInicial = document.getElementById("pagina_inicial");
  paginaInicial.innerHTML = `
  <h2>Bienvenido al Cuestionario</h2>
  <label for="nombre">Introduce tu nombre:</label>
   <input type="text" id="nombre" placeholder="Nombre" />
  <br />
  <label for="numPreguntas">Cantidad de preguntas:</label>
  <input type="number" id="numPreguntas" placeholder="Número de preguntas" min="1" />
  <br />
  <button id="iniciarCuestionario">Iniciar Cuestionario</button>
  <button id="borrarNombre">Borrar Nombre</button>
  `;

  // cargar nombre guardado
  const nombreInput = document.getElementById('nombre');
  if(nombreUsuario){
    nombreInput.value = nombreUsuario;
  }
  
  // Manejar el evento de inicio del cuestionario
  document.getElementById('iniciarCuestionario').addEventListener('click', () => {
    const nombre = nombreInput.value;
    const numPreguntas = parseInt(document.getElementById('numPreguntas').value);

    if(nombre && numPreguntas > 0 ){
      localStorage.setItem("nombreUsuario", nombre),
      cargarPreguntas(numPreguntas); // Esto lo que hace que caarga preguntas segun el numero introducido en el pagina inicial
    }else{
      alert("Por favor, completa todos los campos.")
    }
  });

  document.getElementById('borrarNombre').addEventListener('click', () => {
    localStorage.removeItem("nombreUsuario");
    nombreInput.value = '';
  });
}

// Función para cargar preguntas del backend
function cargarPreguntas() {
  fetch(`http://localhost:8800/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/getPreguntes.php?numPreguntas=${numPreguntas}`)
    .then((response) => response.json())
    .then((data) => {
      preguntas = data; // Cargar las preguntas recibidas
      mostrarPregunta();
      document.getElementById("pagina-inicial").innerHTML = "";
    })
    .catch((error) => console.error("Error al cargar las preguntas:", error));
}

// Función para mostrar una pregunta con las opciones de respuesta
function mostrarPregunta() {
  if (indiceActual >= preguntas.length) {
    finalizarCuestionario();
    return;
  }

  const pregunta = preguntas[indiceActual];
  let htmlString = `<h3>${indiceActual + 1}) ${pregunta.pregunta}</h3>`;
  htmlString += `<img src="${pregunta.imatge}" alt="Imagen de la pregunta" style="width: 200px; height: auto;"><br>`;

  const letras = ["A", "B", "C", "D"];
  pregunta.respostes.forEach((respuesta, index) => {
    htmlString += `
            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                <button type="button" class="btn-respuesta" data-index="${index}" style="margin-right: 10px;">${letras[index]}</button>
                <span>${respuesta.resposta}</span>
            </div>
        `;
  });

  htmlString += `<br><button id="button-siguiente">Siguiente</button>`;
  document.getElementById("pintaPreguntes").innerHTML = htmlString;

  document.querySelectorAll(".btn-respuesta").forEach((btn) => {
    btn.addEventListener("click", seleccionarRespuesta);
  });

  document
    .getElementById("button-siguiente")
    .addEventListener("click", siguientePregunta);
}

// Función para manejar la selección de una respuesta
function seleccionarRespuesta(event) {
  document
    .querySelectorAll(".btn-respuesta")
    .forEach((btn) => btn.classList.remove("selected"));
  event.target.classList.add("selected");
}

// Función para pasar a la siguiente pregunta
function siguientePregunta() {
  const seleccionado = document.querySelector(".btn-respuesta.selected");
  if (seleccionado) {
    const respuestaIndex = parseInt(seleccionado.dataset.index);
    respuestasUsuario[indiceActual] = respuestaIndex;

    // Mostrar en console la pregunta y la opción seleccionada
    const letras = ["A", "B", "C", "D"];
    console.log(
      `Pregunta ${indiceActual + 1}, Opción ${letras[respuestaIndex]}`
    );

    // Actualizar estado de la pregunta

    estadoDeLaPartida.preguntes[indiceActual].feta = true; // Marcar la pregunta como respondida
    estadoDeLaPartida.contadorPreguntas = indiceActual + 1; // Actualizar contador de preguntas respondidas
    estadoDeLaPartida.preguntes[indiceActual].resposta = respuestaIndex; // Guardar respuesta

    actualizarMarcador();

    //Verificar si esta es la ultima pregunta
    if (indiceActual >= preguntas.length - 1) {
      finalizarCuestionario();
    } else {
      indiceActual++;
      mostrarPregunta();
    }
  } else {
    alert("Por favor, selecciona una respuesta antes de continuar.");
  }
}

// Estructura del estado de la partida
let estadoDeLaPartida = {
  contadorPreguntas: 0,
  preguntes: [
    { id: 1, feta: false, resposta: 0 },
    { id: 2, feta: false, resposta: 0 },
    { id: 3, feta: false, resposta: 0 },
    { id: 4, feta: false, resposta: 0 },
    { id: 5, feta: false, resposta: 0 },
    { id: 6, feta: false, resposta: 0 },
    { id: 7, feta: false, resposta: 0 },
    { id: 8, feta: false, resposta: 0 },
    { id: 9, feta: false, resposta: 0 },
    { id: 10, feta: false, resposta: 0 },
  ],
};

function actualizarMarcador() {
  let htmlString = `<h2>${estadoDeLaPartida.contadorPreguntas}/10</h2>`;
  htmlString += `<table>`;

  for (let i = 0; i < estadoDeLaPartida.preguntes.length; i++) {
    htmlString += `<tr><td>Pregunta ${i + 1}</td><td>`;
    htmlString += estadoDeLaPartida.preguntes[i].feta ? "Feta" : "Pendent";
    htmlString += `</td></tr>`;
  }
  htmlString += `</table>`;

  document.getElementById("estadoDeLaPartida").innerHTML = htmlString;
}

// Función para enviar las respuestas y mostrar el resultado final
function finalizarCuestionario() {
  fetch(
    "http://localhost:8800/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/finalitza.php",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(respuestasUsuario),
    }
  )
    .then((response) => response.json())
    .then((data) => {
      console.log("Respuesta del servidor:", data);
      mostrarResultados(data);
    })
    .catch((error) => console.error("Error al enviar las respuestas:", error));
}

// Función para mostrar el resultado final
function mostrarResultados(data) {
  let htmlString = `<h3>Has completado el cuestionario.</h3>`;
  htmlString += `<p>Has acertado ${data.respuestasCorrectas} de ${data.totalPreguntas} preguntas.</p>`; // Asegúrate de que esta línea usa la clave correcta
  htmlString += `<button id="reiniciar-btn">Reiniciar cuestionario</button>`;

  document.getElementById("pintaPreguntes").innerHTML = htmlString;
  document
    .getElementById("reiniciar-btn")
    .addEventListener("click", reiniciarCuestionario);
}

// Función para reiniciar el cuestionario
function reiniciarCuestionario() {
  indiceActual = 0;
  respuestasUsuario = [];

  // Reiniciar el estado de la partida
  estadoDeLaPartida = {
    contadorPreguntas: 0,
    preguntes: preguntas.map((pregunta, index) => ({
      id: pregunta.id,
      feta: false,
      resposta: 0,
    })),
  };
  actualizarMarcador();
  cargarPreguntas();
}

// Cargar las preguntas cuando la página esté lista
window.onload = function () {
  mostrarPaginaInicial();
  //cargarPreguntas();
};
