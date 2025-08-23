<?php
require_once '../config/connexion.php';

$parcours_id = 1; // Remplacez par l'ID de votre parcours

echo "<h2>Test de génération de tokens pour le parcours ID: {$parcours_id}</h2>";

try {
    // Vérifier les équipes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_equipes_parcours WHERE parcours_id = ?");
    $stmt->execute([$parcours_id]);
    $nb_equipes = $stmt->fetchColumn();
    echo "<p>Équipes assignées : {$nb_equipes}</p>";
    
    // Vérifier les lieux
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_parcours_lieux WHERE parcours_id = ?");
    $stmt->execute([$parcours_id]);
    $nb_lieux = $stmt->fetchColumn();
    echo "<p>Lieux dans le parcours : {$nb_lieux}</p>";
    
    if ($nb_equipes > 0 && $nb_lieux > 0) {
        echo "<p>✅ Conditions remplies, génération possible</p>";
        
        // Générer un token de test
        $token = bin2hex(random_bytes(16));
        echo "<p>Token généré : {$token}</p>";
        
        // Insérer dans la base
        $stmt = $pdo->prepare("
            INSERT INTO cyber_token (equipe_id, parcours_id, lieu_id, token_acces, ordre_visite, statut) 
            VALUES (?, ?, ?, ?, ?, 'en_attente')
        ");
        
        // Récupérer la première équipe et le premier lieu
        $stmt_equipe = $pdo->prepare("SELECT equipe_id FROM cyber_equipes_parcours WHERE parcours_id = ? LIMIT 1");
        $stmt_equipe->execute([$parcours_id]);
        $equipe_id = $stmt_equipe->fetchColumn();
        
        $stmt_lieu = $pdo->prepare("SELECT lieu_id FROM cyber_parcours_lieux WHERE parcours_id = ? LIMIT 1");
        $stmt_lieu->execute([$parcours_id]);
        $lieu_id = $stmt_lieu->fetchColumn();
        
        if ($stmt->execute([$equipe_id, $parcours_id, $lieu_id, $token, 1, 'en_attente'])) {
            echo "<p>✅ Token inséré avec succès dans la base !</p>";
        } else {
            echo "<p>❌ Erreur lors de l'insertion</p>";
        }
        
    } else {
        echo "<p>❌ Conditions non remplies</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Erreur : {$e->getMessage()}</p>";
}
?>
