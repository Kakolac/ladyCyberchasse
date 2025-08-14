<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['team_name'])) {
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit();
}

require_once 'config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $equipe_nom = $_SESSION['team_name'];
    $lieu_slug = $data['lieu'] ?? '';
    $enigme_id = $data['enigme_id'] ?? '';
    
    if (empty($lieu_slug) || empty($enigme_id)) {
        echo json_encode(['success' => false, 'error' => 'Données manquantes']);
        exit();
    }
    
    try {
        // Récupérer l'ID de l'équipe
        $stmt = $pdo->prepare("SELECT id FROM equipes WHERE nom = ?");
        $stmt->execute([$equipe_nom]);
        $equipe = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$equipe) {
            echo json_encode(['success' => false, 'error' => 'Équipe non trouvée']);
            exit();
        }
        
        // Récupérer l'ID du lieu
        $stmt = $pdo->prepare("SELECT id FROM lieux WHERE slug = ?");
        $stmt->execute([$lieu_slug]);
        $lieu = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$lieu) {
            echo json_encode(['success' => false, 'error' => 'Lieu non trouvé']);
            exit();
        }
        
        // Enregistrer ou mettre à jour la consultation d'indice
        $stmt = $pdo->prepare("
            INSERT INTO indices_consultes (equipe_id, lieu_id, enigme_id) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE timestamp = CURRENT_TIMESTAMP
        ");
        
        if ($stmt->execute([$equipe['id'], $lieu['id'], $enigme_id])) {
            echo json_encode(['success' => true, 'message' => 'Consultation d\'indice enregistrée']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'enregistrement']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Erreur: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
}
?>
