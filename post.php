<?php
include("functions.php");
validateAccses();
if(!checkAccsesToPost($_SESSION["activeUserId"], $_GET["postId"])){
    reload("index.php");
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

<pre>
    <?php
    print_r(get_defined_vars());
    ?>
</pre>
</main>
</body>
</html>

