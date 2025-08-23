<?php
/**
 * API pour récupérer les timestamps d'énigme depuis la BDD
 * Utilisé par le JavaScript pour synchroniser les timers
 */

session_start();
require_once 'config/connexion.php';

// Vérifier l'authentification
if (!isset($_SESSION['team_name'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

// Récupérer les données POST
$data = json_decode(file_get_contents('php://input'), true);
$lieu_id = $data['lieu_id'] ?? null;
$equipe_id = $data['equipe_id'] ?? null;

if (!$lieu_id || !$equipe_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètres manquants']);
    exit;
}

try {
    // Récupérer les timestamps depuis la BDD
    $stmt = $pdo->prepare("
        SELECT 
            enigme_start_time,
            indice_start_time,
            statut,
            TIMESTAMPDIFF(SECOND, enigme_start_time, NOW()) as enigme_elapsed_seconds,
            TIMESTAMPDIFF(SECOND, indice_start_time, NOW()) as indice_elapsed_seconds
        FROM parcours 
        WHERE equipe_id = ? AND lieu_id = ?
    ");
    $stmt->execute([$equipe_id, $lieu_id]);
    $timing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($timing) {
        // Convertir en timestamps Unix
        $timing['enigme_start_timestamp'] = strtotime($timing['enigme_start_time']);
        $timing['indice_start_timestamp'] = strtotime($timing['indice_start_time']);
        
        // Calculer le temps restant pour l'indice
        $lieu_stmt = $pdo->prepare("SELECT delai_indice FROM lieux WHERE id = ?");
        $lieu_stmt->execute([$lieu_id]);
        $lieu = $lieu_stmt->fetch(PDO::FETCH_ASSOC);
        
        $delai_indice_secondes = ($lieu['delai_indice'] ?? 6) * 60;
        $enigme_elapsed = $timing['enigme_elapsed_seconds'];
        $timing['indice_available'] = ($enigme_elapsed >= $delai_indice_secondes);
        $timing['remaining_time'] = max(0, $delai_indice_secondes - $enigme_elapsed);
    }
    
    header('Content-Type: application/json');
    echo json_encode($timing ?: ['error' => 'Parcours non trouvé']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>