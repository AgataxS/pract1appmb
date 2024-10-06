<?php
// views/admin/facultades.php
include '../../include/auth.php';
requireAdmin();
include '../../include/header.php';
include '../../include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_facultad'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);

    $stmt = $conn->prepare("INSERT INTO facultades (nombre) VALUES (?)");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $nombre);

    if ($stmt->execute()) {
        $success = "Facultad agregada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_facultad'])) {
    $id = intval($_POST['id_facultad']);

    $stmt = $conn->prepare("DELETE FROM facultades WHERE id_facultad = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Facultad eliminada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_facultad'])) {
    $id = intval($_POST['id_facultad']);
    $nombre = $conn->real_escape_string($_POST['nombre']);

    $stmt = $conn->prepare("UPDATE facultades SET nombre = ? WHERE id_facultad = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("si", $nombre, $id);

    if ($stmt->execute()) {
        $success = "Facultad actualizada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM facultades WHERE nombre LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM facultades";
}

$result = $conn->query($sql);
?>
<h2>Facultades</h2>

<?php if (isset($success)): ?>
    <p class="success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Buscar por nombre" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Buscar</button>
</form>

<form method="POST" action="">
    <label for="nombre">Nombre de la Facultad:</label>
    <input type="text" name="nombre" id="nombre" required>
    <button type="submit" name="add_facultad">Agregar</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_facultad']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar esta facultad?');">
                            <input type="hidden" name="id_facultad" value="<?php echo $row['id_facultad']; ?>">
                            <button type="submit" name="delete_facultad">Eliminar</button>
                        </form>
                        <button onclick="openEditModal(<?php echo $row['id_facultad']; ?>, '<?php echo htmlspecialchars($row['nombre'], ENT_QUOTES); ?>')">Editar</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">No hay facultades registradas.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar Facultad</h2>
        <form method="POST" action="">
            <input type="hidden" name="id_facultad" id="edit_id_facultad">
            <label for="edit_nombre">Nombre de la Facultad:</label>
            <input type="text" name="nombre" id="edit_nombre" required>
            <button type="submit" name="edit_facultad">Actualizar</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, nombre) {
    document.getElementById('edit_id_facultad').value = id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('editModal').style.display = 'block';
}
</script>

<?php
$conn->close();
include '../../include/footer.php';
?>
