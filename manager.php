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
        case "kommentera":
            makeComment($_POST["userText"], $_POST["postId"], $_SESSION["activeUserId"]);
            break;
        case "changeDisplayname":
            changeDisplayName($_POST["displayName"]);
            reload("settings.php");
            break;
        case "changeEmail":
            changeEmail($_POST["email"]);
            reload("settings.php");
            break;
        case "changePasw":
            changePassword($_POST["oldPasw"], $_POST["password"], $_POST["confirm_password"]);
            reload("settings.php");
            break;
        case "resetPaswMail":
            resetPaswMail($_POST["email"]);
            break;
        case "resetPasw":
            break;
    }
}

if(isset($_GET["action"])){
    switch($_GET["action"]){
        case "logout":
            logout();
            break;
        case "deletePost":
            if(isset($_GET["reloadLink"])){
                deletePost($_GET["postId"], $_GET["reloadLink"]);
            }else{
                deletePost($_GET["postId"]);
            }
            break;
        case "deleteComment":
            if(isset($_GET["reloadLink"])){
                deleteComment($_GET["commentId"], $_SESSION["activeUserId"], $_GET["reloadLink"]);
            }else{
                deleteComment($_GET["commentId"], $_SESSION["activeUserId"]);
            }
            break;
        case "sendFriendRequest":
            sendFriendRequest($_SESSION["activeUserId"], $_GET["reciverId"]);
            if(isset($_GET["relodTo"])){
                reload($_GET["relodTo"]);
            }
            reload("index.php");
            break;
        case "cancelFriendRequest":
            cancelFriendRequest($_SESSION["activeUserId"], $_GET["reciverId"]);
            if(isset($_GET["relodTo"])){
                reload($_GET["relodTo"]);
            }
            reload("index.php");
            break;
        case "removeFriendRequest":
            removeFriendRequest($_SESSION["activeUserId"], $_GET["reciverId"]);
            if(isset($_GET["relodTo"])){
                reload($_GET["relodTo"]);
            }
            reload("index.php");
            break;
        case "denyFriendRequest":
            denyFriendRequest($_SESSION["activeUserId"], $_GET["reciverId"]);
            if(isset($_GET["relodTo"])){
                reload($_GET["relodTo"]);
            }
            reload("index.php");
            break;
        case "makeMod":
            makeMod($_GET["userId"]);
            break;
        case "removeMod":
            removeMod($_GET["userId"]);
            break;
        case "deActivate":
            deactivate($_GET["userId"]);
            break;
        case "activate":
            activate($_GET["userId"]);
            break;
    }
}
?>

<pre>
    <?php
    print_r(get_defined_vars());
    ?>
</pre>