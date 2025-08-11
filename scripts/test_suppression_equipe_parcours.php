<?php
/**
 * Script de test pour la suppression de tous les parcours d'une équipe
 * Teste la fonctionnalité de suppression en masse des parcours
 */

// Configuration de connexion
$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'cyberchasse';

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
    
    echo "<div style='font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.3);'>";
    echo "<h1 style='text-align: center; margin-bottom: 30px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);'>🧪 Test Suppression Parcours Équipe</h1>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>✅ Connexion à la base de données réussie</h3>";
    echo "</div>";
    
    // 1. Vérifier l'état initial des parcours
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #60a5fa; margin-top: 0;'>📊 État initial des parcours</h3>";
    
    $stmt = $pdo->query("
        SELECT e.nom as equipe_nom, COUNT(p.id) as nb_parcours
        FROM equipes e
        LEFT JOIN parcours p ON e.id = p.equipe_id
        GROUP BY e.id, e.nom
        ORDER BY e.nom
    ");
    
    $equipes_parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>";
    echo "<tr style='background: rgba(255,255,255,0.2);'>";
    echo "<th style='padding: 10px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.3);'>Équipe</th>";
    echo "<th style='padding: 10px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.3);'>Nombre de parcours</th>";
    echo "</tr>";
    
    foreach ($equipes_parcours as $ep) {
        echo "<tr>";
        echo "<td style='padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.1);'>{$ep['equipe_nom']}</td>";
        echo "<td style='padding: 10px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1);'>{$ep['nb_parcours']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // 2. Tester la suppression des parcours d'une équipe (simulation)
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #fbbf24; margin-top: 0;'>🧪 Test de suppression (simulation)</h3>";
    
    // Sélectionner une équipe avec des parcours
    $stmt = $pdo->query("
        SELECT e.id, e.nom, COUNT(p.id) as nb_parcours
        FROM equipes e
        LEFT JOIN parcours p ON e.id = p.equipe_id
        GROUP BY e.id, e.nom
        HAVING COUNT(p.id) > 0
        ORDER BY COUNT(p.id) DESC
        LIMIT 1
    ");
    
    $equipe_test = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($equipe_test) {
        echo "<p>✅ Équipe sélectionnée pour le test : <strong>{$equipe_test['nom']}</strong> ({$equipe_test['nb_parcours']} parcours)</p>";
        
        // Simuler la suppression (sans l'exécuter réellement)
        echo "<p>🔄 Simulation de suppression de tous les parcours de l'équipe '{$equipe_test['nom']}'...</p>";
        
        // Vérifier les parcours qui seraient supprimés
        $stmt = $pdo->prepare("
            SELECT p.*, l.nom as lieu_nom
            FROM parcours p
            JOIN lieux l ON p.lieu_id = l.id
            WHERE p.equipe_id = ?
            ORDER BY p.ordre_visite
        ");
        $stmt->execute([$equipe_test['id']]);
        $parcours_a_supprimer = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>📋 Parcours qui seraient supprimés :</p>";
        echo "<ul>";
        foreach ($parcours_a_supprimer as $p) {
            echo "<li><strong>{$p['lieu_nom']}</strong> (Ordre: {$p['ordre_visite']}, Statut: {$p['statut']})</li>";
        }
        echo "</ul>";
        
        echo "<div style='background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin-top: 15px;'>";
        echo "<h4 style='margin-top: 0; color: #fbbf24;'>⚠️ Note importante</h4>";
        echo "<p>Ceci est une simulation. Pour tester la suppression réelle, utilisez l'interface d'administration :</p>";
        echo "<p><strong>URL :</strong> <a href='../admin/parcours.php' style='color: #60a5fa;'>../admin/parcours.php</a></p>";
        echo "<p><strong>Action :</strong> Section 'Actions Rapides' → Supprimer tous les parcours d'une équipe</p>";
        echo "</div>";
        
    } else {
        echo "<p>❌ Aucune équipe avec des parcours trouvée pour le test</p>";
    }
    echo "</div>";
    
    // 3. Instructions d'utilisation
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #a78bfa; margin-top: 0;'>📖 Instructions d'utilisation</h3>";
    
    echo "<h4>1. Accès à l'interface</h4>";
    echo "<p>Ouvrez votre navigateur et allez sur : <strong>http://localhost:8888/admin/parcours.php</strong></p>";
    
    echo "<h4>2. Supprimer tous les parcours d'une équipe</h4>";
    echo "<ol>";
    echo "<li>Dans la section 'Actions Rapides', sélectionnez l'équipe dans le menu déroulant</li>";
    echo "<li>Le bouton 'Supprimer tout' s'active automatiquement</li>";
    echo "<li>Cliquez sur le bouton rouge de suppression</li>";
    echo "<li>Confirmez l'action dans la popup de confirmation</li>";
    echo "<li>Tous les parcours de l'équipe sont supprimés</li>";
    echo "</ol>";
    
    echo "<h4>3. Vérification</h4>";
    echo "<p>Après suppression, rechargez la page pour vérifier que tous les parcours de l'équipe ont bien disparu.</p>";
    echo "</div>";
    
    // 4. Sécurité et précautions
    echo "<div style='background: rgba(220, 38, 38, 0.2); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid rgba(220, 38, 38, 0.5);'>";
    echo "<h3 style='color: #fca5a5; margin-top: 0;'>⚠️ Sécurité et précautions</h3>";
    echo "<ul>";
    echo "<li><strong>Action irréversible :</strong> La suppression est définitive et ne peut pas être annulée</li>";
    echo "<li><strong>Confirmation double :</strong> L'interface demande une double confirmation</li>";
    echo "<li><strong>Impact :</strong> Tous les tokens, statuts et données de progression sont perdus</li>";
    echo "<li><strong>Recommandation :</strong> Faites une sauvegarde avant toute suppression en masse</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 20px auto; padding: 20px; background: #dc2626; color: white; border-radius: 15px;'>";
    echo "<h2>❌ Erreur de connexion</h2>";
    echo "<p><strong>Message :</strong> " . $e->getMessage() . "</p>";
    echo "<p>Vérifiez que votre serveur MySQL est démarré et que les paramètres de connexion sont corrects.</p>";
    echo "</div>";
}
?>
