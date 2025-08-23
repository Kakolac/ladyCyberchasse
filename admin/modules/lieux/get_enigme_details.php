<?php
session_start();
require_once '../../../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès refusé']);
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID d\'énigme invalide']);
    exit();
}

$enigme_id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("
        SELECT e.*, te.nom as type_nom 
        FROM enigmes e 
        JOIN types_enigmes te ON e.type_enigme_id = te.id 
        WHERE e.id = ?
    ");
    
    if ($stmt->execute([$enigme_id])) {
        $enigme = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($enigme) {
            echo json_encode([
                'success' => true,
                'enigme' => $enigme
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Énigme non trouvée'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de la récupération'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur : ' . $e->getMessage()
    ]);
}
?>
