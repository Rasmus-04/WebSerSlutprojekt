<?php
$head = <<<EOD
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Slutprojekt</title>

<link href="css/navbar.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css"> 

<link rel="stylesheet" href="css/main.css">
<script src="js/main.js"></script>  
EOD;

$navbar = <<<EOD
<header>
    <nav class="main-nav">
        <ul>
            <li><a href="index.php">Hem</a><div class="line"></div></div></li>
            <li><a href="friends.php">Vänner</a><div class="line"></div></div></li>
            <li><a href="userpage.php?userid={$_SESSION["activeUserId"]}">Profil</a><div class="line"></div></li>
            <li><a href="manager.php?action=logout">Logga ut</a><div class="line"></div></li>
        </ul>
        <button class="hamburger">
            <span class="bar"></span>
        </button>
    </nav>

        <nav class="mobile-nav">
            <a href="index.php">Hem</a>
            <a href="friends.php">Vänner</a>
            <a href="userpage.php?userid={$_SESSION["activeUserId"]}">Profil</a>
            <a href="manager.php?action=logout">Logga ut</a>
        </nav>
        <div id="navFiller"></div>
</header>
EOD;

?>