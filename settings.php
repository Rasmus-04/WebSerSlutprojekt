<?php
include("functions.php");
validateAccses();
include("template.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php echo $head ?>
</head>
<body>
<?php echo $navbar; ?>
<main>
  <?php
  if(getUserLevel($_SESSION["activeUserId"]) > 1){
    echo '<a href="admin.php"><h2>Admin Sida</h2></a>'; 
  }
  ?>
  <?php 
  if(isset($_GET["mess"])){
    echo settingMsg($_GET["mess"]);
  }
  ?>
<form action="manager.php" method="POST">
    <h2>Byt displayname</h2>
    <input type="text" name="displayName" id="" value="<?php echo getDisplayNameFromId($_SESSION["activeUserId"]) ?>">
    <input type="hidden" name="action" value="changeDisplayname">
    <input type="submit" name="" id="" value="Byt Displayname">
</form>
<form action="manager.php" method="POST">
    <h2>Byt Email</h2>
    <input type="text" name="email" id="" value="<?php echo getDatabaseData("email", "slutprojekt_user", "id='{$_SESSION['activeUserId']}'")[0]["email"] ?>">
    <input type="hidden" name="action" value="changeEmail">
    <input type="submit" name="" id="" value="Byt email">
</form>

<form action="manager.php" method="POST">
    <h2>Byt Lösenord</h2>
    <label for="currentPasw">Nuvarande lösenord</label>
    <input type="password" name="oldPasw" id="currentPasw">
    <label for="newPasw">Ange nytt lösenord</label>
    <input type="password" name="password" id="password">
    <label for="repNewPasw">Upprepa Lösenordet</label>
    <input type="password" name="confirm_password" id="confirm_password">
    <input type="hidden" name="action" value="changePasw">
    <input type="submit" name="" id="" value="Byt lösenord">
</form>
</main>
</body>
</html>