<?php
$servername = "localhost";
$database_name="dates";
$username = "pharmacy";
$password = "pharmacy";

try{
// Create connection
$conn = new PDO("mysql:host=$servername;dbname=$database_name",$username,$password);
//set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

echo "connected successfully";
}catch(PDOException $e)
{
	echo "connection failed ". $e->getMessage();
}
?>