<?php
// include/header.php

// Iniciar sesión si aún no se ha iniciado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definir la ruta base del proyecto si no está definida
if (!defined('BASE_URL')) {
    define('BASE_URL', '/practica1'); // Ajusta '/practica1' a la ruta correcta de tu proyecto
}

// Definir la ruta raíz del proyecto si no está definida
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema Universitario</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    <script src="<?php echo BASE_URL; ?>/js/scripts.js" defer></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistema Universitario</h1>
            <?php if (isset($_SESSION['usuario'])): ?>
                <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?> | <a href="<?php echo BASE_URL; ?>/logout.php">Cerrar Sesión</a></p>
            <?php else: ?>
                <p><a href="<?php echo BASE_URL; ?>/login.php">Iniciar Sesión</a> | <a href="<?php echo BASE_URL; ?>/views/student/register.php">Registrarse</a></p>
            <?php endif; ?>
        </header>
        <nav class="nav">
            <ul>
                <li><a href="<?php echo BASE_URL; ?>/index.php">Inicio</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <!-- Opciones para Administradores -->
                    <li><a href="<?php echo BASE_URL; ?>/views/admin/dashboard.php">Dashboard</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/admin/asignaciones.php">Asignaciones</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/admin/aulas.php">Aulas</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/admin/carreras.php">Carreras</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/admin/estudiantes.php">Estudiantes</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/admin/facultades.php">Facultades</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/admin/horarios.php">Horarios</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/admin/inscripciones.php">Inscripciones</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/admin/materias.php">Materias</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/admin/popularidad_materias.php">Popularidad de Materias</a></li>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'student'): ?>
                    <!-- Opciones para Estudiantes -->
                    <li><a href="<?php echo BASE_URL; ?>/views/student/preinscripcion.php">Inscripción de Materias</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/student/mis_inscripciones.php">Mis Inscripciones</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/student/solicitar_materia.php">Solicitar Materia</a></li>
                <?php else: ?>
                    <!-- Opciones para Visitantes -->
                    <li><a href="<?php echo BASE_URL; ?>/login.php">Iniciar Sesión</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/student/register.php">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <!-- Contenido principal -->
        <main>
