<?php
// Réception des logs de la console
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if ($data) {
        $log_file = '../logs/console_debug.log';
        $log_dir = dirname($log_file);
        
        // Créer le dossier logs s'il n'existe pas
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        // Écrire le log
        $log_entry = sprintf(
            "[%s] %s: %s\n",
            $data['timestamp'] ?? date('Y-m-d H:i:s'),
            $data['level'] ?? 'UNKNOWN',
            $data['message'] ?? 'No message'
        );
        
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
        
        // Réponse de succès
        http_response_code(200);
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
} else {
    // Affichage des logs
    $log_file = '../logs/console_debug.log';
    
    if (file_exists($log_file)) {
        echo "<h1>�� Logs de la Console</h1>";
        echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px; max-height: 600px; overflow-y: auto;'>";
        echo htmlspecialchars(file_get_contents($log_file));
        echo "</pre>";
        
        echo "<hr>";
        echo "<form method='POST'>";
        echo "<button type='submit' name='clear' value='1' class='btn btn-danger'>Vider les logs</button>";
        echo "</form>";
    } else {
        echo "<h1>�� Aucun log trouvé</h1>";
        echo "<p>Les logs apparaîtront ici après avoir redirigé la console.</p>";
    }
}

// Vider les logs si demandé
if (isset($_POST['clear']) && $_POST['clear'] === '1') {
    if (file_exists($log_file)) {
        unlink($log_file);
        echo "<script>alert('Logs vidés !'); window.location.reload();</script>";
    }
}
?>
