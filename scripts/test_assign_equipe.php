<?php
require_once '../config/connexion.php';

$parcours_id = 1; // ID de votre parcours

echo "<h2>Test d'assignation d'équipe au parcours ID: {$parcours_id}</h2>";

try {
    // 1. Vérifier les équipes disponibles
    $stmt = $pdo->query("SELECT id, nom, couleur, statut FROM cyber_equipes WHERE statut = 'active' ORDER BY nom");
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>1. Équipes disponibles :</h3>";
    if (empty($equipes)) {
        echo "<p>❌ Aucune équipe active trouvée</p>";
    } else {
        echo "<ul>";
        foreach ($equipes as $equipe) {
            echo "<li><span style='color: {$equipe['couleur']};'>●</span> {$equipe['nom']} (ID: {$equipe['id']})</li>";
        }
        echo "</ul>";
    }
    
    // 2. Vérifier si des équipes sont déjà assignées
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_equipes_parcours WHERE parcours_id = ?");
    $stmt->execute([$parcours_id]);
    $nb_assignees = $stmt->fetchColumn();
    echo "<h3>2. Équipes déjà assignées : {$nb_assignees}</h3>";
    
    // 3. Assigner la première équipe disponible si aucune n'est assignée
    if ($nb_assignees == 0 && !empty($equipes)) {
        $equipe_id = $equipes[0]['id'];
        
        $stmt = $pdo->prepare("
            INSERT INTO cyber_equipes_parcours (equipe_id, parcours_id, statut, date_debut) 
            VALUES (?, ?, 'en_cours', NOW())
        ");
        
        if ($stmt->execute([$equipe_id, $parcours_id])) {
            echo "<div style='color: green;'>✅ Équipe '{$equipes[0]['nom']}' assignée au parcours avec succès !</div>";
            
            // 4. Maintenant tester la génération de tokens
            echo "<h3>3. Test de génération de tokens :</h3>";
            
            // Récupérer les lieux du parcours
            $stmt = $pdo->prepare("SELECT lieu_id, ordre FROM cyber_parcours_lieux WHERE parcours_id = ? ORDER BY ordre");
            $stmt->execute([$parcours_id]);
            $lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $tokens_generes = 0;
            foreach ($lieux as $lieu) {
                $token = bin2hex(random_bytes(16));
                
                // CORRECTION : Ajouter le paramètre manquant pour 'statut'
                $stmt = $pdo->prepare("
                    INSERT INTO cyber_token (equipe_id, parcours_id, lieu_id, token_acces, ordre_visite, statut) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                if ($stmt->execute([$equipe_id, $parcours_id, $lieu['lieu_id'], $token, $lieu['ordre'], 'en_attente'])) {
                    $tokens_generes++;
                }
            }
            
            echo "<div style='color: green;'>✅ {$tokens_generes} tokens générés avec succès !</div>";
            
        } else {
            echo "<div style='color: red;'>❌ Erreur lors de l'assignation de l'équipe</div>";
        }
    } else {
        echo "<p>ℹ️ Des équipes sont déjà assignées ou aucune équipe disponible</p>";
    }
    
    // 5. Vérification finale
    echo "<h3>4. Vérification finale :</h3>";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_equipes_parcours WHERE parcours_id = ?");
    $stmt->execute([$parcours_id]);
    $nb_equipes = $stmt->fetchColumn();
    echo "<p>Équipes assignées : {$nb_equipes}</p>";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_token WHERE parcours_id = ?");
    $stmt->execute([$parcours_id]);
    $nb_tokens = $stmt->fetchColumn();
    echo "<p>Tokens générés : {$nb_tokens}</p>";
    
    // 6. Afficher les tokens générés
    if ($nb_tokens > 0) {
        echo "<h3>5. Détail des tokens générés :</h3>";
        $stmt = $pdo->prepare("
            SELECT ct.*, e.nom as equipe_nom, l.nom as lieu_nom
            FROM cyber_token ct
            JOIN cyber_equipes e ON ct.equipe_id = e.id
            JOIN cyber_lieux l ON ct.lieu_id = l.id
            WHERE ct.parcours_id = ?
            ORDER BY ct.ordre_visite
        ");
        $stmt->execute([$parcours_id]);
        $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table class='table table-striped'>";
        echo "<thead><tr><th>Équipe</th><th>Lieu</th><th>Ordre</th><th>Token</th><th>Statut</th></tr></thead>";
        echo "<tbody>";
        foreach ($tokens as $token) {
            echo "<tr>";
            echo "<td>{$token['equipe_nom']}</td>";
            echo "<td>{$token['lieu_nom']}</td>";
            echo "<td>{$token['ordre_visite']}</td>";
            echo "<td><code>" . substr($token['token_acces'], 0, 8) . "...</code></td>";
            echo "<td>{$token['statut']}</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : {$e->getMessage()}</p>";
    
    // Afficher plus de détails sur l'erreur
    echo "<h3>Détails de l'erreur :</h3>";
    echo "<p><strong>Message :</strong> {$e->getMessage()}</p>";
    echo "<p><strong>Fichier :</strong> {$e->getFile()}</p>";
    echo "<p><strong>Ligne :</strong> {$e->getLine()}</p>";
}
?>
