<?php
include 'mode.php'; // Incluir la lógica del modo y los botones
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - New York Casino</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="css/inicio.css">
    <link rel="stylesheet" href="<?php echo $cssFile; ?>"> <!-- Usar el archivo CSS correcto -->
</head>
<body>
    <div class="container d-flex flex-column justify-content-center align-items-center animate__animated animate__fadeIn" style="height: 100vh;">
        <div class="card p-4 animate__animated animate__zoomIn">
            <div class="text-center">
                <img src="img/logo/logo.png.jpg" alt="Starlight Fortuna Casino" class="logo animate__animated animate__bounceIn">
            </div>
            <h1 class="text-center animate__animated animate__fadeInDown">Bienvenido a New York Casino</h1>
            <p class="text-center animate__animated animate__fadeInUp">¡El mejor lugar para probar tu suerte!</p>
            <div class="text-center">
                <a href="login.php" class="btn btn-secondary mt-2">Iniciar Sesión</a>
                <a href="register.php" class="btn btn-success mt-2">Registrarse</a>
            </div>
        </div>
        <!-- Pie de Página -->
        <div class="footer animate__animated animate__fadeInUp">
            <p>&copy; <?php echo date("Y"); ?> New York Casino. Todos los derechos reservados.</p>
            <p>Desarrollado por Alexander Valdivia</p>
        </div>
    </div>
</body>
</html>
