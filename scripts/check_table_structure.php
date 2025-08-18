<?php
/**
 * Script pour vérifier la structure de la table enigmes
 */

require_once '../config/connexion.php';

echo "<h1>🔍 Structure de la table enigmes</h1>";

try {
    // Voir la structure de la table
    $stmt = $pdo->query("DESCRIBE enigmes");
    echo "<h3>📋 Colonnes de la table enigmes :</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Voir quelques exemples de données
    $stmt = $pdo->query("SELECT * FROM enigmes LIMIT 3");
    echo "<h3>📊 Exemples de données :</h3>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
        echo "\n---\n";
    }
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "<div style='color: red;'>";
    echo "❌ Erreur : " . $e->getMessage();
    echo "</div>";
}
?>
