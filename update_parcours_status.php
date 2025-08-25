<?php
/**
 * Script de mise à jour du statut du parcours après résolution d'énigme
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['team_name']) || !isset($_SESSION['equipe_id']) || !isset($_SESSION['parcours_id'])) {
    echo json_encode(['success' => false, 'error' => 'Session invalide']);
    exit();
}

require_once 'config/connexion.php';

try {
    // Récupération des données POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'Données invalides']);
        exit();
    }
    
    $lieu_slug = $data['lieu'] ?? '';
    $equipe_id = $_SESSION['equipe_id'];
    $parcours_id = $_SESSION['parcours_id'];
    
    if (empty($lieu_slug)) {
        echo json_encode(['success' => false, 'error' => 'Lieu manquant']);
        exit();
    }
    
    // Récupérer l'ID du lieu depuis cyber_lieux
    $stmt = $pdo->prepare("SELECT id FROM cyber_lieux WHERE slug = ?");
    $stmt->execute([$lieu_slug]);
    $lieu = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$lieu) {
        echo json_encode(['success' => false, 'error' => 'Lieu non trouvé']);
        exit();
    }
    
    $lieu_id = $lieu['id'];
    
    // Mettre à jour le statut dans cyber_token
    $stmt = $pdo->prepare("
        UPDATE cyber_token 
        SET statut = 'termine', 
            score_obtenu = 10,
            updated_at = NOW()
        WHERE equipe_id = ? AND lieu_id = ? AND parcours_id = ?
    ");
    
    $result = $stmt->execute([$equipe_id, $lieu_id, $parcours_id]);
    
    if ($result) {
        // Vérifier si c'était le dernier lieu du parcours
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_lieux,
                   COUNT(CASE WHEN statut = 'termine' THEN 1 END) as lieux_termines
            FROM cyber_token 
            WHERE parcours_id = ?
        ");
        $stmt->execute([$parcours_id]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $parcours_termine = ($stats['total_lieux'] > 0 && $stats['lieux_termines'] == $stats['total_lieux']);
        
        // Si le parcours est terminé, le marquer comme tel
        if ($parcours_termine) {
            $stmt = $pdo->prepare("
                UPDATE cyber_parcours 
                SET statut = 'termine', 
                    date_fin = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$parcours_id]);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Réponse validée avec succès',
            'score' => 10,
            'parcours_termine' => $parcours_termine
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la mise à jour']);
    }
    
} catch (PDOException $e) {
    error_log("Erreur PDO dans update_parcours_status.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Erreur de base de données']);
} catch (Exception $e) {
    error_log("Erreur générale dans update_parcours_status.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Erreur interne du serveur']);
}
?>
