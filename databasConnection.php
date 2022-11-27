<?php
# Variabler
$host = "localhost";      // Den server där databasen ligger
$user = "root";
$pwd  = "root";
$db   = "slutprojekt";           // Databasen vi vill jobba mot

# dsn - data source name
$dsn = "mysql:host=$host;dbname=$db";

# Inställningar som körs när objektet skapas
$options  = array(
  PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
  PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_EMULATE_PREPARES, false);

# Skapa objektet eller kasta ett fel
try {
  $pdo = new PDO($dsn, $user, $pwd, $options);
}
catch(Exception $e) {
    die('Could not connect to the database:<br/>'.$e);
}
?>