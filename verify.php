<?php
session_start();
require_once 'config/connexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $team_name = $_POST['team_name'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM teams WHERE team_name = ?");
        $stmt->execute([$team_name]);
        $team = $stmt->fetch();
        
        if ($team && $password === $team['password']) {
            $_SESSION['team_name'] = $team_name;
            $_SESSION['start_time'] = time();
            
            header('Location: scenario.php');
            exit();
        } else {
            header('Location: login.php?error=1');
            exit();
        }
    } catch(PDOException $e) {
        error_log("Erreur de base de données : " . $e->getMessage());
        header('Location: login.php?error=2');
        exit();
    }
}