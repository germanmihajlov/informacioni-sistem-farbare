<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "farbara";

$db = mysqli_connect($host, $username, $password, $database);

if (!$db) {
    die("Greška pri povezivanju: " . mysqli_connect_error());
}
?>
