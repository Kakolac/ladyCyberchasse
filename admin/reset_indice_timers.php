<?php
session_start();
require_once '../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'reset_equipe_timer':
            $equipe_id = $_POST['equipe_id'];
            
            // Récupérer l'équipe
            $stmt = $pdo->prepare("SELECT nom FROM equipes WHERE id = ?");
            $stmt->execute([$equipe_id]);
            $equipe = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($equipe) {
                // Récupérer tous les lieux où cette équipe a consulté des indices
                $stmt = $pdo->prepare("
                    SELECT DISTINCT l.id, l.slug 
                    FROM indices_consultes ic
                    JOIN lieux l ON ic.lieu_id = l.id
                    WHERE ic.equipe_id = ?
                ");
                $stmt->execute([$equipe_id]);
                $lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Créer un fichier de reset pour cette équipe
                $reset_data = [
                    'equipe_id' => $equipe_id,
                    'equipe_nom' => $equipe['nom'],
                    'lieux' => $lieux,
                    'timestamp' => time()
                ];
                
                $reset_file = "../data/reset_timers_{$equipe_id}.json";
                if (!is_dir('../data')) {
                    mkdir('../data', 0755, true);
                }
                
                if (file_put_contents($reset_file, json_encode($reset_data))) {
                    echo json_encode(['success' => true, 'message' => 'Timer reseté pour l\'équipe ' . $equipe['nom']]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Erreur lors de la création du fichier de reset']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Équipe non trouvée']);
            }
            break;
            
        case 'reset_all_timers':
            // Créer un fichier de reset global
            $reset_data = [
                'type' => 'global',
                'timestamp' => time(),
                'admin' => $_SESSION['admin']
            ];
            
            $reset_file = "../data/reset_timers_global.json";
            if (!is_dir('../data')) {
                mkdir('../data', 0755, true);
            }
            
            if (file_put_contents($reset_file, json_encode($reset_data))) {
                echo json_encode(['success' => true, 'message' => 'Tous les timers ont été resetés']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erreur lors de la création du fichier de reset global']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Action non reconnue']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
}
?>
