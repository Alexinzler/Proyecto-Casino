<?php
// Iniciar la sesión
session_start();
include 'conexion.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['apodo'])) {
    header("Location: home.php");
    exit;
}

$apodo = $_SESSION['apodo'];

try {
    // Obtener los datos del jugador
    $stmt = $conn->prepare("SELECT * FROM jugador WHERE apodo = :apodo");
    $stmt->bindParam(':apodo', $apodo);
    $stmt->execute();
    $jugador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$jugador) {
        die("Jugador no encontrado.");
    }

    // Obtener las jugadas del jugador
    $stmt_jugadas = $conn->prepare("SELECT * FROM jugada WHERE id_jugador = :id_jugador");
    $stmt_jugadas->bindParam(':id_jugador', $jugador['id_jugador']);
    $stmt_jugadas->execute();
    $result_jugadas = $stmt_jugadas->fetchAll(PDO::FETCH_ASSOC);

    // Calcular el saldo inicial y actual
    $initialBalance = isset($_SESSION['initial_balance']) ? $_SESSION['initial_balance'] : $jugador['saldo'];
    $currentBalance = $jugador['saldo'];

} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Juego - Casino Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Informe de Juego</h2>
        <div class="card p-4">
            <h4>Datos del Jugador</h4>
            <ul class="list-unstyled">
                <li><strong>Nickname:</strong> <?= htmlspecialchars($jugador['apodo']) ?></li>
                <li><strong>Saldo Inicial:</strong> <?= htmlspecialchars($initialBalance) ?> €</li>
                <li><strong>Saldo Actual:</strong> <?= htmlspecialchars($currentBalance) ?> €</li>
            </ul>

            <h4 class="mt-4">Estadísticas de Juegos</h4>
            <?php if (count($result_jugadas) > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Apuesta</th>
                            <th>Lanzamiento</th>
                            <th>Saldo Inicial</th>
                            <th>Saldo Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result_jugadas as $jugada): ?>
                            <tr>
                                <td><?= htmlspecialchars($jugada['hora']) ?></td>
                                <td><?= htmlspecialchars($jugada['apuesta']) ?> €</td>
                                <td><?= htmlspecialchars($jugada['lanzamiento']) ?></td>
                                <td><?= htmlspecialchars($jugada['saldo_inicial']) ?> €</td>
                                <td><?= htmlspecialchars($jugada['saldo_final']) ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No has jugado aún.</p>
            <?php endif; ?>

            <div class="mt-4">
                <a href="game.php" class="btn btn-secondary">Volver al Juego</a>
            </div>
        </div>
    </div>
    <div class="mt-4">
    <form action="correo.php" method="POST">
        <input type="hidden" name="id_jugador" value="<?= htmlspecialchars($jugador['id_jugador']) ?>">
        <button type="submit" name="baja" class="btn btn-danger">Darse de Baja</button>
    </form>
</div>

</body>
</html>

<?php
// Cerrar la conexión
$conn = null; // PDO cierra la conexión automáticamente al finalizar el script
?>
