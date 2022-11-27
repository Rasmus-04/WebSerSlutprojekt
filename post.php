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
    $post = getDatabaseData("*", "post", "id = '{$_GET["postId"]}'", "id DESC");
    if(isset($post[0])){
        $post = $post[0];
    }else{
        reload("index.php");
    }
    if($_SESSION["activeUserId"] == $post["user_id"] or intval(getUserLevel($_SESSION["activeUserId"])) > 0){
        $b = "onclick=\"return confirm('Är du säker att du vill ta bort detta inlägget?')\"";
        $x = '<a href="manager.php?action=deletePost&postId='.$post["id"].'" '.$b.'>Delete Post</a>';
    }else{
        $x = "";
    }

    switch($post["privacy"]){
        case "0":
            $privacy = "Public";
            break;
        case "1":
            $privacy = "Friends";
            break;
        case "2":
            $privacy = "Private";
            break;
    }

    $content = "";

    $displayname = getNameFromId($post["user_id"]);
    $content .= '
    <section>
    <div class="postHead">
    <h4>'.$displayname.'</h4> <h5>'.$privacy.'</h5>
    </div>
    <div class="postHead">
    <h5>Created: '.$post["created"].'</h5>
    '.$x.'
    </div>
    <p>'.$post["text"].'</p>
    </section>
    ';

    echo $content;
?>


<form action="manager.php" method="POST">
<h2>Kommentera något!</h2>
<textarea name="userText" rows="4" cols="50" required maxlength="200"></textarea>
<input type="hidden"name="action" value="kommentera">
<input type="submit" value="Kommentera">
</form>
<div class="comments">


</div>

<pre>
    <?php
    print_r(get_defined_vars());
    ?>
</pre>
</main>
</body>
</html>

