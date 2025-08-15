<?php
require_once '../config/connexion.php';

echo "<h1>🔍 Test de la table indices_consultes</h1>";

try {
    // Vérifier que la table existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'indices_consultes'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table indices_consultes existe<br>";
        
        // Vérifier la structure
        $stmt = $pdo->query("DESCRIBE indices_consultes");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Structure de la table :</h3>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Vérifier les contraintes
        $stmt = $pdo->query("SHOW CREATE TABLE indices_consultes");
        $create_table = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<h3>Contraintes :</h3>";
        echo "<pre>" . htmlspecialchars($create_table['Create Table']) . "</pre>";
        
        // Test d'insertion
        echo "<h3>Test d'insertion :</h3>";
        
        // Vérifier qu'il y a des équipes et lieux
        $stmt = $pdo->query("SELECT COUNT(*) FROM equipes");
        $nb_equipes = $stmt->fetchColumn();
        echo "Nombre d'équipes : $nb_equipes<br>";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM lieux");
        $nb_lieux = $stmt->fetchColumn();
        echo "Nombre de lieux : $nb_lieux<br>";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM enigmes");
        $nb_enigmes = $stmt->fetchColumn();
        echo "Nombre d'énigmes : $nb_enigmes<br>";
        
        if ($nb_equipes > 0 && $nb_lieux > 0 && $nb_enigmes > 0) {
            // Récupérer la première équipe, lieu et énigme pour le test
            $stmt = $pdo->query("SELECT id FROM equipes LIMIT 1");
            $equipe_id = $stmt->fetchColumn();
            
            $stmt = $pdo->query("SELECT id FROM lieux LIMIT 1");
            $lieu_id = $stmt->fetchColumn();
            
            $stmt = $pdo->query("SELECT id FROM enigmes LIMIT 1");
            $enigme_id = $stmt->fetchColumn();
            
            echo "Test avec : Équipe ID=$equipe_id, Lieu ID=$lieu_id, Enigme ID=$enigme_id<br>";
            
            // Test d'insertion
            $stmt = $pdo->prepare("
                INSERT INTO indices_consultes (equipe_id, lieu_id, enigme_id) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE timestamp = CURRENT_TIMESTAMP
            ");
            
            if ($stmt->execute([$equipe_id, $lieu_id, $enigme_id])) {
                echo "✅ Test d'insertion réussi<br>";
                
                // Vérifier l'insertion
                $stmt = $pdo->prepare("SELECT * FROM indices_consultes WHERE equipe_id = ? AND lieu_id = ? AND enigme_id = ?");
                $stmt->execute([$equipe_id, $lieu_id, $enigme_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result) {
                    echo "✅ Données trouvées en BDD :<br>";
                    echo "<pre>" . print_r($result, true) . "</pre>";
                }
                
                // Nettoyer le test
                $stmt = $pdo->prepare("DELETE FROM indices_consultes WHERE equipe_id = ? AND lieu_id = ? AND enigme_id = ?");
                $stmt->execute([$equipe_id, $lieu_id, $enigme_id]);
                echo "🧹 Données de test supprimées<br>";
                
            } else {
                echo "❌ Test d'insertion échoué<br>";
            }
        } else {
            echo "⚠️ Impossible de tester : données insuffisantes<br>";
        }
        
    } else {
        echo "❌ Table indices_consultes n'existe pas<br>";
        echo "<a href='create_indices_table.php' class='btn btn-primary'>Créer la table</a>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
