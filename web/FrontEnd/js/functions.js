let data;


fetch('http://localhost:8000/tr0-2024-2025-un-munt-de-preguntes-Purvish69/back/BackEnd/index.php')
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







