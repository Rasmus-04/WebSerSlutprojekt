<?php
use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/src/PHPMailer.php";
require_once "PHPMailer/src/SMTP.php";
require_once "PHPMailer/src/Exception.php";
$mail = new PHPMailer();

$password = '4X,sILWfV!_*';

$mail->isSMTP();
$mail->Host = 'mail.serrestam.online';
$mail->SMTPAuth = true;
$mail->Username = 'slutprojekt@serrestam.online';
$mail->Password = $password;
$mail->SMTPSecure = "ssl";
$mail->Port = '465';

$mail->setFrom('slutprojekt@serrestam.online');

$mail->isHTML(true);
?>
