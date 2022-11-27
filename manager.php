<?php
include("functions.php");


if(isset($_POST["action"])){
    switch($_POST["action"]){
        case "regrestrera":
            createUser($_POST["user"], $_POST["password"], $_POST["displayName"], $_POST["email"]);
            break;
        case "login":
            login($_POST["user"], $_POST["password"]);
            break;
        case "makePost":
            makePost($_POST["userText"], $_SESSION["activeUserId"], $_POST["privicy"]);
            break;
    }
}

if(isset($_GET["action"])){
    switch($_GET["action"]){
        case "logout":
            logout();
            break;
        case "deletePost":
            deletePost($_GET["postId"]);
            break;
    }
}
?>

<pre>
    <?php
    print_r(get_defined_vars());
    ?>
</pre>