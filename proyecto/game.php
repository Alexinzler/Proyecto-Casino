<?php
session_start();
include 'mode.php'; // Incluir la lógica del modo y los botones
include 'conexion.php';
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configura el modo de error de PDO
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

// Verificar si el jugador ha iniciado sesión
if (!isset($_SESSION['apodo'])) {
    header("Location: index.php"); // Redirigir a la página de inicio si no ha iniciado sesión
    exit;
}

// Inicializar variables de sesión si es la primera vez que se accede a la página
if (!isset($_SESSION['entry_time'])) {
    $_SESSION['entry_time'] = date('Y-m-d H:i:s');
    $_SESSION['initial_balance'] = $_SESSION['saldo'];
    $_SESSION['recargas'] = []; // Inicializar el historial de recargas
    $_SESSION['jugadas_count'] = 0; // Inicializar contador de jugadas
}

// Variables iniciales
$showDice = false;
$mensaje_recarga = '';
$mostrar_alerta = false; // Controla si se muestra la alerta después de 3 jugadas

// Comprobar si se ha enviado un formulario de recarga
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recargar_saldo'])) {
    $monto_recarga = (int)$_POST['monto_recarga'];

    // Validar el monto de recarga
    if ($monto_recarga >= 20 && $monto_recarga <= 100) {
        $_SESSION['saldo'] += $monto_recarga; // Actualizar saldo de sesión
        $apodo = $_SESSION['apodo']; // Usar el apodo del jugador

        // Actualizar el saldo en la base de datos
        $stmt = $conn->prepare("UPDATE jugador SET saldo = :saldo WHERE apodo = :apodo");
        $stmt->execute([':saldo' => $_SESSION['saldo'], ':apodo' => $apodo]);

        // Agregar la recarga al historial
        $_SESSION['recargas'][] = [
            'fecha' => date('Y-m-d H:i:s'),
            'monto' => $monto_recarga,
            'nuevo_saldo' => $_SESSION['saldo']
        ];

        $mensaje_recarga = "Saldo recargado con éxito. Nuevo saldo: €" . $_SESSION['saldo'];
    } else {
        $mensaje_recarga = "El monto de recarga debe estar entre 20 y 100 euros.";
    }
}

// Procesar la apuesta y juego de dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['recargar_saldo'])) {
    $apuesta = (int)$_POST['apuesta'];
    $dado1 = rand(1, 6);
    $dado2 = rand(1, 6);
    $suma = $dado1 + $dado2;
    $resultado = ($suma == 7 || $suma == 11) ? 'ganó' : 'perdió';

    // Actualizar el saldo según el resultado
    if ($resultado === 'ganó') {
        $_SESSION['saldo'] += $apuesta;
    } else {
        $_SESSION['saldo'] -= $apuesta;
    }

    // Obtener el id_jugador del jugador en sesión
    $apodo = $_SESSION['apodo'];
    $stmt_jugador = $conn->prepare("SELECT id_jugador FROM jugador WHERE apodo = :apodo");
    $stmt_jugador->bindParam(':apodo', $apodo);
    $stmt_jugador->execute();
    $jugador = $stmt_jugador->fetch(PDO::FETCH_ASSOC);

    // Actualizar el saldo en la base de datos
    $stmt = $conn->prepare("UPDATE jugador SET saldo = :saldo WHERE apodo = :apodo");
    $stmt->execute([':saldo' => $_SESSION['saldo'], ':apodo' => $apodo]);

    // Guardar la jugada en la base de datos
    $stmt_jugada = $conn->prepare("INSERT INTO jugada (id_jugador, lanzamiento, apuesta, saldo_inicial, saldo_final, hora) VALUES (:id_jugador, :lanzamiento, :apuesta, :saldo_inicial, :saldo_final, :hora)");
    $stmt_jugada->execute([
        ':id_jugador' => $jugador['id_jugador'], // Usar el id_jugador obtenido
        ':lanzamiento' => $suma,
        ':apuesta' => $apuesta,
        ':saldo_inicial' => $_SESSION['saldo'] - ($resultado === 'ganó' ? 0 : $apuesta),
        ':saldo_final' => $_SESSION['saldo'],
        ':hora' => date('Y-m-d H:i:s')
    ]);

    // Guardar la jugada en la sesión
    $_SESSION['games_played'][] = [
        'fecha' => date('Y-m-d H:i:s'),
        'apuesta' => $apuesta,
        'resultado' => $resultado,
        'saldo' => $_SESSION['saldo']
    ];

    // Incrementar el contador de jugadas
    $_SESSION['jugadas_count']++;

    // Mostrar la alerta después de 3 jugadas
    if ($_SESSION['jugadas_count'] % 3 == 0) {
        $mostrar_alerta = true;
    }

    // Si el saldo es 0 o negativo, redirigir al usuario
    if ($_SESSION['saldo'] <= 0) {
        header("Location: index.php");
        exit;
    }

    $showDice = true; // Mostrar los dados
}

// Obtener el apodo del usuario desde la sesión
$apodo = $_SESSION['apodo'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jugar a los Dados - Casino Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="<?php echo $cssFile; ?>"> <!-- Usar el archivo CSS correcto -->
    <style>
        .dice-container {
            display: flex;
            justify-content: center;
        }
        .dice {
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">New York Casino</span>
            <span class="navbar-text">Bienvenido, <?php echo htmlspecialchars($apodo); ?>!</span>
        </div>
    </nav>

    <!-- Mostrar el saldo actual -->
    <div class="container mt-3 text-center">
        <h5>Saldo Actual: €<?php echo $_SESSION['saldo']; ?></h5>
    </div>

    <div class="container mt-5">
        <h2 class="text-center">Jugar a los Dados</h2>

        <?php if ($_SESSION['saldo'] <= 0): ?>
            <!-- Mostrar mensaje si el saldo es insuficiente -->
            <div class="recarga">
                <h2>Tu saldo es insuficiente</h2>
                <p><?php echo $mensaje_recarga; ?></p>
                <form action="game.php" method="post">
                    <label for="monto_recarga">Monto de Recarga (€):</label>
                    <input type="number" id="monto_recarga" name="monto_recarga" min="20" max="100" required>
                    <button type="submit" name="recargar_saldo" class="btn btn-primary">Recargar Saldo</button>
                    <a href="index.php" class="btn btn-secondary">Salir</a> <!-- Botón para salir -->
                </form>
            </div>
        <?php else: ?>
            <?php if (!empty($mensaje_recarga)): ?>
                <div class="alert alert-info"><?php echo $mensaje_recarga; ?></div>
            <?php endif; ?>

            <?php if ($mostrar_alerta): ?>
                <div class="alert alert-warning text-center">
                    Recuerda que si no hay diversión, no hay juego.
                </div>
            <?php endif; ?>

            <!-- Formulario para realizar una apuesta -->
            <form method="post" id="dice-form">
                <div class="mb-3">
                    <label for="apuesta" class="form-label">Apuesta (€)</label>
                    <input type="number" class="form-control" id="apuesta" name="apuesta" min="1" max="<?= $_SESSION['saldo'] ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Lanzar Dados</button>
            </form>

            <?php if ($showDice): ?>
    <!-- Mostrar los resultados del lanzamiento de los dados -->
    <div class="dice-container mt-4 text-center" id="dice-container">
        <div class="dice" id="dice1">
            <img src="img/dados/dado<?= $dado1 ?>.png" alt="Dado 1" id="dado1-img">
        </div>
        <div class="dice" id="dice2">
            <img src="img/dados/dado<?= $dado2 ?>.png" alt="Dado 2" id="dado2-img">
        </div>
        <h3>Resultado: <?php echo $suma; ?> - <?php echo ucfirst($resultado); ?></h3>
    </div>
<?php endif; ?>

            <!-- Botones para ver el informe de uso o salir -->
            <div class="mt-4">
                <a href="report.php" class="btn btn-secondary">Ver Informe de Uso</a>
                <a href="logout.php" class="btn btn-danger">Salir</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Incluir el script para la animación de dados -->
    <script defer src="js/dice-animation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
