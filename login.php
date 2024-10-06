<?php
// login.php

// Incluir archivos necesarios
include 'include/db_connection.php';
include 'include/auth.php'; // Mover session_start() aquí
include 'include/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id_usuario, password, role FROM usuarios WHERE username = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->bind_result($id_usuario, $hashed_password, $role);
    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {
           
            $_SESSION['id_usuario'] = $id_usuario;
            $_SESSION['usuario'] = $usuario;
            $_SESSION['role'] = $role;

          
            if ($role == 'admin') {
                header('Location: ' . BASE_URL . '/views/admin/dashboard.php');
            } else if ($role == 'student') {
                header('Location: ' . BASE_URL . '/views/student/preinscripcion.php');
            }
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
    $stmt->close();
}
?>
<h2>Iniciar Sesión</h2>

<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="usuario">Usuario:</label>
    <input type="text" name="usuario" required>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" required>

    <button type="submit">Iniciar Sesión</button>
</form>

<?php
include 'include/footer.php';
?>
