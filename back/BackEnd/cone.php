<?php
$servername = "localhost"; 
$username = "a23purpatmah_root"; 
$password = "Patel.9898";
$db = "a23purpatmah_autoescuela";

// Crear conexion
$conn = new mysqli($servername, $username, $password, $db);

if ($conn->connect_error) { 
    die("Connected faild: " . $conn->connect_error);
}else{
    echo"Connected susccessfully<br>";
}





// // $host = "localhost";
// // $usuario = "root";
// // $password = "";
// // $nombreBD = "autoescuela";

// // Crear conexion
// $conn = new mysqli($servername, $username, $password, $db);

// if ($conn->connect_error) { 
//     die("Conexión fallida: " . $conn->connect_error);
// }

?>