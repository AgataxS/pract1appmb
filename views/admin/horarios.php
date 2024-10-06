<?php
// views/admin/horarios.php
include '../../include/auth.php';
requireAdmin();
include '../../include/header.php';
include '../../include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_horario'])) {
    $turno = $conn->real_escape_string($_POST['turno']);
    $hora_inicio = $conn->real_escape_string($_POST['hora_inicio']);
    $hora_fin = $conn->real_escape_string($_POST['hora_fin']);
    $receso = isset($_POST['receso']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO horarios (turno, hora_inicio, hora_fin, receso) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("sssi", $turno, $hora_inicio, $hora_fin, $receso);

    if ($stmt->execute()) {
        $success = "Horario agregado exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_horario'])) {
    $id = intval($_POST['id_horario']);

    $stmt = $conn->prepare("DELETE FROM horarios WHERE id_horario = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Horario eliminado exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_horario'])) {
    $id = intval($_POST['id_horario']);
    $turno = $conn->real_escape_string($_POST['turno']);
    $hora_inicio = $conn->real_escape_string($_POST['hora_inicio']);
    $hora_fin = $conn->real_escape_string($_POST['hora_fin']);
    $receso = isset($_POST['receso']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE horarios SET turno = ?, hora_inicio = ?, hora_fin = ?, receso = ? WHERE id_horario = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("sssii", $turno, $hora_inicio, $hora_fin, $receso, $id);

    if ($stmt->execute()) {
        $success = "Horario actualizado exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$sql = "SELECT * FROM horarios";
$result = $conn->query($sql);
?>
<h2>Horarios</h2>

<?php if (isset($success)): ?>
    <p class="success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="turno">Turno:</label>
    <select name="turno" id="turno" required>
        <option value="">Selecciona un turno</option>
        <option value="mañana">Mañana</option>
        <option value="tarde">Tarde</option>
        <option value="noche">Noche</option>
    </select>

    <label for="hora_inicio">Hora Inicio:</label>
    <input type="time" name="hora_inicio" id="hora_inicio" required>

    <label for="hora_fin">Hora Fin:</label>
    <input type="time" name="hora_fin" id="hora_fin" required>

    <label for="receso">Receso:</label>
    <input type="checkbox" name="receso" id="receso" value="1">

    <button type="submit" name="add_horario">Agregar</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Turno</th>
            <th>Hora Inicio</th>
            <th>Hora Fin</th>
            <th>Receso</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_horario']); ?></td>
                    <td><?php echo htmlspecialchars($row['turno']); ?></td>
                    <td><?php echo htmlspecialchars($row['hora_inicio']); ?></td>
                    <td><?php echo htmlspecialchars($row['hora_fin']); ?></td>
                    <td><?php echo $row['receso'] ? 'Sí' : 'No'; ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este horario?');">
                            <input type="hidden" name="id_horario" value="<?php echo $row['id_horario']; ?>">
                            <button type="submit" name="delete_horario">Eliminar</button>
                        </form>
                        <button onclick='openEditModal(<?php echo $row['id_horario']; ?>, <?php echo json_encode($row['turno']); ?>, <?php echo json_encode($row['hora_inicio']); ?>, <?php echo json_encode($row['hora_fin']); ?>, <?php echo $row['receso']; ?>)'>Editar</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No hay horarios registrados.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Editar Horario</h2>
        <form method="POST" action="">
            <input type="hidden" name="id_horario" id="edit_id_horario">

            <label for="edit_turno">Turno:</label>
            <select name="turno" id="edit_turno" required>
                <option value="">Selecciona un turno</option>
                <option value="mañana">Mañana</option>
                <option value="tarde">Tarde</option>
                <option value="noche">Noche</option>
            </select>

            <label for="edit_hora_inicio">Hora Inicio:</label>
            <input type="time" name="hora_inicio" id="edit_hora_inicio" required>

            <label for="edit_hora_fin">Hora Fin:</label>
            <input type="time" name="hora_fin" id="edit_hora_fin" required>

            <label for="edit_receso">Receso:</label>
            <input type="checkbox" name="receso" id="edit_receso" value="1">

            <button type="submit" name="edit_horario">Actualizar</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, turno, horaInicio, horaFin, receso) {
    document.getElementById('edit_id_horario').value = id;
    document.getElementById('edit_turno').value = turno;
    document.getElementById('edit_hora_inicio').value = horaInicio;
    document.getElementById('edit_hora_fin').value = horaFin;
    document.getElementById('edit_receso').checked = receso == 1;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

<?php
$conn->close();
include '../../include/footer.php';
?>
