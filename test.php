<?php

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
    $saltBefore = "2#4nL8";
    $saltAfter = "!59Gf7";
    $saltMiddle = "8G$3x9";
    $authCode = prepPassword(sha1("$saltBefore$email$saltMiddle$expiryTime$saltAfter"));
    if($authCode != $auth){
        return false;
    }
    return true;
}

function generatePaswResetLink($mail){
    $expiryTime = time()+3600;
    $saltBefore = "2#4nL8";
    $saltAfter = "!59Gf7";
    $saltMiddle = "8G$3x9";
    $authCode = prepPassword(sha1("$saltBefore$mail$saltMiddle$expiryTime$saltAfter"));
    $link = "mail=$mail&expire=$expiryTime&auth=$authCode";
    return "youtube.com?$link";
}

echo generatePaswResetLink("r.serrestam@gmail.com");
echo "<br>";
echo validateAuthCode("r.serrestam@gmail.com", "1670879229", "c08ca597af077bd90bd3baf6cf229a961cca2635");
?>