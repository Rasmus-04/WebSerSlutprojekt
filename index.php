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

<section>
    <div class="postHead">
    <h4>Dinmamma</h4> <h5>Public</h5>
    </div>
    <p>@test</p>
    <div class="postHead">
    <h5>Created: 2022-12-01 13:50:22</h5>
    
    </div>
    <p>dsadasda</p>
    <a href="post.php?postId=53">Gå till inlägget</a>
    </section>
<?php
echo generateAllHtmlPost();
?>
</main>
</body>
</html>

