<?php
/**
 * Script pour ajouter le type d'énigme YouTube
 */

require_once '../config/connexion.php';

echo "<h1>�� Ajout du type d'énigme YouTube</h1>";

try {
    // Vérifier si le type existe déjà
    $stmt = $pdo->prepare("SELECT id FROM types_enigmes WHERE nom = ?");
    $stmt->execute(['YouTube']);
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo "ℹ️ Le type YouTube existe déjà avec l'ID : " . $existing['id'] . "<br>";
    } else {
        // Ajouter le nouveau type
        $stmt = $pdo->prepare("INSERT INTO types_enigmes (nom, template, actif) VALUES (?, ?, ?)");
        $stmt->execute(['YouTube', 'youtube', 1]);
        
        $youtube_id = $pdo->lastInsertId();
        echo "✅ Type YouTube ajouté avec l'ID : $youtube_id<br>";
    }
    
    // Afficher tous les types d'énigmes
    echo "<h3>📋 Types d'énigmes disponibles :</h3>";
    $stmt = $pdo->query("SELECT * FROM types_enigmes ORDER BY id");
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nom</th><th>Template</th><th>Actif</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['nom'] . "</td>";
        echo "<td>" . $row['template'] . "</td>";
        echo "<td>" . ($row['actif'] ? 'Oui' : 'Non') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}

echo "<br><a href='../admin/enigmes.php'>← Retour à la gestion des énigmes</a>";
?>
