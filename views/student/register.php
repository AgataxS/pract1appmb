<?php
// views/student/register.php

// Incluir archivos necesarios
include '../../include/db_connection.php';
include '../../include/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $ci = $conn->real_escape_string($_POST['ci']);
    $carrera_id = intval($_POST['carrera_id']);
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen_nombre = basename($_FILES["imagen"]["name"]);
        $imagen_extension = strtolower(pathinfo($imagen_nombre, PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");

        if (in_array($imagen_extension, $allowed_extensions)) {
            $imagen_ruta = ROOT_PATH . "/uploads/" . uniqid() . "." . $imagen_extension;
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $imagen_ruta)) {
                $imagen_db = "uploads/" . basename($imagen_ruta);
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
        // Verificar si el nombre de usuario ya existe
        $stmt_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE username = ?");
        $stmt_check->bind_param("s", $usuario);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = "El nombre de usuario ya está en uso.";
        } else {
            $stmt = $conn->prepare("INSERT INTO usuarios (username, password, role) VALUES (?, ?, 'student')");
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt->bind_param("ss", $usuario, $password);

            if ($stmt->execute()) {
                $usuario_id = $stmt->insert_id;
                $stmt->close();

                $stmt = $conn->prepare("INSERT INTO estudiantes (nombre, apellido, correo, carrera_id, ci, imagen, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    die("Error en la preparación de la consulta: " . $conn->error);
                }
                $stmt->bind_param("sssissi", $nombre, $apellido, $correo, $carrera_id, $ci, $imagen_db, $usuario_id);

                if ($stmt->execute()) {
                    $success = "Registro exitoso. Ahora puedes iniciar sesión.";
                } else {
                    $error = "Error al registrar estudiante: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $error = "Error al crear usuario: " . $stmt->error;
            }
        }
        $stmt_check->close();
    }
}

$carreras = $conn->query("SELECT * FROM carreras");
?>
<h2>Registro de Estudiante</h2>

<?php if (isset($success)): ?>
    <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <p><a href="<?php echo BASE_URL; ?>/login.php">Iniciar Sesión</a></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" required>

    <label for="apellido">Apellido:</label>
    <input type="text" name="apellido" required>

    <label for="correo">Correo:</label>
    <input type="email" name="correo" required>

    <label for="ci">CI:</label>
    <input type="text" name="ci" required>

    <label for="carrera_id">Carrera:</label>
    <select name="carrera_id" required>
        <option value="">Selecciona una carrera</option>
        <?php while ($row = $carreras->fetch_assoc()): ?>
            <option value="<?php echo $row['id_carrera']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
        <?php endwhile; ?>
    </select>

    <label for="imagen">Imagen:</label>
    <input type="file" name="imagen" accept="image/*">

    <label for="usuario">Usuario:</label>
    <input type="text" name="usuario" required>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" required>

    <button type="submit">Registrarse</button>
</form>

<?php
$conn->close();
include '../../include/footer.php';
?>
