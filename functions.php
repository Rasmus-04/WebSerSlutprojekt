<?php
require_once("databasConnection.php");
session_start();
date_default_timezone_set("Europe/Stockholm");

function reload($path, $mess=""){
    # Redirectar dig till $path och om $mess har angets så sickar det i en getvariabel med index mess
    if($mess != ""){
        $mess = "?mess=$mess";
    }
    header("location: $path$mess");
    exit();
}

function sendDatabaseData($into, $index, $values){
    global $pdo;
    $sql = "INSERT INTO $into ($index) VALUES ($values);";
    $stm = $pdo->prepare($sql);
    $stm->execute();
}

function getDatabaseData($what, $from, $where="", $order=""){
    global $pdo;
    if($where != ""){
        $where = "WHERE $where";
    }
    if($order != ""){
        $order = "ORDER BY $order";
    }
    $sql = "SELECT $what FROM $from $where $order";
    $stm = $pdo->prepare($sql);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}

function updateDatabaseData($what, $set, $where){
    global $pdo;
    $sql = "UPDATE $what SET $set WHERE $where";
    $stm = $pdo->prepare($sql);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}

function removeDatabaseData($from, $where){
    global $pdo;
    $where = "WHERE $where";
    $sql = "DELETE FROM $from $where;";
    $stm = $pdo->prepare($sql);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}

function containsIllegalChars($input, $x=true){
    if($x){
        $illegalChars = array("'",'"', "<", "/", "*", "\\", "|", ">", " ", ",", ".", "=");
    }else{
        $illegalChars = array("'",'"', "<", "/", "*", "\\", "|", ">");
    }
    foreach ($illegalChars as $key) {
        if(str_contains($input, $key)){
            return true;
        }
    }
    return false;
}

function currentDateTime(){
    return date("Y-m-d H:i:s");
}

function validateUserName($username){
    if(containsIllegalChars($username) or strlen($username) > 20 or strlen($username) < 3){
        return false;
    }
    return $username;
}

function validatePassword($pasw){
    if(containsIllegalChars($pasw) or strlen($pasw) > 120 or strlen($pasw) < 3){
        return false;
    }
    return $pasw;
}

function userExist($username){
    return getDatabaseData("username", "slutprojekt_user", "username = '$username'");
}

function emailExist($email){
    return getDatabaseData("email", "slutprojekt_user", "email = '$email'");
}

function prepPassword($password){
    $saltBefore = "gI0&97";
    $saltAfter = "2!8dQ7";
    $password = $saltBefore.$password.$saltAfter;
    $password = sha1($password);
    return $password;
}

function createUser($username, $pasw, $displayname, $email){
    $username = trim(mb_strtolower($username));
    if (validateUserName($username) == false){
        reload("registrera.php", "invalidUsername");
    }else if(validatePassword($pasw) == false){
        reload("registrera.php", "invalidPasw");
    }else if(isset(userExist($username)[0])){
        reload("registrera.php", "userTaken");
    }else if(validateUserName($displayname) == false){
        reload("registrera.php", "invalidDisplayname");
    }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        reload("registrera.php", "invalidEmail");
    }else if(isset(emailExist($email)[0])){
        reload("registrera.php", "emailTaken");
    }else{
    $pasw = prepPassword($pasw);
    sendDatabaseData("slutprojekt_user", "username, pasword, displayName, email", "'$username', '$pasw', '$displayname', '$email'");
    reload("registrera.php", "accountCreated");
    }
}

function validateLogin($user, $pasw){
    $user = trim($user);
    $data = getDatabaseData("username, pasword", "slutprojekt_user", "username = '$user'")[0];
    if($user == $data["username"] && prepPassword($pasw) == $data["pasword"]){
        return true;
    }
    return false;
}

function login($user, $pasw){
    if(validateLogin($user, $pasw)){
        if(isset($_POST["keepLoggedIn"])){
            setcookie("activeUser", $user, time()+(3600*24));
            setcookie("valitadecode", prepPassword($user), time()+(3600*24));
        }

        $_SESSION["activeUser"] = $user;
        $_SESSION["activeUserId"] = getUserId($_SESSION["activeUser"]);
        $lastSeen = currentDateTime();
        updateDatabaseData("slutprojekt_user", "lastSeen = '$lastSeen'", "username = '{$_SESSION["activeUser"]}'");
        reload("index.php");
    }else{
        reload("login.php", "wrongCredentials");
    }
}

function validateAccses(){
    if(!isset($_SESSION["activeUser"])){
        reload("login.php");
    }
}

function logout(){
    if(isset($_COOKIE["activeUser"])){
        setcookie("activeUser", "", time()-(3600*24));
        setcookie("valitadecode", "", time()-(3600*24));
    }
    session_unset();
    session_destroy();
    reload("login.php");
}

function getUserId($user){
    return getDatabaseData("id", "slutprojekt_user", "username = '$user'")[0]["id"];
}

function makePost($text, $userId, $privacy){
    if(containsIllegalChars($text, false)){
        reload("index.php", "illigalChars");
    }

    switch($privacy){
        case "public":
            $x = 0;
            break;
        case "friends":
            $x = 1;
            break;
        case "private":
            $x = 2;
            break;
    }

    sendDatabaseData("slutprojekt_post", "text, user_id, privacy", "'$text', '$userId', '$x'");
    reload("index.php", "postCreated");
}

function getDisplayNameFromId($userId){
    return ucfirst(getDatabaseData("displayName", "slutprojekt_user", "id = '$userId'")[0]["displayName"]);
}

function isFriends($user1, $user2){
    $a = isset(getDatabaseData("*", "slutprojekt_friends", "friendId = '$user1' AND user_id = '$user2'")[0]);
    $b = isset(getDatabaseData("*", "slutprojekt_friends", "friendId = '$user2' AND user_id = '$user1'")[0]);
    if($a && $b){
        return true;
    }else{
        return false;
    }
}

function generateAllHtmlPost(){
    $allPosts = getDatabaseData("*", "slutprojekt_post", "active = '1'", "id DESC");
    $content = "";    

    foreach($allPosts as $post){
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

        if($privacy == "Friends" && $post["user_id"] != $_SESSION["activeUserId"] && getUserLevel($_SESSION["activeUserId"]) != "2" && getUserLevel($_SESSION["activeUserId"]) != "1"){
            if(!isFriends($_SESSION["activeUserId"], $post["user_id"])){
                continue;
            }
        }else if($privacy == "Private" && $post["user_id"] != $_SESSION["activeUserId"] && getUserLevel($_SESSION["activeUserId"]) != "2"){
            continue;
        }
        $content .= getPostHtml($post["id"], true);
    }
    return $content;
}

function getPostHtml($postId, $linkToPost=false){
    $post = getDatabaseData("*", "slutprojekt_post", "id = '$postId' AND active = '1'", "id DESC");
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

    if($linkToPost){
        $link = '<a href="post.php?postId='.$postId.'">Gå till inlägget</a>';
    }else{
        $link = "";
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

    $displayname = getDisplayNameFromId($post["user_id"]);
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
    '.$link.'
    </section>
    ';

    return $content;
}

function getUserLevel($userId){
    return getDatabaseData("level", "slutprojekt_user", "id = '$userId'")[0]["level"];
}

function deletePost($postId){
    $postOwnerId = getDatabaseData("user_id", "slutprojekt_post", "id = '$postId' AND active = '1'")[0]["user_id"];

    if($postOwnerId == $_SESSION["activeUserId"] or intval(getUserLevel($_SESSION["activeUserId"])) > 0){
        updateDatabaseData("slutprojekt_post", "active = 0", "id='$postId'");
    }
    reload("index.php");
}

function checkAccsesToPost($userId, $postId){
    $x = getDatabaseData("user_id, privacy", "slutprojekt_post", "id='$postId' AND active = '1'")[0];
    switch($x["privacy"]){
        case "0":
            return true;
            break;
        case "1":
            if($userId == $x["user_id"] or isFriends($userId, $x["user_id"]) or intval(getUserLevel($userId)) > 0){
                return true;
            }else{
                return false;
            }
            break;
        case "2":
            if($userId == $x["user_id"] or intval(getUserLevel($userId)) > 1){
                return true;
            }else{
                return false;
            }
            break;
    }

}

function makeComment($comment, $postId, $commmentUserId){
    if(!isset(getDatabaseData("id", "slutprojekt_post", "id='$postId' AND active = '1'")[0])){
        reload("index.php");
    }
    if(!checkAccsesToPost($commmentUserId, $postId)){
        reload("index.php?mess=jajaj");
    }

    sendDatabaseData("slutprojekt_comment", "text, user_id, post_id", "'$comment', '$commmentUserId', '$postId'");
    reload("post.php?postId=".$postId);
}

function loadAllCommentHtml($postId){
    $comments = getDatabaseData("*", "slutprojekt_comment", "post_id = '$postId'", "id DESC");
    $owner = getDatabaseData("user_id", "slutprojekt_post", "id = '$postId' AND active = '1'")[0];

    $content = "";
    foreach($comments as $comment){
        $x = "";

        if($comment["user_id"] == $_SESSION["activeUserId"] || intval(getUserLevel($_SESSION["activeUserId"])) > 0 || $owner["user_id"] == $_SESSION["activeUserId"]){
            $x = '<a href="manager.php?action=deleteComment&commentId='.$comment["id"].'" onclick="return confirm(\'Är du säker att du vill ta bort denna kommentar?\')">Delete comment</a>';
        }

        $content.= 
        '<section class="comment">
        <div class="postHead">
        <h4>'.getDisplayNameFromId($comment["user_id"]).'</h4>'.$x.'
        </div>
        <div class="postHead"><h5>Created: '.$comment["created"].'</h5></div>
        <p>'.$comment["text"].'</p>
    </section>';
    }
    if($content == ""){
        $content = '<section class="comment">
        <h4>Inga kommentarer ännu</h4>
        <p>Bli den första och kommentera</p>
        </section>';
    }
    return $content;
}

function deleteComment($commentId, $userId){
    $comment = getDatabaseData("*", "slutprojekt_comment", "id = '$commentId'")[0];
    $owner = getDatabaseData("user_id, id", "slutprojekt_post", "id = '{$comment["post_id"]}' AND active = '1'")[0];
    if($comment["user_id"] == $userId || intval(getUserLevel($userId)) > 0 || $owner["user_id"] == $userId){
        removeDatabaseData("slutprojekt_comment", "id = $commentId");
        reload("post.php?postId={$owner["id"]}");
    }
    reload("index.php");
}

function registerMsg(){
    if(isset($_GET["mess"])){
        switch($_GET["mess"]){
            case "userTaken":
                echo "<p style=color:red;>Användar namet är redan taget!</p>";
                break;
            case "accountCreated":
                echo "<p style=color:green;>Kontot har skapats!</p>";
                break;
            case "invalidUsername":
                echo "<p style=color:red;>Ogiltigt användarnamn!</p>";
                break;
            case "invalidPasw":
                echo "<p style=color:red;>Ogiltigt lösenord!</p>";
                break;
            case "invalidDisplayname":
                echo "<p style=color:red;>Ogiltigt displayname!</p>";
                break;
            case "invalidEmail":
                echo "<p style=color:red;>Ogiltig email!</p>";
                break;
            case "emailTaken":
                echo "<p style=color:red;>Emailen används redan!</p>";
                break;
        }
    }
}

function loginError(){
    if(isset($_GET["mess"])){
        switch($_GET["mess"]){
            case "wrongCredentials":
                echo "<p style=color:red;>Fel användarnamn eller lösenord</p>";
                break;
        }
    }
}

?>