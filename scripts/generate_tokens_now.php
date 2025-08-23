<?php
require_once '../config/connexion.php';

$parcours_id = 1; // ID de votre parcours

echo "<h2>�� Génération des Tokens pour le Parcours ID: {$parcours_id}</h2>";

try {
    // 1. Vérifier l'état actuel
    echo "<h3>1. État actuel :</h3>";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_equipes_parcours WHERE parcours_id = ?");
    $stmt->execute([$parcours_id]);
    $nb_equipes = $stmt->fetchColumn();
    echo "<p>✅ Équipes assignées : {$nb_equipes}</p>";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_parcours_lieux WHERE parcours_id = ?");
    $stmt->execute([$parcours_id]);
    $nb_lieux = $stmt->fetchColumn();
    echo "<p>✅ Lieux dans le parcours : {$nb_lieux}</p>";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_token WHERE parcours_id = ?");
    $stmt->execute([$parcours_id]);
    $nb_tokens = $stmt->fetchColumn();
    echo "<p>�� Tokens existants : {$nb_tokens}</p>";
    
    // 2. Générer les tokens manquants
    if ($nb_equipes > 0 && $nb_lieux > 0) {
        echo "<h3>2. Génération des tokens :</h3>";
        
        // Récupérer toutes les équipes assignées
        $stmt = $pdo->prepare("SELECT equipe_id FROM cyber_equipes_parcours WHERE parcours_id = ?");
        $stmt->execute([$parcours_id]);
        $equipes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Récupérer tous les lieux du parcours
        $stmt = $pdo->prepare("SELECT lieu_id, ordre FROM cyber_parcours_lieux WHERE parcours_id = ? ORDER BY ordre");
        $stmt->execute([$parcours_id]);
        $lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $tokens_generes = 0;
        $tokens_existants = 0;
        
        foreach ($equipes as $equipe_id) {
            foreach ($lieux as $lieu) {
                // Vérifier si le token existe déjà
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) FROM cyber_token 
                    WHERE equipe_id = ? AND parcours_id = ? AND lieu_id = ?
                ");
                $stmt->execute([$equipe_id, $parcours_id, $lieu['lieu_id']]);
                
                if ($stmt->fetchColumn() == 0) {
                    // Générer un nouveau token
                    $token = bin2hex(random_bytes(16));
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO cyber_token (equipe_id, parcours_id, lieu_id, token_acces, ordre_visite, statut) 
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    
                    if ($stmt->execute([$equipe_id, $parcours_id, $lieu['lieu_id'], $token, $lieu['ordre'], 'en_attente'])) {
                        $tokens_generes++;
                        echo "<p style='color: green;'>✅ Token généré pour équipe ID {$equipe_id}, lieu ID {$lieu['lieu_id']}</p>";
                    } else {
                        echo "<p style='color: red;'>❌ Erreur lors de la génération du token</p>";
                    }
                } else {
                    $tokens_existants++;
                }
            }
        }
        
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
        echo "<h4>📊 Résumé de la génération :</h4>";
        echo "<p><strong>Tokens existants :</strong> {$tokens_existants}</p>";
        echo "<p><strong>Nouveaux tokens générés :</strong> {$tokens_generes}</p>";
        echo "<p><strong>Total après génération :</strong> " . ($tokens_existants + $tokens_generes) . "</p>";
        echo "</div>";
        
    } else {
        echo "<p style='color: red;'>❌ Impossible de générer des tokens : conditions non remplies</p>";
    }
    
    // 3. Vérification finale
    echo "<h3>3. Vérification finale :</h3>";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_token WHERE parcours_id = ?");
    $stmt->execute([$parcours_id]);
    $nb_tokens_final = $stmt->fetchColumn();
    echo "<p>�� Total des tokens : {$nb_tokens_final}</p>";
    
    // 4. Afficher les détails des tokens
    if ($nb_tokens_final > 0) {
        echo "<h3>4. Détail des tokens :</h3>";
        $stmt = $pdo->prepare("
            SELECT ct.*, e.nom as equipe_nom, e.couleur, l.nom as lieu_nom
            FROM cyber_token ct
            JOIN cyber_equipes e ON ct.equipe_id = e.id
            JOIN cyber_lieux l ON ct.lieu_id = l.id
            WHERE ct.parcours_id = ?
            ORDER BY ct.equipe_id, ct.ordre_visite
        ");
        $stmt->execute([$parcours_id]);
        $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table class='table table-striped' style='background: white;'>";
        echo "<thead class='table-dark'><tr><th>Équipe</th><th>Lieu</th><th>Ordre</th><th>Token</th><th>Statut</th></tr></thead>";
        echo "<tbody>";
        foreach ($tokens as $token) {
            echo "<tr>";
            echo "<td><span style='color: {$token['couleur']};'>●</span> {$token['equipe_nom']}</td>";
            echo "<td>{$token['lieu_nom']}</td>";
            echo "<td><span class='badge bg-secondary'>{$token['ordre_visite']}</span></td>";
            echo "<td><code style='background: #f8f9fa; padding: 2px 4px; border-radius: 3px;'>" . substr($token['token_acces'], 0, 8) . "...</code></td>";
            echo "<td><span class='badge bg-warning'>{$token['statut']}</span></td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        
        echo "<div style='background: #cce5ff; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
        echo "<h4>🎯 Prochaines étapes :</h4>";
        echo "<p>1. <strong>Retournez sur la page de gestion des tokens</strong> : <a href='../admin/modules/parcours/token_manager.php?id={$parcours_id}' target='_blank'>Gérer les Tokens</a></p>";
        echo "<p>2. <strong>Vérifiez les statistiques</strong> - vous devriez voir {$nb_tokens_final} tokens</p>";
        echo "<p>3. <strong>Testez le bouton</strong> 'Générer Tous les Tokens' - il devrait maintenant afficher des statistiques</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : {$e->getMessage()}</p>";
    echo "<p><strong>Fichier :</strong> {$e->getFile()}</p>";
    echo "<p><strong>Ligne :</strong> {$e->getLine()}</p>";
}
?>
