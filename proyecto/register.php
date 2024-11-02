<?php
session_start();

include 'mode.php'; // Incluir la lógica del modo y los botones
include 'conexion.php';
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Configura el modo de error de PDO para que lance excepciones
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = htmlspecialchars($_POST['nombre']);
    $apellidos = htmlspecialchars($_POST['apellidos']);
    $dni = htmlspecialchars($_POST['documento_identidad']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $apodo = htmlspecialchars($_POST['apodo']);
    $sexo = htmlspecialchars($_POST['sexo']);
    $saldo = (float)$_POST['saldo'];
    $email = htmlspecialchars($_POST['email']); // Agregar el campo de email
    $contrasena = htmlspecialchars($_POST['contrasena']);
    
    // Validación de la edad
    $fecha_nacimiento_date = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nacimiento_date)->y;

    if ($edad < 18) {
        echo "<script>alert('Debes ser mayor de 18 años para jugar.'); window.location.href = 'register.php';</script>";
        exit;
    }

    // Hash de la contraseña
    $contrasena_hashed = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar en la base de datos usando PDO
    $sql = "INSERT INTO jugador (nombre, apellidos, dni, edad, apodo, sexo, saldo, email, contrasena, fecha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; // Agregar email a la consulta SQL
    
    try {
        $stmt = $conn->prepare($sql);
        $fecha_actual = date('Y-m-d'); // Fecha actual
        $stmt->execute([$nombre, $apellidos, $dni, $edad, $apodo, $sexo, $saldo, $email, $contrasena_hashed, $fecha_actual]); // Agregar email a los parámetros

        // Guardar en la sesión
        $_SESSION['nombre'] = $nombre;
        $_SESSION['apodo'] = $apodo;
        $_SESSION['saldo'] = $saldo;

        header("Location: game.php"); // Redirigir a la página del juego
        exit;

    } catch (PDOException $e) {
        echo "Error al registrar: " . $e->getMessage(); // Mensaje de error si falla la inserción
    }
}
$conn = null; // Cerrar la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - New York Casino</title>
</head>
<body>
    <div style="margin: 0; height: 100vh; display: flex; justify-content: center; align-items: center;">
        <div style="padding: 1rem; border-radius: 0.5rem; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px;">
            <h2 style="text-align: center; font-size: 1.5rem; margin-bottom: 1rem;">Registro en el Casino</h2>
            <form method="post">
                <div style="margin-bottom: 1rem;">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required style="width: 100%; padding: 0.5rem; font-size: 0.9rem;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" required style="width: 100%; padding: 0.5rem; font-size: 0.9rem;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label for="documento_identidad">DNI</label>
                    <input type="text" id="documento_identidad" name="documento_identidad" required style="width: 100%; padding: 0.5rem; font-size: 0.9rem;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required style="width: 100%; padding: 0.5rem; font-size: 0.9rem;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label for="apodo">Apodo</label>
                    <input type="text" id="apodo" name="apodo" required style="width: 100%; padding: 0.5rem; font-size: 0.9rem;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label for="sexo">Sexo</label>
                    <select id="sexo" name="sexo" required style="width: 100%; padding: 0.5rem; font-size: 0.9rem;">
                        <option value="masculino">Masculino</option>
                        <option value="femenino">Femenino</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required style="width: 100%; padding: 0.5rem; font-size: 0.9rem;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label for="saldo">Saldo Inicial (€)</label>
                    <input type="number" id="saldo" name="saldo" min="20" max="100" step="0.01" required style="width: 100%; padding: 0.5rem; font-size: 0.9rem;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" required style="width: 100%; padding: 0.5rem; font-size: 0.9rem;">
                </div>
                <button type="submit" style="width: 100%; padding: 0.5rem; background-color: #007bff; border: none; color: white; font-size: 0.9rem;">Registrar</button>
                <button type="button" style="width: 100%; padding: 0.5rem; background-color: #6c757d; border: none; color: white; font-size: 0.9rem; margin-top: 0.5rem;" onclick="location.href='index.php'">Salir</button>
            </form>
        </div>
    </div>
</body>
</html>
