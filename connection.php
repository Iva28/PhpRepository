<?php

$host = 'localhost'; 
$dbname = 'testdb'; 
$user = 'root';
$password = '';

/* $conn = new PDO("mysql:host=$host", $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$dbExists = $conn->query("SHOW DATABASES LIKE '$dbname'")->rowCount() > 0;
if (!$dbExists) {
    $sql = "CREATE DATABASE $dbname";
    $conn->exec($sql);
}
$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT, 
    login VARCHAR(25) NOT NULL UNIQUE,
    password VARCHAR(25) NOT NULL,
    email VARCHAR(50))";
$conn->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS books (
    id INT PRIMARY KEY AUTO_INCREMENT, 
    title VARCHAR(25) NOT NULL,
    author VARCHAR(25) NOT NULL,
    year INT)";
$conn->exec($sql); */

?>

