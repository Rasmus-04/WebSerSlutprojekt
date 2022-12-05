<?php
include("functions.php");
validateAccses();
include("template.php");
$content = generateFriendsSiteHtml($_SESSION["activeUserId"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php echo $head ?>
</head>
<body>
<?php echo $navbar; ?>

<main id="friendsPage">
<div class="wrapperFriends">
    <div class="item">
        <h2>Alla användare</h2>
        <div class="friendBlockWrapper">
            <?php echo $content[0]; ?>
        </div>
    </div>
    <div class="item">
        <h2>Mina vänner</h2>
        <div class="friendBlockWrapper">
        <?php echo $content[1]; ?>
        </div>
    </div>
    <div class="item">
        <h2>Vänförfrågor</h2>
        <div class="friendBlockWrapper">
            <?php echo $content[2]; ?>
        </div>
    </div>
</div>

<h2 style="text-align:center;">Alla Post från vänner</h2>
<article>
<?php
echo loadAllFriendsPosts();
?>
</article>

</main>
</body>
</html>