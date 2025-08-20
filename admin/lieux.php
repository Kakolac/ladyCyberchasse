<?php
session_start();
require_once '../config/connexion.php';

// Logs pour le débogage
error_log("=== DÉBUT TRAITEMENT CRÉATION LIEU ===");
error_log("POST reçu : " . print_r($_POST, true));

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

$success_message = '';
$error_message = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'update_enigme':
            $lieu_id = $_POST['lieu_id'];
            $reponse_enigme = $_POST['reponse_enigme'];
            $enigme_texte = trim($_POST['enigme_texte']);
            $options = [
                'A' => trim($_POST['option_a']),
                'B' => trim($_POST['option_b']),
                'C' => trim($_POST['option_c']),
                'D' => trim($_POST['option_d'])
            ];
            
            if (!empty($enigme_texte) && !empty($reponse_enigme)) {
                $stmt = $pdo->prepare("UPDATE lieux SET reponse_enigme = ?, enigme_texte = ?, options_enigme = ? WHERE id = ?");
                if ($stmt->execute([$reponse_enigme, $enigme_texte, json_encode($options), $lieu_id])) {
                    $success_message = "Énigme mise à jour avec succès !";
                } else {
                    $error_message = "Erreur lors de la mise à jour de l'énigme";
                }
            } else {
                $error_message = "Le texte de l'énigme et la réponse sont obligatoires";
            }
            break;
            
        // NOUVELLE ACTION : Affecter une énigme
        case 'affecter_enigme':
            $lieu_id = $_POST['lieu_id'];
            $enigme_id = $_POST['enigme_id'];
            
            if (!empty($lieu_id) && !empty($enigme_id)) {
                $stmt = $pdo->prepare("UPDATE lieux SET enigme_id = ? WHERE id = ?");
                if ($stmt->execute([$enigme_id, $lieu_id])) {
                    $success_message = "Énigme affectée au lieu avec succès !";
                } else {
                    $error_message = "Erreur lors de l'affectation de l'énigme";
                }
            } else {
                $error_message = "Sélectionnez une énigme à affecter";
            }
            break;
            
        // NOUVELLE ACTION : Supprimer l'affectation d'énigme
        case 'supprimer_enigme':
            $lieu_id = $_POST['lieu_id'];
            
            $stmt = $pdo->prepare("UPDATE lieux SET enigme_id = NULL WHERE id = ?");
            if ($stmt->execute([$lieu_id])) {
                $success_message = "Affectation d'énigme supprimée avec succès !";
            } else {
                $error_message = "Erreur lors de la suppression de l'affectation";
            }
            break;

        // NOUVELLE ACTION : Mettre à jour le délai d'indice
        case 'update_delai_indice':
            $lieu_id = $_POST['lieu_id'];
            $delai_indice = (int)$_POST['delai_indice'];
            
            if ($delai_indice >= 1 && $delai_indice <= 60) { // Limite entre 1 et 60 minutes
                $stmt = $pdo->prepare("UPDATE lieux SET delai_indice = ? WHERE id = ?");
                if ($stmt->execute([$delai_indice, $lieu_id])) {
                    $success_message = "Délai d'indice mis à jour avec succès !";
                } else {
                    $error_message = "Erreur lors de la mise à jour du délai d'indice";
                }
            } else {
                $error_message = "Le délai d'indice doit être entre 1 et 60 minutes";
            }
            break;

        // NOUVELLE ACTION : Supprimer le lieu
        case 'supprimer_lieu':
            $lieu_id = $_POST['lieu_id'];
            
            // Récupérer les informations du lieu avant suppression
            $stmt = $pdo->prepare("SELECT nom, enigme_requise FROM lieux WHERE id = ?");
            $stmt->execute([$lieu_id]);
            $lieu = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($lieu && $lieu['enigme_requise']) {
                $error_message = "Impossible de supprimer un lieu obligatoire. Décochez d'abord 'Énigme obligatoire'.";
            } else {
                // Supprimer le répertoire physique s'il existe
                $repertoire_lieu = '../lieux/' . strtolower(str_replace(' ', '_', $lieu['nom']));
                $repertoire_supprime = false;
                
                if (is_dir($repertoire_lieu)) {
                    if (deleteDirectory($repertoire_lieu)) {
                        $repertoire_supprime = true;
                    } else {
                        $warning_message = "Attention : Impossible de supprimer le répertoire physique du lieu.";
                    }
                } else {
                    $info_message = "Information : Le répertoire physique du lieu n'existe pas.";
                }
                
                // Essayer de supprimer le lieu avec gestion d'erreur
                try {
                    $stmt = $pdo->prepare("DELETE FROM lieux WHERE id = ?");
                    $stmt->execute([$lieu_id]);
                    
                    // Suppression directe réussie
                    if ($repertoire_supprime) {
                        $success_message = "Lieu et répertoire supprimés avec succès !";
                    } else {
                        $success_message = "Lieu supprimé de la BDD avec succès !";
                    }
                    
                } catch (PDOException $e) {
                    // Erreur de contrainte détectée - faire la suppression en cascade
                    if ($e->getCode() == '23000') {
                        try {
                            $pdo->beginTransaction();
                            
                            // Fonction pour vérifier si une table existe
                            function tableExists($pdo, $table) {
                                try {
                                    $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
                                    return $stmt->rowCount() > 0;
                                } catch (Exception $e) {
                                    return false;
                                }
                            }
                            
                            // Supprimer les données liées dans l'ordre (seulement si les tables existent)
                            $tables_cascade = [
                                'indices_consultes' => 'lieu_id',
                                'indices_forces' => 'lieu_id', 
                                'temps_parcours' => 'lieu_id',
                                'resets' => 'lieu_id'
                            ];
                            
                            $donnees_supprimees = [];
                            
                            foreach ($tables_cascade as $table => $colonne) {
                                if (tableExists($pdo, $table)) {
                                    $stmt = $pdo->prepare("DELETE FROM {$table} WHERE {$colonne} = ?");
                                    if ($stmt->execute([$lieu_id])) {
                                        $count = $stmt->rowCount();
                                        if ($count > 0) {
                                            $donnees_supprimees[] = "{$count} enregistrement(s) de {$table}";
                                        }
                                    }
                                } else {
                                    // Table n'existe pas, on l'ignore
                                    $donnees_supprimees[] = "Table {$table} ignorée (n'existe pas)";
                                }
                            }
                            
                            // Maintenant supprimer le lieu
                            $stmt = $pdo->prepare("DELETE FROM lieux WHERE id = ?");
                            if ($stmt->execute([$lieu_id])) {
                                $pdo->commit();
                                
                                // Message de succès avec détails
                                $message_suppression = "Lieu supprimé avec succès !";
                                if (!empty($donnees_supprimees)) {
                                    $message_suppression .= " Données liées supprimées : " . implode(', ', $donnees_supprimees);
                                }
                                if ($repertoire_supprime) {
                                    $message_suppression .= " Répertoire physique également supprimé.";
                                }
                                
                                $success_message = $message_suppression;
                            } else {
                                throw new Exception("Impossible de supprimer le lieu après nettoyage des données");
                            }
                            
                        } catch (Exception $e2) {
                            $pdo->rollBack();
                            $error_message = "Erreur lors de la suppression en cascade : " . $e2->getMessage();
                        }
                    } else {
                        // Autre type d'erreur PDO
                        $error_message = "Erreur lors de la suppression du lieu : " . $e->getMessage();
                    }
                }
            }
            break;

        // NOUVELLE ACTION : Mettre à jour les propriétés du lieu
        case 'update_lieu_properties':
            $lieu_id = $_POST['lieu_id'];
            $nom = trim($_POST['nom']);
            $ordre = (int)$_POST['ordre'];
            $description = trim($_POST['description']);
            $temps_limite = (int)$_POST['temps_limite'];
            $delai_indice = (int)$_POST['delai_indice'];
            $statut = $_POST['statut'];
            $enigme_requise = isset($_POST['enigme_requise']) ? 1 : 0;
            
            // Validation des données
            if (empty($nom) || $ordre < 1 || $temps_limite < 60 || $delai_indice < 1 || $delai_indice > 60) {
                $error_message = "Données invalides. Vérifiez les valeurs saisies.";
            } else {
                $stmt = $pdo->prepare("
                    UPDATE lieux 
                    SET nom = ?, ordre = ?, description = ?, temps_limite = ?, 
                        delai_indice = ?, statut = ?, enigme_requise = ?
                    WHERE id = ?
                ");
                if ($stmt->execute([$nom, $ordre, $description, $temps_limite, $delai_indice, $statut, $enigme_requise, $lieu_id])) {
                    $success_message = "Propriétés du lieu mises à jour avec succès !";
                } else {
                    $error_message = "Erreur lors de la mise à jour des propriétés";
                }
            }
            break;
    }
}

// Récupération des lieux avec leurs énigmes
try {
    $stmt = $pdo->query("
        SELECT 
            l.*,
            e.id AS enigme_id,
            e.titre AS enigme_titre,
            e.actif AS enigme_active,
            te.nom AS type_nom,
            te.template,
            COALESCE(l.delai_indice, 6) AS delai_indice,
            l.type_lieu
        FROM lieux l
        LEFT JOIN enigmes e ON l.enigme_id = e.id
        LEFT JOIN types_enigmes te ON e.type_enigme_id = te.id
        ORDER BY l.ordre, l.nom
    ");
    $lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // NOUVEAU : Debug des données
    echo "<script>
        console.log('Données des lieux :', " . json_encode($lieux) . ");
    </script>";
    
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des lieux: " . $e->getMessage();
}

// Récupération des énigmes disponibles pour l'affectation
try {
    $stmt = $pdo->query("
        SELECT e.id, e.titre, te.nom as type_nom, te.template
        FROM enigmes e 
        LEFT JOIN types_enigmes te ON e.type_enigme_id = te.id 
        WHERE e.actif = 1
        ORDER BY te.nom, e.titre
    ");
    $enigmes_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $enigmes_disponibles = [];
}

// Récupération du prochain ordre disponible
try {
    $stmt = $pdo->query("SELECT COALESCE(MAX(ordre), 0) + 1 as prochain_ordre FROM lieux");
    $prochain_ordre = $stmt->fetch(PDO::FETCH_ASSOC)['prochain_ordre'];
} catch (Exception $e) {
    $prochain_ordre = 1;
}

// Fonction pour supprimer un répertoire et son contenu
function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            unlink($path);
        }
    }
    return rmdir($dir);
}

// Configuration pour le header
$page_title = 'Gestion des Lieux - Administration Cyberchasse';
$current_page = 'lieux';
$breadcrumb_items = [
    ['text' => 'Tableau de bord', 'url' => 'admin.php', 'active' => false],
    ['text' => 'Gestion des Lieux', 'url' => 'lieux.php', 'active' => true]
];

include 'includes/header.php';
?>

<!-- Zone de debug -->
<div id="debugZone" class="position-fixed bottom-0 end-0 p-3" style="max-height: 300px; width: 400px; overflow-y: auto; background: rgba(0,0,0,0.8); color: #00ff00; font-family: monospace; font-size: 12px; z-index: 9999;">
    <h6 class="text-white">🐛 Debug Console</h6>
    <div id="debugContent" style="white-space: pre-wrap;"></div>
</div>

<script>
// Zone de debug
const debugZone = document.createElement('div');
debugZone.id = 'debugZone';
debugZone.className = 'position-fixed bottom-0 end-0 p-3';
debugZone.style.cssText = 'max-height: 300px; width: 400px; overflow-y: auto; background: rgba(0,0,0,0.8); color: #00ff00; font-family: monospace; font-size: 12px; z-index: 9999;';
debugZone.innerHTML = '<h6 class="text-white">🐛 Debug Console</h6><div id="debugContent" style="white-space: pre-wrap;"></div>';
document.body.appendChild(debugZone);

function debugLog(message, data = null) {
    const debugContent = document.getElementById('debugContent');
    const timestamp = new Date().toLocaleTimeString();
    let logMessage = `[${timestamp}] ${message}`;
    
    if (data !== null) {
        logMessage += '\n' + JSON.stringify(data, null, 2);
    }
    
    debugContent.innerHTML = logMessage + '\n\n' + debugContent.innerHTML;
}

function debugFormSubmit() {
    const form = document.getElementById('createLieuForm');
    const formData = new FormData(form);
    
    debugLog('=== SOUMISSION DU FORMULAIRE ===');
    for (let [key, value] of formData.entries()) {
        debugLog(`${key}:`, value);
    }
}

// Écouteurs pour les radios
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="type_lieu"]');
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            debugLog('Radio changé', {
                id: this.id,
                value: this.value,
                checked: this.checked
            });
        });
    });
});
</script>


        


        <?php if (isset($info_message)): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle"></i>
                <?php echo $info_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Styles CSS spécifiques à cette page -->
        <style>
            .admin-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
            .lieu-card { transition: transform 0.3s ease; }
            .lieu-card:hover { transform: translateY(-5px); }
            .enigme-preview { background: rgba(0,0,0,0.05); border-radius: 8px; padding: 15px; }
            .option-correct { border-left: 4px solid #28a745; background: rgba(40, 167, 69, 0.1); }
            .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
            .btn-close { filter: invert(1); }
            .enigme-status { font-size: 0.9em; }
        </style>

        <!-- En-tête de la page -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-map-marker-alt"></i> Gestion des Lieux</h1>
                <p class="text-muted">Administrer les lieux et leurs énigmes de la cyberchasse</p>
            </div>
            <div>
                <a href="enigmes.php" class="btn btn-info me-2">
                    <i class="fas fa-puzzle-piece"></i> Gérer les Énigmes
                </a>
                <button type="button" class="btn btn-success me-2" onclick="ouvrirModalCreerLieu()">
                    <i class="fas fa-plus"></i> Créer un lieu
                </button>
                <a href="admin.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
            </div>
        </div>

        <!-- Statistiques des lieux -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card admin-card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                        <h3><?php echo count($lieux); ?></h3>
                        <p class="mb-0">Total Lieux</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-puzzle-piece fa-3x mb-3"></i>
                        <h3><?php echo count(array_filter($lieux, function($l) { return !empty($l['enigme_id']); })); ?></h3>
                        <p class="mb-0">Lieux avec Énigmes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-warning text-dark">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-3x mb-3"></i>
                        <h3><?php echo count(array_filter($lieux, function($l) { return $l['enigme_requise']; })); ?></h3>
                        <p class="mb-0">Lieux Obligatoires</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h3><?php echo count(array_filter($lieux, function($l) { return $l['statut'] === 'actif'; })); ?></h3>
                        <p class="mb-0">Lieux Actifs</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des lieux -->
        <div class="card admin-card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-list"></i> Liste des Lieux et Énigmes</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <h6>❌ Erreur</h6>
                        <p><?php echo $error; ?></p>
                    </div>
                <?php elseif (empty($lieux)): ?>
                    <div class="alert alert-info">
                        <h6>ℹ️ Aucun lieu trouvé</h6>
                        <p>Créez d'abord des lieux dans la base de données.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($lieux as $lieu): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card lieu-card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <?php if ($lieu['type_lieu'] === 'fin'): ?>
                                                <i class="fas fa-flag-checkered text-success"></i>
                                            <?php else: ?>
                                                <i class="fas fa-map-marker-alt text-primary"></i>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($lieu['nom']); ?>
                                        </h5>
                                        <div class="d-flex gap-2">
                                            <!-- NOUVEAU : Bouton Gestion -->
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#gestionLieuModal"
                                                    data-lieu-id="<?php echo $lieu['id']; ?>"
                                                    data-lieu-nom="<?php echo htmlspecialchars($lieu['nom']); ?>"
                                                    data-lieu-ordre="<?php echo $lieu['ordre']; ?>"
                                                    data-lieu-description="<?php echo htmlspecialchars($lieu['description'] ?? ''); ?>"
                                                    data-lieu-temps-limite="<?php echo $lieu['temps_limite']; ?>"
                                                    data-lieu-delai-indice="<?php echo $lieu['delai_indice']; ?>"
                                                    data-lieu-statut="<?php echo $lieu['statut']; ?>"
                                                    data-lieu-enigme-requise="<?php echo $lieu['enigme_requise'] ? '1' : '0'; ?>">
                                                <i class="fas fa-cog"></i> Gestion
                                            </button>
                                            
                                            <!-- Type de lieu -->
                                            <span class="badge <?php echo $lieu['type_lieu'] === 'fin' ? 'bg-success' : 'bg-primary'; ?>">
                                                <?php echo $lieu['type_lieu'] === 'fin' ? 'Lieu de fin' : 'Lieu standard'; ?>
                                            </span>
                                            <!-- Statut existant -->
                                            <span class="badge bg-<?php echo $lieu['statut'] === 'actif' ? 'success' : 'secondary'; ?>">
                                                <?php echo $lieu['statut']; ?>
                                            </span>
                                            <?php if ($lieu['enigme_requise']): ?>
                                                <span class="badge bg-warning">Obligatoire</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Ordre :</small>
                                                <strong><?php echo $lieu['ordre']; ?></strong>
                                            </div>
                                            <?php if ($lieu['type_lieu'] !== 'fin'): ?>
                                                <div class="col-6">
                                                    <small class="text-muted">Temps limite :</small>
                                                    <strong><?php echo gmdate('i:s', $lieu['temps_limite']); ?></strong>
                                                </div>
                                            </div>
                                            
                                            <!-- NOUVEAU : Affichage du délai d'indice seulement pour les lieux standards -->
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <small class="text-muted">Délai d'indice :</small>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <strong><?php echo $lieu['delai_indice']; ?> minutes</strong>
                                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#delaiIndiceModal"
                                                                data-lieu-id="<?php echo $lieu['id']; ?>"
                                                                data-lieu-nom="<?php echo htmlspecialchars($lieu['nom']); ?>"
                                                                data-delai-actuel="<?php echo $lieu['delai_indice']; ?>">
                                                            <i class="fas fa-clock"></i> Modifier
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if ($lieu['type_lieu'] === 'fin'): ?>
                                                <!-- Message pour lieu de fin -->
                                                <div class="alert alert-success">
                                                    <i class="fas fa-flag-checkered"></i>
                                                    <strong>Lieu de fin</strong>
                                                    <br>
                                                    <small>Ce lieu affiche la page de fin avec les statistiques du parcours</small>
                                                </div>
                                            <?php else: ?>
                                                <?php if ($lieu['enigme_id']): ?>
                                                    <!-- Énigme affectée -->
                                                    <div class="enigme-preview mb-3">
                                                        <h6><i class="fas fa-puzzle-piece text-success"></i> Énigme affectée</h6>
                                                        <p class="mb-2"><strong><?php echo htmlspecialchars($lieu['enigme_titre']); ?></strong></p>
                                                        <div class="enigme-status">
                                                            <span class="badge bg-info"><?php echo htmlspecialchars($lieu['type_nom']); ?></span>
                                                            <span class="badge bg-<?php echo $lieu['enigme_active'] ? 'success' : 'secondary'; ?>">
                                                                <?php echo $lieu['enigme_active'] ? 'Active' : 'Inactive'; ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Aucune énigme configurée -->
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        <strong>Aucune énigme configurée</strong>
                                                        <br>
                                                        <small>Cliquez sur "Affecter une énigme" pour en ajouter une</small>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            
                                            <div class="d-flex gap-2">
                                                <?php if ($lieu['type_lieu'] !== 'fin'): ?>
                                                    <?php if ($lieu['enigme_id']): ?>
                                                        <!-- Actions pour lieu avec énigme -->
                                                        <button type="button" class="btn btn-primary btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#affecterEnigmeModal"
                                                                data-lieu-id="<?php echo $lieu['id']; ?>"
                                                                data-lieu-nom="<?php echo htmlspecialchars($lieu['nom']); ?>"
                                                                data-enigme-id="<?php echo $lieu['enigme_id']; ?>">
                                                            <i class="fas fa-edit"></i> Modifier l'énigme
                                                        </button>
                                                        
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer l\'affectation de cette énigme ?')">
                                                            <input type="hidden" name="action" value="supprimer_enigme">
                                                            <input type="hidden" name="lieu_id" value="<?php echo $lieu['id']; ?>">
                                                            <button type="submit" class="btn btn-warning btn-sm">
                                                                <i class="fas fa-unlink"></i> Supprimer l'affectation
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <!-- Actions pour lieu sans énigme -->
                                                        <button type="button" class="btn btn-success btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#affecterEnigmeModal"
                                                                data-lieu-id="<?php echo $lieu['id']; ?>"
                                                                data-enigme-id="">
                                                            <i class="fas fa-plus"></i> Affecter une énigme
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                
                                                <!-- Bouton supprimer le lieu (toujours disponible) -->
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('⚠️ ATTENTION : Êtes-vous sûr de vouloir supprimer définitivement ce lieu ? Cette action est irréversible.')">
                                                    <input type="hidden" name="action" value="supprimer_lieu">
                                                    <input type="hidden" name="lieu_id" value="<?php echo $lieu['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                            <?php echo $lieu['enigme_requise'] ? 'disabled title="Impossible de supprimer un lieu obligatoire"' : ''; ?>>
                                                        <i class="fas fa-trash"></i> Supprimer le lieu
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal d'affectation d'énigme -->
    <div class="modal fade" id="affecterEnigmeModal" tabindex="-1" aria-labelledby="affecterEnigmeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="affecterEnigmeModalLabel">
                        <i class="fas fa-puzzle-piece"></i> Affectation d'Énigme
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="affecter_enigme">
                        <input type="hidden" name="lieu_id" id="affecterLieuId">
                        
                        <div class="mb-3">
                            <label class="form-label">Lieu</label>
                            <input type="text" class="form-control" id="affecterLieuNom" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="enigme_id" class="form-label">Sélectionner une énigme</label>
                            <select class="form-select" name="enigme_id" id="enigme_id" required>
                                <option value="">Choisir une énigme...</option>
                                <?php foreach ($enigmes_disponibles as $enigme): ?>
                                    <option value="<?php echo $enigme['id']; ?>">
                                        <?php echo htmlspecialchars($enigme['titre']); ?> 
                                        (<?php echo htmlspecialchars($enigme['type_nom']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Sélectionnez une énigme existante à affecter à ce lieu</small>
                        </div>
                        
                        <?php if (empty($enigmes_disponibles)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Aucune énigme disponible</strong>
                                <br>
                                <p class="mb-0">Créez d'abord des énigmes dans la section "Énigmes" avant de pouvoir les affecter aux lieux.</p>
                                <a href="enigmes.php" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-plus"></i> Créer une énigme
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Conseil :</strong> Vous pouvez créer de nouvelles énigmes dans la section "Énigmes" puis les affecter ici.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <?php if (!empty($enigmes_disponibles)): ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Affecter l'énigme
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- NOUVEAU : Modal de modification du délai d'indice -->
    <div class="modal fade" id="delaiIndiceModal" tabindex="-1" aria-labelledby="delaiIndiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="delaiIndiceModalLabel">
                        <i class="fas fa-clock"></i> Modifier le Délai d'Indice
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_delai_indice">
                        <input type="hidden" name="lieu_id" id="delaiLieuId">
                        
                        <div class="mb-3">
                            <label class="form-label">Lieu</label>
                            <input type="text" class="form-control" id="delaiLieuNom" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="delai_indice" class="form-label">Délai d'indice (en minutes)</label>
                            <input type="number" class="form-control" name="delai_indice" id="delai_indice" 
                                   min="1" max="60" required>
                            <small class="text-muted">
                                Temps d'attente avant que l'indice soit disponible (entre 1 et 60 minutes)
                            </small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Conseil :</strong> Un délai plus court permet un accès plus rapide à l'indice, 
                            un délai plus long encourage la réflexion avant de consulter l'aide.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de création de lieu -->
    <div class="modal fade" id="creerLieuModal" tabindex="-1" aria-labelledby="creerLieuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="creerLieuModalLabel">
                        <i class="fas fa-plus"></i> Créer un Nouveau Lieu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="creer-lieux.php" id="createLieuForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom du lieu *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required 
                                       placeholder="Ex: CDI, Cour, Laboratoire...">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="ordre" class="form-label">Ordre de passage *</label>
                                <input type="number" class="form-control" id="ordre" name="ordre" 
                                       min="1" required>
                                <small class="text-muted">Ordre dans lequel les équipes doivent visiter ce lieu</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Description optionnelle du lieu..."></textarea>
                        </div>
                        
                        <!-- NOUVEAU : Choix du type de lieu -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">Type de lieu *</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type_lieu" 
                                               id="type_standard" value="standard" checked>
                                        <label class="form-check-label" for="type_standard">
                                            <i class="fas fa-puzzle-piece"></i> Lieu standard
                                            <small class="form-text text-muted d-block">
                                                Avec énigme à résoudre
                                            </small>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type_lieu" 
                                               id="type_fin" value="fin">
                                        <label class="form-check-label" for="type_fin">
                                            <i class="fas fa-flag-checkered"></i> Lieu de fin
                                            <small class="form-text text-muted d-block">
                                                Page de fin avec statistiques
                                            </small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Champs pour énigme (à masquer pour lieu de fin) -->
                        <div class="row champs-enigme">
                            <div class="col-md-6 mb-3">
                                <label for="temps_limite" class="form-control-label">Temps limite (secondes) *</label>
                                <input type="number" class="form-control" id="temps_limite" name="temps_limite" 
                                       value="300" min="60" max="3600" required>
                                <small class="text-muted">Temps maximum pour résoudre l'énigme (60s à 3600s)</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="delai_indice" class="form-label">Délai d'indice (minutes)</label>
                                <input type="number" class="form-control" id="delai_indice" name="delai_indice" 
                                       value="6" min="1" max="60">
                                <small class="text-muted">Temps d'attente avant que l'indice soit disponible</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut *</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="actif">Actif</option>
                                    <option value="inactif">Inactif</option>
                                </select>
                            </div>
                            
                            <!-- Checkbox énigme requise (à masquer pour lieu de fin) -->
                            <div class="col-md-6 mb-3 d-flex align-items-end champs-enigme">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="enigme_requise" name="enigme_requise">
                                    <label class="form-check-label" for="enigme_requise">
                                        Énigme obligatoire
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Si coché, ce lieu doit être visité pour terminer le parcours
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Conseil :</strong> Vous pourrez affecter une énigme à ce lieu après sa création 
                            depuis la page de gestion des lieux.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Créer le lieu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- NOUVELLE MODALE : Gestion des propriétés du lieu -->
    <div class="modal fade" id="gestionLieuModal" tabindex="-1" aria-labelledby="gestionLieuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="gestionLieuModalLabel">
                        <i class="fas fa-cog"></i> Gestion des Propriétés du Lieu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_lieu_properties">
                        <input type="hidden" name="lieu_id" id="gestionLieuId">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gestionNom" class="form-label">Nom du lieu *</label>
                                <input type="text" class="form-control" name="nom" id="gestionNom" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="gestionOrdre" class="form-label">Ordre de passage *</label>
                                <input type="number" class="form-control" name="ordre" id="gestionOrdre" min="1" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gestionDescription" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="gestionDescription" rows="3" 
                                      placeholder="Description optionnelle du lieu..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gestionTempsLimite" class="form-label">Temps limite (secondes) *</label>
                                <input type="number" class="form-control" name="temps_limite" id="gestionTempsLimite" 
                                       min="60" max="3600" required>
                                <small class="text-muted">Temps maximum pour résoudre l'énigme (60s à 3600s)</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="gestionDelaiIndice" class="form-label">Délai d'indice (minutes)</label>
                                <input type="number" class="form-control" name="delai_indice" id="gestionDelaiIndice" 
                                       min="1" max="60">
                                <small class="text-muted">Temps d'attente avant que l'indice soit disponible</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gestionStatut" class="form-label">Statut *</label>
                                <select class="form-select" name="statut" id="gestionStatut" required>
                                    <option value="actif">Actif</option>
                                    <option value="inactif">Inactif</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="enigme_requise" id="gestionEnigmeRequise">
                                    <label class="form-check-label" for="gestionEnigmeRequise">
                                        Énigme obligatoire
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Si coché, ce lieu doit être visité pour terminer le parcours
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Conseil :</strong> Modifiez les propriétés de base du lieu ici. 
                            Pour gérer les énigmes, utilisez les boutons d'action spécifiques.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save"></i> Sauvegarder les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts spécifiques à cette page -->
    <script>
        // Gestion du modal d'affectation d'énigme
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM chargé, initialisation des modales...');
            
            // Test de la modale de création de lieu
            const creerLieuModal = document.getElementById('creerLieuModal');
            console.log('Modal creerLieuModal trouvé:', creerLieuModal);
            
            if (creerLieuModal) {
                // Test d'ouverture de la modale
                const testButton = document.querySelector('[data-bs-target="#creerLieuModal"]');
                console.log('Bouton trouvé:', testButton);
                
                if (testButton) {
                    testButton.addEventListener('click', function(e) {
                        console.log('Clic sur le bouton Créer un lieu');
                        e.preventDefault();
                        
                        // Ouvrir la modale manuellement
                        const modal = new bootstrap.Modal(creerLieuModal);
                        modal.show();
                    });
                }
            }
            
            const affecterEnigmeModal = document.getElementById('affecterEnigmeModal');
            if (affecterEnigmeModal) {
                affecterEnigmeModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const lieuId = button.getAttribute('data-lieu-id');
                    const lieuNom = button.getAttribute('data-lieu-nom');
                    const enigmeId = button.getAttribute('data-enigme-id');
                    
                    // Mettre à jour le modal
                    document.getElementById('affecterLieuId').value = lieuId;
                    document.getElementById('affecterLieuNom').value = lieuNom;
                    
                    // Si une énigme est déjà affectée, la présélectionner
                    if (enigmeId) {
                        document.getElementById('enigme_id').value = enigmeId;
                    } else {
                        document.getElementById('enigme_id').value = '';
                    }
                });
            }
            
            // NOUVEAU : Gestion du modal de délai d'indice
            const delaiIndiceModal = document.getElementById('delaiIndiceModal');
            if (delaiIndiceModal) {
                delaiIndiceModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const lieuId = button.getAttribute('data-lieu-id');
                    const lieuNom = button.getAttribute('data-lieu-nom');
                    const delaiActuel = button.getAttribute('data-delai-actuel');
                    
                    // Mettre à jour le modal
                    document.getElementById('delaiLieuId').value = lieuId;
                    document.getElementById('delaiLieuNom').value = lieuNom;
                    document.getElementById('delai_indice').value = delaiActuel;
                });
            }

            // NOUVEAU : Gestion du modal de gestion des propriétés
            const gestionLieuModal = document.getElementById('gestionLieuModal');
            if (gestionLieuModal) {
                gestionLieuModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const lieuId = button.getAttribute('data-lieu-id');
                    const lieuNom = button.getAttribute('data-lieu-nom');
                    const lieuOrdre = button.getAttribute('data-lieu-ordre');
                    const lieuDescription = button.getAttribute('data-lieu-description');
                    const lieuTempsLimite = button.getAttribute('data-lieu-temps-limite');
                    const lieuDelaiIndice = button.getAttribute('data-lieu-delai-indice');
                    const lieuStatut = button.getAttribute('data-lieu-statut');
                    const lieuEnigmeRequise = button.getAttribute('data-lieu-enigme-requise');
                    
                    // Mettre à jour le modal avec les données du lieu
                    document.getElementById('gestionLieuId').value = lieuId;
                    document.getElementById('gestionNom').value = lieuNom;
                    document.getElementById('gestionOrdre').value = lieuOrdre;
                    document.getElementById('gestionDescription').value = lieuDescription;
                    document.getElementById('gestionTempsLimite').value = lieuTempsLimite;
                    document.getElementById('gestionDelaiIndice').value = lieuDelaiIndice;
                    document.getElementById('gestionStatut').value = lieuStatut;
                    document.getElementById('gestionEnigmeRequise').checked = (lieuEnigmeRequise === '1');
                });
            }
        });
        
        // Fonction pour ouvrir la modale de création de lieu
        function ouvrirModalCreerLieu() {
            console.log('Fonction ouvrirModalCreerLieu appelée');
            const modal = document.getElementById('creerLieuModal');
            console.log('Modal trouvé:', modal);
            
            if (modal) {
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();
                console.log('Modale ouverte avec succès');
            } else {
                console.error('Modal creerLieuModal non trouvé');
            }
        }
    </script>

    <script>
    // Gestion du type de lieu dans le formulaire de création
    document.addEventListener('DOMContentLoaded', function() {
        const typeRadios = document.querySelectorAll('input[name="type_lieu"]');
        const champsEnigme = document.querySelectorAll('.champs-enigme');
        
        function toggleChampsEnigme() {
            const typeFin = document.getElementById('type_fin').checked;
            champsEnigme.forEach(champ => {
                champ.style.display = typeFin ? 'none' : 'block';
            });
        }
        
        // Ajouter l'écouteur sur chaque radio
        typeRadios.forEach(radio => {
            radio.addEventListener('change', toggleChampsEnigme);
        });
        
        // État initial
        toggleChampsEnigme();
    });
    </script>

<?php include 'includes/footer.php'; ?>
