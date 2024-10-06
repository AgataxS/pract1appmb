<?php
// views/admin/aulas.php
include '../../include/auth.php';
requireAdmin();
include '../../include/header.php';
include '../../include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_aula'])) {
    $nombre_aula = $conn->real_escape_string($_POST['nombre_aula']);
    $numero = intval($_POST['numero']);

    $stmt = $conn->prepare("INSERT INTO aulas (nombre_aula, numero) VALUES (?, ?)");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("si", $nombre_aula, $numero);

    if ($stmt->execute()) {
        $success = "Aula agregada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_aula'])) {
    $id = intval($_POST['id_aula']);

    $stmt = $conn->prepare("DELETE FROM aulas WHERE id_aula = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Aula eliminada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_aula'])) {
    $id = intval($_POST['id_aula']);
    $nombre_aula = $conn->real_escape_string($_POST['nombre_aula']);
    $numero = intval($_POST['numero']);

    $stmt = $conn->prepare("UPDATE aulas SET nombre_aula = ?, numero = ? WHERE id_aula = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("sii", $nombre_aula, $numero, $id);

    if ($stmt->execute()) {
        $success = "Aula actualizada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM aulas WHERE nombre_aula LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM aulas";
}

$result = $conn->query($sql);
?>
<h2>Aulas</h2>

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
    <label for="nombre_aula">Nombre del Aula:</label>
    <input type="text" name="nombre_aula" id="nombre_aula" required>

    <label for="numero">Número:</label>
    <input type="number" name="numero" id="numero" required>

    <button type="submit" name="add_aula">Agregar</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre del Aula</th>
            <th>Número</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_aula']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre_aula']); ?></td>
                    <td><?php echo htmlspecialchars($row['numero']); ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar esta aula?');">
                            <input type="hidden" name="id_aula" value="<?php echo $row['id_aula']; ?>">
                            <button type="submit" name="delete_aula">Eliminar</button>
                        </form>
                        <button onclick="openEditModal(<?php echo $row['id_aula']; ?>, '<?php echo htmlspecialchars($row['nombre_aula'], ENT_QUOTES); ?>', <?php echo $row['numero']; ?>)">Editar</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No hay aulas registradas.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar Aula</h2>
        <form method="POST" action="">
            <input type="hidden" name="id_aula" id="edit_id_aula">

            <label for="edit_nombre_aula">Nombre del Aula:</label>
            <input type="text" name="nombre_aula" id="edit_nombre_aula" required>

            <label for="edit_numero">Número:</label>
            <input type="number" name="numero" id="edit_numero" required>

            <button type="submit" name="edit_aula">Actualizar</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, nombre_aula, numero) {
    document.getElementById('edit_id_aula').value = id;
    document.getElementById('edit_nombre_aula').value = nombre_aula;
    document.getElementById('edit_numero').value = numero;
    document.getElementById('editModal').style.display = 'block';
}
</script>

<?php
$conn->close();
include '../../include/footer.php';
?>
