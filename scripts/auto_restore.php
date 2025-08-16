#!/usr/bin/env php
<?php
/**
 * Script de restauration automatique des bases de données
 * Restaure automatiquement toutes les bases depuis un fichier de sauvegarde
 */

// Configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '1G');

// Chargement de la configuration
require_once __DIR__ . '/../config/env.php';

// Couleurs pour la console
class Colors {
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const CYAN = "\033[36m";
    const WHITE = "\033[37m";
    const BOLD = "\033[1m";
    const RESET = "\033[0m";
}

function printMessage($message, $color = Colors::WHITE, $bold = false) {
    $prefix = $bold ? Colors::BOLD : '';
    echo $prefix . $color . $message . Colors::RESET . "\n";
}

// Fonction pour obtenir la connexion MySQL
function getMySQLConnection() {
    $host = env('DB_HOST', 'localhost');
    $username = env('DB_USER', 'root');
    $password = env('DB_PASS', 'root');
    
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET NAMES utf8");
        return $pdo;
    } catch(PDOException $e) {
        throw new Exception("Erreur de connexion MySQL : " . $e->getMessage());
    }
}

// Fonction pour lister les sauvegardes disponibles
function listBackups() {
    $backupDir = __DIR__ . '/../backups/';
    $backups = [];
    
    if (is_dir($backupDir)) {
        $files = glob($backupDir . '*.sql');
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'date' => filemtime($file)
            ];
        }
        
        // Trier par date (plus récent en premier)
        usort($backups, function($a, $b) {
            return $b['date'] - $a['date'];
        });
    }
    
    return $backups;
}

// Fonction pour restaurer depuis une sauvegarde
function restoreFromBackup($pdo, $backupFile, $verbose = false) {
    if (!file_exists($backupFile)) {
        throw new Exception("Fichier de sauvegarde introuvable : $backupFile");
    }
    
    if ($verbose) {
        printMessage("Restauration depuis : $backupFile", Colors::CYAN);
    }
    
    // Lire le fichier de sauvegarde
    $sqlContent = file_get_contents($backupFile);
    
    // Extraire les noms des bases de données
    preg_match_all('/CREATE DATABASE IF NOT EXISTS `([^`]+)`/', $sqlContent, $matches);
    $databases = $matches[1] ?? [];
    
    if ($verbose) {
        printMessage("Bases à restaurer : " . implode(', ', $databases), Colors::YELLOW);
    }
    
    $successCount = 0;
    $errorCount = 0;
    
    // Restaurer chaque base
    foreach ($databases as $database) {
        try {
            printMessage("Restauration de : $database", Colors::GREEN);
            
            // Supprimer la base si elle existe
            $pdo->exec("DROP DATABASE IF EXISTS `$database`");
            
            // Créer la base
            $pdo->exec("CREATE DATABASE `$database`");
            $pdo->exec("USE `$database`");
            
            // Exécuter les requêtes pour cette base
            $pattern = '/CREATE DATABASE IF NOT EXISTS `' . preg_quote($database, '/') . '`.*?USE `' . preg_quote($database, '/') . '`.*?(?=CREATE DATABASE|SET FOREIGN_KEY_CHECKS|$)/s';
            if (preg_match($pattern, $sqlContent, $match)) {
                $databaseSQL = $match[0];
                $queries = explode(';', $databaseSQL);
                
                foreach ($queries as $query) {
                    $query = trim($query);
                    if (!empty($query) && !preg_match('/^--/', $query) && !preg_match('/^CREATE DATABASE/', $query) && !preg_match('/^USE/', $query)) {
                        try {
                            $pdo->exec($query);
                        } catch (Exception $e) {
                            if ($verbose) {
                                printMessage("  Erreur requête : " . $e->getMessage(), Colors::RED);
                            }
                            $errorCount++;
                        }
                    }
                }
                
                $successCount++;
                printMessage("  ✅ Base $database restaurée avec succès", Colors::GREEN);
                
            } else {
                printMessage("  ⚠️ Aucune donnée trouvée pour $database", Colors::YELLOW);
            }
            
        } catch (Exception $e) {
            printMessage("  ❌ Erreur lors de la restauration de $database : " . $e->getMessage(), Colors::RED);
            $errorCount++;
        }
    }
    
    printMessage("\nRésumé de la restauration :", Colors::CYAN);
    printMessage("  - Bases restaurées avec succès : $successCount", Colors::GREEN);
    printMessage("  - Erreurs rencontrées : $errorCount", Colors::RED);
    
    return $successCount > 0;
}

// Fonction principale
function main() {
    printMessage("🔄 Script de restauration automatique MySQL", Colors::CYAN, true);
    printMessage("==========================================", Colors::WHITE);
    
    try {
        // Vérifier la connexion
        printMessage("🔌 Test de connexion MySQL...", Colors::YELLOW);
        $pdo = getMySQLConnection();
        printMessage("✅ Connexion MySQL établie", Colors::GREEN);
        
        // Lister les sauvegardes
        printMessage("\n📁 Recherche des sauvegardes...", Colors::YELLOW);
        $backups = listBackups();
        
        if (empty($backups)) {
            printMessage("❌ Aucune sauvegarde trouvée dans le dossier backups/", Colors::RED);
            printMessage("💡 Utilisez d'abord le script d'export pour créer une sauvegarde", Colors::YELLOW);
            exit(1);
        }
        
        printMessage("📋 Sauvegardes disponibles :", Colors::GREEN);
        foreach ($backups as $index => $backup) {
            $size = number_format($backup['size'] / 1024 / 1024, 2);
            $date = date('d/m/Y H:i:s', $backup['date']);
            echo sprintf("  %d. %s (%s MB) - %s\n", 
                $index + 1, 
                $backup['name'], 
                $size, 
                $date
            );
        }
        
        // Demander confirmation
        echo "\n";
        printMessage("⚠️  ATTENTION : Cette opération va écraser toutes les bases existantes !", Colors::RED, true);
        printMessage("Êtes-vous sûr de vouloir continuer ? (oui/non) : ", Colors::YELLOW);
        
        $handle = fopen("php://stdin", "r");
        $response = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($response) !== 'oui') {
            printMessage("❌ Restauration annulée par l'utilisateur", Colors::YELLOW);
            exit(0);
        }
        
        // Choisir la sauvegarde (par défaut la plus récente)
        $selectedBackup = $backups[0];
        if (count($backups) > 1) {
            printMessage("\n💾 Quelle sauvegarde voulez-vous restaurer ? (1-" . count($backups) . ") [1] : ", Colors::YELLOW);
            $handle = fopen("php://stdin", "r");
            $choice = trim(fgets($handle));
            fclose($handle);
            
            if (!empty($choice) && is_numeric($choice) && $choice >= 1 && $choice <= count($backups)) {
                $selectedBackup = $backups[$choice - 1];
            }
        }
        
        printMessage("\n🚀 Début de la restauration depuis : " . $selectedBackup['name'], Colors::CYAN);
        
        // Restaurer
        $startTime = microtime(true);
        $success = restoreFromBackup($pdo, $selectedBackup['path'], true);
        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);
        
        if ($success) {
            printMessage("\n🎉 Restauration terminée avec succès en {$duration}s !", Colors::GREEN, true);
        } else {
            printMessage("\n❌ Restauration terminée avec des erreurs", Colors::RED);
        }
        
    } catch (Exception $e) {
        printMessage("💥 Erreur fatale : " . $e->getMessage(), Colors::RED);
        exit(1);
    }
}

// Exécution du script
if (php_sapi_name() === 'cli') {
    main();
} else {
    die("Ce script doit être exécuté en ligne de commande");
}
?>
