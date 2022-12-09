<?php
include("functions.php");
validateAccses();
if(!checkAccsesToPost($_SESSION["activeUserId"], $_GET["postId"])){
    reload("index.php");
}

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
<a href="index.php">Gå tillbaka</a>
<?php
    echo getPostHtml($_GET["postId"]);
?>

<form action="manager.php" method="POST">
<h2>Kommentera något!</h2>
<textarea name="userText" rows="4" cols="50" required maxlength="200"></textarea>
<input type="hidden"name="action" value="kommentera">
<input type="hidden"name="postId" value="<?php echo $_GET["postId"] ?>">
<input type="submit" value="Kommentera">
</form>

<div class="comments">
    <?php echo loadAllCommentHtml($_GET["postId"]); ?>
</div>

</main>
</body>
</html>

