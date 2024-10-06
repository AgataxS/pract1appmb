<?php
// include/auth.php


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!defined('BASE_URL')) {
    define('BASE_URL', '/practica1');
}

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));
}

function requireAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        // Redirigir al usuario a la página de inicio de sesión
        header('Location: ' . BASE_URL . '/login.php');
        exit();
    }
}


function requireStudent() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
        // Redirigir al usuario a la página de inicio de sesión
        header('Location: ' . BASE_URL . '/login.php');
        exit();
    }
}
?>
