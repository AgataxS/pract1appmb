<?php
// views/student/preinscripcion.php

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

// Manejar la inscripción en una materia
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inscribir'])) {
    $id_materia = intval($_POST['id_materia']);

    // Verificar si ya está inscrito
    $stmt = $conn->prepare("SELECT * FROM inscripciones WHERE id_estudiante = ? AND id_materia = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("ii", $id_estudiante, $id_materia);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $error = "Ya estás inscrito en esta materia.";
    } else {
        // Insertar inscripción
        $stmt_insert = $conn->prepare("INSERT INTO inscripciones (id_estudiante, id_materia, estado) VALUES (?, ?, 'pendiente')");
        if (!$stmt_insert) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt_insert->bind_param("ii", $id_estudiante, $id_materia);

        if ($stmt_insert->execute()) {
            $success = "Inscripción realizada exitosamente.";
        } else {
            $error = "Error al inscribirse: " . $stmt_insert->error;
        }

        $stmt_insert->close();
    }

    $stmt->close();
}

// Obtener materias disponibles
$sql = "SELECT m.id_materia, m.nombre, m.codigo
        FROM materias m
        WHERE m.estado = 'abierta'";
$materias = $conn->query($sql);
?>
<h2>Inscripción de Materias</h2>

<?php if (isset($success)): ?>
    <p class="success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre de la Materia</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($materias && $materias->num_rows > 0): ?>
            <?php while ($row = $materias->fetch_assoc()): ?>
                <?php
                // Verificar si el estudiante ya está inscrito en esta materia
                $stmt_check = $conn->prepare("SELECT id_inscripcion FROM inscripciones WHERE id_estudiante = ? AND id_materia = ?");
                $stmt_check->bind_param("ii", $id_estudiante, $row['id_materia']);
                $stmt_check->execute();
                $stmt_check->store_result();
                $already_enrolled = ($stmt_check->num_rows > 0);
                $stmt_check->close();
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td>
                        <?php if ($already_enrolled): ?>
                            Ya inscrito
                        <?php else: ?>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="id_materia" value="<?php echo $row['id_materia']; ?>">
                                <button type="submit" name="inscribir">Inscribirse</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">No hay materias disponibles para inscribirse.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$conn->close();
include '../../include/footer.php';
?>
