<?php
include("functions.php");
validateAccses();
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
<form action="manager.php" method="POST">
<h2>Skriv något!</h2>
<textarea name="userText" rows="4" cols="50" required maxlength="200"></textarea>
<input type="hidden"name="action" value="makePost">
<label for="privicy">Vilka får se</label>
<select name="privicy" id="privicy">
  <option value="public">Public</option>
  <option value="friends">Friends</option>
  <option value="private">private</option>
</select>
<input type="submit" value="Sicka">
</form>


<section style="display:none;">
    <div class="postHead">
    <h4>Rasmus Serrstam</h4> <h5>Public</h5>
    </div>
    <div class="postHead"><h5>Created: 2022-11-26</h5> <a href="#">Delete Post</a> </div>
    
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam quis ante ac lacus tempor aliquam non sed magna. Vestibulum maximus tristique sapien, sed molestie diam cursus dapibus. In malesuada sit amet diam vitae ornare. Donec imperdiet convallis lectus. Pellentesque efficitur dignissim sem eu luctus.</p>
    <a href="post.php?postId=16">Gå till inlägget</a>
</section>

<?php 
echo generateAllHtmlPost();
?>

<a href="manager.php?action=logout">Logga ut</a>
<pre>
    <?php
    print_r(get_defined_vars());
    ?>
</pre>
</main>
</body>
</html>

