<?php
session_start();
$host = "localhost";
$usuario = "root";
$password = "";
$nombreBD = "autoescuela";

// Crear conexión a la base de datos
$conn = new mysqli($host, $usuario, $password, $nombreBD);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id = $_GET['id'];
$sql = "SELECT * FROM preguntes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pregunta = $_POST['pregunta'];
    $opcion1 = $_POST['opcion1'];
    $opcion2 = $_POST['opcion2'];
    $opcion3 = $_POST['opcion3'];
    $opcion4 = $_POST['opcion4'];
    $respuesta_correcta = $_POST['respuesta_correcta'];
    if (!empty($_FILES['imagen']['name'])) {
        $imagen = $_FILES['imagen']['name'];
        move_uploaded_file($_FILES['imagen']['tmp_name'], '../uploads/' . $imagen);
    } else {
        $imagen = $row['imagen']; // Mantener la imagen anterior si no se subió una nueva
    }

    // Actualizar en la base de datos
    $sql = "UPDATE preguntes SET pregunta = ?, opcion1 = ?, opcion2 = ?, opcion3 = ?, opcion4 = ?, respuesta_correcta = ?, imagen = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $pregunta, $opcion1, $opcion2, $opcion3, $opcion4, $respuesta_correcta, $imagen, $id);
    $stmt->execute();
    $stmt->close();

    echo "Pregunta actualizada exitosamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Pregunta</title>
</head>
<body>
    <h1>Modificar Pregunta</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>Pregunta:</label><br>
        <input type="text" name="pregunta" value="<?php echo $row['pregunta']; ?>" required><br>
        
        <label>Opción 1:</label><br>
        <input type="text" name="opcion1" value="<?php echo $row['opcion1']; ?>" required><br>

        <label>Opción 2:</label><br>
        <input type="text" name="opcion2" value="<?php echo $row['opcion2']; ?>" required><br>

        <label>Opción 3:</label><br>
        <input type="text" name="opcion3" value="<?php echo $row['opcion3']; ?>" required><br>

        <label>Opción 4:</label><br>
        <input type="text" name="opcion4" value="<?php echo $row['opcion4']; ?>" required><br>

        <label>Respuesta Correcta:</label><br>
        <select name="respuesta_correcta" required>
            <option value="opcion1" <?php if ($row['respuesta_correcta'] == 'opcion1') echo 'selected'; ?>>Opción 1</option>
            <option value="opcion2" <?php if ($row['respuesta_correcta'] == 'opcion2') echo 'selected'; ?>>Opción 2</option>
            <option value="opcion3" <?php if ($row['respuesta_correcta'] == 'opcion3') echo 'selected'; ?>>Opción 3</option>
            <option value="opcion4" <?php if ($row['respuesta_correcta'] == 'opcion4') echo 'selected'; ?>>Opción 4</option>
        </select><br>

        <label>Imagen:</label><br>
        <input type="file" name="imagen" accept="image/*"><br>
        <img src="../uploads/<?php echo $row['imagen']; ?>" alt="Imagen Actual" width="100"><br><br>

        <button type="submit">Actualizar Pregunta</button>
    </form>
    <a href="read.php">Volver a la Lista de Preguntas</a>
</body>
</html>
