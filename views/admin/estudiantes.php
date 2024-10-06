<?php
// views/admin/estudiantes.php
include '../../include/auth.php';
requireAdmin();
include '../../include/header.php';
include '../../include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_estudiante'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $carrera_id = intval($_POST['carrera_id']);
    $ci = $conn->real_escape_string($_POST['ci']);

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen_nombre = basename($_FILES["imagen"]["name"]);
        $imagen_extension = strtolower(pathinfo($imagen_nombre, PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");

        if (in_array($imagen_extension, $allowed_extensions)) {
            $imagen_ruta = "../../uploads/" . uniqid() . "." . $imagen_extension;
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $imagen_ruta)) {
                $imagen_db = substr($imagen_ruta, 5); // Para quitar '../../' y guardar ruta relativa
            } else {
                $error = "Error al subir la imagen.";
            }
        } else {
            $error = "Tipo de archivo de imagen no permitido.";
        }
    } else {
        $imagen_db = NULL;
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO estudiantes (nombre, apellido, correo, carrera_id, ci, imagen) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("sssiss", $nombre, $apellido, $correo, $carrera_id, $ci, $imagen_db);

        if ($stmt->execute()) {
            // Crear usuario para el estudiante
            $usuario_nombre = strtolower(str_replace(' ', '', $nombre)) . "." . strtolower(str_replace(' ', '', $apellido));
            $usuario_password = password_hash('password123', PASSWORD_DEFAULT);
            $role = 'student';
            $estudiante_id = $stmt->insert_id;

            $stmt_user = $conn->prepare("INSERT INTO usuarios (username, password, role, estudiante_id) VALUES (?, ?, ?, ?)");
            if (!$stmt_user) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt_user->bind_param("sssi", $usuario_nombre, $usuario_password, $role, $estudiante_id);

            if ($stmt_user->execute()) {
                // Actualizar el estudiante con el usuario_id
                $usuario_id = $stmt_user->insert_id;
                $stmt_update = $conn->prepare("UPDATE estudiantes SET usuario_id = ? WHERE id_estudiante = ?");
                $stmt_update->bind_param("ii", $usuario_id, $estudiante_id);
                $stmt_update->execute();
                $stmt_update->close();

                $success = "Estudiante agregado exitosamente. Usuario: $usuario_nombre | Contraseña: password123";
            } else {
                $error = "Estudiante agregado, pero error al crear el usuario: " . $stmt_user->error;
            }

            $stmt_user->close();
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_estudiante'])) {
    $id = intval($_POST['id_estudiante']);

    $stmt_user = $conn->prepare("SELECT usuario_id FROM estudiantes WHERE id_estudiante = ?");
    if (!$stmt_user) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt_user->bind_param("i", $id);
    $stmt_user->execute();
    $stmt_user->bind_result($usuario_id);
    $stmt_user->fetch();
    $stmt_user->close();

    if ($usuario_id) {
        $stmt_delete_user = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        if (!$stmt_delete_user) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt_delete_user->bind_param("i", $usuario_id);
        $stmt_delete_user->execute();
        $stmt_delete_user->close();
    }

    $stmt = $conn->prepare("DELETE FROM estudiantes WHERE id_estudiante = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Estudiante eliminado exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_estudiante'])) {
    $id = intval($_POST['id_estudiante']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $carrera_id = intval($_POST['carrera_id']);
    $ci = $conn->real_escape_string($_POST['ci']);

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen_nombre = basename($_FILES["imagen"]["name"]);
        $imagen_extension = strtolower(pathinfo($imagen_nombre, PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");

        if (in_array($imagen_extension, $allowed_extensions)) {
            $imagen_ruta = "../../uploads/" . uniqid() . "." . $imagen_extension;
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $imagen_ruta)) {
                $stmt_img = $conn->prepare("SELECT imagen FROM estudiantes WHERE id_estudiante = ?");
                if (!$stmt_img) {
                    die("Error en la preparación de la consulta: " . $conn->error);
                }
                $stmt_img->bind_param("i", $id);
                $stmt_img->execute();
                $stmt_img->bind_result($imagen_antigua);
                $stmt_img->fetch();
                $stmt_img->close();

                if ($imagen_antigua && file_exists('../../' . $imagen_antigua)) {
                    unlink('../../' . $imagen_antigua);
                }

                $imagen_db = substr($imagen_ruta, 5);
            } else {
                $error = "Error al subir la imagen.";
            }
        } else {
            $error = "Tipo de archivo de imagen no permitido.";
        }
    } else {
        $imagen_db = NULL;
    }

    if (!isset($error)) {
        if ($imagen_db) {
            $stmt = $conn->prepare("UPDATE estudiantes SET nombre = ?, apellido = ?, correo = ?, carrera_id = ?, ci = ?, imagen = ? WHERE id_estudiante = ?");
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt->bind_param("sssissi", $nombre, $apellido, $correo, $carrera_id, $ci, $imagen_db, $id);
        } else {
            $stmt = $conn->prepare("UPDATE estudiantes SET nombre = ?, apellido = ?, correo = ?, carrera_id = ?, ci = ? WHERE id_estudiante = ?");
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt->bind_param("sssisi", $nombre, $apellido, $correo, $carrera_id, $ci, $id);
        }

        if ($stmt->execute()) {
            $success = "Estudiante actualizado exitosamente.";
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Obtener las carreras para los select
$carreras = $conn->query("SELECT * FROM carreras");

// Convertir carreras a un arreglo para reutilizar
$carreras_array = [];
while ($row = $carreras->fetch_assoc()) {
    $carreras_array[] = $row;
}
$carreras->data_seek(0); // Reiniciar puntero

$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT e.id_estudiante, e.nombre, e.apellido, e.correo, e.carrera_id, c.nombre AS carrera, e.ci, e.imagen
            FROM estudiantes e
            LEFT JOIN carreras c ON e.carrera_id = c.id_carrera
            WHERE e.nombre LIKE '%$search%' OR e.apellido LIKE '%$search%'";
} else {
    $sql = "SELECT e.id_estudiante, e.nombre, e.apellido, e.correo, e.carrera_id, c.nombre AS carrera, e.ci, e.imagen
            FROM estudiantes e
            LEFT JOIN carreras c ON e.carrera_id = c.id_carrera";
}

$result = $conn->query($sql);
?>
<h2>Estudiantes</h2>

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

<form method="POST" action="" enctype="multipart/form-data">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" id="nombre" required>

    <label for="apellido">Apellido:</label>
    <input type="text" name="apellido" id="apellido" required>

    <label for="correo">Correo:</label>
    <input type="email" name="correo" id="correo" required>

    <label for="carrera_id">Carrera:</label>
    <select name="carrera_id" id="carrera_id" required>
        <option value="">Selecciona una carrera</option>
        <?php foreach ($carreras_array as $row): ?>
            <option value="<?php echo $row['id_carrera']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
        <?php endforeach; ?>
    </select>

    <label for="ci">CI:</label>
    <input type="text" name="ci" id="ci" required>

    <label for="imagen">Imagen:</label>
    <input type="file" name="imagen" id="imagen" accept="image/*">

    <button type="submit" name="add_estudiante">Agregar</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Correo</th>
            <th>Carrera</th>
            <th>CI</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_estudiante']); ?></td>
                    <td>
                        <?php if ($row['imagen']): ?>
                            <img src="../../<?php echo htmlspecialchars($row['imagen']); ?>" alt="Imagen" width="50">
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($row['correo']); ?></td>
                    <td><?php echo htmlspecialchars($row['carrera']); ?></td>
                    <td><?php echo htmlspecialchars($row['ci']); ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este estudiante?');">
                            <input type="hidden" name="id_estudiante" value="<?php echo $row['id_estudiante']; ?>">
                            <button type="submit" name="delete_estudiante">Eliminar</button>
                        </form>
                        <button onclick='openEditModal(<?php echo $row['id_estudiante']; ?>, <?php echo json_encode($row['nombre']); ?>, <?php echo json_encode($row['apellido']); ?>, <?php echo json_encode($row['correo']); ?>, <?php echo $row['carrera_id']; ?>, <?php echo json_encode($row['ci']); ?>)'>Editar</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No hay estudiantes registrados.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Editar Estudiante</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="id_estudiante" id="edit_id_estudiante">

            <label for="edit_nombre">Nombre:</label>
            <input type="text" name="nombre" id="edit_nombre" required>

            <label for="edit_apellido">Apellido:</label>
            <input type="text" name="apellido" id="edit_apellido" required>

            <label for="edit_correo">Correo:</label>
            <input type="email" name="correo" id="edit_correo" required>

            <label for="edit_carrera_id">Carrera:</label>
            <select name="carrera_id" id="edit_carrera_id" required>
                <option value="">Selecciona una carrera</option>
                <?php foreach ($carreras_array as $row_carr): ?>
                    <option value="<?php echo $row_carr['id_carrera']; ?>"><?php echo htmlspecialchars($row_carr['nombre']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="edit_ci">CI:</label>
            <input type="text" name="ci" id="edit_ci" required>

            <label for="edit_imagen">Imagen (dejar en blanco para no cambiar):</label>
            <input type="file" name="imagen" id="edit_imagen" accept="image/*">

            <button type="submit" name="edit_estudiante">Actualizar</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, nombre, apellido, correo, carreraId, ci) {
    document.getElementById('edit_id_estudiante').value = id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_apellido').value = apellido;
    document.getElementById('edit_correo').value = correo;
    document.getElementById('edit_carrera_id').value = carreraId;
    document.getElementById('edit_ci').value = ci;
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
