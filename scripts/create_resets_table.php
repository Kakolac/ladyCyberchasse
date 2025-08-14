<?php
require_once '../config/connexion.php';

echo "<h1>🚀 Création de la table de gestion des resets de timers</h1>";

try {
    // Table pour gérer les resets de timers
    $pdo->exec("CREATE TABLE IF NOT EXISTS resets_timers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        equipe_id INT NULL,
        type_reset ENUM('equipe', 'global') NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (equipe_id) REFERENCES equipes(id) ON DELETE CASCADE
    )");
    echo "✅ Table resets_timers créée<br>";

    echo "<h2>🎉 Table créée avec succès !</h2>";
    echo "<p>Vous pouvez maintenant :</p>";
    echo "<ul>";
    echo "<li>Resetter les timers par équipe</li>";
    echo "<li>Resetter tous les timers globalement</li>";
    echo "<li>Gérer les resets via la base de données</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
