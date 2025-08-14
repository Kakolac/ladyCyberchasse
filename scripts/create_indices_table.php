<?php
require_once '../config/connexion.php';

echo "<h1>🚀 Création de la table de suivi des indices</h1>";

try {
    // Table pour tracer les indices consultés
    $pdo->exec("CREATE TABLE IF NOT EXISTS indices_consultes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        equipe_id INT NOT NULL,
        lieu_id INT NOT NULL,
        enigme_id INT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (equipe_id) REFERENCES equipes(id),
        FOREIGN KEY (lieu_id) REFERENCES lieux(id),
        FOREIGN KEY (enigme_id) REFERENCES enigmes(id),
        UNIQUE KEY unique_consultation (equipe_id, lieu_id, enigme_id)
    )");
    echo "✅ Table indices_consultes créée<br>";

    echo "<h2>🎉 Table créée avec succès !</h2>";
    echo "<p>Vous pouvez maintenant :</p>";
    echo "<ul>";
    echo "<li>Suivre l'utilisation des indices par équipe</li>";
    echo "<li>Analyser la difficulté des énigmes</li>";
    echo "<li>Gérer les indices de manière interactive</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>