#!/usr/bin/env php
<?php
/**
 * Gestionnaire MySQL en ligne de commande
 * Permet d'exporter et d'importer toutes les bases de données
 * 
 * Usage: php mysql_cli_manager.php [COMMANDE] [OPTIONS]
 */

// Configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 0); // Pas de limite de temps
ini_set('memory_limit', '1G');

// Chargement de la configuration
require_once __DIR__ . '/../config/env.php';

// Couleurs pour la console
class Colors {
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const MAGENTA = "\033[35m";
    const CYAN = "\033[36m";
    const WHITE = "\033[37m";
    const BOLD = "\033[1m";
    const RESET = "\033[0m";
}

// Fonction pour afficher les messages colorés
function printMessage($message, $color = Colors::WHITE, $bold = false) {
    $prefix = $bold ? Colors::BOLD : '';
    echo $prefix . $color . $message . Colors::RESET . "\n";
}

// Fonction pour afficher l'aide
function showHelp() {
    echo Colors::CYAN . Colors::BOLD . "Gestionnaire MySQL en ligne de commande\n" . Colors::RESET;
    echo Colors::YELLOW . "Usage: php mysql_cli_manager.php [COMMANDE] [OPTIONS]\n\n" . Colors::RESET;
    
    echo Colors::BOLD . "COMMANDES:\n" . Colors::RESET;
    echo "  export-all     " . Colors::GREEN . "Exporter toutes les bases de données\n" . Colors::RESET;
    echo "  export-single  " . Colors::GREEN . "Exporter une base spécifique\n" . Colors::RESET;
    echo "  import         " . Colors::GREEN . "Importer une base depuis un fichier SQL\n" . Colors::RESET;
    echo "  list           " . Colors::GREEN . "Lister toutes les bases de données\n" . Colors::RESET;
    echo "  info           " . Colors::GREEN . "Informations sur une base spécifique\n" . Colors::RESET;
    echo "  backup         " . Colors::GREEN . "Créer une sauvegarde complète\n" . Colors::RESET;
    echo "  restore        " . Colors::GREEN . "Restaurer depuis une sauvegarde\n" . Colors::RESET;
    echo "  help           " . Colors::GREEN . "Afficher cette aide\n" . Colors::RESET;
    
    echo Colors::BOLD . "\nOPTIONS:\n" . Colors::RESET;
    echo "  --database=NAME    " . Colors::YELLOW . "Nom de la base de données\n" . Colors::RESET;
    echo "  --file=PATH        " . Colors::YELLOW . "Chemin du fichier SQL\n" . Colors::RESET;
    echo "  --output=PATH      " . Colors::YELLOW . "Chemin de sortie pour l'export\n" . Colors::RESET;
    echo "  --force            " . Colors::YELLOW . "Forcer l'opération sans confirmation\n" . Colors::RESET;
    echo "  --verbose          " . Colors::YELLOW . "Mode verbeux\n" . Colors::RESET;
    
    echo Colors::BOLD . "\nEXEMPLES:\n" . Colors::RESET;
    echo "  php mysql_cli_manager.php export-all\n";
    echo "  php mysql_cli_manager.php export-single --database=cyberchasse\n";
    echo "  php mysql_cli_manager.php import --database=newdb --file=backup.sql\n";
    echo "  php mysql_cli_manager.php list\n";
    echo "  php mysql_cli_manager.php info --database=cyberchasse\n";
}

// Fonction pour obtenir la connexion MySQL sans base spécifique
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

// Fonction pour obtenir la liste de toutes les bases de données
function getAllDatabases($pdo) {
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dbName = $row['Database'];
        // Exclure les bases système MySQL
        if (!in_array($dbName, ['information_schema', 'mysql', 'performance_schema', 'sys'])) {
            $databases[] = $dbName;
        }
    }
    return $databases;
}

// Fonction pour obtenir la taille d'une base de données
function getDatabaseSize($pdo, $database) {
    try {
        $stmt = $pdo->query("
            SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size_MB'
            FROM information_schema.tables 
            WHERE table_schema = '$database'
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['Size_MB'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

// Fonction pour obtenir la liste des tables d'une base
function getTables($pdo, $database) {
    try {
        $pdo->exec("USE `$database`");
        $stmt = $pdo->query("SHOW TABLES");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        return [];
    }
}

// Fonction pour exporter une base de données complète
function exportDatabase($pdo, $database, $verbose = false) {
    if ($verbose) {
        printMessage("Export de la base : $database", Colors::CYAN);
    }
    
    $output = "-- Export complet de la base de données : $database\n";
    $output .= "-- Généré le : " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- Version MySQL : " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n\n";
    
    $output .= "CREATE DATABASE IF NOT EXISTS `$database`;\n";
    $output .= "USE `$database`;\n\n";
    
    $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    try {
        $pdo->exec("USE `$database`");
        $tables = getTables($pdo, $database);
        
        if ($verbose) {
            printMessage("  - " . count($tables) . " tables trouvées", Colors::YELLOW);
        }
        
        foreach ($tables as $table) {
            if ($verbose) {
                printMessage("    Export de la table : $table", Colors::WHITE);
            }
            
            // Structure de la table
            $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $output .= "-- Structure de la table `$table`\n";
            $output .= "DROP TABLE IF EXISTS `$table`;\n";
            $output .= $result['Create Table'] . ";\n\n";
            
            // Données de la table
            $stmt = $pdo->query("SELECT * FROM `$table`");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($data)) {
                if ($verbose) {
                    printMessage("      " . count($data) . " lignes de données", Colors::GREEN);
                }
                
                $output .= "-- Données de la table `$table`\n";
                $columns = array_keys($data[0]);
                $output .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES\n";
                
                $rows = [];
                foreach ($data as $row) {
                    $values = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }
                    $rows[] = "(" . implode(', ', $values) . ")";
                }
                
                $output .= implode(",\n", $rows) . ";\n\n";
            }
        }
        
        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
    } catch (Exception $e) {
        printMessage("Erreur lors de l'export de $database : " . $e->getMessage(), Colors::RED);
        $output .= "-- Erreur lors de l'export : " . $e->getMessage() . "\n";
    }
    
    return $output;
}

// Fonction pour exporter toutes les bases
function exportAllDatabases($pdo, $outputPath = null, $verbose = false) {
    $databases = getAllDatabases($pdo);
    
    if ($verbose) {
        printMessage("Export de " . count($databases) . " bases de données", Colors::CYAN);
    }
    
    $output = "-- Export complet de toutes les bases de données\n";
    $output .= "-- Généré le : " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- Version MySQL : " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
    $output .= "-- Bases exportées : " . implode(', ', $databases) . "\n\n";
    
    foreach ($databases as $database) {
        $output .= exportDatabase($pdo, $database, $verbose);
        $output .= "\n" . str_repeat("-", 80) . "\n\n";
    }
    
    if ($outputPath) {
        if (file_put_contents($outputPath, $output)) {
            printMessage("Export sauvegardé dans : $outputPath", Colors::GREEN);
        } else {
            printMessage("Erreur lors de la sauvegarde dans : $outputPath", Colors::RED);
        }
    }
    
    return $output;
}

// Fonction pour importer une base de données
function importDatabase($pdo, $database, $sqlFile, $verbose = false) {
    if (!file_exists($sqlFile)) {
        throw new Exception("Fichier SQL introuvable : $sqlFile");
    }
    
    if ($verbose) {
        printMessage("Import de la base : $database depuis $sqlFile", Colors::CYAN);
    }
    
    try {
        // Créer la base si elle n'existe pas
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
        $pdo->exec("USE `$database`");
        
        // Lire le fichier SQL
        $sqlContent = file_get_contents($sqlFile);
        
        if ($verbose) {
            printMessage("  - Fichier lu : " . number_format(strlen($sqlContent)) . " octets", Colors::YELLOW);
        }
        
        // Exécuter les requêtes SQL
        $queries = explode(';', $sqlContent);
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && !preg_match('/^--/', $query)) {
                try {
                    $pdo->exec($query);
                    $successCount++;
                } catch (Exception $e) {
                    $errorCount++;
                    if ($verbose) {
                        printMessage("    Erreur requête : " . $e->getMessage(), Colors::RED);
                    }
                }
            }
        }
        
        if ($verbose) {
            printMessage("  - Requêtes exécutées : $successCount succès, $errorCount erreurs", Colors::GREEN);
        }
        
        printMessage("Import terminé pour la base : $database", Colors::GREEN);
        
    } catch (Exception $e) {
        throw new Exception("Erreur lors de l'import : " . $e->getMessage());
    }
}

// Fonction pour lister les bases de données
function listDatabases($pdo, $verbose = false) {
    $databases = getAllDatabases($pdo);
    
    printMessage("Bases de données disponibles : " . count($databases), Colors::CYAN);
    printMessage(str_repeat("-", 60), Colors::WHITE);
    
    foreach ($databases as $database) {
        $size = getDatabaseSize($pdo, $database);
        $tables = getTables($pdo, $database);
        
        echo sprintf("%-25s | %8s | %3d tables\n", 
            $database, 
            $size . " MB", 
            count($tables)
        );
        
        if ($verbose) {
            foreach ($tables as $table) {
                echo "  └─ $table\n";
            }
        }
    }
}

// Fonction pour afficher les informations d'une base
function showDatabaseInfo($pdo, $database, $verbose = false) {
    try {
        $size = getDatabaseSize($pdo, $database);
        $tables = getTables($pdo, $database);
        
        printMessage("Informations sur la base : $database", Colors::CYAN);
        printMessage(str_repeat("-", 40), Colors::WHITE);
        printMessage("Taille : $size MB", Colors::GREEN);
        printMessage("Tables : " . count($tables), Colors::GREEN);
        
        if ($verbose) {
            printMessage("\nDétail des tables :", Colors::YELLOW);
            foreach ($tables as $table) {
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $rowCount = $result['count'];
                    
                    echo sprintf("  %-20s | %8d lignes\n", $table, $rowCount);
                } catch (Exception $e) {
                    echo sprintf("  %-20s | %8s\n", $table, "ERREUR");
                }
            }
        }
        
    } catch (Exception $e) {
        printMessage("Erreur : " . $e->getMessage(), Colors::RED);
    }
}

// Fonction pour créer une sauvegarde complète
function createBackup($pdo, $verbose = false) {
    $backupDir = __DIR__ . '/../backups/';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $filename = 'cli_backup_' . date('Y-m-d_H-i-s') . '.sql';
    $filepath = $backupDir . $filename;
    
    if ($verbose) {
        printMessage("Création de la sauvegarde : $filename", Colors::CYAN);
    }
    
    exportAllDatabases($pdo, $filepath, $verbose);
    
    $size = number_format(filesize($filepath) / 1024 / 1024, 2);
    printMessage("Sauvegarde créée : $filepath ($size MB)", Colors::GREEN);
    
    return $filepath;
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
    
    // Restaurer chaque base
    foreach ($databases as $database) {
        try {
            // Créer la base
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
            $pdo->exec("USE `$database`");
            
            if ($verbose) {
                printMessage("Restauration de : $database", Colors::GREEN);
            }
            
            // Exécuter les requêtes pour cette base
            $pattern = '/CREATE DATABASE IF NOT EXISTS `' . preg_quote($database, '/') . '`.*?USE `' . preg_quote($database, '/') . '`.*?(?=CREATE DATABASE|SET FOREIGN_KEY_CHECKS|$)/s';
            if (preg_match($pattern, $sqlContent, $match)) {
                $databaseSQL = $match[0];
                $queries = explode(';', $databaseSQL);
                
                foreach ($queries as $query) {
                    $query = trim($query);
                    if (!empty($query) && !preg_match('/^--/', $query)) {
                        try {
                            $pdo->exec($query);
                        } catch (Exception $e) {
                            if ($verbose) {
                                printMessage("  Erreur : " . $e->getMessage(), Colors::RED);
                            }
                        }
                    }
                }
            }
            
        } catch (Exception $e) {
            printMessage("Erreur lors de la restauration de $database : " . $e->getMessage(), Colors::RED);
        }
    }
    
    printMessage("Restauration terminée", Colors::GREEN);
}

// Traitement des arguments de ligne de commande
$command = $argv[1] ?? 'help';
$options = [];

// Parser les options
for ($i = 2; $i < count($argv); $i++) {
    if (strpos($argv[$i], '--') === 0) {
        $option = substr($argv[$i], 2);
        if (strpos($option, '=') !== false) {
            list($key, $value) = explode('=', $option, 2);
            $options[$key] = $value;
        } else {
            $options[$option] = true;
        }
    }
}

$verbose = isset($options['verbose']);
$force = isset($options['force']);

try {
    switch ($command) {
        case 'export-all':
            $pdo = getMySQLConnection();
            $outputPath = $options['output'] ?? null;
            exportAllDatabases($pdo, $outputPath, $verbose);
            break;
            
        case 'export-single':
            if (!isset($options['database'])) {
                throw new Exception("Option --database requise");
            }
            $pdo = getMySQLConnection();
            $outputPath = $options['output'] ?? null;
            $export = exportDatabase($pdo, $options['database'], $verbose);
            if ($outputPath) {
                file_put_contents($outputPath, $export);
                printMessage("Export sauvegardé dans : $outputPath", Colors::GREEN);
            } else {
                echo $export;
            }
            break;
            
        case 'import':
            if (!isset($options['database']) || !isset($options['file'])) {
                throw new Exception("Options --database et --file requises");
            }
            $pdo = getMySQLConnection();
            importDatabase($pdo, $options['database'], $options['file'], $verbose);
            break;
            
        case 'list':
            $pdo = getMySQLConnection();
            listDatabases($pdo, $verbose);
            break;
            
        case 'info':
            if (!isset($options['database'])) {
                throw new Exception("Option --database requise");
            }
            $pdo = getMySQLConnection();
            showDatabaseInfo($pdo, $options['database'], $verbose);
            break;
            
        case 'backup':
            $pdo = getMySQLConnection();
            createBackup($pdo, $verbose);
            break;
            
        case 'restore':
            if (!isset($options['file'])) {
                throw new Exception("Option --file requise");
            }
            $pdo = getMySQLConnection();
            restoreFromBackup($pdo, $options['file'], $verbose);
            break;
            
        case 'help':
        default:
            showHelp();
            break;
    }
    
} catch (Exception $e) {
    printMessage("Erreur : " . $e->getMessage(), Colors::RED);
    exit(1);
}

printMessage("\nOpération terminée avec succès !", Colors::GREEN);
?>
