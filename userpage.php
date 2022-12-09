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
<div class="wrapper">
<?php
echo generateUserPageHtml($_GET["userid"]);
?>

</div>

<article>
    <h3>Alla posts och kommentarer</h3>
    <?php
    echo loadAllUserPagePostsAndComments($_GET["userid"], $limit);
    ?>
</article>
<!--<a href="userpage.php?userid=<?php echo $_GET["userid"] ?>&limit=<?php echo $limit+5 ?>" id="loadMore">Visa Fler</a>-->
<div style="height: 5rem;"></div>
</main>
</body>
</html>