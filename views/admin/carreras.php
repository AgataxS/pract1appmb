<?php
// views/admin/carreras.php
include '../../include/auth.php';
requireAdmin();
include '../../include/header.php';
include '../../include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_carrera'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $facultad_id = intval($_POST['facultad_id']);

    $stmt = $conn->prepare("INSERT INTO carreras (nombre, facultad_id) VALUES (?, ?)");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("si", $nombre, $facultad_id);

    if ($stmt->execute()) {
        $success = "Carrera agregada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_carrera'])) {
    $id = intval($_POST['id_carrera']);

    $stmt = $conn->prepare("DELETE FROM carreras WHERE id_carrera = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Carrera eliminada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_carrera'])) {
    $id = intval($_POST['id_carrera']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $facultad_id = intval($_POST['facultad_id']);

    $stmt = $conn->prepare("UPDATE carreras SET nombre = ?, facultad_id = ? WHERE id_carrera = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("sii", $nombre, $facultad_id, $id);

    if ($stmt->execute()) {
        $success = "Carrera actualizada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Obtener las facultades para los select
$facultades = $conn->query("SELECT * FROM facultades");

// Reiniciar el puntero de resultados de facultades para reutilizarlo
$facultades_array = [];
while ($row = $facultades->fetch_assoc()) {
    $facultades_array[] = $row;
}
$facultades->data_seek(0); // Reiniciar puntero

$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT c.id_carrera, c.nombre, c.facultad_id, f.nombre AS facultad
            FROM carreras c
            LEFT JOIN facultades f ON c.facultad_id = f.id_facultad
            WHERE c.nombre LIKE '%$search%'";
} else {
    $sql = "SELECT c.id_carrera, c.nombre, c.facultad_id, f.nombre AS facultad
            FROM carreras c
            LEFT JOIN facultades f ON c.facultad_id = f.id_facultad";
}

$result = $conn->query($sql);
?>
<h2>Carreras</h2>

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
    <label for="nombre">Nombre de la Carrera:</label>
    <input type="text" name="nombre" id="nombre" required>

    <label for="facultad_id">Facultad:</label>
    <select name="facultad_id" id="facultad_id" required>
        <option value="">Selecciona una facultad</option>
        <?php foreach ($facultades_array as $row): ?>
            <option value="<?php echo $row['id_facultad']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" name="add_carrera">Agregar</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Facultad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_carrera']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['facultad']); ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar esta carrera?');">
                            <input type="hidden" name="id_carrera" value="<?php echo $row['id_carrera']; ?>">
                            <button type="submit" name="delete_carrera">Eliminar</button>
                        </form>
                        <button onclick='openEditModal(<?php echo $row['id_carrera']; ?>, <?php echo json_encode($row['nombre']); ?>, <?php echo $row['facultad_id']; ?>)'>Editar</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No hay carreras registradas.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Editar Carrera</h2>
        <form method="POST" action="">
            <input type="hidden" name="id_carrera" id="edit_id_carrera">
            <label for="edit_nombre">Nombre de la Carrera:</label>
            <input type="text" name="nombre" id="edit_nombre" required>

            <label for="edit_facultad_id">Facultad:</label>
            <select name="facultad_id" id="edit_facultad_id" required>
                <option value="">Selecciona una facultad</option>
                <?php foreach ($facultades_array as $row_fac): ?>
                    <option value="<?php echo $row_fac['id_facultad']; ?>"><?php echo htmlspecialchars($row_fac['nombre']); ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="edit_carrera">Actualizar</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, nombre, facultadId) {
    document.getElementById('edit_id_carrera').value = id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_facultad_id').value = facultadId;
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
