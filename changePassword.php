<?php
include("functions.php");
$content = "";
if(isset($_GET["mail"]) && isset($_GET["expire"]) && isset($_GET["auth"])){
    if(validateAuthCode($_GET["mail"], $_GET["expire"], $_GET["auth"])){
        $content = '<form action="manager.php" method="POST">
        <h1>Ändra Lösenord</h1>
        <input type="password" name="pasw" id="" placeholder="Lösenord">
        <input type="password" name="repPasw" id="" placeholder="Uprepa lösenordet">
        <input type="hidden" name="action" value="resetPasw">

        <input type="hidden" name="email" value="'.$_GET["mail"].'">
        <input type="hidden" name="expire" value="'.$_GET["expire"].'">
        <input type="hidden" name="auth" value="'.$_GET["auth"].'">

        <input type="submit" name="" value="Updatera lösenord">
</form>';
    }else{
    $content = '<h1>Ogiltlig Länk</h1>
    <a href="login.php">Logga in!</a>
    <br>
    <a href="registrera.php">Skapa Konto!</a>';
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
<main style="margin-top:3rem;">


<?php echo $content ?>

<pre>
    <?php
    #print_r(get_defined_vars());
    ?>
</pre>
</main>
</body>
</html>