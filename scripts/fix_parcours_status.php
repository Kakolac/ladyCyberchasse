<?php
require_once '../config/connexion.php';

echo "<div style='font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.3);'>";
echo "<h1 style='text-align: center; margin-bottom: 30px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);'>🔧 Correction des statuts de parcours</h1>";

try {
    // 1. Vérifier les parcours existants
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>📊 État actuel des parcours</h3>";
    
    $stmt = $pdo->query("
        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
        FROM parcours p
        JOIN equipes e ON p.equipe_id = e.id
        JOIN lieux l ON p.lieu_id = l.id
        ORDER BY p.equipe_id, p.ordre_visite
    ");
    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($parcours)) {
        echo "<p style='color: #fbbf24;'>ℹ️ Aucun parcours trouvé</p>";
    } else {
        foreach ($parcours as $parcour) {
            $status_color = $parcour['statut'] === 'termine' ? '#ef4444' : '#4ade80';
            echo "<p>• <strong>{$parcour['equipe_nom']}</strong> → <strong>{$parcour['lieu_nom']}</strong> ({$parcour['lieu_slug']}) - <span style='color: {$status_color}'>{$parcour['statut']}</span></p>";
        }
    }
    echo "</div>";

    // 2. Option de remise à zéro des statuts
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_status'])) {
        echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
        echo "<h3 style='color: #4ade80; margin-top: 0;'>🔄 Remise à zéro des statuts</h3>";
        
        $stmt = $pdo->prepare("UPDATE parcours SET statut = 'en_attente', score_obtenu = 0, temps_debut = NULL, temps_fin = NULL, temps_ecoule = 0");
        if ($stmt->execute()) {
            echo "<p style='color: #4ade80;'>✅ Tous les parcours ont été remis à l'état 'en_attente'</p>";
        } else {
            echo "<p style='color: #ef4444;'>❌ Erreur lors de la remise à zéro</p>";
        }
        echo "</div>";
    }

    // 3. Option de suppression des parcours spécifiques
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_parcours'])) {
        $parcours_id = $_POST['parcours_id'];
        
        echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
        echo "<h3 style='color: #4ade80; margin-top: 0;'>🗑️ Suppression de parcours</h3>";
        
        $stmt = $pdo->prepare("DELETE FROM parcours WHERE id = ?");
        if ($stmt->execute([$parcours_id])) {
            echo "<p style='color: #4ade80;'>✅ Parcours supprimé avec succès</p>";
        } else {
            echo "<p style='color: #ef4444;'>❌ Erreur lors de la suppression</p>";
        }
        echo "</div>";
    }

    // 4. Formulaire de remise à zéro
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #60a5fa; margin-top: 0;'>🔄 Actions disponibles</h3>";
    
    echo "<form method='POST' style='margin-bottom: 20px;'>";
    echo "<button type='submit' name='reset_status' class='btn btn-warning' style='background: #f59e0b; border: none; color: white; padding: 10px 20px; border-radius: 5px; cursor: pointer;'>🔄 Remettre tous les parcours à 'en_attente'</button>";
    echo "</form>";
    
    echo "<p style='color: #fbbf24;'>⚠️ <strong>Attention :</strong> Cette action remettra tous les parcours à l'état initial.</p>";
    echo "</div>";

    // 5. Liste des parcours avec options de suppression
    if (!empty($parcours)) {
        echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
        echo "<h3 style='color: #60a5fa; margin-top: 0;'>🗑️ Supprimer des parcours spécifiques</h3>";
        
        foreach ($parcours as $parcour) {
            echo "<div style='background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; margin-bottom: 10px;'>";
            echo "<p><strong>{$parcour['equipe_nom']}</strong> → <strong>{$parcour['lieu_nom']}</strong> ({$parcour['lieu_slug']})</p>";
            echo "<p>Statut: <span style='color: " . ($parcour['statut'] === 'termine' ? '#ef4444' : '#4ade80') . "'>{$parcour['statut']}</span></p>";
            
            echo "<form method='POST' style='display: inline;'>";
            echo "<input type='hidden' name='parcours_id' value='{$parcour['id']}'>";
            echo "<button type='submit' name='delete_parcours' class='btn btn-danger' style='background: #ef4444; border: none; color: white; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 12px;'>️ Supprimer</button>";
            echo "</form>";
            echo "</div>";
        }
        echo "</div>";
    }

} catch (Exception $e) {
    echo "<div style='background: rgba(239,68,68,0.2); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #ef4444; margin-top: 0;'>❌ Erreur</h3>";
    echo "<p>{$e->getMessage()}</p>";
    echo "</div>";
}

echo "</div>";
?>
