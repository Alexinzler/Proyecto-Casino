<?php
session_start();
include 'mode.php'; // Incluir la lógica de modo
include 'conexion.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Configura el modo de error de PDO para que lance excepciones
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

$error = ""; // Inicializar la variable de error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_input = htmlspecialchars($_POST['usuario']); // Cambia 'apodo' a 'usuario'
    $contrasena = $_POST['contrasena'];

    // Consulta para obtener el usuario usando apodo o email
    $stmt = $conn->prepare("SELECT * FROM jugador WHERE apodo = :usuario OR email = :usuario");
    $stmt->execute(['usuario' => $usuario_input]); // Usar el mismo valor para ambos parámetros
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Verificar la contraseña
        if (password_verify($contrasena, $usuario['contrasena'])) {
            // Iniciar sesión
            $_SESSION['id_jugador'] = $usuario['id_jugador']; // Almacenar ID de jugador en la sesión
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['apellidos'] = $usuario['apellidos'];
            $_SESSION['apodo'] = $usuario['apodo']; // Asegúrate de almacenar el apodo también
            $_SESSION['saldo'] = $usuario['saldo'];
            $_SESSION['rol'] = $usuario['rol']; // Guardar el rol del usuario en la sesión

            // Redirigir según el rol
            if ($usuario['rol'] === 'admin') {
                // Redirigir al panel de administración si es administrador
                header("Location: admin_panel.php");
            } else {
                // Redirigir al juego si es un usuario regular
                header("Location: game.php");
            }
            exit; // Asegúrate de salir después de redirigir
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}

$conn = null; // Cerrar la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - New York Casino</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $cssFile; ?>"> <!-- Usar el archivo CSS correcto -->
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4">
            <h2 class="text-center">Iniciar Sesión</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php elseif (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Apodo o Correo Electrónico</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required> <!-- Cambiar 'apodo' a 'usuario' -->
                </div>
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                </div>
                <button type="submit" class="btn btn-primary">Entrar al Casino</button>
                <button type="button" class="btn btn-secondary" onclick="location.href='index.php'">Salir</button>
            </form>
        </div>
    </div>
</body>
</html>
