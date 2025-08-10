<?php
session_start();

// Récupérer les données JSON envoyées
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les données sont valides
if (isset($data['seconds'])) {
    // Sauvegarder le temps dans la session
    $_SESSION['timer_seconds'] = $data['seconds'];
    
    // Envoyer une réponse de succès
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    // Envoyer une réponse d'erreur
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
}