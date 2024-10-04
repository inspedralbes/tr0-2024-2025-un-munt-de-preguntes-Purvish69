document.addEventListener('DOMContentLoaded', function() {
  cargarPreguntas();

  // Botón para mostrar el formulario de crear preguntas
  const crearPreguntaBtn = document.createElement('button');
  crearPreguntaBtn.textContent = 'Crear Nueva Pregunta';
  crearPreguntaBtn.id = 'crear-pregunta'; // id para poner estilo en los button
  crearPreguntaBtn.addEventListener('click', mostrarFormulario);
  document.body.appendChild(crearPreguntaBtn);
});

// Función para cargar preguntas desde la base de datos
function cargarPreguntas() {
  fetch("http://localhost:8800/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/read.php")
      .then(response => response.json())
      .then(data => {
          const container = document.getElementById('questions-container');
          container.innerHTML = ''; // Limpiar el contenido antes de agregar las preguntas

          // Crear la tabla
          const table = document.createElement('table');
          const headerRow = document.createElement('tr');
          headerRow.innerHTML = `
              <th>Pregunta</th>
              <th>Imagen</th>
              <th>Opciones</th>
              <th>Respuesta Correcta</th>
              <th>Acciones</th>
          `;
          table.appendChild(headerRow);

          // Iterar sobre cada pregunta
          data.forEach(pregunta => {
              const row = document.createElement('tr');

              // Columna de la pregunta
              const preguntaCell = document.createElement('td');
              preguntaCell.textContent = pregunta.pregunta;
              row.appendChild(preguntaCell);

              // Columna de la imagen
              const imagenCell = document.createElement('td');
              if (pregunta.imagen) {
                  const img = document.createElement('img');
                  img.src = pregunta.imagen;
                  img.alt = 'Imagen de la pregunta';
                  img.width = 100;
                  imagenCell.appendChild(img);
              } else {
                  imagenCell.textContent = 'No hay imagen';
              }
              row.appendChild(imagenCell);

              // Columna de opciones
              const opcionesCell = document.createElement('td');
              const opcionesList = document.createElement('ul');
              pregunta.respuestas.forEach((respuesta, index) => {
                  const li = document.createElement('li');
                  li.textContent = `${index + 1}. ${respuesta.respuesta}`;
                  opcionesList.appendChild(li);
              });
              opcionesCell.appendChild(opcionesList);
              row.appendChild(opcionesCell);

              // Columna de respuesta correcta
              const correctaCell = document.createElement('td');
              const correcta = pregunta.respuestas.find(respuesta => respuesta.correcta == 1);
              correctaCell.textContent = correcta ? correcta.respuesta : 'N/A';
              row.appendChild(correctaCell);

              // Columna de acciones (editar y borrar)
              const accionesCell = document.createElement('td');
              const editarBtn = document.createElement('button');
              editarBtn.textContent = 'Editar';
              editarBtn.classList.add('editar'); // Esto para que pueda modificar los botones de estilo
              editarBtn.addEventListener('click', () => editarPregunta(pregunta)); // Modificado para pasar el objeto completo

              const borrarBtn = document.createElement('button');
              borrarBtn.textContent = 'Borrar';
              borrarBtn.classList.add('borrar'); // Esto para que pueda modificar los botones de estilo
              borrarBtn.addEventListener('click', () => borrarPregunta(pregunta.id, row));

              accionesCell.appendChild(editarBtn);
              accionesCell.appendChild(borrarBtn);
              row.appendChild(accionesCell);

              // Agregar la fila a la tabla
              table.appendChild(row);
          });

          // Agregar la tabla al contenedor
          container.appendChild(table);
      })
      .catch(error => console.error('Error al cargar preguntas:', error));
}

// Función para mostrar el formulario de creación de preguntas
function mostrarFormulario() {
  const formDiv = document.createElement('div');
  formDiv.innerHTML = `
      <h3>Crear Nueva Pregunta</h3>
      <label for="pregunta">Pregunta:</label>
      <input type="text" id="pregunta" required><br>
      
      <label for="imagen">Imagen (URL):</label>
      <input type="text" id="imagen" required><br>
      
      <label for="opcion1">Opción 1:</label>
      <input type="text" id="opcion1" required><br>
      
      <label for="opcion2">Opción 2:</label>
      <input type="text" id="opcion2" required><br>
      
      <label for="opcion3">Opción 3:</label>
      <input type="text" id="opcion3" required><br>
      
      <label for="opcion4">Opción 4:</label>
      <input type="text" id="opcion4" required><br>
      
      <label for="respuesta-correcta">Respuesta Correcta (1-4):</label>
      <input type="number" id="respuesta-correcta" min="1" max="4" required><br>
      
      <button id="guardar-btn">Guardar Pregunta</button>
  `;

  document.body.appendChild(formDiv);

  // Evento para guardar la pregunta
  document.getElementById('guardar-btn').addEventListener('click', () => guardarPregunta(formDiv));
}

// Función para guardar la nueva pregunta en la base de datos
function guardarPregunta(formDiv) {
  const pregunta = document.getElementById('pregunta').value;
  const imagen = document.getElementById('imagen').value;
  const opciones = [
      document.getElementById('opcion1').value,
      document.getElementById('opcion2').value,
      document.getElementById('opcion3').value,
      document.getElementById('opcion4').value
  ];
  const respuestaCorrecta = document.getElementById('respuesta-correcta').value;

  // Crear un objeto FormData para enviar los datos
  const formData = new FormData();
  formData.append('pregunta', pregunta);
  formData.append('imagen', imagen);
  formData.append('opcion1', opciones[0]);
  formData.append('opcion2', opciones[1]);
  formData.append('opcion3', opciones[2]);
  formData.append('opcion4', opciones[3]);
  formData.append('respuesta_correcta', respuestaCorrecta);

  // Realizar la solicitud POST para insertar la pregunta
  fetch("http://localhost:8800/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/create.php", {
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          console.log('Pregunta creada exitosamente');
          cargarPreguntas(); // Recargar preguntas para mostrar la nueva
      } else {
          console.error('Error al crear la pregunta');
      }
  })
  .catch(error => console.error('Error al enviar la solicitud:', error));

  // Eliminar el formulario después de guardar
  formDiv.remove();
}

function editarPregunta(pregunta) {
    // Hacer una solicitud para obtener los datos completos de la pregunta
    fetch(`http://localhost:8800/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/update.php?id=${pregunta.id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const preguntaData = data.data;

                const formDiv = document.createElement('div');
                formDiv.innerHTML = `
                    <h3>Editar Pregunta</h3>
                    <label for="pregunta">Pregunta:</label>
                    <input type="text" id="pregunta" value="${preguntaData.pregunta}" required><br>
                    
                    <label for="imagen">Imagen (URL):</label>
                    <input type="text" id="imagen" value="${preguntaData.imagen}" required><br>
                    
                    <label for="opcion1">Opción 1:</label>
                    <input type="text" id="opcion1" value="${preguntaData.respuestas[0].respuesta}" required><br>
                    
                    <label for="opcion2">Opción 2:</label>
                    <input type="text" id="opcion2" value="${preguntaData.respuestas[1].respuesta}" required><br>
                    
                    <label for="opcion3">Opción 3:</label>
                    <input type="text" id="opcion3" value="${preguntaData.respuestas[2].respuesta}" required><br>
                    
                    <label for="opcion4">Opción 4:</label>
                    <input type="text" id="opcion4" value="${preguntaData.respuestas[3].respuesta}" required><br>
                    
                    <label for="respuesta-correcta">Respuesta Correcta (1-4):</label>
                    <input type="number" id="respuesta-correcta" value="${preguntaData.respuestas.findIndex(res => res.correcta == 1) + 1}" min="1" max="4" required><br>
                    
                    <button id="actualizar-btn">Actualizar Pregunta</button>
                    <button id="cancelar-btn">Cancelar</button>
                `;

                document.body.appendChild(formDiv);

                // Evento para actualizar la pregunta
                document.getElementById('actualizar-btn').addEventListener('click', () => {
                    actualizarPregunta(pregunta.id, formDiv, preguntaData);
                });

                // Evento para cancelar la edición
                document.getElementById('cancelar-btn').addEventListener('click', () => {
                    formDiv.remove(); // Eliminar el formulario al cancelar
                });
            } else {
                alert('No se pudieron cargar los datos de la pregunta.');
            }
        })
        .catch(error => console.error('Error al cargar la pregunta para editar:', error));
}

function actualizarPregunta(id, formDiv, preguntaData) {
    const pregunta = document.getElementById('pregunta').value;
    const imagen = document.getElementById('imagen').value;
    const opciones = [
        document.getElementById('opcion1').value,
        document.getElementById('opcion2').value,
        document.getElementById('opcion3').value,
        document.getElementById('opcion4').value,
    ];
    const respuestaCorrecta = document.getElementById('respuesta-correcta').value;

    // Crear un objeto FormData para enviar los datos
    const formData = new FormData();
    formData.append('id', id);
    formData.append('pregunta', pregunta);
    formData.append('imagen', imagen);
    formData.append('opcion1', opciones[0]);
    formData.append('opcion2', opciones[1]);
    formData.append('opcion3', opciones[2]);
    formData.append('opcion4', opciones[3]);
    formData.append('respuesta_correcta', respuestaCorrecta);
    // Añadir los ids de las respuestas
    formData.append('respuesta_id_1', preguntaData.respuestas[0].respuesta_id);
    formData.append('respuesta_id_2', preguntaData.respuestas[1].respuesta_id);
    formData.append('respuesta_id_3', preguntaData.respuestas[2].respuesta_id);
    formData.append('respuesta_id_4', preguntaData.respuestas[3].respuesta_id);

    // Realizar la solicitud POST para actualizar la pregunta
    fetch("http://localhost:8800/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/update.php", {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Pregunta actualizada exitosamente');
            cargarPreguntas(); // Recargar preguntas para mostrar la actualizada
            formDiv.remove();  // Eliminar el formulario después de actualizar
        } else {
            console.error('Error al actualizar la pregunta:', data.message);
        }
    })
    .catch(error => console.error('Error al enviar la solicitud:', error));
}


// Función para borrar una pregunta de la base de datos
function borrarPregunta(id, row) {
  if (confirm('¿Estás seguro de que deseas borrar esta pregunta?')) {
      fetch(`http://localhost:8800/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/delete.php?id=${id}`, {
          method: 'DELETE' // Cambiado a DELETE
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              console.log('Pregunta borrada exitosamente');
              row.remove(); // Remover la fila de la tabla
          } else {
              console.error('Error al borrar la pregunta', data.message);
          }
      })
      .catch(error => console.error('Error al enviar la solicitud:', error));
  }
}
