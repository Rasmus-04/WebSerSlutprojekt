<?php
use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/src/PHPMailer.php";
require_once "PHPMailer/src/SMTP.php";
require_once "PHPMailer/src/Exception.php";
$mail = new PHPMailer();

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'slutprojekt.serrestam@gmail.com';
$mail->Password = 'virxqponzxnclvyj';
$mail->SMTPSecure = "tls";
$mail->Port = '587';

$mail->setFrom('slutprojekt.serrestam@gmail.com');

$mail->isHTML(true);
?>