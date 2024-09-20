let data;

fetch('http://localhost:8000/Proyecto1/data.json')
.then(response => response.json())
.then(data => {
  console.log(data)
  pintaPreguntes(data)
});


function pintaPreguntes(info){
  let htmlString = '';
  data = info.preguntes;

  for (let indexP = 0; indexP < data.length; indexP++){

    // Esta lina es para que envolver la imagen y la pregunta 
   //htmlString += `<div class="pregunta-container"></div>`; 

    htmlString += `<h3>${indexP + 1}) ${data[indexP].pregunta}</h3>`;
    htmlString += `<img src="${data[indexP].imatge}" alt="Imagen de la pregunta" style="width: 200px; height: auto;"><br><br>`;
    
    for (let indexR = 0; indexR < data[indexP].respostes.length; indexR++) {
      let resposta = data[indexP].respostes[indexR];
      let opcions = ['A', 'B', 'C', 'D'];
      htmlString += `<button class="buttonDePreguntas" onclick="buttonClick(${indexP}, '${opcions[indexR]}')">${opcions[indexR]}) ${resposta.resposta}</button><br><br>`;
    
    }
    
    htmlString += '<hr>';
  }
  document.getElementById('pintaPreguntes').innerHTML = htmlString;
}

function buttonClick(indexPregunta, opcion){
  console.log(`Pregunta: ${indexPregunta + 1}, Opcion seleccionada: ${opcion}`);
}





/*
//Recorremos todas las preguntas usando bucle
for (let index = 0; index < data.preguntes.length; index++) {
  let pregunta = data.preguntes[index];
  let img = data.preguntes[index].imatge;

  document.write("<h4>" + (index + 1) + ") " + pregunta.pregunta + "</h4><br>");
  document.write('<img src="' + img + '"alt= "Imagen de las preguntas" style =" max-width:300px;"> <br><br>');

  //Recorremos todas las respuestas de la pregunta 
  for (let j = 0; j < data.preguntes[index].respostes.length; j++) {
    let opcions = ['A', 'B', 'C', 'D']
    let resposta = pregunta.respostes[j];
    
    document.write('<button class="buttonDePreguntas" >' + opcions[j] + ") "+ resposta.resposta + "</button><br><br>")
  }
  document.write(`<hr>`);
}
*/

//document.write(htmlString);
//document.write(data.preguntes[0].pregunta);




