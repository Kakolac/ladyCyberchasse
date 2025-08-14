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
        case 'create_type':
            $nom = trim($_POST['nom']);
            $description = trim($_POST['description']);
            $template = trim($_POST['template']);
            
            if (!empty($nom) && !empty($template)) {
                $stmt = $pdo->prepare("INSERT INTO types_enigmes (nom, description, template) VALUES (?, ?, ?)");
                if ($stmt->execute([$nom, $description, $template])) {
                    $success_message = "Type d'énigme créé avec succès !";
                } else {
                    $error_message = "Erreur lors de la création du type d'énigme";
                }
            } else {
                $error_message = "Le nom et le template sont obligatoires";
            }
            break;
            
        case 'update_type':
            $type_id = $_POST['type_id'];
            $nom = trim($_POST['nom']);
            $description = trim($_POST['description']);
            $template = trim($_POST['template']);
            $actif = isset($_POST['actif']) ? 1 : 0;
            
            if (!empty($nom) && !empty($template)) {
                $stmt = $pdo->prepare("UPDATE types_enigmes SET nom = ?, description = ?, template = ?, actif = ? WHERE id = ?");
                if ($stmt->execute([$nom, $description, $template, $actif, $type_id])) {
                    $success_message = "Type d'énigme mis à jour avec succès !";
                } else {
                    $error_message = "Erreur lors de la mise à jour du type d'énigme";
                }
            } else {
                $error_message = "Le nom et le template sont obligatoires";
            }
            break;
            
        case 'delete_type':
            $type_id = $_POST['type_id'];
            
            // Vérifier qu'aucune énigme n'utilise ce type
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM enigmes WHERE type_enigme_id = ?");
            $stmt->execute([$type_id]);
            $count = $stmt->fetchColumn();
            
            if ($count == 0) {
                $stmt = $pdo->prepare("DELETE FROM types_enigmes WHERE id = ?");
                if ($stmt->execute([$type_id])) {
                    $success_message = "Type d'énigme supprimé avec succès !";
                } else {
                    $error_message = "Erreur lors de la suppression du type d'énigme";
                }
            } else {
                $error_message = "Impossible de supprimer ce type : il est utilisé par " . $count . " énigme(s)";
            }
            break;
    }
}

// Récupération des types d'énigmes
try {
    $stmt = $pdo->query("SELECT * FROM types_enigmes ORDER BY nom");
    $types_enigmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des types d'énigmes: " . $e->getMessage();
}

// Configuration pour le header
$page_title = 'Gestion des Types d\'Énigmes - Administration Cyberchasse';
$current_page = 'types_enigmes';
$breadcrumb_items = [
    ['text' => 'Tableau de bord', 'url' => 'admin.php', 'active' => false],
    ['text' => 'Types d\'Énigmes', 'url' => 'types_enigmes.php', 'active' => true]
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

        <!-- Styles CSS spécifiques -->
        <style>
            .admin-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
            .type-card { transition: transform 0.3s ease; }
            .type-card:hover { transform: translateY(-5px); }
            .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
            .btn-close { filter: invert(1); }
        </style>

        <!-- En-tête de la page -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-puzzle-piece"></i> Gestion des Types d'Énigmes</h1>
                <p class="text-muted">Définir et configurer les différents types d'énigmes disponibles</p>
            </div>
            <div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTypeModal">
                    <i class="fas fa-plus"></i> Nouveau Type
                </button>
                <a href="admin.php" class="btn btn-secondary ms-2">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card admin-card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-puzzle-piece fa-3x mb-3"></i>
                        <h3><?php echo count($types_enigmes); ?></h3>
                        <p class="mb-0">Types d'Énigmes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h3><?php echo count(array_filter($types_enigmes, function($t) { return $t['actif']; })); ?></h3>
                        <p class="mb-0">Types Actifs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-list fa-3x mb-3"></i>
                        <h3><?php echo count(array_filter($types_enigmes, function($t) { return !$t['actif']; })); ?></h3>
                        <p class="mb-0">Types Inactifs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-warning text-dark">
                    <div class="card-body text-center">
                        <i class="fas fa-cogs fa-3x mb-3"></i>
                        <h3><?php echo count(array_filter($types_enigmes, function($t) { return !empty($t['template']); })); ?></h3>
                        <p class="mb-0">Templates Configurés</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des types d'énigmes -->
        <div class="card admin-card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-list"></i> Types d'Énigmes Disponibles</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <h6>❌ Erreur</h6>
                        <p><?php echo $error; ?></p>
                    </div>
                <?php elseif (empty($types_enigmes)): ?>
                    <div class="alert alert-info">
                        <h6>ℹ️ Aucun type d'énigme trouvé</h6>
                        <p>Créez votre premier type d'énigme pour commencer.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($types_enigmes as $type): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card type-card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-puzzle-piece text-primary"></i>
                                            <?php echo htmlspecialchars($type['nom']); ?>
                                        </h5>
                                        <div>
                                            <span class="badge bg-<?php echo $type['actif'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $type['actif'] ? 'Actif' : 'Inactif'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">
                                            <?php echo htmlspecialchars($type['description']); ?>
                                        </p>
                                        
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Template :</small>
                                                <strong><?php echo htmlspecialchars($type['template']); ?></strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Créé le :</small>
                                                <strong><?php echo date('d/m/Y', strtotime($type['created_at'])); ?></strong>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editTypeModal"
                                                    data-type-id="<?php echo $type['id']; ?>"
                                                    data-type-nom="<?php echo htmlspecialchars($type['nom']); ?>"
                                                    data-type-description="<?php echo htmlspecialchars($type['description']); ?>"
                                                    data-type-template="<?php echo htmlspecialchars($type['template']); ?>"
                                                    data-type-actif="<?php echo $type['actif']; ?>">
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>
                                            
                                            <?php if ($type['actif']): ?>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir désactiver ce type d\'énigme ?')">
                                                    <input type="hidden" name="action" value="update_type">
                                                    <input type="hidden" name="type_id" value="<?php echo $type['id']; ?>">
                                                    <input type="hidden" name="nom" value="<?php echo htmlspecialchars($type['nom']); ?>">
                                                    <input type="hidden" name="description" value="<?php echo htmlspecialchars($type['description']); ?>">
                                                    <input type="hidden" name="template" value="<?php echo htmlspecialchars($type['template']); ?>">
                                                    <input type="hidden" name="actif" value="0">
                                                    <button type="submit" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-pause"></i> Désactiver
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_type">
                                                    <input type="hidden" name="type_id" value="<?php echo $type['id']; ?>">
                                                    <input type="hidden" name="nom" value="<?php echo htmlspecialchars($type['nom']); ?>">
                                                    <input type="hidden" name="description" value="<?php echo htmlspecialchars($type['description']); ?>">
                                                    <input type="hidden" name="template" value="<?php echo htmlspecialchars($type['template']); ?>">
                                                    <input type="hidden" name="actif" value="1">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-play"></i> Activer
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce type d\'énigme ?')">
                                                <input type="hidden" name="action" value="delete_type">
                                                <input type="hidden" name="type_id" value="<?php echo $type['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </form>
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

    <!-- Modal de création de type -->
    <div class="modal fade" id="createTypeModal" tabindex="-1" aria-labelledby="createTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTypeModalLabel">
                        <i class="fas fa-plus"></i> Nouveau Type d'Énigme
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_type">
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du type</label>
                            <input type="text" class="form-control" name="nom" id="nom" required 
                                   placeholder="Ex: Question à choix multiples">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="3" 
                                      placeholder="Description détaillée du type d'énigme"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="template" class="form-label">Template</label>
                            <input type="text" class="form-control" name="template" id="template" required 
                                   placeholder="Ex: qcm, texte_libre, calcul">
                            <small class="text-muted">Nom du fichier template sans l'extension .php</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Créer le type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'édition de type -->
    <div class="modal fade" id="editTypeModal" tabindex="-1" aria-labelledby="editTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTypeModalLabel">
                        <i class="fas fa-edit"></i> Modifier le Type d'Énigme
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_type">
                        <input type="hidden" name="type_id" id="editTypeId">
                        
                        <div class="mb-3">
                            <label for="editNom" class="form-label">Nom du type</label>
                            <input type="text" class="form-control" name="nom" id="editNom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="editDescription" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editTemplate" class="form-label">Template</label>
                            <input type="text" class="form-control" name="template" id="editTemplate" required>
                            <small class="text-muted">Nom du fichier template sans l'extension .php</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="actif" id="editActif" value="1">
                                <label class="form-check-label" for="editActif">
                                    Type actif
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts spécifiques -->
    <script>
        // Gestion du modal d'édition
        document.addEventListener('DOMContentLoaded', function() {
            const editTypeModal = document.getElementById('editTypeModal');
            if (editTypeModal) {
                editTypeModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const typeId = button.getAttribute('data-type-id');
                    const typeNom = button.getAttribute('data-type-nom');
                    const typeDescription = button.getAttribute('data-type-description');
                    const typeTemplate = button.getAttribute('data-type-template');
                    const typeActif = button.getAttribute('data-type-actif');
                    
                    // Mettre à jour le modal
                    document.getElementById('editTypeId').value = typeId;
                    document.getElementById('editNom').value = typeNom;
                    document.getElementById('editDescription').value = typeDescription;
                    document.getElementById('editTemplate').value = typeTemplate;
                    document.getElementById('editActif').checked = typeActif === '1';
                });
            }
        });
    </script>

<?php include 'includes/footer.php'; ?>