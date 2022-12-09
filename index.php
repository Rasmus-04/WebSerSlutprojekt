<?php
include("functions.php");
validateAccses();
include("template.php");
if(isset($_GET["limit"])){
  $limit = $_GET["limit"];
}else{
  $limit = 5;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $head ?>
</head>
<body>

<?php echo $navbar; ?>

<main>
<form action="manager.php" method="POST">
<h2>Skriv något!</h2>
<textarea name="userText" rows="4" cols="50" required maxlength="200"></textarea>
<input type="hidden"name="action" value="makePost">
<label for="privicy">Vilka får se</label>
<select name="privicy" id="privicy">
  <option value="public">Public</option>
  <option value="friends">Friends</option>
  <option value="private">Private</option>
</select>
<input type="submit" value="Sicka">
</form>
<?php
echo generateAllHtmlPost($limit);
?>

<!--<a href="index.php?limit=<?php echo $limit+5 ?>" id="loadMore">Visa Fler</a>-->
<div style="height: 5rem;"></div>
</main>
</body>
</html>

