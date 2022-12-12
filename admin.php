<?php
include("functions.php");
include("template.php");
validateAccses();
if(getUserLevel($_SESSION["activeUserId"]) < 2){
    reload("index.php");
  }
?>


<!DOCTYPE html>
<html lang="en">
<head>
<?php echo $head ?>
</head>
<body>
<?php echo $navbar; ?>
<main id="friendsPage">
    <a href="settings.php">Gå tillbaka</a>
<div class="wrapperFriends">
    <div class="item">
        <h2>Alla användare</h2>
        <div class="friendBlockWrapper">
            <?php echo loadAllActiveUsers() ?>
        </div>
    </div>
    <div class="item">
        <h2>Alla mods</h2>
        <div class="friendBlockWrapper">
        <?php echo loadAllMods() ?>
        </div>
    </div>
    <div class="item">
        <h2>Avakteverade konton</h2>
        <div class="friendBlockWrapper">
        <?php echo loadAllInactiveAccounts() ?>
        </div>
    </div>
</div>
</main>
</body>
</html>