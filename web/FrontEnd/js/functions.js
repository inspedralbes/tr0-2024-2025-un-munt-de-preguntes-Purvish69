let preguntas = [];
let indiceActual = 0;
let respuestasUsuario = [];
let tiempoRestante = 30; // Tiempo en segundos
let temporizador; // Variable para el temporizador

// Almacenar el nombre del usuario en localStorage
let nombreUsuario = localStorage.getItem("nombreUsuario") || "";

// Estructura del estado de la partida
let estadoDeLaPartida = {
  contadorPreguntas: 0,
  preguntes: [],
};

// Mostrar la página inicial para poner el nombre de jugadores y las preguntas que queremos
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

  const nombreInput = document.getElementById('nombre');
  nombreInput.value = nombreUsuario;

  document.getElementById('iniciarCuestionario').addEventListener('click', () => {
    const nombre = nombreInput.value;
    const numPreguntas = parseInt(document.getElementById('numPreguntas').value);

    if (nombre && numPreguntas > 0) {
      localStorage.setItem("nombreUsuario", nombre);
      cargarPreguntas(numPreguntas); // Cargar preguntas según el número introducido
    } else {
      alert("Por favor, completa todos los campos.");
    }
  });

  document.getElementById('borrarNombre').addEventListener('click', () => {
    localStorage.removeItem("nombreUsuario");
    nombreInput.value = '';
  });
}

// Función para cargar preguntas del backend
function cargarPreguntas(numPreguntas) {
  fetch(
    `../back/BackEnd/getPreguntes.php?numPreguntas=${numPreguntas}`
  )
    .then((response) => response.json())
    .then((data) => {
      preguntas = data; // Cargar las preguntas recibidas

      // Inicializar estado de la partida
      estadoDeLaPartida = {
        contadorPreguntas: 0,
        preguntes: preguntas.map((pregunta) => ({
          id: pregunta.id,
          feta: false,
          resposta: 0,
        })),
      };

      iniciarTemporizador();
      mostrarPregunta();
      actualizarMarcador();
      document.getElementById("pagina_inicial").innerHTML = ""; // Ocultar página inicial
    })
    .catch((error) => console.error("Error al cargar las preguntas:", error));
}

function iniciarTemporizador() {
  tiempoRestante = 30; // Reiniciar el tiempo a 30 segundos
  actualizarTemporizador();

  temporizador = setInterval(() => {
    tiempoRestante--;
    actualizarTemporizador();

    if (tiempoRestante <= 0) { // Corregir aquí: debe ser tiempoRestante
      clearInterval(temporizador);
      finalizarCuestionario();
    }

  }, 1000);
}

function actualizarTemporizador() {
  document.getElementById("temporizador").innerHTML = `Tiempo restante: ${tiempoRestante} segundos`;
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

  document.getElementById("button-siguiente").addEventListener("click", siguientePregunta);
}

// Función para manejar la selección de una respuesta
function seleccionarRespuesta(event) {
  document.querySelectorAll(".btn-respuesta").forEach((btn) => btn.classList.remove("selected"));
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
    console.log(`Pregunta ${indiceActual + 1}, Opción ${letras[respuestaIndex]}`);

    // Actualizar estado de la pregunta
    estadoDeLaPartida.preguntes[indiceActual].feta = true; // Marcar la pregunta como respondida
    estadoDeLaPartida.contadorPreguntas = indiceActual + 1; // Actualizar contador de preguntas respondidas
    estadoDeLaPartida.preguntes[indiceActual].resposta = respuestaIndex; // Guardar respuesta

    actualizarMarcador();

    // Verificar si es la última pregunta
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

// Función para actualizar el marcador
function actualizarMarcador() {
  let htmlString = `<h2>${estadoDeLaPartida.contadorPreguntas}/${preguntas.length}</h2>`;
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
  clearInterval(temporizador);

  fetch(
    "../back/BackEnd/finalitza.php",
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
  htmlString += `<p>Has acertado ${data.respuestasCorrectas} de ${data.totalPreguntas} preguntas.</p>`;
  htmlString += `<button id="reiniciar-btn">Reiniciar cuestionario</button>`;

  document.getElementById("pintaPreguntes").innerHTML = htmlString;
  document.getElementById("reiniciar-btn").addEventListener("click", reiniciarCuestionario);
}

// Función para reiniciar el cuestionario
function reiniciarCuestionario() {
  // Reiniciar las variables
  indiceActual = 0;
  respuestasUsuario = [];

  // Reiniciar el estado de la partida
  estadoDeLaPartida = {
    contadorPreguntas: 0,
    preguntes: [],
  };

  // Limpiar el contenido de la página de resultados
  document.getElementById("pintaPreguntes").innerHTML = "";
  document.getElementById("estadoDeLaPartida").innerHTML = "";

  // Mostrar la página inicial
  mostrarPaginaInicial();
}

// Cargar las preguntas cuando la página esté lista
document.addEventListener("DOMContentLoaded", () => {
  mostrarPaginaInicial();
});
