<?php
session_start();
require_once 'includes/save_progress.php';

// Exemple d'utilisation
if(isset($_POST['solution'])) {
    // Si l'énigme est résolue
    if($_POST['solution'] === 'bonne_reponse') {
        // Sauvegarde du progrès
        saveProgress($_SESSION['team_id'], 1, 10);
        header('Location: enigme2.php');
        exit();
    }
}