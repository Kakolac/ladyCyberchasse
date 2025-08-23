<?php
/**
 * Script de nettoyage des timestamps NULL dans la table parcours
 * Lancement : https://localhost/scripts/cleanup_null_timestamps.php
 */

require_once '../config/connexion.php';

echo "<h2>🧹 Nettoyage des timestamps NULL</h2>";

try {
    // 1. Identifier les enregistrements avec des timestamps NULL
    $stmt = $pdo->query("
        SELECT COUNT(*) as total_null 
        FROM parcours 
        WHERE enigme_start_time IS NULL OR indice_start_time IS NULL
    ");
    $total_null = $stmt->fetchColumn();
    
    echo "<p>📊 Enregistrements avec timestamps NULL : <strong>$total_null</strong></p>";
    
    if ($total_null > 0) {
        // 2. Corriger les enregistrements NULL
        $stmt = $pdo->prepare("
            UPDATE parcours 
            SET enigme_start_time = NOW(),
                indice_start_time = DATE_ADD(NOW(), INTERVAL 6 MINUTE)
            WHERE enigme_start_time IS NULL OR indice_start_time IS NULL
        ");
        $stmt->execute();
        
        $rows_affected = $stmt->rowCount();
        echo "<p>✅ <strong>$rows_affected</strong> enregistrements corrigés avec succès !</p>";
        echo "<p>🎯 Tous les timestamps NULL ont été remplacés par des valeurs valides.</p>";
    } else {
        echo "<p>✅ Aucun timestamp NULL trouvé. La base de données est propre !</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Erreur : " . $e->getMessage() . "</p>";
}

echo "<p><a href='../admin/indices_stats.php' class='btn btn-primary'>Retour aux statistiques</a></p>";
?>
