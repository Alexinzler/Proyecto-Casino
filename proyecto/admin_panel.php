<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_jugador'])) {
    header("Location: index.php");
    exit;
}

$id_jugador = $_SESSION['id_jugador'];

// Obtener el rol del usuario desde la base de datos
$query = $conn->prepare("SELECT rol FROM jugador WHERE id_jugador = :id_jugador");
$query->execute(['id_jugador' => $id_jugador]);
$usuario = $query->fetch();

// Verificar si se obtuvo el rol correctamente
if (!$usuario || !isset($usuario['rol'])) {
    echo "Error: No se pudo obtener el rol del usuario.";
    exit;
}

// Redirigir a la página de usuario si no es administrador
if ($usuario['rol'] !== 'admin') {
    header("Location: usuario.php");
    exit;
}

// Operación de eliminación de usuario
if (isset($_GET['eliminar'])) {
    $id_eliminar = $_GET['eliminar'];

    // Prevenir eliminación del propio administrador
    if ($id_eliminar == $id_jugador) {
        echo "No puedes eliminar tu propia cuenta.";
    } else {
        $query = $conn->prepare("DELETE FROM jugador WHERE id_jugador = :id_jugador");
        $query->execute(['id_jugador' => $id_eliminar]);
        echo "Usuario eliminado exitosamente.";
    }
}

// Operación de modificación de usuario
if (isset($_POST['modificar'])) {
    $id_modificar = $_POST['id_jugador'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];  // 'usuario' o 'admin'

    // Asegurar que no se desasigne el rol 'admin' a sí mismo
    if ($id_modificar == $id_jugador && $rol !== 'admin') {
        echo "No puedes cambiar tu propio rol de administrador.";
    } else {
        $query = $conn->prepare("UPDATE jugador SET nombre = :nombre, apellidos = :apellidos, email = :email, rol = :rol WHERE id_jugador = :id_jugador");
        $query->execute([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => $email,
            'rol' => $rol,
            'id_jugador' => $id_modificar
        ]);
        echo "Usuario modificado exitosamente.";
    }
}

// Listar todos los usuarios
$query = $conn->prepare("SELECT id_jugador, nombre, apellidos, email, rol FROM jugador");
$query->execute();
$usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center">Panel de Administración</h1>

        <h2 class="mt-4">Lista de Usuarios</h2>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['id_jugador']) ?></td>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td><?= htmlspecialchars($usuario['apellidos']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($usuario['rol']) ?></td>
                    <td>
                        <a href="admin_panel.php?eliminar=<?= $usuario['id_jugador'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</a>
                        <form action="admin_panel.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id_jugador" value="<?= $usuario['id_jugador'] ?>">
                            <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required class="form-control form-control-sm">
                            <input type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required class="form-control form-control-sm">
                            <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required class="form-control form-control-sm">
                            <select name="rol" class="form-control form-control-sm">
                                <option value="usuario" <?= $usuario['rol'] == 'usuario' ? 'selected' : '' ?>>Usuario</option>
                                <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                            </select>
                            <button type="submit" name="modificar" class="btn btn-warning btn-sm">Modificar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="logout.php" class="btn btn-secondary">Cerrar sesión</a>
    </div>
</body>
</html>
