<?php 
$host = "localhost";
$dbname = "cyber_audit_tracker";
$username = "root"; 
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { 
    die ("Database connection failed: " . $e->getMessage());
}
?>