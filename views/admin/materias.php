<?php
// views/admin/materias.php
include '../../include/auth.php';
requireAdmin();
include '../../include/header.php';
include '../../include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_materia'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $codigo = $conn->real_escape_string($_POST['codigo']);
    $estado = $conn->real_escape_string($_POST['estado']);

    $stmt = $conn->prepare("INSERT INTO materias (nombre, codigo, estado) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("sss", $nombre, $codigo, $estado);

    if ($stmt->execute()) {
        $success = "Materia agregada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_materia'])) {
    $id = intval($_POST['id_materia']);

    $stmt = $conn->prepare("DELETE FROM materias WHERE id_materia = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Materia eliminada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_materia'])) {
    $id = intval($_POST['id_materia']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $codigo = $conn->real_escape_string($_POST['codigo']);
    $estado = $conn->real_escape_string($_POST['estado']);

    $stmt = $conn->prepare("UPDATE materias SET nombre = ?, codigo = ?, estado = ? WHERE id_materia = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("sssi", $nombre, $codigo, $estado, $id);

    if ($stmt->execute()) {
        $success = "Materia actualizada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM materias WHERE nombre LIKE '%$search%' OR codigo LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM materias";
}

$result = $conn->query($sql);
?>
<h2>Materias</h2>

<?php if (isset($success)): ?>
    <p class="success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Buscar por nombre o código" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Buscar</button>
</form>

<form method="POST" action="">
    <label for="nombre">Nombre de la Materia:</label>
    <input type="text" name="nombre" id="nombre" required>

    <label for="codigo">Código:</label>
    <input type="text" name="codigo" id="codigo" required>

    <label for="estado">Estado:</label>
    <select name="estado" id="estado" required>
        <option value="abierta">Abierta</option>
        <option value="cerrada">Cerrada</option>
    </select>

    <button type="submit" name="add_materia">Agregar</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Código</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_materia']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                    <td><?php echo htmlspecialchars($row['estado']); ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar esta materia?');">
                            <input type="hidden" name="id_materia" value="<?php echo $row['id_materia']; ?>">
                            <button type="submit" name="delete_materia">Eliminar</button>
                        </form>
                        <button onclick="openEditModal(<?php echo $row['id_materia']; ?>, '<?php echo htmlspecialchars($row['nombre'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['codigo'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['estado'], ENT_QUOTES); ?>')">Editar</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No hay materias registradas.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar Materia</h2>
        <form method="POST" action="">
            <input type="hidden" name="id_materia" id="edit_id_materia">

            <label for="edit_nombre">Nombre de la Materia:</label>
            <input type="text" name="nombre" id="edit_nombre" required>

            <label for="edit_codigo">Código:</label>
            <input type="text" name="codigo" id="edit_codigo" required>

            <label for="edit_estado">Estado:</label>
            <select name="estado" id="edit_estado" required>
                <option value="abierta">Abierta</option>
                <option value="cerrada">Cerrada</option>
            </select>

            <button type="submit" name="edit_materia">Actualizar</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, nombre, codigo, estado) {
    document.getElementById('edit_id_materia').value = id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_codigo').value = codigo;
    document.getElementById('edit_estado').value = estado;
    document.getElementById('editModal').style.display = 'block';
}
</script>

<?php
$conn->close();
include '../../include/footer.php';
?>
