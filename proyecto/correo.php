<?php
session_start();
include 'conexion.php'; // Asegúrate de que este archivo contenga la conexión a la base de datos

// Crear la conexión PDO
$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Verificar si se ha enviado el formulario
if (isset($_POST['id_jugador'])) {
    $id_jugador = $_POST['id_jugador'];

    // Obtener el correo electrónico del jugador
    $stmt = $pdo->prepare("SELECT email FROM jugador WHERE id_jugador = :id_jugador");
    $stmt->bindParam(':id_jugador', $id_jugador);
    $stmt->execute();
    $jugador = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($jugador) {
        // Obtener las jugadas del jugador
        $stmt_jugadas = $pdo->prepare("SELECT * FROM jugada WHERE id_jugador = :id_jugador ORDER BY hora DESC"); // Cambia el límite según tus necesidades
        $stmt_jugadas->bindParam(':id_jugador', $id_jugador);
        $stmt_jugadas->execute();
        $jugadas = $stmt_jugadas->fetchAll(PDO::FETCH_ASSOC);

        // Crear el cuerpo del correo con la tabla de jugadas
        $tablaJugadas = '<h2>Últimas Jugadas</h2>';
        $tablaJugadas .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
        $tablaJugadas .= '<thead><tr><th>Fecha</th><th>Apuesta</th><th>Lanzamiento</th><th>Saldo Inicial</th><th>Saldo Final</th></tr></thead>';
        $tablaJugadas .= '<tbody>';

        foreach ($jugadas as $jugada) {
            $tablaJugadas .= '<tr>';
            $tablaJugadas .= '<td>' . htmlspecialchars($jugada['hora']) . '</td>';
            $tablaJugadas .= '<td>' . htmlspecialchars($jugada['apuesta']) . ' €</td>';
            $tablaJugadas .= '<td>' . htmlspecialchars($jugada['lanzamiento']) . '</td>';
            $tablaJugadas .= '<td>' . htmlspecialchars($jugada['saldo_inicial']) . ' €</td>';
            $tablaJugadas .= '<td>' . htmlspecialchars($jugada['saldo_final']) . ' €</td>';
            $tablaJugadas .= '</tr>';
        }

        $tablaJugadas .= '</tbody>';
        $tablaJugadas .= '</table>';

        // Lógica para enviar el correo
        require "vendor/autoload.php";
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->Username = "pruebasalexander05@gmail.com"; // Tu usuario de Google
        $mail->Password = "wqri hsqb hlxv xyxj"; // Tu clave de Google
        $mail->SetFrom('pruebasalexander05@gmail.com', 'vuestro nombre');
        $mail->Subject = "Informe de baja del casino";

        // Agregar la tabla de jugadas al cuerpo del correo
        $mail->MsgHTML($tablaJugadas . '<br>Lamentamos que hayas decidido darte de baja.');

        $mail->AddAddress($jugador['email']); // Usar el correo del jugador

        // Enviar el correo
        if (!$mail->Send()) {
            echo "Error: " . $mail->ErrorInfo;
        } else {
            echo "Correo enviado correctamente.";
        }
    } else {
        echo "No se encontró el jugador.";
    }

    // Cerrar la sesión
    session_unset();
    session_destroy();
    header("Location: index.php"); // Redirigir a la página de inicio
    exit;
}
?>
