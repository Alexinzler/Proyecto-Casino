<?php
// Verificar si la sesión ya está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si la cookie de modo no existe, establecer el modo diurno por defecto
if (!isset($_COOKIE['mode'])) {
    setcookie('mode', 'day', time() + (86400 * 30), "/"); // Cookie de 30 días
}

// Cambiar el modo si se selecciona un nuevo modo
if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
    setcookie('mode', $mode, time() + (86400 * 30), "/");
    header("Location: " . $_SERVER['PHP_SELF']); // Redirigir a la misma página
    exit;
}

// Establecer la ruta del archivo CSS según el modo
$mode = isset($_COOKIE['mode']) ? $_COOKIE['mode'] : 'day';
$cssFile = ($mode == 'night') ? 'css/night.css' : 'css/day.css'; // Ruta corregida
?>

<!-- HTML para los botones de alternancia de modo -->
<nav>
    <a href="?mode=day" class="btn btn-light">Modo Diurno</a>
    <a href="?mode=night" class="btn btn-dark">Modo Nocturno</a>
</nav>
