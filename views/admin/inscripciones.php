<?php
// views/admin/inscripciones.php

include '../../include/auth.php';
requireAdmin();
include '../../include/db_connection.php';
include '../../include/header.php';

// Manejar la aprobación o rechazo de inscripciones
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_inscripcion = intval($_POST['id_inscripcion']);
    $accion = $_POST['accion'];

    if ($accion == 'aprobar') {
        $stmt = $conn->prepare("UPDATE inscripciones SET estado = 'aprobada' WHERE id_inscripcion = ?");
    } elseif ($accion == 'rechazar') {
        $stmt = $conn->prepare("UPDATE inscripciones SET estado = 'rechazada' WHERE id_inscripcion = ?");
    }

    if ($stmt) {
        $stmt->bind_param("i", $id_inscripcion);
        if ($stmt->execute()) {
            $success = "Inscripción actualizada correctamente.";
        } else {
            $error = "Error al actualizar la inscripción.";
        }
        $stmt->close();
    }
}

// Obtener inscripciones pendientes
$sql = "SELECT i.id_inscripcion, e.nombre, e.apellido, m.nombre AS materia
        FROM inscripciones i
        JOIN estudiantes e ON i.id_estudiante = e.id_estudiante
        JOIN materias m ON i.id_materia = m.id_materia
        WHERE i.estado = 'pendiente'";
$result = $conn->query($sql);
?>

<h2>Gestionar Inscripciones</h2>

<?php if (isset($success)): ?>
    <p class="success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>ID Inscripción</th>
            <th>Estudiante</th>
            <th>Materia</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_inscripcion']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($row['materia']); ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="id_inscripcion" value="<?php echo $row['id_inscripcion']; ?>">
                            <button type="submit" name="accion" value="aprobar">Aprobar</button>
                            <button type="submit" name="accion" value="rechazar">Rechazar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No hay inscripciones pendientes.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$conn->close();
include '../../include/footer.php';
?>
