<?php
include("functions.php");
if(isset($_COOKIE["activeUser"]) && isset($_COOKIE["valitadecode"])){
    if($_COOKIE["valitadecode"] == prepPassword($_COOKIE["activeUser"])){
        $_SESSION["activeUser"] = $_COOKIE["activeUser"];
        $_SESSION["activeUserId"] = getUserId($_SESSION["activeUser"]);
        $lastSeen = currentDateTime();
        updateDatabaseData("user", "lastSeen = '$lastSeen'", "username = '{$_SESSION["activeUser"]}'");
        reload("index.php");
}else if(isset($_SESSION["activeUser"])){
    reload("index.php");
}
}

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
<form action="manager.php" method="post" id="logIn">
            <h2>Logga in</h2>
            <?php loginError() ?>
            <input type="text" placeholder="Användarnamn" name="user" required>
            <br>
            <input type="password" placeholder="Lösenord" name="password" required>
            <br>
            <a href="#">Glömt Lösenordet?</a>
            <label class="form-checkbox">
                <input type="checkbox" name="keepLoggedIn"> Håll mig inloggad (Använder cookies!)</label>
            <input type="submit" name="action" value="login">
</form>
<a href="registrera.php">Inget konto? Skapa ett!</a>
<br>
<a href="csource.php">CSource</a>
</main>
</body>
</html>