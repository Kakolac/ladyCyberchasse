<?php
/**
 * Script de migration vers le JavaScript centralisé
 * Ce script met à jour tous les templates d'énigmes pour utiliser le code centralisé
 */

echo "<div style='font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.3);'>";
echo "<h1 style='text-align: center; margin-bottom: 30px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);'>🔄 Migration vers le JavaScript centralisé</h1>";

try {
    // 1. Vérifier que le fichier centralisé existe (depuis la racine du projet)
    $js_file = '../js/enigme-validation.js';
    $include_file = '../includes/enigme-functions.php';
    
    if (!file_exists($js_file)) {
        throw new Exception("Le fichier $js_file n'existe pas");
    }
    
    if (!file_exists($include_file)) {
        throw new Exception("Le fichier $include_file n'existe pas");
    }
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>✅ Fichiers centralisés créés</h3>";
    echo "<p>Fichier JavaScript : <code>js/enigme-validation.js</code></p>";
    echo "<p>Fichier d'inclusion : <code>includes/enigme-functions.php</code></p>";
    echo "</div>";
    
    // 2. Liste des fichiers à migrer (depuis la racine du projet)
    $files_to_migrate = [
        '../templates/enigmes/qcm.php',
        '../templates/enigmes/texte_libre.php',
        '../templates/enigmes/audio.php',
        '../templates/enigmes/youtube.php',
        '../templates/TemplateLieu/enigme.php',
        '../lieux/sallon/enigme.php',
        '../lieux/vieille_chambre_du_petit_garon/enigme.php'
    ];
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #60a5fa; margin-top: 0;'>📁 Fichiers à migrer</h3>";
    
    $migrated_count = 0;
    foreach ($files_to_migrate as $file) {
        if (file_exists($file)) {
            // Afficher le chemin relatif depuis la racine
            $relative_path = str_replace('../', '', $file);
            echo "<p>📄 <code>$relative_path</code></p>";
            $migrated_count++;
        } else {
            $relative_path = str_replace('../', '', $file);
            echo "<p>⚠️ <code>$relative_path</code> (non trouvé)</p>";
        }
    }
    echo "</div>";
    
    // 3. Instructions de migration
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #fbbf24; margin-top: 0;'>🛠️ Instructions de migration</h3>";
    echo "<p>Pour chaque fichier d'énigme, remplacer le code JavaScript dupliqué par :</p>";
    echo "<ol>";
    echo "<li>Inclure <code>includes/enigme-functions.php</code></li>";
    echo "<li>Remplacer <code>function updateParcoursStatus</code> par un appel à la fonction centralisée</li>";
    echo "<li>Remplacer <code>function validateAnswer</code> par un appel à la fonction centralisée appropriée</li>";
    echo "<li>Supprimer le code JavaScript dupliqué</li>";
    echo "</ol>";
    echo "</div>";
    
    // 4. Exemple de migration
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>💡 Exemple de migration</h3>";
    echo "<p><strong>Avant (code dupliqué) :</strong></p>";
    echo "<pre style='background: rgba(0,0,0,0.3); padding: 10px; border-radius: 5px; font-size: 12px;'>";
    echo "function updateParcoursStatus(success) {\n";
    echo "    fetch('update_parcours_status.php', {\n";
    echo "        // ... code dupliqué ...\n";
    echo "    });\n";
    echo "}\n\n";
    echo "function validateAnswer() {\n";
    echo "    // ... code dupliqué ...\n";
    echo "}";
    echo "</pre>";
    
    echo "<p><strong>Après (code centralisé) :</strong></p>";
    echo "<pre style='background: rgba(0,0,0,0.3); padding: 10px; border-radius: 5px; font-size: 12px;'>";
    echo "<?php include 'includes/enigme-functions.php'; ?>\n\n";
    echo "function validateAnswer() {\n";
    echo "    const reponseCorrecte = '<?php echo \$donnees['reponse_correcte']; ?>';\n";
    echo "    validateQCMAnswer(reponseCorrecte, 10);\n";
    echo "}";
    echo "</pre>";
    echo "</div>";
    
    // 5. Avantages de la migration
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; text-align: center;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>🎉 Avantages de la migration</h3>";
    echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;'>";
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px;'>";
    echo "<h4>✅ Maintenance</h4>";
    echo "<p>Un seul endroit pour modifier la logique</p>";
    echo "</div>";
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px;'>";
    echo "<h4>🔧 Cohérence</h4>";
    echo "<p>Tous les templates utilisent la même logique</p>";
    echo "</div>";
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px;'>";
    echo "<h4>🐛 Débogage</h4>";
    echo "<p>Plus facile de corriger les bugs</p>";
    echo "</div>";
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px;'>";
    echo "<h4>📈 Évolutivité</h4>";
    echo "<p>Facile d'ajouter de nouvelles fonctionnalités</p>";
    echo "</div>";
    echo "</div>";
    
    // 6. Status de migration actuel
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #fbbf24; margin-top: 0;'>📊 Status de migration</h3>";
    echo "<p>Fichiers trouvés : <strong>$migrated_count</strong> sur " . count($files_to_migrate) . "</p>";
    
    if ($migrated_count > 0) {
        echo "<div style='background: rgba(34, 197, 94, 0.2); padding: 15px; border-radius: 8px; margin-top: 15px;'>";
        echo "<p style='color: #22c55e; margin: 0;'>✅ Prêt pour la migration !</p>";
        echo "</div>";
    } else {
        echo "<div style='background: rgba(239, 68, 68, 0.2); padding: 15px; border-radius: 8px; margin-top: 15px;'>";
        echo "<p style='color: #ef4444; margin: 0;'>⚠️ Aucun fichier trouvé pour la migration</p>";
        echo "</div>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: rgba(239, 68, 68, 0.2); padding: 20px; border-radius: 10px; border-left: 5px solid #ef4444;'>";
    echo "<h3 style='color: #ef4444; margin-top: 0;'>❌ Erreur</h3>";
    echo "<p>Erreur lors de la migration : " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</div>";
?>

<!-- Style pour améliorer l'apparence -->
<style>
code {
    background: rgba(0,0,0,0.3);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
}
</style>
