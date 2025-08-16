<?php
require_once '../config/connexion.php';

echo "<div style='font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.3);'>";
echo "<h1 style='text-align: center; margin-bottom: 30px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);'>🔧 Ajout du statut 'parcours_termine'</h1>";

try {
    // 1. Vérifier la structure actuelle
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>�� Structure actuelle de la colonne 'statut'</h3>";
    
    $stmt = $pdo->query("DESCRIBE parcours");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['Field'] === 'statut') {
            echo "<p><strong>Colonne :</strong> {$row['Field']}</p>";
            echo "<p><strong>Type :</strong> {$row['Type']}</p>";
            echo "<p><strong>Valeurs acceptées :</strong> {$row['Type']}</p>";
        }
    }
    echo "</div>";

    // 2. Modifier la colonne pour accepter le nouveau statut
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #60a5fa; margin-top: 0;'>�� Modification de la colonne 'statut'</h3>";
    
    $sql = "ALTER TABLE parcours MODIFY COLUMN statut ENUM('en_attente', 'en_cours', 'termine', 'echec', 'parcours_termine') DEFAULT 'en_attente'";
    
    if ($pdo->exec($sql)) {
        echo "<p style='color: #4ade80;'>✅ Colonne 'statut' modifiée avec succès !</p>";
        echo "<p>Le statut 'parcours_termine' est maintenant accepté.</p>";
    } else {
        echo "<p style='color: #fbbf24;'>ℹ️ La colonne était déjà modifiée ou aucune modification nécessaire</p>";
    }
    echo "</div>";

    // 3. Vérifier la nouvelle structure
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #a78bfa; margin-top: 0;'>✅ Vérification de la nouvelle structure</h3>";
    
    $stmt = $pdo->query("DESCRIBE parcours");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['Field'] === 'statut') {
            echo "<p><strong>Nouvelle structure :</strong> {$row['Type']}</p>";
        }
    }
    echo "</div>";

    // 4. Test d'insertion
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #fbbf24; margin-top: 0;'>🧪 Test d'insertion du nouveau statut</h3>";
    
    // Créer un token unique pour le test
    $test_token = bin2hex(random_bytes(16));
    
    // Vérifier que ce token n'existe pas déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM parcours WHERE token_acces = ?");
    $stmt->execute([$test_token]);
    $token_exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    
    if ($token_exists) {
        echo "<p style='color: #fbbf24;'>ℹ️ Token déjà existant, génération d'un nouveau...</p>";
        $test_token = bin2hex(random_bytes(16));
    }
    
    // Utiliser des valeurs d'ID très élevées pour éviter les conflits
    $test_equipe_id = 999999;
    $test_lieu_id = 999999;
    
    echo "<p>Test avec des IDs fictifs (ID: {$test_equipe_id}, Lieu: {$test_lieu_id})</p>";
    echo "<p>Token de test : <code>{$test_token}</code></p>";
    
    try {
        // Créer un parcours de test temporaire
        $stmt = $pdo->prepare("INSERT INTO parcours (equipe_id, lieu_id, ordre_visite, token_acces, statut) VALUES (?, ?, 999, ?, 'parcours_termine')");
        
        if ($stmt->execute([$test_equipe_id, $test_lieu_id, $test_token])) {
            echo "<p style='color: #4ade80;'>✅ Test d'insertion réussi ! Le statut 'parcours_termine' fonctionne.</p>";
            
            // Supprimer le test
            $stmt = $pdo->prepare("DELETE FROM parcours WHERE token_acces = ?");
            $stmt->execute([$test_token]);
            echo "<p style='color: #4ade80;'>✅ Parcours de test supprimé.</p>";
        } else {
            echo "<p style='color: #ef4444;'>❌ Erreur lors du test d'insertion</p>";
        }
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            echo "<p style='color: #4ade80;'>✅ Test réussi ! Le statut 'parcours_termine' est accepté (l'erreur de clé étrangère est normale avec des IDs fictifs)</p>";
        } else {
            echo "<p style='color: #ef4444;'>❌ Erreur lors du test d'insertion : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    echo "</div>";

    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; text-align: center;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>🎉 Modification terminée !</h3>";
    echo "<p>Vous pouvez maintenant utiliser le statut 'parcours_termine' dans l'interface d'administration.</p>";
    echo "<a href='../admin/parcours.php' style='color: #60a5fa; text-decoration: none; font-weight: bold;'>← Retour à la gestion des parcours</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='background: rgba(239, 68, 68, 0.2); padding: 20px; border-radius: 10px; border: 1px solid #ef4444;'>";
    echo "<h3 style='color: #ef4444; margin-top: 0;'>❌ Erreur</h3>";
    echo "<p><strong>Message :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Fichier :</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Ligne :</strong> " . htmlspecialchars($e->getLine()) . "</p>";
    echo "</div>";
}

echo "</div>";
?>
