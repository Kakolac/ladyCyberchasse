<?php
require_once '../config/connexion.php';

echo "<h1>🧹 Nettoyage des resets de timers</h1>";

try {
    // Vérifier si la table existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'resets_timers'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Table resets_timers n'existe pas<br>";
        echo "Création de la table...<br>";
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS resets_timers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            equipe_id INT NULL,
            type_reset ENUM('equipe', 'global') NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (equipe_id) REFERENCES equipes(id) ON DELETE CASCADE
        )");
        echo "✅ Table resets_timers créée<br>";
    }
    
    // Compter les resets existants
    $stmt = $pdo->query("SELECT COUNT(*) FROM resets_timers");
    $count_before = $stmt->fetchColumn();
    echo "📊 Nombre de resets avant nettoyage : $count_before<br>";
    
    // Afficher les resets existants
    if ($count_before > 0) {
        echo "<h3>�� Resets existants :</h3>";
        $stmt = $pdo->query("
            SELECT r.*, e.nom as equipe_nom 
            FROM resets_timers r 
            LEFT JOIN equipes e ON r.equipe_id = e.id 
            ORDER BY r.timestamp DESC
        ");
        $resets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Équipe</th><th>Type</th><th>Date/Heure</th></tr>";
        foreach ($resets as $reset) {
            $equipe_nom = $reset['equipe_nom'] ?? 'Global';
            echo "<tr>";
            echo "<td>{$reset['id']}</td>";
            echo "<td>{$equipe_nom}</td>";
            echo "<td>{$reset['type_reset']}</td>";
            echo "<td>{$reset['timestamp']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Nettoyer tous les resets
    $stmt = $pdo->query("DELETE FROM resets_timers");
    $deleted = $stmt->rowCount();
    echo "🗑️ $deleted resets supprimés<br>";
    
    // Vérifier le nettoyage
    $stmt = $pdo->query("SELECT COUNT(*) FROM resets_timers");
    $count_after = $stmt->fetchColumn();
    echo "📊 Nombre de resets après nettoyage : $count_after<br>";
    
    // Nettoyer aussi les consultations d'indices si demandé
    if (isset($_GET['clean_indices']) && $_GET['clean_indices'] === '1') {
        echo "<h3>🧹 Nettoyage des consultations d'indices :</h3>";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM indices_consultes");
        $indices_before = $stmt->fetchColumn();
        echo "📊 Consultations d'indices avant nettoyage : $indices_before<br>";
        
        $stmt = $pdo->query("DELETE FROM indices_consultes");
        $indices_deleted = $stmt->rowCount();
        echo "🗑️ $indices_deleted consultations supprimées<br>";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM indices_consultes");
        $indices_after = $stmt->fetchColumn();
        echo "📊 Consultations d'indices après nettoyage : $indices_after<br>";
    }
    
    echo "<h2>🎉 Nettoyage terminé !</h2>";
    echo "<p>Vous pouvez maintenant :</p>";
    echo "<ul>";
    echo "<li>Relancer l'énigme sans reset détecté</li>";
    echo "<li>Tester la persistance du timer</li>";
    echo "<li>Vérifier que l'indice est bloqué pendant 6 minutes</li>";
    echo "</ul>";
    
    echo "<h3>🔧 Actions disponibles :</h3>";
    echo "<p><a href='?clean_indices=1' class='btn btn-warning'>�� Nettoyer aussi les consultations d'indices</a></p>";
    echo "<p><a href='../admin/indices_stats.php' class='btn btn-primary'>📊 Aller aux statistiques des indices</a></p>";
    echo "<p><a href='../lieux/direction/' class='btn btn-success'>�� Tester l'énigme</a></p>";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
