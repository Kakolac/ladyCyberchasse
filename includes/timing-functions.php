<?php
/**
 * Fonctions centralisées pour la gestion du timing des indices
 * Inclure ce fichier dans les templates d'énigmes
 */

// Vérifier que les variables nécessaires sont définies
if (!isset($enigme_start_time) || !isset($indice_available)) {
    error_log('Variables de timing manquantes - inclure ce fichier après enigme_launcher.php');
    return;
}

// Fonction pour vérifier si l'indice a été consulté
function getIndiceConsulte($pdo, $equipe_id, $enigme_id) {
    if (!$equipe_id || !$enigme_id) {
        return false;
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM indices_consultes WHERE equipe_id = ? AND enigme_id = ?");
    $stmt->execute([$equipe_id, $enigme_id]);
    return $stmt->fetchColumn() > 0;
}

// Fonction pour calculer le temps restant
function getRemainingTime($enigme_start_time, $delai_indice_secondes) {
    $elapsed = time() - $enigme_start_time;
    return max(0, $delai_indice_secondes - $elapsed);
}

// Fonction pour formater le temps
function formatTime($seconds) {
    $minutes = floor($seconds / 60);
    $secs = $seconds % 60;
    return sprintf('%02d:%02d', $minutes, $secs);
}
?>
