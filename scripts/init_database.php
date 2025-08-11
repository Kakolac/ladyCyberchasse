<?php
/**
 * Script d'initialisation de la base de données pour la cyberchasse
 * Crée la base de données et les tables nécessaires pour le système complet
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
    
    echo "<div style='font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.3);'>";
    echo "<h1 style='text-align: center; margin-bottom: 30px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);'>🚀 Initialisation Base de Données Cyberchasse</h1>";
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #4ade80; margin-top: 0;'>✅ Connexion au serveur MySQL réussie</h3>";
    
    // Création de la base de données si elle n'existe pas
    $sql = "CREATE DATABASE IF NOT EXISTS cyberchasse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    echo "<p style='color: #4ade80;'>✅ Base de données 'cyberchasse' créée ou déjà existante</p>";
    
    // Sélection de la base de données
    $pdo->exec("USE cyberchasse");
    echo "<p style='color: #4ade80;'>✅ Base de données 'cyberchasse' sélectionnée</p>";
    echo "</div>";
    
    // ===== CRÉATION DES TABLES =====
    
    // 1. Table des équipes
    $sql = "CREATE TABLE IF NOT EXISTS equipes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(50) NOT NULL UNIQUE,
        couleur VARCHAR(20) NOT NULL,
        mot_de_passe VARCHAR(255) NOT NULL,
        statut ENUM('active', 'inactive', 'terminee') DEFAULT 'active',
        temps_total INT DEFAULT 0 COMMENT 'Temps total en secondes',
        score INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_nom (nom),
        INDEX idx_statut (statut)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-bottom: 15px;'>";
    echo "<p style='color: #4ade80;'>✅ Table 'equipes' créée ou déjà existante</p>";
    echo "</div>";
    
    // 2. Table des lieux
    $sql = "CREATE TABLE IF NOT EXISTS lieux (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL UNIQUE,
        slug VARCHAR(100) NOT NULL UNIQUE COMMENT 'URL friendly',
        description TEXT,
        ordre INT DEFAULT 0 COMMENT 'Ordre dans le parcours',
        temps_limite INT DEFAULT 300 COMMENT 'Temps limite en secondes',
        enigme_requise BOOLEAN DEFAULT FALSE,
        statut ENUM('actif', 'inactif') DEFAULT 'actif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_slug (slug),
        INDEX idx_ordre (ordre),
        INDEX idx_statut (statut)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-bottom: 15px;'>";
    echo "<p style='color: #4ade80;'>✅ Table 'lieux' créée ou déjà existante</p>";
    echo "</div>";
    
    // 3. Table des parcours (relation équipe-lieux)
    $sql = "CREATE TABLE IF NOT EXISTS parcours (
        id INT AUTO_INCREMENT PRIMARY KEY,
        equipe_id INT NOT NULL,
        lieu_id INT NOT NULL,
        ordre_visite INT NOT NULL COMMENT 'Ordre de visite pour cette équipe',
        token_acces VARCHAR(255) UNIQUE NOT NULL COMMENT 'Token unique pour accéder au lieu',
        qr_code_generer BOOLEAN DEFAULT FALSE,
        statut ENUM('en_attente', 'en_cours', 'termine', 'echec') DEFAULT 'en_attente',
        temps_debut TIMESTAMP NULL COMMENT 'Début du timer',
        temps_fin TIMESTAMP NULL COMMENT 'Fin du timer',
        temps_ecoule INT DEFAULT 0 COMMENT 'Temps écoulé en secondes',
        score_obtenu INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (equipe_id) REFERENCES equipes(id) ON DELETE CASCADE,
        FOREIGN KEY (lieu_id) REFERENCES lieux(id) ON DELETE CASCADE,
        UNIQUE KEY unique_equipe_lieu (equipe_id, lieu_id),
        INDEX idx_token (token_acces),
        INDEX idx_statut (statut),
        INDEX idx_ordre (ordre_visite)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-bottom: 15px;'>";
    echo "<p style='color: #4ade80;'>✅ Table 'parcours' créée ou déjà existante</p>";
    echo "</div>";
    
    // 4. Table des sessions de jeu
    $sql = "CREATE TABLE IF NOT EXISTS sessions_jeu (
        id INT AUTO_INCREMENT PRIMARY KEY,
        equipe_id INT NOT NULL,
        lieu_id INT NOT NULL,
        session_id VARCHAR(255) NOT NULL,
        token_validation VARCHAR(255) NOT NULL,
        statut ENUM('active', 'terminee', 'expiree') DEFAULT 'active',
        temps_debut TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        temps_fin TIMESTAMP NULL,
        temps_restant INT DEFAULT 0 COMMENT 'Temps restant en secondes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (equipe_id) REFERENCES equipes(id) ON DELETE CASCADE,
        FOREIGN KEY (lieu_id) REFERENCES lieux(id) ON DELETE CASCADE,
        INDEX idx_session (session_id),
        INDEX idx_token (token_validation),
        INDEX idx_statut (statut)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-bottom: 15px;'>";
    echo "<p style='color: #4ade80;'>✅ Table 'sessions_jeu' créée ou déjà existante</p>";
    echo "</div>";
    
    // 5. Table des logs d'activité
    $sql = "CREATE TABLE IF NOT EXISTS logs_activite (
        id INT AUTO_INCREMENT PRIMARY KEY,
        equipe_id INT NULL,
        lieu_id INT NULL,
        action VARCHAR(100) NOT NULL COMMENT 'Type d''action (connexion, acces_lieu, validation, etc.)',
        details TEXT,
        ip_address VARCHAR(45),
        user_agent TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_equipe (equipe_id),
        INDEX idx_lieu (lieu_id),
        INDEX idx_action (action),
        INDEX idx_date (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-bottom: 15px;'>";
    echo "<p style='color: #4ade80;'>✅ Table 'logs_activite' créée ou déjà existante</p>";
    echo "</div>";
    
    // ===== INSERTION DES DONNÉES DE TEST =====
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #fbbf24; margin-top: 0;'>🔧 Insertion des données de test</h3>";
    
    // Insertion des équipes existantes
    $equipes = [
        ['Rouge', 'red', 'Egour2023#!'],
        ['Bleu', 'blue', 'Uelb2023#!'],
        ['Vert', 'green', 'Trev2023#!'],
        ['Jaune', 'yellow', 'Enuaj2023#!']
    ];
    
    foreach ($equipes as $equipe) {
        $stmt = $pdo->prepare("SELECT id FROM equipes WHERE nom = ?");
        $stmt->execute([$equipe[0]]);
        
        if (!$stmt->fetch()) {
            $sql = "INSERT INTO equipes (nom, couleur, mot_de_passe) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$equipe[0], $equipe[1], password_hash($equipe[2], PASSWORD_DEFAULT)]);
            echo "<p style='color: #4ade80;'>✅ Équipe '{$equipe[0]}' créée</p>";
        } else {
            echo "<p style='color: #fbbf24;'>ℹ️  Équipe '{$equipe[0]}' existe déjà</p>";
        }
    }
    
    // Insertion des lieux existants
    $lieux = [
        ['Accueil', 'accueil', 'Point de départ de la cyberchasse', 1, 120],
        ['Cantine', 'cantine', 'Zone de restauration', 2, 300],
        ['CDI', 'cdi', 'Centre de Documentation et d\'Information', 3, 420],
        ['Cour', 'cour', 'Espace extérieur', 4, 180],
        ['Direction', 'direction', 'Bureau de la direction', 5, 360],
        ['Gymnase', 'gymnase', 'Salle de sport', 6, 240],
        ['Infirmerie', 'infirmerie', 'Zone médicale', 7, 300],
        ['Internat', 'internat', 'Zone d\'hébergement', 8, 360],
        ['Labo Chimie', 'labo_chimie', 'Laboratoire de chimie', 9, 480],
        ['Labo Physique', 'labo_physique', 'Laboratoire de physique', 10, 480],
        ['Labo SVT', 'labo_svt', 'Laboratoire de SVT', 11, 480],
        ['Salle Arts', 'salle_arts', 'Salle d\'arts plastiques', 12, 300],
        ['Salle Info', 'salle_info', 'Salle informatique', 13, 420],
        ['Salle Langues', 'salle_langues', 'Salle de langues', 14, 300],
        ['Salle Musique', 'salle_musique', 'Salle de musique', 15, 300],
        ['Salle Profs', 'salle_profs', 'Salle des professeurs', 16, 240],
        ['Salle Réunion', 'salle_reunion', 'Salle de réunion', 17, 360],
        ['Secrétariat', 'secretariat', 'Bureau du secrétariat', 18, 300],
        ['Vie Scolaire', 'vie_scolaire', 'Bureau de la vie scolaire', 19, 300],
        ['Atelier Techno', 'atelier_techno', 'Atelier de technologie', 20, 480]
    ];
    
    foreach ($lieux as $lieu) {
        $stmt = $pdo->prepare("SELECT id FROM lieux WHERE slug = ?");
        $stmt->execute([$lieu[1]]);
        
        if (!$stmt->fetch()) {
            $sql = "INSERT INTO lieux (nom, slug, description, ordre, temps_limite) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($lieu);
            echo "<p style='color: #4ade80;'>✅ Lieu '{$lieu[0]}' créé</p>";
        } else {
            echo "<p style='color: #fbbf24;'>ℹ️  Lieu '{$lieu[0]}' existe déjà</p>";
        }
    }
    
    echo "</div>";
    
    // ===== AFFICHAGE DE LA STRUCTURE =====
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #60a5fa; margin-top: 0;'>📋 Structure de la base de données</h3>";
    
    $tables = ['equipes', 'lieux', 'parcours', 'sessions_jeu', 'logs_activite'];
    
    foreach ($tables as $table) {
        echo "<details style='margin-bottom: 15px;'>";
        echo "<summary style='cursor: pointer; color: #60a5fa; font-weight: bold;'>📊 Table: {$table}</summary>";
        echo "<div style='background: rgba(0,0,0,0.2); padding: 10px; border-radius: 5px; margin-top: 10px;'>";
        
        $stmt = $pdo->query("DESCRIBE {$table}");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $null = $row['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
            echo "<p style='margin: 5px 0; font-family: monospace;'>• {$row['Field']}: {$row['Type']} ({$null})</p>";
        }
        echo "</div>";
        echo "</details>";
    }
    
    echo "</div>";
    
    // ===== STATISTIQUES =====
    
    echo "<div style='background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #a78bfa; margin-top: 0;'> Statistiques de la base</h3>";
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p style='color: #a78bfa;'>• Table '{$table}': {$count} enregistrement(s)</p>";
    }
    
    echo "</div>";
    
    echo "<div style='text-align: center; background: rgba(34, 197, 94, 0.2); padding: 20px; border-radius: 10px; border: 2px solid #22c55e;'>";
    echo "<h2 style='color: #22c55e; margin: 0;'>🎉 ÉTAPE 1 TERMINÉE AVEC SUCCÈS !</h2>";
    echo "<p style='color: #22c55e; margin: 10px 0 0 0;'>La base de données est prête pour le système de cyberchasse</p>";
    echo "</div>";
    
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background: #fee2e2; color: #dc2626; border-radius: 10px; border: 2px solid #dc2626;'>";
    echo "<h2 style='color: #dc2626;'>❌ Erreur lors de l'initialisation</h2>";
    echo "<p><strong>Message d'erreur:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Code d'erreur:</strong> " . $e->getCode() . "</p>";
    echo "</div>";
}
?>
