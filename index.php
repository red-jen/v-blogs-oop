<?php 

$dbname = "blogs_oop";
$dbuser = "root";
$dbpass = "Ren-ji24";
$dbhost = "localhost";

try {
    $pdo = new PDO("mysql:host=". $dbhost . ";dbname=". $dbname, $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "connected";
} catch(PDOException $err) {
    die("Connection failed: " . $err->getMessage());
}