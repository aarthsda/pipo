<?php
require __DIR__ . '/../_PHPMAILER/src/Exception.php';
require __DIR__ . '/../_PHPMAILER/src/PHPMailer.php';
require __DIR__ . '/../_PHPMAILER/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Configurações do servidor SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Servidor SMTP
    $mail->SMTPAuth   = true;
    $mail->Username   = 'pipo.atendimento@gmail.com'; // Seu e-mail
    $mail->Password   = 'eqzd dxcc lzbh ssgi'; // Veja nota abaixo
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Criptografia TLS
    $mail->Port       = 587;
} catch (Exception $e) {
    echo $mail->ErrorInfo;
}

?>