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
            // Mise à jour du score total de l'équipe
            $stmt = $pdo->prepare("
                UPDATE equipes 
                SET score = score + ?, 
                    temps_total = temps_total + COALESCE((
                        SELECT temps_ecoule 
                        FROM parcours 
                        WHERE equipe_id = ? AND lieu_id = ?
                    ), 0)
                WHERE id = ?
            ");
            $stmt->execute([$score, $equipe['id'], $lieu['id'], $equipe['id']]);
            
            // Log de l'activité
            $stmt = $pdo->prepare("
                INSERT INTO logs_activite (equipe_id, lieu_id, action, details) 
                VALUES (?, ?, 'enigme_resolue', ?)
            ");
            $stmt->execute([
                $equipe['id'], 
                $lieu['id'], 
                json_encode([
                    'lieu' => $lieu_slug,
                    'score_obtenu' => $score,
                    'timestamp' => date('Y-m-d H:i:s')
                ])
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Parcours mis à jour avec succès',
                'score_obtenu' => $score
            ]);
        } else {
            throw new Exception('Erreur lors de la mise à jour du parcours');
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
