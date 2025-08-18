<?php
/**
 * Script pour corriger automatiquement les IDs des types d'énigmes
 * Remet les IDs dans l'ordre attendu par le code (1,2,3,4,5,6)
 */

session_start();
require_once '../config/connexion.php';

echo "<h1>�� Correction automatique des IDs des types d'énigmes</h1>";
echo "<p><strong>Ce script va réorganiser les IDs pour qu'ils correspondent au code existant.</strong></p>";

// Vérification de sécurité
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
    echo "❌ Accès refusé. Vous devez être connecté en tant qu'administrateur.";
    echo "</div>";
    exit;
}

// Confirmation de correction
if (!isset($_POST['confirm_fix'])) {
    echo "<form method='POST' style='margin: 20px 0;'>";
    echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>ℹ️ Information</h3>";
    echo "<p>Ce script va :</p>";
    echo "<ul>";
    echo "<li><strong>Vérifier</strong> les IDs actuels des types d'énigmes</li>";
    echo "<li><strong>Réorganiser</strong> les IDs pour qu'ils soient dans l'ordre 1,2,3,4,5,6</li>";
    echo "<li><strong>Mettre à jour</strong> toutes les énigmes qui utilisent ces types</li>";
    echo "<li><strong>Préserver</strong> toutes les données existantes</li>";
    echo "</ul>";
    echo "<p><strong>⚠️ ATTENTION :</strong> Cette opération modifie la base de données. Faites une sauvegarde avant !</p>";
    echo "</div>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<label style='display: block; margin: 10px 0;'>";
    echo "<input type='checkbox' name='confirm_fix' required> ";
    echo "Je confirme que je veux corriger les IDs des types d'énigmes";
    echo "</label>";
    echo "</div>";
    
    echo "<button type='submit' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
    echo "🔧 CORRIGER LES IDs";
    echo "</button>";
    echo "</form>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='../admin/enigmes.php' style='color: #007bff; text-decoration: none;'>";
    echo "← Retour à la gestion des énigmes";
    echo "</a>";
    echo "</div>";
    
    exit;
}

// Correction confirmée - procéder
echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>🔄 Correction en cours...</h3>";
echo "</div>";

try {
    // 1. Vérifier l'état actuel
    echo "<h3>🔍 État actuel des types d'énigmes</h3>";
    
    $stmt = $pdo->query("SELECT * FROM types_enigmes ORDER BY id");
    $types_actuels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr><th>ID Actuel</th><th>Nom</th><th>Template</th><th>Actif</th></tr>";
    
    foreach ($types_actuels as $type) {
        echo "<tr>";
        echo "<td>" . $type['id'] . "</td>";
        echo "<td>" . $type['nom'] . "</td>";
        echo "<td>" . $type['template'] . "</td>";
        echo "<td>" . ($type['actif'] ? 'Oui' : 'Non') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 2. Définir le mapping des IDs souhaités
    $mapping_souhaite = [
        'qcm' => 1,
        'texte_libre' => 2,
        'calcul' => 3,
        'image' => 4,
        'audio' => 5,
        'youtube' => 6
    ];
    
    echo "<h3>🎯 Mapping des IDs souhaités</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr><th>Template</th><th>ID Souhaité</th></tr>";
    
    foreach ($mapping_souhaite as $template => $id) {
        echo "<tr>";
        echo "<td>$template</td>";
        echo "<td>$id</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 3. Créer une table temporaire pour la réorganisation
    echo "<h3>🔄 Création de la table temporaire...</h3>";
    
    $pdo->exec("CREATE TEMPORARY TABLE types_enigmes_temp LIKE types_enigmes");
    echo "✅ Table temporaire créée<br>";
    
    // 4. Insérer les types dans le bon ordre
    echo "<h3>�� Réorganisation des types...</h3>";
    
    $stmt_insert = $pdo->prepare("INSERT INTO types_enigmes_temp (id, nom, description, template, actif, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($mapping_souhaite as $template => $id) {
        // Trouver le type correspondant
        $type_trouve = null;
        foreach ($types_actuels as $type) {
            if ($type['template'] === $template) {
                $type_trouve = $type;
                break;
            }
        }
        
        if ($type_trouve) {
            $stmt_insert->execute([
                $id,
                $type_trouve['nom'],
                $type_trouve['description'] ?? '',
                $type_trouve['template'],
                $type_trouve['actif'],
                $type_trouve['created_at'] ?? date('Y-m-d H:i:s')
            ]);
            echo "✅ Type <strong>{$type_trouve['nom']}</strong> réorganisé : ID {$type_trouve['id']} → $id<br>";
        } else {
            echo "⚠️ Type <strong>$template</strong> non trouvé, création d'un nouveau<br>";
            $stmt_insert->execute([
                $id,
                ucfirst(str_replace('_', ' ', $template)),
                'Type d\'énigme ' . ucfirst(str_replace('_', ' ', $template)),
                $template,
                1,
                date('Y-m-d H:i:s')
            ]);
        }
    }
    
    // 5. Mettre à jour les énigmes avec les nouveaux IDs
    echo "<h3>�� Mise à jour des énigmes...</h3>";
    
    // Créer une table de correspondance
    $stmt = $pdo->query("SELECT id, template FROM types_enigmes_temp ORDER BY id");
    $correspondance = [];
    while ($row = $stmt->fetch()) {
        $correspondance[$row['template']] = $row['id'];
    }
    
    // Mettre à jour chaque énigme
    $stmt_enigmes = $pdo->query("SELECT e.id, e.type_enigme_id, te.template FROM enigmes e JOIN types_enigmes te ON e.type_enigme_id = te.id");
    $enigmes_updated = 0;
    
    while ($enigme = $stmt_enigmes->fetch()) {
        if (isset($correspondance[$enigme['template']])) {
            $nouveau_id = $correspondance[$enigme['template']];
            $stmt_update = $pdo->prepare("UPDATE enigmes SET type_enigme_id = ? WHERE id = ?");
            $stmt_update->execute([$nouveau_id, $enigme['id']]);
            $enigmes_updated++;
            echo "✅ Énigme ID {$enigme['id']} : type {$enigme['type_enigme_id']} → $nouveau_id<br>";
        }
    }
    
    echo "<p>📊 <strong>$enigmes_updated</strong> énigmes mises à jour</p>";
    
    // 6. Remplacer la table originale
    echo "<h3>�� Remplacement de la table originale...</h3>";
    
    $pdo->exec("DROP TABLE types_enigmes");
    $pdo->exec("RENAME TABLE types_enigmes_temp TO types_enigmes");
    echo "✅ Table originale remplacée<br>";
    
    // 7. Vérifier le résultat final
    echo "<h3>✅ Résultat final</h3>";
    
    $stmt = $pdo->query("SELECT * FROM types_enigmes ORDER BY id");
    $types_finaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr><th>ID Final</th><th>Nom</th><th>Template</th><th>Actif</th></tr>";
    
    foreach ($types_finaux as $type) {
        echo "<tr>";
        echo "<td>" . $type['id'] . "</td>";
        echo "<td>" . $type['nom'] . "</td>";
        echo "<td>" . $type['template'] . "</td>";
        echo "<td>" . ($type['actif'] ? 'Oui' : 'Non') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>🎉 Correction terminée avec succès !</h3>";
    echo "<p>✅ Les IDs des types d'énigmes ont été réorganisés</p>";
    echo "<p>✅ Toutes les énigmes ont été mises à jour</p>";
    echo "<p>✅ Le code existant devrait maintenant fonctionner sans modification</p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Erreur lors de la correction</h3>";
    echo "<p>Erreur : " . $e->getMessage() . "</p>";
    echo "</div>";
}

// Bouton de retour
echo "<div style='margin: 20px 0;'>";
echo "<a href='../admin/enigmes.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>";
echo "← Retour à la gestion des énigmes";
echo "</a>";
echo "</div>";

echo "<div style='margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px;'>";
echo "<h4>�� Note importante</h4>";
echo "<p>Ce script a réorganisé les IDs pour qu'ils correspondent exactement au code existant :</p>";
echo "<ul>";
echo "<li><strong>QCM</strong> : ID 1</li>";
echo "<li><strong>Texte Libre</strong> : ID 2</li>";
echo "<li><strong>Calcul</strong> : ID 3</li>";
echo "<li><strong>Image</strong> : ID 4</li>";
echo "<li><strong>Audio</strong> : ID 5</li>";
echo "<li><strong>YouTube</strong> : ID 6</li>";
echo "</ul>";
echo "<p>Aucune modification du code n'est nécessaire !</p>";
echo "</div>";
?>
