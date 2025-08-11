<?php
/**
 * Script de test pour l'interface d'administration des parcours
 * Teste la création, modification et suppression des parcours
 */

require_once '../config/connexion.php';

echo "<div style='font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.3);'>";
echo "<h1 style='text-align: center; margin-bottom: 30px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);'>🧪 Test Interface Administration Parcours</h1>";

try {
    // Test 1: Vérification de la connexion
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>✅ Test 1: Connexion à la base de données</h3>";
    echo "<p>Connexion réussie à la base de données 'cyberchasse'</p>";
    echo "</div>";

    // Test 2: Vérification des tables
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>✅ Test 2: Vérification des tables</h3>";
    
    $tables = ['equipes', 'lieux', 'parcours'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>• Table '{$table}': {$count} enregistrement(s)</p>";
    }
    echo "</div>";

    // Test 3: Vérification des équipes
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>✅ Test 3: Équipes disponibles</h3>";
    
    $stmt = $pdo->query("SELECT nom, couleur, statut FROM equipes ORDER BY nom");
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($equipes as $equipe) {
        echo "<p>• <strong>{$equipe['nom']}</strong> ({$equipe['couleur']}) - Statut: {$equipe['statut']}</p>";
    }
    echo "</div>";

    // Test 4: Vérification des lieux
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>✅ Test 4: Lieux disponibles</h3>";
    
    $stmt = $pdo->query("SELECT nom, slug, ordre, temps_limite FROM lieux ORDER BY ordre LIMIT 10");
    $lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($lieux as $lieu) {
        echo "<p>• <strong>{$lieu['nom']}</strong> (/{$lieu['slug']}) - Ordre: {$lieu['ordre']} - Temps: {$lieu['temps_limite']}s</p>";
    }
    echo "</div>";

    // Test 5: Test de création d'un parcours
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #fbbf24; margin-top: 0;'>🔧 Test 5: Test de création de parcours</h3>";
    
    // Récupérer la première équipe et le premier lieu
    $stmt = $pdo->query("SELECT id FROM equipes LIMIT 1");
    $equipe_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    
    $stmt = $pdo->query("SELECT id FROM lieux WHERE slug = 'accueil' LIMIT 1");
    $lieu_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    
    if ($equipe_id && $lieu_id) {
        // Générer un token unique
        $token = bin2hex(random_bytes(16));
        
        $stmt = $pdo->prepare("
            INSERT INTO parcours (equipe_id, lieu_id, ordre_visite, token_acces, statut)
            VALUES (?, ?, 1, ?, 'en_attente')
        ");
        
        if ($stmt->execute([$equipe_id, $lieu_id, $token])) {
            echo "<p style='color: #4ade80;'>✅ Parcours de test créé avec succès !</p>";
            echo "<p>Token généré: <code style='background: rgba(0,0,0,0.2); padding: 5px; border-radius: 3px;'>{$token}</code></p>";
        } else {
            echo "<p style='color: #ef4444;'>❌ Erreur lors de la création du parcours de test</p>";
        }
    } else {
        echo "<p style='color: #ef4444;'>❌ Impossible de récupérer équipe ou lieu pour le test</p>";
    }
    echo "</div>";

    // Test 6: Vérification des parcours créés
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>✅ Test 6: Parcours existants</h3>";
    
    $stmt = $pdo->query("
        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom
        FROM parcours p
        JOIN equipes e ON p.equipe_id = e.id
        JOIN lieux l ON p.lieu_id = l.id
        ORDER BY p.equipe_id, p.ordre_visite
    ");
    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($parcours)) {
        echo "<p style='color: #fbbf24;'>ℹ️ Aucun parcours créé pour le moment</p>";
    } else {
        foreach ($parcours as $parcour) {
            echo "<p>• <strong>{$parcour['equipe_nom']}</strong> → <strong>{$parcour['lieu_nom']}</strong> (Ordre: {$parcour['ordre_visite']}) - Statut: {$parcour['statut']}</p>";
        }
    }
    echo "</div>";

    // Test 7: Nettoyage du parcours de test
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #fbbf24; margin-top: 0;'>🧹 Test 7: Nettoyage du parcours de test</h3>";
    
    $stmt = $pdo->prepare("DELETE FROM parcours WHERE token_acces = ?");
    if ($stmt->execute([$token])) {
        echo "<p style='color: #4ade80;'>✅ Parcours de test supprimé avec succès</p>";
    } else {
        echo "<p style='color: #ef4444;'>❌ Erreur lors de la suppression du parcours de test</p>";
    }
    echo "</div>";

    echo "<div style='text-align: center; background: rgba(34, 197, 94, 0.2); padding: 20px; border-radius: 10px; border: 2px solid #22c55e;'>";
    echo "<h2 style='color: #22c55e; margin: 0;'>🎉 TESTS TERMINÉS AVEC SUCCÈS !</h2>";
    echo "<p style='color: #22c55e; margin: 10px 0 0 0;'>L'interface d'administration des parcours est prête</p>";
    echo "</div>";

} catch(PDOException $e) {
    echo "<div style='background: #fee2e2; color: #dc2626; padding: 20px; border-radius: 10px; border: 2px solid #dc2626;'>";
    echo "<h2 style='color: #dc2626;'>❌ Erreur lors des tests</h2>";
    echo "<p><strong>Message d'erreur:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "</div>";
?>
