<?php
require_once '../config/connexion.php';

echo "<h1>🚀 Création de la structure des types d'énigmes</h1>";

try {
    // Table des types d'énigmes
    $pdo->exec("CREATE TABLE IF NOT EXISTS types_enigmes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        description TEXT,
        template VARCHAR(100) NOT NULL,
        actif BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Table types_enigmes créée<br>";

    // Table des énigmes
    $pdo->exec("CREATE TABLE IF NOT EXISTS enigmes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type_enigme_id INT NOT NULL,
        titre VARCHAR(255) NOT NULL,
        donnees JSON NOT NULL,
        actif BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (type_enigme_id) REFERENCES types_enigmes(id)
    )");
    echo "✅ Table enigmes créée<br>";

    // Vérifier si la colonne enigme_id existe déjà
    $stmt = $pdo->query("SHOW COLUMNS FROM lieux LIKE 'enigme_id'");
    if ($stmt->rowCount() == 0) {
        // Ajout de la colonne enigme_id à la table lieux
        $pdo->exec("ALTER TABLE lieux ADD COLUMN enigme_id INT NULL");
        echo "✅ Colonne enigme_id ajoutée à la table lieux<br>";
    } else {
        echo "✅ Colonne enigme_id existe déjà<br>";
    }

    // Insertion des types d'énigmes de base
    $types = [
        ['QCM', 'Question à choix multiples avec 4 options', 'qcm'],
        ['Texte Libre', 'Réponse libre à saisir', 'texte_libre'],
        ['Calcul', 'Énigme mathématique', 'calcul'],
        ['Image', 'Énigme basée sur une image', 'image']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO types_enigmes (nom, description, template) VALUES (?, ?, ?)");
    foreach ($types as $type) {
        $stmt->execute($type);
    }
    echo "✅ Types d'énigmes de base insérés<br>";

    // Migration des énigmes existantes
    $stmt = $pdo->query("SELECT id, reponse_enigme, enigme_texte, options_enigme FROM lieux WHERE enigme_texte IS NOT NULL AND enigme_texte != ''");
    $enigmes_existantes = $stmt->fetchAll();

    if (!empty($enigmes_existantes)) {
        $type_qcm_id = $pdo->query("SELECT id FROM types_enigmes WHERE template = 'qcm'")->fetchColumn();
        
        $stmt_insert = $pdo->prepare("INSERT INTO enigmes (type_enigme_id, titre, donnees) VALUES (?, ?, ?)");
        $stmt_update = $pdo->prepare("UPDATE lieux SET enigme_id = ? WHERE id = ?");
        
        foreach ($enigmes_existantes as $enigme) {
            $donnees = [
                'question' => $enigme['enigme_texte'],
                'reponse_correcte' => $enigme['reponse_enigme'],
                'options' => json_decode($enigme['options_enigme'], true)
            ];
            
            $stmt_insert->execute([$type_qcm_id, 'Énigme QCM', json_encode($donnees)]);
            $enigme_id = $pdo->lastInsertId();
            $stmt_update->execute([$enigme_id, $enigme['id']]);
        }
        echo "✅ Migration des énigmes existantes terminée<br>";
    }

    echo "<h2>🎉 Structure créée avec succès !</h2>";
    echo "<p>Vous pouvez maintenant :</p>";
    echo "<ul>";
    echo "<li>Gérer les types d'énigmes dans l'admin</li>";
    echo "<li>Créer des énigmes de différents types</li>";
    echo "<li>Affecter des énigmes aux lieux</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
