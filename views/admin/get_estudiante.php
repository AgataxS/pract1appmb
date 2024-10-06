<?php
// get_estudiante.php
include '../../include/db_connection.php';

if (isset($_GET['id_estudiante'])) {
    $id_estudiante = intval($_GET['id_estudiante']);

    $stmt = $conn->prepare("SELECT e.*, c.nombre AS carrera FROM estudiantes e LEFT JOIN carreras c ON e.carrera_id = c.id_carrera WHERE e.id_estudiante = ?");
    $stmt->bind_param("i", $id_estudiante);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        echo "<p><strong>Nombre:</strong> " . htmlspecialchars($row['nombre']) . "</p>";
        echo "<p><strong>Apellido:</strong> " . htmlspecialchars($row['apellido']) . "</p>";
        echo "<p><strong>Correo:</strong> " . htmlspecialchars($row['correo']) . "</p>";
        echo "<p><strong>Carrera:</strong> " . htmlspecialchars($row['carrera']) . "</p>";
        echo "<p><strong>CI:</strong> " . htmlspecialchars($row['ci']) . "</p>";
        if ($row['imagen']) {
            echo "<p><strong>Imagen:</strong><br><img src='../../" . htmlspecialchars($row['imagen']) . "' alt='Imagen' width='200'></p>";
        } else {
            echo "<p><strong>Imagen:</strong> No disponible</p>";
        }
    } else {
        echo "Estudiante no encontrado.";
    }
    $stmt->close();
}

$conn->close();
?>
