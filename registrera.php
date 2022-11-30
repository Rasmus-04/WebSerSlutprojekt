<?php
include("functions.php");
?>

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

<main>
<form action="manager.php" method="post">
            <h2>Regristrera konto</h2>
            <?php registerMsg() ?>
            <input type="text" placeholder="Användarnamn" name="user" maxlength="20" minlength="3" required>
            <br>
            <input type="text" placeholder="Display Name" name="displayName" maxlength="20" minlength="3" required>
            <br>
            <input type="text" placeholder="Email" name="email" maxlength="40" minlength="3" required>
            <br>
            <input type="password" placeholder="Lösenord" name="password" maxlength="120" minlength="5" id="password" required>
            <br>
            <input type="password" placeholder="Upprepa lösenord" name="repetedpassword" id="confirm_password" required>
            <input type="submit" name="action" value="regrestrera">
            <br>
            <a href="login.php">Har du redan ett konto? Logga in!</a>
</form>
</main>
<script src="js/main.js"></script>
</body>
</html>