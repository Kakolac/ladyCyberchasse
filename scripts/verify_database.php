<?php
/**
 * Script de vérification de la base de données pour la cyberchasse
 * Vérifie la structure et les données
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
    
    echo "✅ Connexion à la base de données 'cyberchasse' réussie\n\n";
    
    // Vérifier la structure de la table
    echo "📋 Structure de la table 'users':\n";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
        echo "  - {$row['Field']}: {$row['Type']} ({$row['Null']} NULL)\n";
    }
    
    // Vérifier les index
    echo "\n🔍 Index de la table:\n";
    $stmt = $pdo->query("SHOW INDEX FROM users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['Key_name']}: {$row['Column_name']}\n";
    }
    
    // Vérifier les équipes
    echo "\n🏆 Équipes enregistrées:\n";
    $stmt = $pdo->query("SELECT id, teamName, email, created_at FROM users ORDER BY id");
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($teams)) {
        echo "  ❌ Aucune équipe trouvée\n";
    } else {
        foreach ($teams as $team) {
            echo "  - ID: {$team['id']}, Équipe: {$team['teamName']}, Email: {$team['email']}, Créée: {$team['created_at']}\n";
        }
    }
    
    // Test de connexion pour l'équipe rouge
    echo "\n🧪 Test de connexion pour l'équipe 'rouge':\n";
    $stmt = $pdo->prepare("SELECT * FROM users WHERE teamName = ?");
    $stmt->execute(['rouge']);
    $team = $stmt->fetch();
    
    if ($team) {
        echo "  ✅ Équipe 'rouge' trouvée\n";
        echo "  📝 Test du mot de passe 'Egour2023#!': ";
        
        if (password_verify('Egour2023#!', $team['password'])) {
            echo "✅ Mot de passe valide\n";
        } else {
            echo "❌ Mot de passe invalide\n";
        }
    } else {
        echo "  ❌ Équipe 'rouge' non trouvée\n";
    }
    
    echo "\n🎉 Vérification terminée !\n";
    
} catch(PDOException $e) {
    die("❌ Erreur lors de la vérification : " . $e->getMessage() . "\n");
}
?>
