<?php
// views/admin/docentes.php
include '../../include/auth.php';
requireAdmin();
include '../../include/header.php';
include '../../include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_docente'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $turno = $conn->real_escape_string($_POST['turno']);

    $stmt = $conn->prepare("INSERT INTO docentes (nombre, apellido, telefono, turno) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("ssss", $nombre, $apellido, $telefono, $turno);

    if ($stmt->execute()) {
        $success = "Docente agregado exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_docente'])) {
    $id = intval($_POST['id_docente']);

    $stmt = $conn->prepare("DELETE FROM docentes WHERE id_docente = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Docente eliminado exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_docente'])) {
    $id = intval($_POST['id_docente']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $turno = $conn->real_escape_string($_POST['turno']);

    $stmt = $conn->prepare("UPDATE docentes SET nombre = ?, apellido = ?, telefono = ?, turno = ? WHERE id_docente = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("sss si", $nombre, $apellido, $telefono, $turno, $id);

    if ($stmt->execute()) {
        $success = "Docente actualizado exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM docentes WHERE nombre LIKE '%$search%' OR apellido LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM docentes";
}

$result = $conn->query($sql);
?>
<h2>Docentes</h2>

<?php if (isset($success)): ?>
    <p class="success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Buscar por nombre o apellido" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Buscar</button>
</form>

<form method="POST" action="">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" id="nombre" required>

    <label for="apellido">Apellido:</label>
    <input type="text" name="apellido" id="apellido" required>

    <label for="telefono">Teléfono:</label>
    <input type="text" name="telefono" id="telefono" required>

    <label for="turno">Turno:</label>
    <select name="turno" id="turno" required>
        <option value="mañana">Mañana</option>
        <option value="tarde">Tarde</option>
        <option value="noche">Noche</option>
    </select>

    <button type="submit" name="add_docente">Agregar</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Teléfono</th>
            <th>Turno</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_docente']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($row['turno']); ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este docente?');">
                            <input type="hidden" name="id_docente" value="<?php echo $row['id_docente']; ?>">
                            <button type="submit" name="delete_docente">Eliminar</button>
                        </form>
                        <button onclick="openEditModal(<?php echo $row['id_docente']; ?>, '<?php echo htmlspecialchars($row['nombre'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['apellido'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['telefono'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['turno'], ENT_QUOTES); ?>')">Editar</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No hay docentes registrados.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar Docente</h2>
        <form method="POST" action="">
            <input type="hidden" name="id_docente" id="edit_id_docente">

            <label for="edit_nombre">Nombre:</label>
            <input type="text" name="nombre" id="edit_nombre" required>

            <label for="edit_apellido">Apellido:</label>
            <input type="text" name="apellido" id="edit_apellido" required>

            <label for="edit_telefono">Teléfono:</label>
            <input type="text" name="telefono" id="edit_telefono" required>

            <label for="edit_turno">Turno:</label>
            <select name="turno" id="edit_turno" required>
                <option value="mañana">Mañana</option>
                <option value="tarde">Tarde</option>
                <option value="noche">Noche</option>
            </select>

            <button type="submit" name="edit_docente">Actualizar</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, nombre, apellido, telefono, turno) {
    document.getElementById('edit_id_docente').value = id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_apellido').value = apellido;
    document.getElementById('edit_telefono').value = telefono;
    document.getElementById('edit_turno').value = turno;
    document.getElementById('editModal').style.display = 'block';
}
</script>

<?php
$conn->close();
include '../../include/footer.php';
?>
