<?php
session_start();
$page_title = 'Sauvegarde de la Base de Données';
$breadcrumb_items = [
    ['url' => 'admin.php', 'text' => 'Administration', 'active' => false],
    ['url' => 'savBDD.php', 'text' => 'Sauvegarde BDD', 'active' => true]
];

require_once 'includes/header.php';
require_once '../config/connexion.php';

// Fonction pour obtenir la liste des tables
function getTables($pdo) {
    $stmt = $pdo->query("SHOW TABLES");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Fonction pour obtenir la structure d'une table
function getTableStructure($pdo, $table) {
    $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['Create Table'];
}

// Fonction pour obtenir les données d'une table
function getTableData($pdo, $table) {
    $stmt = $pdo->query("SELECT * FROM `$table`");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour générer la sauvegarde
function generateBackup($pdo) {
    $output = "-- Sauvegarde de la base de données Cyberchasse\n";
    $output .= "-- Générée le : " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- Version MySQL : " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n\n";
    
    $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    $tables = getTables($pdo);
    
    foreach ($tables as $table) {
        // Structure de la table
        $output .= "-- Structure de la table `$table`\n";
        $output .= "DROP TABLE IF EXISTS `$table`;\n";
        $output .= getTableStructure($pdo, $table) . ";\n\n";
        
        // Données de la table
        $data = getTableData($pdo, $table);
        if (!empty($data)) {
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
    
    return $output;
}

// Fonction pour générer un script d'export compatible mysql dump
function generateMysqlDumpScript($pdo) {
    $output = "-- Script d'export/import pour la base de données Cyberchasse\n";
    $output .= "-- Compatible avec mysql dump et import\n";
    $output .= "-- Généré le : " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- Version MySQL : " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n\n";
    
    $output .= "-- Configuration pour l'import\n";
    $output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $output .= "SET AUTOCOMMIT = 0;\n";
    $output .= "START TRANSACTION;\n";
    $output .= "SET time_zone = \"+00:00\";\n\n";
    
    $output .= "-- Désactiver les vérifications de clés étrangères\n";
    $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
    
    $tables = getTables($pdo);
    
    foreach ($tables as $table) {
        // Structure de la table
        $output .= "-- --------------------------------------------------------\n";
        $output .= "-- Structure de la table `$table`\n";
        $output .= "-- --------------------------------------------------------\n\n";
        $output .= "DROP TABLE IF EXISTS `$table`;\n";
        $output .= "/*!40101 SET @saved_cs_client     = @@character_set_client */;\n";
        $output .= "/*!40101 SET character_set_client = utf8 */;\n";
        $output .= getTableStructure($pdo, $table) . ";\n";
        $output .= "/*!40101 SET character_set_client = @saved_cs_client */;\n\n";
        
        // Données de la table
        $data = getTableData($pdo, $table);
        if (!empty($data)) {
            $output .= "-- --------------------------------------------------------\n";
            $output .= "-- Contenu de la table `$table`\n";
            $output .= "-- --------------------------------------------------------\n\n";
            
            // Utiliser LOCK TABLES pour optimiser l'import
            $output .= "LOCK TABLES `$table` WRITE;\n";
            $output .= "/*!40000 ALTER TABLE `$table` DISABLE KEYS */;\n\n";
            
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
            
            $output .= "/*!40000 ALTER TABLE `$table` ENABLE KEYS */;\n";
            $output .= "UNLOCK TABLES;\n\n";
        }
    }
    
    $output .= "-- Réactiver les vérifications de clés étrangères\n";
    $output .= "SET FOREIGN_KEY_CHECKS = 1;\n\n";
    
    $output .= "-- Validation de la transaction\n";
    $output .= "COMMIT;\n\n";
    $output .= "-- Script terminé avec succès\n";
    
    return $output;
}

// Traitement de la sauvegarde
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'backup') {
                $backupContent = generateBackup($pdo);
                $filename = 'cyberchasse_backup_' . date('Y-m-d_H-i-s') . '.sql';
                
                // Créer le dossier de sauvegarde s'il n'existe pas
                $backupDir = '../backups/';
                if (!is_dir($backupDir)) {
                    mkdir($backupDir, 0755, true);
                }
                
                $filepath = $backupDir . $filename;
                
                if (file_put_contents($filepath, $backupContent)) {
                    $message = "Sauvegarde créée avec succès : $filename";
                    $messageType = 'success';
                } else {
                    throw new Exception("Erreur lors de l'écriture du fichier");
                }
                
            } elseif ($_POST['action'] === 'download') {
                $backupContent = generateBackup($pdo);
                $filename = 'cyberchasse_backup_' . date('Y-m-d_H-i-s') . '.sql';
                
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-Length: ' . strlen($backupContent));
                header('Cache-Control: no-cache, must-revalidate');
                header('Pragma: no-cache');
                
                echo $backupContent;
                exit;
                
            } elseif ($_POST['action'] === 'mysql_dump') {
                $dumpContent = generateMysqlDumpScript($pdo);
                $filename = 'cyberchasse_mysql_dump_' . date('Y-m-d_H-i-s') . '.sql';
                
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-Length: ' . strlen($dumpContent));
                header('Cache-Control: no-cache, must-revalidate');
                header('Pragma: no-cache');
                
                echo $dumpContent;
                exit;
                
            } elseif ($_POST['action'] === 'mysql_dump_save') {
                $dumpContent = generateMysqlDumpScript($pdo);
                $filename = 'cyberchasse_mysql_dump_' . date('Y-m-d_H-i-s') . '.sql';
                
                // Créer le dossier de sauvegarde s'il n'existe pas
                $backupDir = '../backups/';
                if (!is_dir($backupDir)) {
                    mkdir($backupDir, 0755, true);
                }
                
                $filepath = $backupDir . $filename;
                
                if (file_put_contents($filepath, $dumpContent)) {
                    $message = "Script MySQL dump créé avec succès : $filename";
                    $messageType = 'success';
                } else {
                    throw new Exception("Erreur lors de l'écriture du fichier");
                }
            }
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

// Obtenir la liste des sauvegardes existantes
$backupDir = '../backups/';
$existingBackups = [];
if (is_dir($backupDir)) {
    $files = glob($backupDir . '*.sql');
    foreach ($files as $file) {
        $existingBackups[] = [
            'name' => basename($file),
            'size' => filesize($file),
            'date' => filemtime($file),
            'path' => $file
        ];
    }
    // Trier par date (plus récent en premier)
    usort($existingBackups, function($a, $b) {
        return $b['date'] - $a['date'];
    });
}
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-database me-2"></i>
                    Sauvegarde de la Base de Données
                </h4>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Informations sur la base de données -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Informations BDD
                                </h6>
                                <p class="mb-1"><strong>Nom :</strong> cyberchasse</p>
                                <p class="mb-1"><strong>Hôte :</strong> <?php echo env('DB_HOST', 'localhost'); ?></p>
                                <p class="mb-0"><strong>Version MySQL :</strong> <?php echo $pdo->getAttribute(PDO::ATTR_SERVER_VERSION); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-table me-2"></i>
                                    Tables
                                </h6>
                                <p class="mb-1"><strong>Nombre :</strong> <?php echo count(getTables($pdo)); ?></p>
                                <p class="mb-0"><strong>Dernière sauvegarde :</strong> 
                                    <?php echo !empty($existingBackups) ? date('d/m/Y H:i', $existingBackups[0]['date']) : 'Aucune'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions de sauvegarde -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-download me-2"></i>
                                    Actions de Sauvegarde
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="backup">
                                            <button type="submit" class="btn btn-success btn-lg w-100 mb-2">
                                                <i class="fas fa-save me-2"></i>
                                                Créer une Sauvegarde
                                            </button>
                                        </form>
                                        <small class="text-muted">
                                            Crée une sauvegarde complète sur le serveur
                                        </small>
                                    </div>
                                    <div class="col-md-3">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="download">
                                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                                                <i class="fas fa-download me-2"></i>
                                                Télécharger Sauvegarde
                                            </button>
                                        </form>
                                        <small class="text-muted">
                                            Télécharge directement la sauvegarde
                                        </small>
                                    </div>
                                    <div class="col-md-3">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="mysql_dump">
                                            <button type="submit" class="btn btn-warning btn-lg w-100 mb-2">
                                                <i class="fas fa-file-code me-2"></i>
                                                Télécharger MySQL Dump
                                            </button>
                                        </form>
                                        <small class="text-muted">
                                            Script compatible mysql dump pour import
                                        </small>
                                    </div>
                                    <div class="col-md-3">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="mysql_dump_save">
                                            <button type="submit" class="btn btn-info btn-lg w-100 mb-2">
                                                <i class="fas fa-save me-2"></i>
                                                Créer MySQL Dump
                                            </button>
                                        </form>
                                        <small class="text-muted">
                                            Crée le script sur le serveur
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des sauvegardes existantes -->
                <?php if (!empty($existingBackups)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-history me-2"></i>
                                    Sauvegardes Existantes
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-info">
                                            <tr>
                                                <th>Nom du fichier</th>
                                                <th>Taille</th>
                                                <th>Date de création</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($existingBackups as $backup): ?>
                                            <tr>
                                                <td>
                                                    <i class="fas fa-file-code text-primary me-2"></i>
                                                    <?php echo $backup['name']; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?php echo number_format($backup['size'] / 1024, 2); ?> KB
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="fas fa-calendar-alt text-info me-2"></i>
                                                    <?php echo date('d/m/Y H:i:s', $backup['date']); ?>
                                                </td>
                                                <td>
                                                    <a href="../backups/<?php echo $backup['name']; ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       download="<?php echo $backup['name']; ?>"
                                                       title="Télécharger">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteBackup('<?php echo $backup['name']; ?>')"
                                                            title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Instructions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-lightbulb me-2"></i>
                                Instructions
                            </h6>
                            <ul class="mb-0">
                                <li><strong>Sauvegarde standard :</strong> Inclut la structure et toutes les données de la base</li>
                                <li><strong>MySQL Dump :</strong> Script optimisé pour l'import sur d'autres serveurs avec mysql dump</li>
                                <li>Les sauvegardes sont stockées dans le dossier <code>backups/</code></li>
                                <li>Utilisez la sauvegarde pour restaurer la base en cas de problème</li>
                                <li>Le script MySQL Dump est compatible avec : <code>mysql -u user -p database < fichier.sql</code></li>
                                <li>Conservez plusieurs versions de sauvegarde pour plus de sécurité</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteBackup(filename) {
    Swal.fire({
        title: '⚠️ Confirmer la suppression',
        text: `Voulez-vous vraiment supprimer la sauvegarde "${filename}" ?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            // Appel AJAX pour supprimer
            fetch('delete_backup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'filename=' + encodeURIComponent(filename)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('✅ Succès', 'Sauvegarde supprimée', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire('❌ Erreur', data.message, 'error');
                }
            });
        }
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
