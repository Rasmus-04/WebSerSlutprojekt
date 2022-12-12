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

function getDatabaseData($what, $from, $where="", $order="", $limit=""){
    global $pdo;
    if($where != ""){
        $where = "WHERE $where";
    }
    if($order != ""){
        $order = "ORDER BY $order";
    }
    if($limit != ""){
        $limit = "LIMIT $limit";
    }
    $sql = "SELECT $what FROM $from $where $order $limit";
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
        $illegalChars = array("'",'"', "<", "/", "*", "\\", "|", ">", " ", ",", ".", "=", "-");
    }else{
        $illegalChars = array("'",'"', "<", "*", "\\", "|", ">", "-");
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
    }elseif(validatePassword($pasw) == false){
        reload("registrera.php", "invalidPasw");
    }elseif(isset(userExist($username)[0])){
        reload("registrera.php", "userTaken");
    }elseif(validateUserName($displayname) == false){
        reload("registrera.php", "invalidDisplayname");
    }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        reload("registrera.php", "invalidEmail");
    }elseif(isset(emailExist($email)[0])){
        reload("registrera.php", "emailTaken");
    }else{
    $pasw = prepPassword($pasw);
    sendDatabaseData("slutprojekt_user", "username, pasword, displayName, email", "'$username', '$pasw', '$displayname', '$email'");
    reload("registrera.php", "accountCreated");
    }
}

function validateLogin($user, $pasw){
    $user = strtolower(trim($user));
    $data = getDatabaseData("username, pasword", "slutprojekt_user", "username = '$user' AND active = 1")[0];
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

function getusernameFromId($userId){
    return ucfirst(getDatabaseData("username", "slutprojekt_user", "id = '$userId'")[0]["username"]);
}

function isFriends($user1, $user2){
    $a = isset(getDatabaseData("*", "slutprojekt_friends", "reciverId = '$user1' AND user_id = '$user2'")[0]);
    $b = isset(getDatabaseData("*", "slutprojekt_friends", "reciverId = '$user2' AND user_id = '$user1'")[0]);
    if($a && $b){
        return true;
    }else{
        return false;
    }
}

function isActiveAccount($userId){
    if(getDatabaseData("active", "slutprojekt_user", "id = $userId")[0]["active"] == 1){
        return true;
    }else{
        return false;
    }
}

function generateAllHtmlPost(){
    $allPosts = getDatabaseData("*", "slutprojekt_post", "active = '1'", "id DESC");
    $content = "";    

    foreach($allPosts as $post){
        if(!isActiveAccount($post["user_id"])){
            continue;
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

        if($privacy == "Friends" && $post["user_id"] != $_SESSION["activeUserId"] && getUserLevel($_SESSION["activeUserId"]) != "2" && getUserLevel($_SESSION["activeUserId"]) != "1"){
            if(!isFriends($_SESSION["activeUserId"], $post["user_id"])){
                continue;
            }
        }elseif($privacy == "Private" && $post["user_id"] != $_SESSION["activeUserId"] && getUserLevel($_SESSION["activeUserId"]) != "2"){
            continue;
        }
        $content .= getPostHtml($post["id"], true);
    }
    return $content;
}

function getPostHtml($postId, $linkToPost=false, $reloadLink=""){
    $post = getDatabaseData("*", "slutprojekt_post", "id = '$postId' AND active = '1'", "id DESC");
    if(isset($post[0])){
        $post = $post[0];
    }else{
        reload("index.php");
    }

    if($reloadLink != ""){
        $reloadLink = "&reloadLink=".$reloadLink."";
    }

    if($_SESSION["activeUserId"] == $post["user_id"] or intval(getUserLevel($_SESSION["activeUserId"])) > 0){
        $b = "onclick=\"return confirm('Är du säker att du vill ta bort detta inlägget?')\"";
        $x = '<a href="manager.php?action=deletePost&postId='.$post["id"].''.$reloadLink.'" '.$b.'>Delete Post</a>';
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
    $userName = getUsernameFromId($post["user_id"]);
    $content .= '
    <section>
    <div class="postHead">
    <h4>'.$displayname.'</h4> <h5>'.$privacy.'</h5>
    </div>
    <p>@'.$userName.'</p>
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

function deletePost($postId, $reloadLink="index.php"){
    $postOwnerId = getDatabaseData("user_id", "slutprojekt_post", "id = '$postId' AND active = '1'")[0]["user_id"];

    if($postOwnerId == $_SESSION["activeUserId"] or intval(getUserLevel($_SESSION["activeUserId"])) > 0){
        updateDatabaseData("slutprojekt_post", "active = 0", "id='$postId'");
    }
    reload($reloadLink);
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
        '<section class="comment" id="'.$comment["id"].'">
        <div class="postHead">
        <h4>'.getDisplayNameFromId($comment["user_id"]).'</h4>'.$x.'
        </div>
        <p>@'.getusernameFromId($comment["user_id"]).'</p>
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

function deleteComment($commentId, $userId, $reloadLink="index.php"){
    $comment = getDatabaseData("*", "slutprojekt_comment", "id = '$commentId'")[0];
    $owner = getDatabaseData("user_id, id", "slutprojekt_post", "id = '{$comment["post_id"]}' AND active = '1'")[0];
    if($comment["user_id"] == $userId || intval(getUserLevel($userId)) > 0 || $owner["user_id"] == $userId){
        removeDatabaseData("slutprojekt_comment", "id = $commentId");
        if($reloadLink != "index.php"){
            reload($reloadLink);
        }else{
            reload("post.php?postId={$owner["id"]}");
        }
    }
    reload($reloadLink);
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

function friendRequestRecived($reciver, $sender){
    return isset(getDatabaseData("id", "slutprojekt_friends", "reciverId='$reciver' AND user_id='$sender'")[0]["id"]);
}

function friendRequestSent($sender, $reciver){
    return isset(getDatabaseData("id", "slutprojekt_friends", "reciverId='$reciver' AND user_id='$sender'")[0]["id"]);
}

function sendFriendRequest($sender, $reciver){
    sendDatabaseData("slutprojekt_friends", "reciverId, user_id", "'$reciver', '$sender'");
}

function cancelFriendRequest($sender, $reciver){
    removeDatabaseData("slutprojekt_friends", "reciverId = '$reciver' AND user_id = '$sender'");
}

function denyFriendRequest($sender, $reciver){
    removeDatabaseData("slutprojekt_friends", "reciverId = '$sender' AND user_id = '$reciver'");
}

function removeFriendRequest($sender, $reciver){
    removeDatabaseData("slutprojekt_friends", "reciverId = '$reciver' AND user_id = '$sender'");
    removeDatabaseData("slutprojekt_friends", "reciverId = '$sender' AND user_id = '$reciver'");
}

function generateFriendsSiteHtml($userId){
    $users = getDatabaseData("username, id", "slutprojekt_user", "active = 1");
    $allUsersContent = "";
    $myFriendsContent = "";
    $friendRequestContent = "";

    foreach($users as $user){
        if($userId == $user["id"]){
            continue;
        }elseif(isFriends($userId, $user["id"])){
            $myFriendsContent .= "<p>".$user["username"]."</p><a href='manager.php?action=removeFriendRequest&senderId=".$userId."&reciverId=".$user["id"]."&relodTo=friends.php'>Ta bort</a> <a href='userpage.php?userid=".$user["id"]."'>Profil</a><hr><hr><hr>";
        }elseif(friendRequestRecived($userId, $user["id"])){
            $friendRequestContent .= "<p>".$user["username"]."</p><a href='manager.php?action=sendFriendRequest&senderId=".$userId."&reciverId=".$user["id"]."&relodTo=friends.php'>Acceptera</a> <a href='manager.php?action=denyFriendRequest&senderId=".$userId."&reciverId=".$user["id"]."&relodTo=friends.php'>Neka</a><hr><hr><hr>";
            $allUsersContent .= "<p>".$user["username"]."</p><a href='manager.php?action=sendFriendRequest&senderId=".$userId."&reciverId=".$user["id"]."&relodTo=friends.php'>Acceptera</a> <a href='userpage.php?userid=".$user["id"]."'>Profil</a><hr><hr><hr>";
        }elseif(friendRequestSent($userId, $user["id"])){
            $allUsersContent .= "<p>".$user["username"]."</p><a href='manager.php?action=cancelFriendRequest&senderId=".$userId."&reciverId=".$user["id"]."&relodTo=friends.php'>Avbryt förfrågan</a> <a href='userpage.php?userid=".$user["id"]."'>Profil</a><hr><hr><hr>";
            $friendRequestContent .= "<p>".$user["username"]."</p><a href='manager.php?action=cancelFriendRequest&senderId=".$userId."&reciverId=".$user["id"]."&relodTo=friends.php'>Avbryt förfrågan</a> <a href='userpage.php?userid=".$user["id"]."'>Profil</a><hr><hr><hr>";
        }else{
            $allUsersContent .= "<p>".$user["username"]."</p><a href='manager.php?action=sendFriendRequest&senderId=".$userId."&reciverId=".$user["id"]."&relodTo=friends.php'>Add friend</a> <a href='userpage.php?userid=".$user["id"]."'>Profil</a><hr><hr><hr>";
        }
    }

    $content = array($allUsersContent, $myFriendsContent, $friendRequestContent);
    return $content;
}

function generateUserPageHtml($pageId){
    $user = getDatabaseData("*", "slutprojekt_user", "id='$pageId'");

    if(!isset($user[0])){
        reload("index.php");
    }
    $user = $user[0];
    $content = "";

    if($_SESSION["activeUserId"] == $pageId){
        $content .= '<h2 class="left">'.$user["displayName"].'</h2> <a href="settings.php"><h2 class="right">inställningar</h2></a>';
    }elseif(isFriends($pageId, $_SESSION["activeUserId"])){
        $content .= '<h2 class="left">'.$user["displayName"].'</h2> <a href="manager.php?action=removeFriendRequest&amp;senderId='.$_SESSION["activeUserId"].'&reciverId='.$pageId.'&relodTo=userpage.php?userid='.$pageId.'"><h2 class="right">Ta bort vän</h2></a>';
    }elseif(friendRequestRecived($_SESSION["activeUserId"], $pageId)){
        $content .= '<h2 class="left">'.$user["displayName"].'</h2> <h2 class="right"><a href="manager.php?action=sendFriendRequest&senderId='.$_SESSION["activeUserId"].'&reciverId='.$pageId.'&relodTo=userpage.php?userid='.$pageId.'">Acceptera</a><a href="manager.php?action=denyFriendRequest&senderId='.$_SESSION["activeUserId"].'&reciverId='.$pageId.'&relodTo=userpage.php?userid='.$pageId.'" style="margin-left:1.5rem;">Neka</a></h2>';
    }elseif(friendRequestSent($_SESSION["activeUserId"], $pageId)){
        $content .= '<h2 class="left">'.$user["displayName"].'</h2> <a href="manager.php?action=cancelFriendRequest&senderId='.$_SESSION["activeUserId"].'&reciverId='.$pageId.'&relodTo=userpage.php?userid='.$pageId.'"><h2 class="right">Avbryt förfrågan</h2></a>';
    }else{
        $content .= '<h2 class="left">'.$user["displayName"].'</h2> <a href="manager.php?action=sendFriendRequest&senderId='.$_SESSION["activeUserId"].'&reciverId='.$pageId.'&relodTo=userpage.php?userid='.$pageId.'"><h2 class="right">Lägg till vän</h2></a>';
    }

    $content .= '<h5>@'.getusernameFromId($pageId).'</h5> <h5 style="text-align: right;">Senast aktiv: '.$user["lastSeen"].'</h5>';

    return $content;
}

function loadAllFriendsPosts(){
    $allPosts = getDatabaseData("*", "slutprojekt_post", "active = '1'", "id DESC");
    $content = "";   

    foreach($allPosts as $post){
        if(!isFriends($_SESSION["activeUserId"], $post["user_id"])){
                continue;
            }
        $content .= getPostHtml($post["id"], true);
    }
    return $content;

}

function specialRequest($userId){
    global $pdo;
    $sql = "SELECT id, text, created, privacy, 'post' AS Type FROM slutprojekt_post WHERE user_id = $userId AND active = 1 UNION SELECT id, text, created, post_id, 'comment' AS Type FROM slutprojekt_comment WHERE user_id = $userId ORDER BY created DESC;";
    $stm = $pdo->prepare($sql);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}


function getCommentHtml($comment, $userId){
    $postId = $comment["privacy"];
    $x = "";
    $postData = getDatabaseData("privacy, user_id", "slutprojekt_post", "id = $postId")[0];
    $content = "";

    if($postData["privacy"] == 0 or $postData["user_id"] == $_SESSION["activeUserId"] or getUserLevel($_SESSION["activeUserId"]) > 1){
       
    }elseif($postData["privacy"] == 1 AND (isFriends($postData["user_id"], $_SESSION["activeUserId"]) or getUserLevel($_SESSION["activeUserId"]) > 0)){
        
    }else{
        return;
    }

    if($postData["user_id"] == $_SESSION["activeUserId"] or getUserLevel($_SESSION["activeUserId"]) > 0 or $userId == $_SESSION["activeUserId"]){
        $x = '<a href="manager.php?action=deleteComment&commentId='.$comment["id"].'&reloadLink=userpage.php?userid='.$userId.'" onclick="return confirm(\'Är du säker att du vill ta bort denna kommentar?\')">Delete comment</a>';
    }

    $content.= 
    '<section>
    <div class="postHead">
    <h4>'.getDisplayNameFromId($userId).'</h4>'.$x.'
    </div>
    <p>@'.getusernameFromId($userId).'</p>
    <div class="postHead"><h5>Created: '.$comment["created"].'</h5></div>
    <p>'.$comment["text"].'</p>
    <a href="post.php?postId='.$postId.'#'.$comment["id"].'">Gå till kommentaren</a>
    </section>';

    return $content;
}

function loadAllUserPagePostsAndComments($userId){
    $allPostAndComments = specialRequest($userId);
    $content = "";
    foreach($allPostAndComments as $post){
        if($post["Type"] == "post"){
            if($userId == $_SESSION["activeUserId"] or getUserLevel($_SESSION["activeUserId"]) > 1 or $post["privacy"] == 0){
                $content .= getPostHtml($post["id"], true, "userpage.php?userid=".$userId."");
            }elseif($post["privacy"] == 1 and (isFriends($userId, $_SESSION["activeUserId"]) or getUserLevel($_SESSION["activeUserId"]) > 0)){
                $content .= getPostHtml($post["id"], true, "userpage.php?userid=".$userId."");
            }
        }elseif($post["Type"] == "comment"){
            $content .= getCommentHtml($post, $userId);
        }
        
    }
    return $content;
}

function changeDisplayName($displayname){
    if(validateUserName($displayname)){
        updateDatabaseData("slutprojekt_user", "displayName = '$displayname'", "id = '{$_SESSION['activeUserId']}'");
        reload("settings.php", "displayNameUpdate");
    }
    reload("settings.php", "invalidDisplayName");
}

function changeEmail($email){
    if(filter_var($email, FILTER_VALIDATE_EMAIL) && !isset(emailExist($email)[0])){
        updateDatabaseData("slutprojekt_user", "email = '$email'", "id = '{$_SESSION['activeUserId']}'");
        reload("settings.php", "emailUpdate");
    }
    reload("settings.php", "invalidEmail");
}

function changePassword($oldPasw, $newPasw, $confirmPasw){
    $oldPasw = prepPassword($oldPasw);
    if($newPasw == $confirmPasw){
        if(validatePassword($newPasw)){
            $currentPasw = getDatabaseData("pasword", "slutprojekt_user", "id = '{$_SESSION['activeUserId']}'")[0]["pasword"];
            $newPasw = prepPassword($newPasw);
            if($currentPasw == $oldPasw){
                updateDatabaseData("slutprojekt_user", "pasword = '$newPasw'", "id = '{$_SESSION['activeUserId']}'");
                reload("settings.php", "paswUpdated");
            }reload("settings.php", "oldNotMatchCurrent");
        }reload("settings.php", "invalidPasw");
    }
}

function settingMsg($msg){
    switch($msg){
        case "displayNameUpdate":
            return "<p style='color:green;'>Ditt displayname har uppdaterats!</p>";
            break;
        case "invalidDisplayName":
            return "<p style='color:red;'>Ogiltilgt displayname!</p>";
            break;
        case "emailUpdate":
            return "<p style='color:green;'>Emailen har uppdateras!</p>";
            break;
        case "invalidEmail":
            return "<p style='color:red;'>Ogiltilg email eller så är mailen redan upptagen!</p>";
            break;
        case "invalidPasw":
            return "<p style='color:red;'>Ogiltligt lössenord!</p>";
            break;
        case "oldNotMatchCurrent":
            return "<p style='color:red;'>Gammla lösenordet matchar inte nuvarande lösenord!</p>";
            break;
        case "paswUpdated":
            return "<p style='color:green;'>Ditt lösenord har uppdaterats!</p>";
            break;
    }
}

function loadAllActiveUsers(){
    $users = getDatabaseData("username, id", "slutprojekt_user", "active = 1 AND level = 0");
    $content = "";
    foreach($users as $user){
    $content .= "<p>".$user["username"]."</p><a href='manager.php?action=makeMod&userId={$user['id']}'>Gör mod</a> <a href='manager.php?action=deActivate&userId={$user['id']}'>Avaktevera</a><hr><hr><hr>";
    }
    return $content;
}

function loadAllMods(){
    $users = getDatabaseData("username, id", "slutprojekt_user", "active = 1 AND level = 1");
    $content = "";
    foreach($users as $user){
    $content .= "<p>".$user["username"]."</p><a href='manager.php?action=removeMod&userId={$user['id']}'>Ta bort mod</a> <a href='userpage.php?userid=".$user["id"]."'>Profil</a><hr><hr><hr>";
    }
    return $content;
}

function loadAllInactiveAccounts(){
    $users = getDatabaseData("username, id", "slutprojekt_user", "active = 0");
    $content = "";
    foreach($users as $user){
    $content .= "<p>".$user["username"]."</p><a href='manager.php?action=activate&userId={$user['id']}'>Aktevera</a> <a href='userpage.php?userid=".$user["id"]."'>Profil</a><hr><hr><hr>";
    }
    return $content;
}

function makeMod($userId){
    if(getUserLevel($_SESSION["activeUserId"]) == 2){
        updateDatabaseData("slutprojekt_user", "level = 1", "id = $userId");
        reload("admin.php");
    }
    reload("admin.php", "fail");
}

function removeMod($userId){
    if(getUserLevel($_SESSION["activeUserId"]) == 2){
        updateDatabaseData("slutprojekt_user", "level = 0", "id = $userId");
        reload("admin.php");
    }
    reload("admin.php", "fail");
}

function deactivate($userId){
    if(getUserLevel($_SESSION["activeUserId"]) == 2){
        updateDatabaseData("slutprojekt_user", "active = 0", "id = $userId");
        reload("admin.php");
    }
    reload("admin.php", "fail");
}

function activate($userId){
    if(getUserLevel($_SESSION["activeUserId"]) == 2){
        updateDatabaseData("slutprojekt_user", "active = 1", "id = $userId");
        reload("admin.php");
    }
    reload("admin.php", "fail");
}
?>