// Almacenar todas las preguntas cargadas del Backend
let preguntas = [];
// Mostrar la primera pregunta cuando carga la página
let preguntaActual = 0;

// Función para mostrar la pregunta actual
function mostrarPregunta() {
  if (preguntaActual < preguntas.length) {
    const pregunta = preguntas[preguntaActual];
    let htmlString = "";

    htmlString += `<h3>${preguntaActual + 1}) ${pregunta.pregunta}</h3>`;
    htmlString += `<img src="${pregunta.imatge}" alt="Imagen de la pregunta" style="width: 200px; height: auto;"><br><br>`;

    for (let i = 0; i < pregunta.respostes.length; i++) {
      const respuesta = pregunta.respostes[i];
      htmlString += `<input type="radio" name="respuesta" value="${respuesta.id}" id="respuesta${respuesta.id}"> 
                     <label for="respuesta${respuesta.id}">${respuesta.resposta}</label><br><br>`;
    }
    htmlString += '<button onclick="siguientePregunta()">Siguiente</button>';

    // Insertar el HTML generado en el contenedor de preguntas
    document.getElementById("pintaPreguntes").innerHTML = htmlString;
  } else {
    mostrarPuntuacion();
  }
}

// Función para manejar el paso a la siguiente pregunta
function siguientePregunta() {
  const seleccionado = document.querySelector(
    'input[name="respuesta"]:checked'
  );

  if (!seleccionado) {
    alert("Por favor, selecciona una respuesta.");
    return;
  }

  const respuestaId = seleccionado.value;

  console.log(
    `Pregunta ${preguntaActual + 1}, Opción seleccionada: ${respuestaId}`
  );

  // Enviar la respuesta al backend para procesar y avanzar a la siguiente pregunta
  fetch(
    "http://localhost:8888/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/index.php",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        respuesta: respuestaId,
        action: "next",
      }),
    }
  )
    .then((response) => response.json())
    .then((data) => {
      if (data && data.finished) {
        // Muestro resultado si no hay más preguntas
        mostrarPuntuacion(data.result);
      } else if (data && !data.finished) {
        // Mostrar si la respuesta fue correcta o incorrecta
        const mensaje = data.correcta
          ? "¡Respuesta correcta!"
          : "Respuesta incorrecta.";
        alert(mensaje);

        // Avanzar a la siguiente pregunta
        preguntaActual++;
        mostrarPregunta();
      }
    });
}

// Función para mostrar la puntuación final después de completar el cuestionario
function mostrarPuntuacion(resultado) {
  let htmlString = `<h1>Cuestionario completado</h1><p>${resultado}</p>`;
  // Botón para reiniciar el cuestionario
  htmlString +=
    '<button onclick="reiniciarCuestionario()">Iniciar cuestionario</button>';
  // Insertar el HTML generado en el contenedor de preguntas
  document.getElementById("pintaPreguntes").innerHTML = htmlString;
}

// Función para reiniciar el cuestionario
function reiniciarCuestionario() {
  // Enviar solicitud al backend para reiniciar el cuestionario
  fetch(
    "http://localhost:8888/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/index.php",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "restart",
      }),
    }
  )
    .then((response) => response.json())
    .then((data) => {
      if (data && data.status === "restart") {
        // Reiniciar el índice de la pregunta actual y cargar nuevas preguntas
        preguntaActual = 0;
        fetch(
          "http://localhost:8888/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/index.php"
        )
          .then((response) => response.json())
          .then((data) => {
            if (data && data.length) {
              preguntas = data;
              mostrarPregunta();
            }
          });
      } else {
        console.error("Error al reiniciar el cuestionario.");
      }
    })
    .catch((error) =>
      console.error("Error al reiniciar el cuestionario:", error)
    );
}

// Cargar preguntas al inicio
fetch(
  "http://localhost:8888/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/index.php"
)
  .then((response) => response.json())
  .then((data) => {
    if (data && data.length) {
      preguntas = data;
      mostrarPregunta();
    }
  })
  .catch((error) => console.error("Error al cargar preguntas:", error));
