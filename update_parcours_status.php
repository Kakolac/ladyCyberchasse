<?php
/**
 * Script de mise à jour du statut du parcours après résolution d'énigme
 */

session_start();
require_once 'config/connexion.php';

// Vérification de la session
if (!isset($_SESSION['team_name'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Session non valide']);
    exit();
}

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

// Récupération des données
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['lieu']) || !isset($input['team']) || !isset($input['success'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit();
}

$lieu_slug = trim($input['lieu']);
$team_name = trim($input['team']);
$success = (bool)$input['success'];
$score = isset($input['score']) ? (int)$input['score'] : 10;

try {
    // Récupération de l'équipe
    $stmt = $pdo->prepare("SELECT id FROM equipes WHERE nom = ?");
    $stmt->execute([$team_name]);
    $equipe = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$equipe) {
        throw new Exception('Équipe non trouvée');
    }
    
    // Récupération du lieu
    $stmt = $pdo->prepare("SELECT id FROM lieux WHERE slug = ?");
    $stmt->execute([$lieu_slug]);
    $lieu = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$lieu) {
        throw new Exception('Lieu non trouvé');
    }
    
    if ($success) {
        // Mise à jour du parcours : statut terminé, score obtenu, temps de fin
        $stmt = $pdo->prepare("
            UPDATE parcours 
            SET statut = 'termine', 
                score_obtenu = ?, 
                temps_fin = NOW(),
                temps_ecoule = TIMESTAMPDIFF(SECOND, temps_debut, NOW())
            WHERE equipe_id = ? AND lieu_id = ?
        ");
        
        if ($stmt->execute([$score, $equipe['id'], $lieu['id']])) {
            // 🎯 NOUVELLE FONCTIONNALITÉ : Détection automatique de fin de parcours
            // Vérifier si c'est la fin du parcours pour cette équipe
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as total_lieux, 
                       SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) as lieux_termines
                FROM parcours 
                WHERE equipe_id = ?
            ");
            $stmt->execute([$equipe['id']]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si tous les lieux sont terminés, marquer le parcours comme terminé
            if ($stats['total_lieux'] > 0 && $stats['lieux_termines'] == $stats['total_lieux']) {
                // Mettre à jour tous les parcours de cette équipe avec le statut "parcours_termine"
                $stmt = $pdo->prepare("UPDATE parcours SET statut = 'parcours_termine' WHERE equipe_id = ?");
                $stmt->execute([$equipe['id']]);
                
                // Message spécial pour la fin de parcours
                $response = [
                    'success' => true,
                    'message' => '🎉 Félicitations ! Vous avez terminé TOUT votre parcours !',
                    'parcours_termine' => true,
                    'score_total' => $stats['lieux_termines'] * $score
                ];
            } else {
                // Message normal pour un lieu terminé
                $response = [
                    'success' => true,
                    'message' => '🎯 Bravo ! Lieu terminé. Progression : ' . $stats['lieux_termines'] . '/' . $stats['total_lieux'] . ' lieux',
                    'parcours_termine' => false,
                    'progression' => [
                        'termines' => $stats['lieux_termines'],
                        'total' => $stats['total_lieux']
                    ]
                ];
            }
            
            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'Erreur lors de la mise à jour du parcours']);
        }
    } else {
        // Échec de l'énigme - marquer comme échec
        $stmt = $pdo->prepare("
            UPDATE parcours 
            SET statut = 'echec', 
                temps_fin = NOW(),
                temps_ecoule = TIMESTAMPDIFF(SECOND, temps_debut, NOW())
            WHERE equipe_id = ? AND lieu_id = ?
        ");
        
        if ($stmt->execute([$equipe['id'], $lieu['id']])) {
            echo json_encode([
                'success' => true,
                'message' => 'Statut d\'échec enregistré'
            ]);
        } else {
            throw new Exception('Erreur lors de la mise à jour du statut d\'échec');
        }
    }
    
} catch (Exception $e) {
    error_log("Erreur mise à jour parcours: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur interne du serveur'
    ]);
}
?>
