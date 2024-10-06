<?php
// views/student/solicitar_materia.php

include '../../include/auth.php';
requireStudent();
include '../../include/db_connection.php';
include '../../include/header.php';

// Obtener el ID del estudiante a partir de la sesión
$usuario_id = $_SESSION['id_usuario'];

// Obtener el ID del estudiante y verificar si ya tiene una imagen
$stmt = $conn->prepare("SELECT id_estudiante, imagen FROM estudiantes WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($id_estudiante, $imagen_actual);
$stmt->fetch();
$stmt->close();

// Manejar la solicitud de materia
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['solicitar_materia'])) {
    if (!isset($_POST['id_materia'])) {
        $error = "Por favor, selecciona una materia.";
    } else {
        $id_materia = intval($_POST['id_materia']);

        // Verificar si la materia existe en la base de datos
        $stmt_check_materia = $conn->prepare("SELECT id_materia FROM materias WHERE id_materia = ?");
        $stmt_check_materia->bind_param("i", $id_materia);
        $stmt_check_materia->execute();
        $stmt_check_materia->store_result();

        if ($stmt_check_materia->num_rows == 0) {
            $error = "La materia seleccionada no existe.";
        } else {
            // Verificar si ya ha solicitado esta materia
            $stmt = $conn->prepare("SELECT id_inscripcion FROM inscripciones WHERE id_estudiante = ? AND id_materia = ?");
            $stmt->bind_param("ii", $id_estudiante, $id_materia);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Ya has solicitado esta materia.";
            } else {
                // Procesar la imagen si no tiene una
                if (!$imagen_actual) {
                    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                        $imagen_nombre = basename($_FILES["imagen"]["name"]);
                        $imagen_extension = strtolower(pathinfo($imagen_nombre, PATHINFO_EXTENSION));
                        $allowed_extensions = array("jpg", "jpeg", "png", "gif");

                        if (in_array($imagen_extension, $allowed_extensions)) {
                            $imagen_ruta = ROOT_PATH . "/uploads/" . uniqid() . "." . $imagen_extension;
                            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $imagen_ruta)) {
                                $imagen_db = "uploads/" . basename($imagen_ruta);
                                // Actualizar la imagen en la tabla estudiantes
                                $stmt_update = $conn->prepare("UPDATE estudiantes SET imagen = ? WHERE id_estudiante = ?");
                                $stmt_update->bind_param("si", $imagen_db, $id_estudiante);
                                $stmt_update->execute();
                                $stmt_update->close();
                            } else {
                                $error = "Error al subir la imagen.";
                            }
                        } else {
                            $error = "Tipo de archivo de imagen no permitido.";
                        }
                    } else {
                        $error = "Por favor, sube una imagen.";
                    }
                }

                // Si no hay errores, insertar la solicitud
                if (!isset($error)) {
                    // Insertar la solicitud en inscripciones con estado 'pendiente'
                    $estado = 'pendiente';
                    $stmt_insert = $conn->prepare("INSERT INTO inscripciones (id_estudiante, id_materia, estado) VALUES (?, ?, ?)");
                    if (!$stmt_insert) {
                        die("Error en la preparación de la consulta: " . $conn->error);
                    }
                    $stmt_insert->bind_param("iis", $id_estudiante, $id_materia, $estado);

                    if ($stmt_insert->execute()) {
                        $success = "Solicitud enviada exitosamente.";
                    } else {
                        $error = "Error al enviar la solicitud: " . $stmt_insert->error;
                    }

                    $stmt_insert->close();
                }
            }

            $stmt->close();
        }

        $stmt_check_materia->close();
    }
}

// Obtener todas las materias
$sql = "SELECT m.id_materia, m.nombre, m.codigo, m.estado
        FROM materias m";
$result = $conn->query($sql);
?>
<h2>Solicitar Materias</h2>

<?php if (isset($success)): ?>
    <p class="success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if (!$imagen_actual): ?>
    <p>Nota: Debes subir una foto para poder solicitar una materia.</p>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre de la Materia</th>
                <th>Estado</th>
                <th>Seleccionar</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    // Verificar si el estudiante ya solicitó esta materia
                    $stmt_check = $conn->prepare("SELECT id_inscripcion FROM inscripciones WHERE id_estudiante = ? AND id_materia = ?");
                    $stmt_check->bind_param("ii", $id_estudiante, $row['id_materia']);
                    $stmt_check->execute();
                    $stmt_check->store_result();
                    $already_requested = ($stmt_check->num_rows > 0);
                    $stmt_check->close();
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['estado']); ?></td>
                        <td>
                            <?php if ($already_requested): ?>
                                Ya solicitado
                            <?php else: ?>
                                <input type="radio" name="id_materia" value="<?php echo $row['id_materia']; ?>" required>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No hay materias disponibles para solicitar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (!$imagen_actual): ?>
        <label for="imagen">Sube tu foto:</label>
        <input type="file" name="imagen" id="imagen" accept="image/*" required>
    <?php endif; ?>

    <button type="submit" name="solicitar_materia">Solicitar Materia Seleccionada</button>
</form>

<?php
$conn->close();
include '../../include/footer.php';
?>
