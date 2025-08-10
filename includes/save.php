<?php
require_once '../config/connexion.php';

// Fonction pour sauvegarder la progression
function saveProgress($team_id, $enigma_number, $score) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE progress SET current_enigma = ?, score = ? WHERE team_id = ?");
        return $stmt->execute([$enigma_number, $score, $team_id]);
    } catch(PDOException $e) {
        error_log("Erreur de sauvegarde : " . $e->getMessage());
        return false;
    }
}

// Fonction pour récupérer la progression
function getProgress($team_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM progress WHERE team_id = ?");
        $stmt->execute([$team_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Erreur de récupération : " . $e->getMessage());
        return false;
    }
}