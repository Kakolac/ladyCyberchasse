<?php
/**
 * Script pour vider toutes les énigmes de la base de données
 * ATTENTION : Cette action est irréversible !
 */

session_start();
require_once '../config/connexion.php';

echo "<h1>🗑️ Suppression de toutes les énigmes</h1>";
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
    echo "<p>Vous êtes sur le point de supprimer <strong>TOUTES</strong> les énigmes de la base de données.</p>";
    echo "<p>Cette action est <strong>irréversible</strong> et supprimera :</p>";
    echo "<ul>";
    echo "<li>Toutes les énigmes créées</li>";
    echo "<li>Tous les fichiers uploadés (images, audio)</li>";
    echo "<li>Toutes les données associées</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<label style='display: block; margin: 10px 0;'>";
    echo "<input type='checkbox' name='confirm_delete' required> ";
    echo "Je confirme que je veux supprimer toutes les énigmes";
    echo "</label>";
    echo "</div>";
    
    echo "<button type='submit' style='background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
    echo "🗑️ SUPPRIMER TOUTES LES ÉNIGMES";
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
    // 1. Compter les énigmes avant suppression
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM enigmes");
    $count_before = $stmt->fetch()['total'];
    
    echo "<p>📊 Nombre d'énigmes avant suppression : <strong>$count_before</strong></p>";
    
    if ($count_before == 0) {
        echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "ℹ️ Aucune énigme à supprimer. La base de données est déjà vide.";
        echo "</div>";
    } else {
        // 2. Supprimer les fichiers uploadés
        echo "<h3>🗂️ Suppression des fichiers uploadés...</h3>";
        
        // Récupérer tous les fichiers à supprimer (correction : colonne 'donnees' au lieu de 'donnees_json')
        $stmt = $pdo->query("SELECT donnees FROM enigmes WHERE donnees IS NOT NULL");
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
            
            // Supprimer les fichiers image
            if (isset($donnees['url_image']) && !empty($donnees['url_image'])) {
                $file_path = '../' . $donnees['url_image'];
                if (file_exists($file_path)) {
                    if (unlink($file_path)) {
                        echo "✅ Fichier image supprimé : " . basename($donnees['url_image']) . "<br>";
                        $files_deleted++;
                    } else {
                        echo "❌ Erreur lors de la suppression du fichier : " . basename($donnees['url_image']) . "<br>";
                    }
                }
            }
        }
        
        echo "<p>📁 <strong>$files_deleted</strong> fichiers supprimés</p>";
        
        // 3. Supprimer toutes les énigmes de la base
        echo "<h3>🗄️ Suppression des données de la base...</h3>";
        
        $stmt = $pdo->prepare("DELETE FROM enigmes");
        $stmt->execute();
        
        $count_after = $stmt->rowCount();
        
        echo "<p>✅ <strong>$count_after</strong> énigmes supprimées de la base de données</p>";
        
        // 4. Vérifier que la table est vide
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM enigmes");
        $count_final = $stmt->fetch()['total'];
        
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3> Suppression terminée avec succès !</h3>";
        echo "<p>✅ Toutes les énigmes ont été supprimées</p>";
        echo "<p>📊 Résumé :</p>";
        echo "<ul>";
        echo "<li>Énigmes supprimées : <strong>$count_before</strong></li>";
        echo "<li>Fichiers supprimés : <strong>$files_deleted</strong></li>";
        echo "<li>Énigmes restantes : <strong>$count_final</strong></li>";
        echo "</ul>";
        echo "</div>";
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
echo "<p>Ce script a supprimé toutes les énigmes et fichiers associés. Si vous aviez des données importantes, elles ont été perdues définitivement.</p>";
echo "<p>Pour éviter cela à l'avenir, pensez à faire des sauvegardes régulières de votre base de données.</p>";
echo "</div>";
?>
