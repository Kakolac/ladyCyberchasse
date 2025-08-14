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
        case 'create_enigme':
            $type_enigme_id = $_POST['type_enigme_id'];
            $titre = trim($_POST['titre']);
            $donnees_json = $_POST['donnees_json'];
            
            if (!empty($type_enigme_id) && !empty($titre) && !empty($donnees_json)) {
                // Validation JSON
                $donnees = json_decode($donnees_json, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $stmt = $pdo->prepare("INSERT INTO enigmes (type_enigme_id, titre, donnees) VALUES (?, ?, ?)");
                    if ($stmt->execute([$type_enigme_id, $titre, $donnees_json])) {
                        $success_message = "Énigme créée avec succès !";
                    } else {
                        $error_message = "Erreur lors de la création de l'énigme";
                    }
                } else {
                    $error_message = "Format JSON invalide";
                }
            } else {
                $error_message = "Tous les champs sont obligatoires";
            }
            break;
            
        case 'update_enigme':
            $enigme_id = $_POST['enigme_id'];
            $type_enigme_id = $_POST['type_enigme_id'];
            $titre = trim($_POST['titre']);
            $donnees_json = $_POST['donnees_json'];
            $actif = isset($_POST['actif']) ? 1 : 0;
            
            if (!empty($enigme_id) && !empty($type_enigme_id) && !empty($titre) && !empty($donnees_json)) {
                $donnees = json_decode($donnees_json, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $stmt = $pdo->prepare("UPDATE enigmes SET type_enigme_id = ?, titre = ?, donnees = ?, actif = ? WHERE id = ?");
                    if ($stmt->execute([$type_enigme_id, $titre, $donnees_json, $actif, $enigme_id])) {
                        $success_message = "Énigme mise à jour avec succès !";
                    } else {
                        $error_message = "Erreur lors de la mise à jour de l'énigme";
                    }
                } else {
                    $error_message = "Format JSON invalide";
                }
            } else {
                $error_message = "Tous les champs sont obligatoires";
            }
            break;
            
        case 'delete_enigme':
            $enigme_id = $_POST['enigme_id'];
            
            // Vérifier qu'aucun lieu n'utilise cette énigme
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM lieux WHERE enigme_id = ?");
            $stmt->execute([$enigme_id]);
            $count = $stmt->fetchColumn();
            
            if ($count == 0) {
                $stmt = $pdo->prepare("DELETE FROM enigmes WHERE id = ?");
                if ($stmt->execute([$enigme_id])) {
                    $success_message = "Énigme supprimée avec succès !";
                } else {
                    $error_message = "Erreur lors de la suppression de l'énigme";
                }
            } else {
                $error_message = "Impossible de supprimer cette énigme : elle est utilisée par " . $count . " lieu(x)";
            }
            break;
    }
}

// Récupération des énigmes avec leurs types
try {
    $stmt = $pdo->query("
        SELECT e.*, te.nom as type_nom, te.template, te.actif as type_actif
        FROM enigmes e 
        LEFT JOIN types_enigmes te ON e.type_enigme_id = te.id 
        ORDER BY e.created_at DESC
    ");
    $enigmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des énigmes: " . $e->getMessage();
}

// Récupération des types d'énigmes pour les formulaires
try {
    $stmt = $pdo->query("SELECT id, nom, template FROM types_enigmes WHERE actif = 1 ORDER BY nom");
    $types_enigmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $types_enigmes = [];
}

// Configuration pour le header
$page_title = 'Gestion des Énigmes - Administration Cyberchasse';
$current_page = 'enigmes';
$breadcrumb_items = [
    ['text' => 'Tableau de bord', 'url' => 'admin.php', 'active' => false],
    ['text' => 'Énigmes', 'url' => 'enigmes.php', 'active' => true]
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
            .enigme-card { transition: transform 0.3s ease; }
            .enigme-card:hover { transform: translateY(-5px); }
            .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
            .btn-close { filter: invert(1); }
            .json-editor { font-family: 'Courier New', monospace; font-size: 0.9em; }
        </style>

        <!-- En-tête de la page -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-puzzle-piece"></i> Gestion des Énigmes</h1>
                <p class="text-muted">Créer et configurer les énigmes de différents types</p>
            </div>
            <div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createEnigmeModal">
                    <i class="fas fa-plus"></i> Nouvelle Énigme
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
                        <h3><?php echo count($enigmes); ?></h3>
                        <p class="mb-0">Total Énigmes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h3><?php echo count(array_filter($enigmes, function($e) { return $e['actif']; })); ?></h3>
                        <p class="mb-0">Énigmes Actives</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-list fa-3x mb-3"></i>
                        <h3><?php echo count(array_unique(array_column($enigmes, 'type_nom'))); ?></h3>
                        <p class="mb-0">Types Utilisés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-warning text-dark">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                        <h3><?php 
                            $stmt = $pdo->query("SELECT COUNT(*) FROM lieux WHERE enigme_id IS NOT NULL");
                            echo $stmt->fetchColumn();
                        ?></h3>
                        <p class="mb-0">Lieux avec Énigmes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des énigmes -->
        <div class="card admin-card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-list"></i> Énigmes Disponibles</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <h6>❌ Erreur</h6>
                        <p><?php echo $error; ?></p>
                    </div>
                <?php elseif (empty($enigmes)): ?>
                    <div class="alert alert-info">
                        <h6>ℹ️ Aucune énigme trouvée</h6>
                        <p>Créez votre première énigme pour commencer.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($enigmes as $enigme): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card enigme-card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-puzzle-piece text-primary"></i>
                                            <?php echo htmlspecialchars($enigme['titre']); ?>
                                        </h5>
                                        <div>
                                            <span class="badge bg-<?php echo $enigme['actif'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $enigme['actif'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                            <span class="badge bg-info ms-1">
                                                <?php echo htmlspecialchars($enigme['type_nom']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Type :</small>
                                                <strong><?php echo htmlspecialchars($enigme['type_nom']); ?></strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Template :</small>
                                                <strong><?php echo htmlspecialchars($enigme['template']); ?></strong>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">Données JSON :</small>
                                            <div class="json-editor bg-light p-2 rounded mt-1" style="max-height: 100px; overflow-y: auto;">
                                                <pre><?php echo htmlspecialchars(substr($enigme['donnees'], 0, 200)); ?><?php echo strlen($enigme['donnees']) > 200 ? '...' : ''; ?></pre>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editEnigmeModal"
                                                    data-enigme-id="<?php echo $enigme['id']; ?>"
                                                    data-type-enigme-id="<?php echo $enigme['type_enigme_id']; ?>"
                                                    data-titre="<?php echo htmlspecialchars($enigme['titre']); ?>"
                                                    data-donnees="<?php echo htmlspecialchars($enigme['donnees']); ?>"
                                                    data-actif="<?php echo $enigme['actif']; ?>">
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>
                                            
                                            <?php if ($enigme['actif']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_enigme">
                                                    <input type="hidden" name="enigme_id" value="<?php echo $enigme['id']; ?>">
                                                    <input type="hidden" name="type_enigme_id" value="<?php echo $enigme['type_enigme_id']; ?>">
                                                    <input type="hidden" name="titre" value="<?php echo htmlspecialchars($enigme['titre']); ?>">
                                                    <input type="hidden" name="donnees_json" value="<?php echo htmlspecialchars($enigme['donnees']); ?>">
                                                    <input type="hidden" name="actif" value="0">
                                                    <button type="submit" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-pause"></i> Désactiver
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_enigme">
                                                    <input type="hidden" name="enigme_id" value="<?php echo $enigme['id']; ?>">
                                                    <input type="hidden" name="type_enigme_id" value="<?php echo $enigme['type_enigme_id']; ?>">
                                                    <input type="hidden" name="titre" value="<?php echo htmlspecialchars($enigme['titre']); ?>">
                                                    <input type="hidden" name="donnees_json" value="<?php echo htmlspecialchars($enigme['donnees']); ?>">
                                                    <input type="hidden" name="actif" value="1">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-play"></i> Activer
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette énigme ?')">
                                                <input type="hidden" name="action" value="delete_enigme">
                                                <input type="hidden" name="enigme_id" value="<?php echo $enigme['id']; ?>">
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

    <!-- Modal de création d'énigme -->
    <div class="modal fade" id="createEnigmeModal" tabindex="-1" aria-labelledby="createEnigmeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEnigmeModalLabel">
                        <i class="fas fa-plus"></i> Nouvelle Énigme
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_enigme">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type_enigme_id" class="form-label">Type d'énigme</label>
                                    <select class="form-select" name="type_enigme_id" id="type_enigme_id" required>
                                        <option value="">Sélectionner un type</option>
                                        <?php foreach ($types_enigmes as $type): ?>
                                            <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['nom']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="titre" class="form-label">Titre de l'énigme</label>
                                    <input type="text" class="form-control" name="titre" id="titre" required 
                                           placeholder="Ex: Question sur la cybersécurité">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="donnees_json" class="form-label">Données JSON</label>
                            <textarea class="form-control json-editor" name="donnees_json" id="donnees_json" rows="10" required 
                                      placeholder='{"question": "Votre question ici", "reponse_correcte": "A", "options": {"A": "Option A", "B": "Option B", "C": "Option C", "D": "Option D"}}'></textarea>
                            <small class="text-muted">Format JSON valide selon le type d'énigme sélectionné</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Conseil :</strong> Le format JSON dépend du type d'énigme. Consultez la documentation du type pour connaître la structure attendue.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Créer l'énigme
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'édition d'énigme -->
    <div class="modal fade" id="editEnigmeModal" tabindex="-1" aria-labelledby="editEnigmeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEnigmeModalLabel">
                        <i class="fas fa-edit"></i> Modifier l'Énigme
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_enigme">
                        <input type="hidden" name="enigme_id" id="editEnigmeId">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editTypeEnigmeId" class="form-label">Type d'énigme</label>
                                    <select class="form-select" name="type_enigme_id" id="editTypeEnigmeId" required>
                                        <?php foreach ($types_enigmes as $type): ?>
                                            <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['nom']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editTitre" class="form-label">Titre de l'énigme</label>
                                    <input type="text" class="form-control" name="titre" id="editTitre" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editDonneesJson" class="form-label">Données JSON</label>
                            <textarea class="form-control json-editor" name="donnees_json" id="editDonneesJson" rows="10" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="actif" id="editActif" value="1">
                                <label class="form-check-label" for="editActif">
                                    Énigme active
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
            const editEnigmeModal = document.getElementById('editEnigmeModal');
            if (editEnigmeModal) {
                editEnigmeModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const enigmeId = button.getAttribute('data-enigme-id');
                    const typeEnigmeId = button.getAttribute('data-type-enigme-id');
                    const titre = button.getAttribute('data-titre');
                    const donnees = button.getAttribute('data-donnees');
                    const actif = button.getAttribute('data-actif');
                    
                    // Mettre à jour le modal
                    document.getElementById('editEnigmeId').value = enigmeId;
                    document.getElementById('editTypeEnigmeId').value = typeEnigmeId;
                    document.getElementById('editTitre').value = titre;
                    document.getElementById('editDonneesJson').value = donnees;
                    document.getElementById('editActif').checked = actif === '1';
                });
            }
        });
    </script>

<?php include 'includes/footer.php'; ?>