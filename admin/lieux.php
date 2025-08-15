<?php
session_start();
require_once '../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

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
    }
}

// Récupération des lieux avec leurs énigmes
try {
    $stmt = $pdo->query("
        SELECT l.*, e.id as enigme_id, e.titre as enigme_titre, e.actif as enigme_active,
               te.nom as type_nom, te.template, COALESCE(l.delai_indice, 6) as delai_indice
        FROM lieux l 
        LEFT JOIN enigmes e ON l.enigme_id = e.id 
        LEFT JOIN types_enigmes te ON e.type_enigme_id = te.id
        ORDER BY l.ordre, l.nom
    ");
    $lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

// Configuration pour le header
$page_title = 'Gestion des Lieux - Administration Cyberchasse';
$current_page = 'lieux';
$breadcrumb_items = [
    ['text' => 'Tableau de bord', 'url' => 'admin.php', 'active' => false],
    ['text' => 'Gestion des Lieux', 'url' => 'lieux.php', 'active' => true]
];

include 'includes/header.php';
?>

        <!-- Messages de succès/erreur -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
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
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                            <?php echo htmlspecialchars($lieu['nom']); ?>
                                        </h5>
                                        <div>
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
                                            <div class="col-6">
                                                <small class="text-muted">Temps limite :</small>
                                                <strong><?php echo gmdate('i:s', $lieu['temps_limite']); ?></strong>
                                            </div>
                                        </div>
                                        
                                        <!-- NOUVEAU : Affichage du délai d'indice -->
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
                                        
                                        <div class="d-flex gap-2">
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
                                                        data-lieu-nom="<?php echo htmlspecialchars($lieu['nom']); ?>"
                                                        data-enigme-id="">
                                                    <i class="fas fa-plus"></i> Affecter une énigme
                                                </button>
                                            <?php endif; ?>
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

    <!-- Scripts spécifiques à cette page -->
    <script>
        // Gestion du modal d'affectation d'énigme
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>

<?php include 'includes/footer.php'; ?>
