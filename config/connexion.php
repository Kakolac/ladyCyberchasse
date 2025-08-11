<?php
require_once 'env.php';

$host = env('DB_HOST', 'localhost');
$dbname = env('DB_NAME', 'cyberchasse');
$username = env('DB_USER', 'root');
$password = env('DB_PASS', 'root');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}