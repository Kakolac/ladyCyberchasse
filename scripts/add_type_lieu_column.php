<?php
// scripts/add_type_lieu_column.php

require_once '../config/connexion.php';

try {
    // 1. Vérifier si la colonne existe déjà
    $stmt = $pdo->query("SHOW COLUMNS FROM lieux LIKE 'type_lieu'");
    $column_exists = $stmt->fetch();

    if (!$column_exists) {
        // 2. Ajouter la colonne si elle n'existe pas
        $pdo->exec("ALTER TABLE lieux ADD COLUMN type_lieu VARCHAR(10) DEFAULT 'standard'");
        echo "✅ Colonne 'type_lieu' ajoutée avec succès à la table 'lieux'<br>";
        
        // 3. Mettre à jour les lieux existants
        $pdo->exec("UPDATE lieux SET type_lieu = 'standard' WHERE type_lieu IS NULL");
        echo "✅ Lieux existants mis à jour comme type 'standard'<br>";
    } else {
        echo "ℹ️ La colonne 'type_lieu' existe déjà dans la table 'lieux'<br>";
    }

    echo "🎉 Script exécuté avec succès !";

} catch (PDOException $e) {
    die("❌ Erreur lors de la modification de la table : " . $e->getMessage());
}
