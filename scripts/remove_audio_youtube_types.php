<?php
/**
 * Script pour supprimer les types d'énigmes Audio et YouTube
 * ATTENTION : Cette action est irréversible !
 */

session_start();
require_once '../config/connexion.php';

echo "<h1>🗑️ Suppression des types Audio et YouTube</h1>";
echo "<p><strong>ATTENTION : Cette action est irréversible !</strong></p>";

// Vérification de sécurité
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
    echo "❌ Accès refusé. Vous devez être connecté en tant qu'administrateur.";
    echo "</div>";
    exit;
}

// Confirmation de suppression
if (!isset($_POST['confirm_delete'])) {
    echo "<form method='POST' style='margin: 20px 0;'>";
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>⚠️ Confirmation requise</h3>";
    echo "<p>Vous êtes sur le point de supprimer les types d'énigmes <strong>Audio</strong> et <strong>YouTube</strong>.</p>";
    echo "<p>Cette action est <strong>irréversible</strong> et supprimera :</p>";
    echo "<ul>";
    echo "<li>Les types Audio et YouTube de la base de données</li>";
    echo "<li>Toutes les énigmes de ces types</li>";
    echo "<li>Tous les fichiers audio uploadés</li>";
    echo "<li>Toutes les données associées</li>";
    echo "</ul>";
    echo "<p><strong>⚠️ ATTENTION :</strong> Si des lieux utilisent ces énigmes, ils deviendront inutilisables !</p>";
    echo "</div>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<label style='display: block; margin: 10px 0;'>";
    echo "<input type='checkbox' name='confirm_delete' required> ";
    echo "Je confirme que je veux supprimer les types Audio et YouTube";
    echo "</label>";
    echo "</div>";
    
    echo "<button type='submit' style='background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
    echo "🗑️ SUPPRIMER AUDIO ET YOUTUBE";
    echo "</button>";
    echo "</form>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='../admin/enigmes.php' style='color: #007bff; text-decoration: none;'>";
    echo "← Retour à la gestion des énigmes";
    echo "</a>";
    echo "</div>";
    
    exit;
}

// Suppression confirmée - procéder
echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>🔄 Suppression en cours...</h3>";
echo "</div>";

try {
    // 1. Vérifier les types existants
    echo "<h3>🔍 Vérification des types existants...</h3>";
    
    $stmt = $pdo->prepare("SELECT id, nom, template FROM types_enigmes WHERE template IN (?, ?)");
    $stmt->execute(['audio', 'youtube']);
    $types_to_delete = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($types_to_delete)) {
        echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "ℹ️ Aucun type Audio ou YouTube trouvé à supprimer.";
        echo "</div>";
    } else {
        echo "<p>📋 Types trouvés à supprimer :</p>";
        foreach ($types_to_delete as $type) {
            echo "- <strong>{$type['nom']}</strong> (ID: {$type['id']}, Template: {$type['template']})<br>";
        }
        
        // 2. Vérifier les énigmes utilisant ces types
        echo "<h3>🔍 Vérification des énigmes...</h3>";
        
        $type_ids = array_column($types_to_delete, 'id');
        $placeholders = str_repeat('?,', count($type_ids) - 1) . '?';
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM enigmes WHERE type_enigme_id IN ($placeholders)");
        $stmt->execute($type_ids);
        $enigmes_count = $stmt->fetchColumn();
        
        echo "<p>📊 Nombre d'énigmes Audio/YouTube : <strong>$enigmes_count</strong></p>";
        
        if ($enigmes_count > 0) {
            // 3. Vérifier les lieux utilisant ces énigmes
            $stmt = $pdo->prepare("
                SELECT l.nom as lieu_nom, e.titre as enigme_titre, te.nom as type_nom
                FROM lieux l
                JOIN enigmes e ON l.enigme_id = e.id
                JOIN types_enigmes te ON e.type_enigme_id = te.id
                WHERE e.type_enigme_id IN ($placeholders)
            ");
            $stmt->execute($type_ids);
            $lieux_affectes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($lieux_affectes)) {
                echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
                echo "<h4>⚠️ ATTENTION : Lieux affectés</h4>";
                echo "<p>Les lieux suivants utilisent des énigmes Audio/YouTube et deviendront inutilisables :</p>";
                echo "<ul>";
                foreach ($lieux_affectes as $lieu) {
                    echo "<li><strong>{$lieu['lieu_nom']}</strong> - Énigme : {$lieu['enigme_titre']} ({$lieu['type_nom']})</li>";
                }
                echo "</ul>";
                echo "</div>";
            }
        }
        
        // 4. Supprimer les fichiers audio
        echo "<h3>🗂️ Suppression des fichiers audio...</h3>";
        
        $stmt = $pdo->prepare("SELECT donnees FROM enigmes WHERE type_enigme_id IN ($placeholders)");
        $stmt->execute($type_ids);
        $files_deleted = 0;
        
        while ($row = $stmt->fetch()) {
            $donnees = json_decode($row['donnees'], true);
            
            // Supprimer les fichiers audio
            if (isset($donnees['audio_file']) && !empty($donnees['audio_file'])) {
                $file_path = '../' . $donnees['audio_file'];
                if (file_exists($file_path)) {
                    if (unlink($file_path)) {
                        echo "✅ Fichier audio supprimé : " . basename($donnees['audio_file']) . "<br>";
                        $files_deleted++;
                    } else {
                        echo "❌ Erreur lors de la suppression du fichier : " . basename($donnees['audio_file']) . "<br>";
                    }
                }
            }
        }
        
        echo "<p>📁 <strong>$files_deleted</strong> fichiers audio supprimés</p>";
        
        // 5. Supprimer les énigmes
        echo "<h3>🗄️ Suppression des énigmes...</h3>";
        
        $stmt = $pdo->prepare("DELETE FROM enigmes WHERE type_enigme_id IN ($placeholders)");
        $stmt->execute($type_ids);
        $enigmes_deleted = $stmt->rowCount();
        
        echo "<p>✅ <strong>$enigmes_deleted</strong> énigmes supprimées</p>";
        
        // 6. Supprimer les types
        echo "<h3>🗑️ Suppression des types...</h3>";
        
        $stmt = $pdo->prepare("DELETE FROM types_enigmes WHERE template IN (?, ?)");
        $stmt->execute(['audio', 'youtube']);
        $types_deleted = $stmt->rowCount();
        
        echo "<p>✅ <strong>$types_deleted</strong> types supprimés</p>";
        
        // 7. Vérifier que tout est supprimé
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM types_enigmes");
        $count_final = $stmt->fetch()['total'];
        
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3> Suppression terminée avec succès !</h3>";
        echo "<p>✅ Types Audio et YouTube supprimés</p>";
        echo "<p>📊 Résumé :</p>";
        echo "<ul>";
        echo "<li>Types supprimés : <strong>$types_deleted</strong></li>";
        echo "<li>Énigmes supprimées : <strong>$enigmes_deleted</strong></li>";
        echo "<li>Fichiers supprimés : <strong>$files_deleted</strong></li>";
        echo "<li>Types restants : <strong>$count_final</strong></li>";
        echo "</ul>";
        echo "</div>";
        
        // 8. Afficher les types restants
        echo "<h3>📋 Types d'énigmes restants :</h3>";
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
    }
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Erreur lors de la suppression</h3>";
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
echo "<h4> Note importante</h4>";
echo "<p>Ce script a supprimé définitivement les types Audio et YouTube ainsi que toutes leurs données associées.</p>";
echo "<p>Si vous aviez des données importantes, elles ont été perdues définitivement.</p>";
echo "<p>Pour éviter cela à l'avenir, pensez à faire des sauvegardes régulières de votre base de données.</p>";
echo "</div>";
?>
