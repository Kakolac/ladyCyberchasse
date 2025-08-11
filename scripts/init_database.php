<?php
/**
 * Script d'initialisation de la base de données pour la cyberchasse
 * Crée la base de données et les tables nécessaires
 */

// Configuration de connexion au serveur MySQL (sans spécifier de base de données)
$host = 'localhost';
$username = 'root';
$password = 'root'; // Mot de passe par défaut de MAMP

try {
    // Connexion au serveur MySQL sans spécifier de base de données
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
    
    echo "✅ Connexion au serveur MySQL réussie\n";
    
    // Création de la base de données si elle n'existe pas
    $sql = "CREATE DATABASE IF NOT EXISTS cyberchasse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    echo "✅ Base de données 'cyberchasse' créée ou déjà existante\n";
    
    // Sélection de la base de données
    $pdo->exec("USE cyberchasse");
    echo "✅ Base de données 'cyberchasse' sélectionnée\n";
    
    // Création de la table des utilisateurs
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_username (username)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "✅ Table 'users' créée ou déjà existante\n";
    
    // Création d'un utilisateur de test (optionnel)
    $testUsername = 'admin';
    $testPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Vérifier si l'utilisateur admin existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$testUsername]);
    
    if (!$stmt->fetch()) {
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$testUsername, $testPassword, 'admin@cyberchasse.local']);
        echo "✅ Utilisateur de test 'admin' créé (mot de passe: admin123)\n";
    } else {
        echo "ℹ️  L'utilisateur 'admin' existe déjà\n";
    }
    
    // Affichage de la structure de la table
    echo "\n📋 Structure de la table 'users':\n";
    $stmt = $pdo->query("DESCRIBE users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['Field']}: {$row['Type']} ({$row['Null']} NULL)\n";
    }
    
    // Affichage des utilisateurs existants
    echo "\n�� Utilisateurs existants:\n";
    $stmt = $pdo->query("SELECT id, username, email, created_at FROM users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - ID: {$row['id']}, Username: {$row['username']}, Email: {$row['email']}, Créé: {$row['created_at']}\n";
    }
    
    echo "\n🎉 Initialisation de la base de données terminée avec succès !\n";
    
} catch(PDOException $e) {
    die("❌ Erreur lors de l'initialisation : " . $e->getMessage() . "\n");
}
?>
