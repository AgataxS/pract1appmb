<?php
// views/student/mis_inscripciones.php

include '../../include/auth.php';
requireStudent();
include '../../include/db_connection.php';
include '../../include/header.php';

// Obtener el ID del estudiante
$id_usuario = $_SESSION['id_usuario'];
$stmt = $conn->prepare("SELECT id_estudiante FROM estudiantes WHERE usuario_id = ?");
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($id_estudiante);
$stmt->fetch();
$stmt->close();

// Obtener inscripciones del estudiante
$sql = "SELECT i.id_inscripcion, m.nombre AS materia, i.estado, i.fecha_inscripcion
        FROM inscripciones i
        JOIN materias m ON i.id_materia = m.id_materia
        WHERE i.id_estudiante = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param("i", $id_estudiante);
$stmt->execute();
$result = $stmt->get_result();
?>
<h2>Mis Inscripciones</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Materia</th>
            <th>Fecha de Inscripción</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_inscripcion']); ?></td>
                    <td><?php echo htmlspecialchars($row['materia']); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha_inscripcion']); ?></td>
                    <td><?php echo htmlspecialchars($row['estado']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No tienes inscripciones.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();
include '../../include/footer.php';
?>
