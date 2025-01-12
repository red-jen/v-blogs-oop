<?php 
$dbname = "blogs_oop";
$dbuser = "root";
$dbpass = "Ren-ji24";
$dbhost = "localhost";
try{
    $pdo = new PDO("mysql:host=". $dbhost . ";dbname=". $dbname ,$dbuser,$dbpass);

    echo "success";

}catch( PDOException $err ){
    echo "Database connection problem:" . $err->getMessage();
}
// 

?>




