<?php

$host = "sql112.infinityfree.com";
$port = 3306;
$username = "if0_39084952";
$password = "NbiPSsUZzG";
$database = "if0_39084952_webdev";

$db = new PDO("mysql:host=$host;port=$port",
               $username,
               $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("use `$database`");

?>
