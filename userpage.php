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
<div class="wrapper">
<?php
echo generateUserPageHtml($_GET["userid"]);
?>
</div>

<article>
    <h3>Alla posts och kommentarer</h3>
    <section>
    <div class="postHead">
    <h4>Admin</h4> <h5>Public</h5>
    </div>
    <div class="postHead">
    <h5>Created: 2022-11-29 10:28:33</h5>
    </div>
    <p>hej</p>
    <a href="post.php?postId=7">Gå till inlägget</a>
    </section>


    <section>
    <div class="postHead">
    <h4>Admin</h4> <h5>Public</h5>
    </div>
    <div class="postHead">
    <h5>Created: 2022-11-28 12:28:05</h5>
    
    </div>
    <p>hej</p>
    <a href="post.php?postId=2">Gå till inlägget</a>
    </section>
    <section>
        <div class="postHead">
        <h4>Test</h4><a href="manager.php?action=deleteComment&commentId=9" onclick="return confirm('Är du säker att du vill ta bort denna kommentar?')">Delete comment</a>
        </div>
        <div class="postHead"><h5>Created: 2022-12-01 10:49:19</h5></div>
        <p>njfew
</p>
<a href="post.php?postId=2">Gå till kommentaren</a>
    </section>
</article>
</main>
</body>
</html>