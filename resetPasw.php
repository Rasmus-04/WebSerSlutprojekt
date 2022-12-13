<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slutprojekt</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css"> 
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
<main style="margin-top: 3rem;">
<form action="manager.php" method="POST">
<h1>Återställ lösenord</h1>
<?php
if(isset($_GET["mess"])){
    if($_GET["mess"] == "emailSent"){
        echo "<p style='color:green;'>Mailet har sickats, kolla skräppost om du inte ser mailet.";
    }
}
?>
<label for="email">Ange email för kontot</label>
<input type="email" name="email" placeholder="Email för kontot" id="email" required>
<input type="hidden" name="action" value="resetPaswMail">
<input type="submit" name="" value="Återställ lösenord">
</form>
<a href="login.php">Logga in!</a>
<br>
<a href="registrera.php">Skapa Konto!</a>
</main>
</body>
</html>