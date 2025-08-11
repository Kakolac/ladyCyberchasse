<?php
/**
 * Script de modification de la base de données pour la cyberchasse
 * Modifie la structure pour utiliser teamName et ajoute une équipe de test
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
    
    echo "✅ Connexion à la base de données 'cyberchasse' réussie\n";
    
    // 1. Renommer la colonne username en teamName
    echo "\n�� Modification de la structure de la table...\n";
    
    // Vérifier si la colonne teamName existe déjà
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'teamName'");
    if (!$stmt->fetch()) {
        // Renommer la colonne username en teamName
        $sql = "ALTER TABLE users CHANGE username teamName VARCHAR(50) NOT NULL";
        $pdo->exec($sql);
        echo "✅ Colonne 'username' renommée en 'teamName'\n";
        
        // Mettre à jour l'index
        $pdo->exec("DROP INDEX idx_username ON users");
        $pdo->exec("CREATE INDEX idx_teamName ON users (teamName)");
        echo "✅ Index mis à jour pour 'teamName'\n";
    } else {
        echo "ℹ️  La colonne 'teamName' existe déjà\n";
    }
    
    // 2. Supprimer l'utilisateur admin de test
    $stmt = $pdo->prepare("DELETE FROM users WHERE teamName = ?");
    $stmt->execute(['admin']);
    echo "✅ Utilisateur 'admin' supprimé\n";
    
    // 3. Créer l'équipe de test "rouge"
    $teamName = 'rouge';
    $teamPassword = password_hash('Egour2023#!', PASSWORD_DEFAULT);
    
    // Vérifier si l'équipe existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE teamName = ?");
    $stmt->execute([$teamName]);
    
    if (!$stmt->fetch()) {
        $sql = "INSERT INTO users (teamName, password, email) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$teamName, $teamPassword, 'rouge@cyberchasse.local']);
        echo "✅ Équipe de test 'rouge' créée (mot de passe: Egour2023#!)\n";
    } else {
        echo "ℹ️  L'équipe 'rouge' existe déjà\n";
    }
    
    // 4. Afficher la nouvelle structure
    echo "\n�� Nouvelle structure de la table 'users':\n";
    $stmt = $pdo->query("DESCRIBE users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['Field']}: {$row['Type']} ({$row['Null']} NULL)\n";
    }
    
    // 5. Afficher les équipes existantes
    echo "\n�� Équipes existantes:\n";
    $stmt = $pdo->query("SELECT id, teamName, email, created_at FROM users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - ID: {$row['id']}, Équipe: {$row['teamName']}, Email: {$row['email']}, Créée: {$row['created_at']}\n";
    }
    
    echo "\n�� Modification de la base de données terminée avec succès !\n";
    
} catch(PDOException $e) {
    die("❌ Erreur lors de la modification : " . $e->getMessage() . "\n");
}
?>
