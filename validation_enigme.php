<?php
/**
 * Script de validation des énigmes - SÉCURISÉ
 * Vérifie les réponses en base de données sans jamais les exposer
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

if (!isset($input['lieu']) || !isset($input['reponse'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit();
}

$lieu_slug = trim($input['lieu']);
$reponse_equipe = trim($input['reponse']);

// Validation des données
if (empty($lieu_slug) || !in_array($reponse_equipe, ['A', 'B', 'C', 'D'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données invalides']);
    exit();
}

try {
    // Récupération de la réponse correcte depuis la base de données
    $stmt = $pdo->prepare("SELECT e.donnees FROM cyber_lieux l JOIN enigmes e ON l.enigme_id = e.id WHERE l.slug = ? AND l.statut = 'actif'");
    $stmt->execute([$lieu_slug]);
    $enigme = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$enigme) {
        http_response_code(404);
        echo json_encode(['error' => 'Lieu non trouvé']);
        exit();
    }
    
    $donnees = json_decode($enigme['donnees'], true);
    $reponse_correcte = $donnees['reponse_correcte'] ?? '';
    $correct = ($reponse_correcte === $reponse_equipe);
    
    // Log de l'activité
    $stmt = $pdo->prepare("INSERT INTO logs_activite (equipe_id, lieu_id, action, details) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['team_id'] ?? null,
        $enigme['id'] ?? null,
        'validation_enigme',
        json_encode([
            'lieu' => $lieu_slug,
            'reponse_donnee' => $reponse_equipe,
            'correct' => $correct,
            'timestamp' => date('Y-m-d H:i:s')
        ])
    ]);
    
    // Retour de la réponse (uniquement vrai/faux, jamais la bonne réponse)
    echo json_encode([
        'correct' => $correct,
        'message' => $correct ? 'Réponse correcte !' : 'Réponse incorrecte, réessayez.'
    ]);
    
} catch (Exception $e) {
    // Log de l'erreur (sans exposer les détails techniques)
    error_log("Erreur validation énigme: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode(['error' => 'Erreur interne du serveur']);
}
?>
