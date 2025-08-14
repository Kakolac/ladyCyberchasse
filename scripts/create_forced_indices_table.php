<?php
require_once '../config/connexion.php';

echo "<h1>🚀 Création de la table des indices forcés</h1>";

try {
    // Table pour gérer les indices forcés par l'admin
    $pdo->exec("CREATE TABLE IF NOT EXISTS indices_forces (
        id INT AUTO_INCREMENT PRIMARY KEY,
        equipe_id INT NOT NULL,
        lieu_id INT NOT NULL,
        enigme_id INT NOT NULL,
        admin_id VARCHAR(100) NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (equipe_id) REFERENCES equipes(id) ON DELETE CASCADE,
        FOREIGN KEY (lieu_id) REFERENCES lieux(id) ON DELETE CASCADE,
        FOREIGN KEY (enigme_id) REFERENCES enigmes(id) ON DELETE CASCADE,
        UNIQUE KEY unique_force (equipe_id, lieu_id, enigme_id)
    )");
    echo "✅ Table indices_forces créée<br>";

    echo "<h2>🎉 Table créée avec succès !</h2>";
    echo "<p>Vous pouvez maintenant :</p>";
    echo "<ul>";
    echo "<li>Forcer la disponibilité d'un indice pour une équipe</li>";
    echo "<li>Bypasser le timer de 6 minutes</li>";
    echo "<li>Gérer les indices forcés depuis l'admin</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
