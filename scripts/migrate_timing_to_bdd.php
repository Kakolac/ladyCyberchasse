<?php
/**
 * Script de migration pour ajouter les colonnes de timing à la table parcours
 * Lancement : https://localhost/scripts/migrate_timing_to_bdd.php
 */

require_once '../config/connexion.php';

echo "<h2>🚀 Migration des timestamps vers la BDD</h2>";

try {
    // 1. Vérifier si les colonnes existent déjà
    $stmt = $pdo->query("DESCRIBE parcours");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $changes_made = false;
    
    // 2. Ajouter la colonne enigme_start_time si elle n'existe pas
    if (!in_array('enigme_start_time', $columns)) {
        echo "<p>➕ Ajout de la colonne <code>enigme_start_time</code>...</p>";
        $pdo->exec("ALTER TABLE parcours ADD COLUMN enigme_start_time TIMESTAMP NULL");
        $changes_made = true;
    } else {
        echo "<p>✅ Colonne <code>enigme_start_time</code> existe déjà</p>";
    }
    
    // 3. Ajouter la colonne indice_start_time si elle n'existe pas
    if (!in_array('indice_start_time', $columns)) {
        echo "<p>➕ Ajout de la colonne <code>indice_start_time</code>...</p>";
        $pdo->exec("ALTER TABLE parcours ADD COLUMN indice_start_time TIMESTAMP NULL");
        $changes_made = true;
    } else {
        echo "<p>✅ Colonne <code>indice_start_time</code> existe déjà</p>";
    }
    
    // 4. Ajouter la colonne termine_at si elle n'existe pas
    if (!in_array('termine_at', $columns)) {
        echo "<p>➕ Ajout de la colonne <code>termine_at</code>...</p>";
        $pdo->exec("ALTER TABLE parcours ADD COLUMN termine_at TIMESTAMP NULL");
        $changes_made = true;
    } else {
        echo "<p>✅ Colonne <code>termine_at</code> existe déjà</p>";
    }
    
    // 5. Créer un index pour optimiser les requêtes
    echo "<p>🔍 Vérification des index...</p>";
    $stmt = $pdo->query("SHOW INDEX FROM parcours WHERE Key_name = 'idx_parcours_timing'");
    if ($stmt->rowCount() == 0) {
        echo "<p>➕ Création de l'index <code>idx_parcours_timing</code>...</p>";
        $pdo->exec("CREATE INDEX idx_parcours_timing ON parcours(equipe_id, lieu_id, enigme_start_time)");
        $changes_made = true;
    } else {
        echo "<p>✅ Index <code>idx_parcours_timing</code> existe déjà</p>";
    }
    
    // 6. Initialiser les timestamps pour les parcours existants
    echo "<p>�� Initialisation des timestamps existants...</p>";
    $stmt = $pdo->prepare("
        UPDATE parcours 
        SET enigme_start_time = NOW(), 
            indice_start_time = DATE_ADD(NOW(), INTERVAL 6 MINUTE)
        WHERE enigme_start_time IS NULL AND statut = 'en_cours'
    ");
    $stmt->execute();
    $updated_rows = $stmt->rowCount();
    
    if ($updated_rows > 0) {
        echo "<p>✅ <strong>{$updated_rows}</strong> parcours mis à jour avec des timestamps</p>";
        $changes_made = true;
    } else {
        echo "<p>ℹ️ Aucun parcours existant à mettre à jour</p>";
    }
    
    // 7. Afficher la structure finale
    echo "<h3>📋 Structure finale de la table parcours :</h3>";
    $stmt = $pdo->query("DESCRIBE parcours");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td><code>{$col['Field']}</code></td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if ($changes_made) {
        echo "<h3>�� Migration terminée avec succès !</h3>";
        echo "<p>Les colonnes de timing ont été ajoutées à la table parcours.</p>";
    } else {
        echo "<h3>✅ Aucune modification nécessaire</h3>";
        echo "<p>La table parcours est déjà à jour.</p>";
    }
    
} catch (Exception $e) {
    echo "<h3>❌ Erreur lors de la migration</h3>";
    echo "<p><strong>Erreur :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Fichier :</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Ligne :</strong> " . htmlspecialchars($e->getLine()) . "</p>";
}

echo "<hr>";
echo "<p><a href='../admin/'>← Retour à l'administration</a></p>";
?>
