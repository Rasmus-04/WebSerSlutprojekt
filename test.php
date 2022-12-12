<?php
require_once("databasConnection.php");

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

function validateAuthCode($email, $expiryTime, $auth){
    if(time() > $expiryTime){
        return false;
    }
    $last = "";
    if(isset(emailExist($email)[0])){
        $last = getDatabaseData("pasword", "slutprojekt_user", "email = '$email'")[0]["pasword"];
    }
    $saltBefore = "2#4nL8";
    $saltAfter = "!59Gf7";
    $saltMiddle = "8G$3x9";
    $authCode = prepPassword(sha1("$saltBefore$email$saltMiddle$expiryTime$saltAfter$last"));
    if($authCode != $auth){
        return false;
    }
    return true;
}

function generatePaswResetLink($mail){
    $last = getDatabaseData("pasword", "slutprojekt_user", "email = '$mail'")[0]["pasword"];
    $expiryTime = time()+3600;
    $saltBefore = "2#4nL8";
    $saltAfter = "!59Gf7";
    $saltMiddle = "8G$3x9";
    $authCode = prepPassword(sha1("$saltBefore$mail$saltMiddle$expiryTime$saltAfter$last"));
    $link = "mail=$mail&expire=$expiryTime&auth=$authCode";
    return "youtube.com?$link";
}

echo generatePaswResetLink("r.serrestam@gmail.com");
echo "<br>";
echo validateAuthCode("r.serrestam@gmail.com", "1670889823", "8d998f7c6be378c140d072ddfd26aa4390a6120d");
?>