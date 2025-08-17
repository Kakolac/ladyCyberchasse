<?php
session_start();
require_once '../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Traitement des actions de gestion des équipes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create_equipe':
            $nom = trim($_POST['nom']);
            $couleur = $_POST['couleur'];
            $mot_de_passe = $_POST['mot_de_passe'];
            
            if (!empty($nom) && !empty($couleur) && !empty($mot_de_passe)) {
                $hash_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO equipes (nom, couleur, mot_de_passe) VALUES (?, ?, ?)");
                if ($stmt->execute([$nom, $couleur, $hash_password])) {
                    $success_message = "Équipe '{$nom}' créée avec succès !";
                } else {
                    $error_message = "Erreur lors de la création de l'équipe";
                }
            } else {
                $error_message = "Tous les champs sont obligatoires";
            }
            break;
            
        case 'update_equipe':
            $equipe_id = $_POST['equipe_id'];
            $nom = trim($_POST['nom']);
            $couleur = $_POST['couleur'];
            $statut = $_POST['statut'];
            
            if (!empty($nom) && !empty($couleur)) {
                $stmt = $pdo->prepare("UPDATE equipes SET nom = ?, couleur = ?, statut = ? WHERE id = ?");
                if ($stmt->execute([$nom, $couleur, $statut, $equipe_id])) {
                    $success_message = "Équipe '{$nom}' mise à jour avec succès !";
                } else {
                    $error_message = "Erreur lors de la mise à jour de l'équipe";
                }
            } else {
                $error_message = "Tous les champs sont obligatoires";
            }
            break;
            
        case 'delete_equipe':
            $equipe_id = $_POST['equipe_id'];
            $equipe_nom = $_POST['equipe_nom'];
            
            // Vérifier s'il y a des parcours associés
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM parcours WHERE equipe_id = ?");
            $stmt->execute([$equipe_id]);
            $parcours_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($parcours_count > 0) {
                $error_message = "Impossible de supprimer l'équipe '{$equipe_nom}' : {$parcours_count} parcours associés. Supprimez d'abord les parcours.";
            } else {
                $stmt = $pdo->prepare("DELETE FROM equipes WHERE id = ?");
                if ($stmt->execute([$equipe_id])) {
                    $success_message = "Équipe '{$equipe_nom}' supprimée avec succès !";
                } else {
                    $error_message = "Erreur lors de la suppression de l'équipe";
                }
            }
            break;
            
        case 'reset_equipe_password':
            $equipe_id = $_POST['equipe_id'];
            $equipe_nom = $_POST['equipe_nom'];
            $nouveau_mot_de_passe = bin2hex(random_bytes(8)); // Mot de passe aléatoire de 16 caractères
            
            $hash_password = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE equipes SET mot_de_passe = ? WHERE id = ?");
            if ($stmt->execute([$hash_password, $equipe_id])) {
                $success_message = "Mot de passe de l'équipe '{$equipe_nom}' réinitialisé. Nouveau mot de passe : <strong>{$nouveau_mot_de_passe}</strong>";
            } else {
                $error_message = "Erreur lors de la réinitialisation du mot de passe";
            }
            break;
    }
}

// Récupération des statistiques
try {
    // Statistiques des équipes
    $stmt = $pdo->query("SELECT COUNT(*) as total_equipes FROM equipes");
    $totalEquipes = $stmt->fetch(PDO::FETCH_ASSOC)['total_equipes'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as equipes_connectees FROM equipes WHERE statut = 'active'");
    $equipesConnectees = $stmt->fetch(PDO::FETCH_ASSOC)['equipes_connectees'];
    
    // Statistiques des parcours
    $stmt = $pdo->query("SELECT COUNT(*) as total_parcours FROM parcours");
    $totalParcours = $stmt->fetch(PDO::FETCH_ASSOC)['total_parcours'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as parcours_termines FROM parcours WHERE statut = 'termine'");
    $parcoursTermines = $stmt->fetch(PDO::FETCH_ASSOC)['parcours_termines'];
    
    // Statistiques des lieux
    $stmt = $pdo->query("SELECT COUNT(*) as total_lieux FROM lieux");
    $totalLieux = $stmt->fetch(PDO::FETCH_ASSOC)['total_lieux'];
    
    // Récupération des équipes pour la gestion
    $stmt = $pdo->query("SELECT * FROM equipes ORDER BY nom");
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Dernières activités
    $stmt = $pdo->query("
        SELECT s.*, e.nom as equipe_nom, l.nom as lieu_nom 
        FROM sessions_jeu s 
        JOIN equipes e ON s.equipe_id = e.id 
        JOIN lieux l ON s.lieu_id = l.id 
        ORDER BY s.created_at DESC 
        LIMIT 10
    ");
    $dernieresActivites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des données: " . $e->getMessage();
}

// Configuration pour le header
$page_title = 'Tableau de Bord - Administration Cyberchasse';
$breadcrumb_items = [
    ['text' => 'Tableau de bord', 'url' => 'admin.php', 'active' => true]
];

// Inclure le header
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
            .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
            .stat-card.success { background: linear-gradient(135deg, #28a745, #20c997); }
            .stat-card.warning { background: linear-gradient(135deg, #ffc107, #fd7e14); }
            .stat-card.info { background: linear-gradient(135deg, #17a2b8, #6f42c1); }
            .tool-card { transition: transform 0.3s ease; }
            .tool-card:hover { transform: translateY(-5px); }
            .activity-item { border-left: 4px solid #007bff; padding-left: 15px; margin-bottom: 10px; }
            .activity-item.success { border-left-color: #28a745; }
            .activity-item.warning { border-left-color: #ffc107; }
            .activity-item.danger { border-left-color: #dc3545; }
            .equipe-badge {
                padding: 8px 16px;
                border-radius: 20px;
                font-weight: 600;
                color: white;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            }
            .equipe-rouge { background: linear-gradient(45deg, #ff6b6b, #ee5a52); }
            .equipe-bleu { background: linear-gradient(45deg, #4ecdc4, #44a08d); }
            .equipe-vert { background: linear-gradient(45deg, #45b7d1, #96ceb4); }
            .equipe-jaune { background: linear-gradient(45deg, #f9ca24, #f0932b); }
            .equipe-orange { background: linear-gradient(45deg, #fd7e14, #f0932b); }
            .equipe-violet { background: linear-gradient(45deg, #6f42c1, #495057); }
            .equipe-rose { background: linear-gradient(45deg, #e74c3c, #c0392b); }
            .equipe-gris { background: linear-gradient(45deg, #6c757d, #495057); }
            .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
            .btn-close { filter: invert(1); }
        </style>

        <!-- Statistiques -->
        <div class="row mb-4" id="statistiques">
            <div class="col-md-3">
                <div class="card admin-card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h3><?php echo $totalEquipes ?? 0; ?></h3>
                        <p class="mb-0">Total Équipes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card stat-card success">
                    <div class="card-body text-center">
                        <i class="fas fa-user-check fa-3x mb-3"></i>
                        <h3><?php echo $equipesConnectees ?? 0; ?></h3>
                        <p class="mb-0">Équipes Actives</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card stat-card warning">
                    <div class="card-body text-center">
                        <i class="fas fa-route fa-3x mb-3"></i>
                        <h3><?php echo $totalParcours ?? 0; ?></h3>
                        <p class="mb-0">Total Parcours</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card stat-card info">
                    <div class="card-body text-center">
                        <i class="fas fa-flag-checkered fa-3x mb-3"></i>
                        <h3><?php echo $parcoursTermines ?? 0; ?></h3>
                        <p class="mb-0">Parcours Terminés</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <?php include 'includes/outils_administration.php'; ?>
        </div>
    </div>

    <!-- Modal de gestion des équipes -->
    <div class="modal fade" id="equipesModal" tabindex="-1" aria-labelledby="equipesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipesModalLabel">
                        <i class="fas fa-users"></i> Gestion des Équipes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Création d'une nouvelle équipe -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6><i class="fas fa-plus"></i> Créer une Nouvelle Équipe</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="row g-3">
                                <input type="hidden" name="action" value="create_equipe">
                                
                                <div class="col-md-4">
                                    <label for="nom" class="form-label">Nom de l'équipe</label>
                                    <input type="text" class="form-control" name="nom" required placeholder="Ex: Rouge">
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="couleur" class="form-label">Couleur</label>
                                    <select class="form-select" name="couleur" required>
                                        <option value="">Choisir...</option>
                                        <option value="rouge">🔴 Rouge</option>
                                        <option value="bleu">🔵 Bleu</option>
                                        <option value="vert">🟢 Vert</option>
                                        <option value="jaune">🟡 Jaune</option>
                                        <option value="orange">🟠 Orange</option>
                                        <option value="violet">🟣 Violet</option>
                                        <option value="rose">🩷 Rose</option>
                                        <option value="gris">⚫ Gris</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="mot_de_passe" class="form-label">Mot de passe</label>
                                    <input type="text" class="form-control" name="mot_de_passe" required placeholder="Mot de passe">
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-plus"></i> Créer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Liste des équipes existantes -->
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-list"></i> Équipes Existantes (<?php echo count($equipes); ?>)</h6>
                        </div>
                        <div class="card-body">
                            <?php if (empty($equipes)): ?>
                                <div class="alert alert-info">
                                    <h6>ℹ️ Aucune équipe trouvée</h6>
                                    <p>Créez votre première équipe en utilisant le formulaire ci-dessus.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Équipe</th>
                                                <th>Statut</th>
                                                <th>Score</th>
                                                <th>Temps Total</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($equipes as $equipe): ?>
                                                <tr>
                                                    <td>
                                                        <span class="equipe-badge equipe-<?php echo strtolower($equipe['couleur']); ?>">
                                                            <?php echo htmlspecialchars($equipe['nom']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $equipe['statut'] === 'active' ? 'success' : ($equipe['statut'] === 'inactive' ? 'secondary' : 'warning'); ?>">
                                                            <?php echo $equipe['statut']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info"><?php echo $equipe['score'] ?? 0; ?> pts</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary"><?php echo gmdate('H:i:s', $equipe['temps_total'] ?? 0); ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-warning btn-sm" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editEquipeModal"
                                                                    data-equipe-id="<?php echo $equipe['id']; ?>"
                                                                    data-equipe-nom="<?php echo htmlspecialchars($equipe['nom']); ?>"
                                                                    data-equipe-couleur="<?php echo $equipe['couleur']; ?>"
                                                                    data-equipe-statut="<?php echo $equipe['statut']; ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            
                                                            <button type="button" class="btn btn-info btn-sm" 
                                                                    onclick="resetPassword(<?php echo $equipe['id']; ?>, '<?php echo htmlspecialchars($equipe['nom']); ?>')">
                                                                <i class="fas fa-key"></i>
                                                            </button>
                                                            
                                                            <button type="button" class="btn btn-danger btn-sm" 
                                                                    onclick="deleteEquipe(<?php echo $equipe['id']; ?>, '<?php echo htmlspecialchars($equipe['nom']); ?>')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'édition d'équipe -->
    <div class="modal fade" id="editEquipeModal" tabindex="-1" aria-labelledby="editEquipeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEquipeModalLabel">
                        <i class="fas fa-edit"></i> Modifier l'Équipe
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_equipe">
                        <input type="hidden" name="equipe_id" id="editEquipeId">
                        
                        <div class="mb-3">
                            <label for="editNom" class="form-label">Nom de l'équipe</label>
                            <input type="text" class="form-control" name="nom" id="editNom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editCouleur" class="form-label">Couleur</label>
                            <select class="form-select" name="couleur" id="editCouleur" required>
                                <option value="rouge">🔴 Rouge</option>
                                <option value="blue">🔵 Bleu</option>
                                <option value="vert">🟢 Vert</option>
                                <option value="jaune">🟡 Jaune</option>
                                <option value="orange">🟠 Orange</option>
                                <option value="violet">🟣 Violet</option>
                                <option value="rose">🩷 Rose</option>
                                <option value="gris">⚫ Gris</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editStatut" class="form-label">Statut</label>
                            <select class="form-select" name="statut" id="editStatut" required>
                                <option value="active">🟢 Active</option>
                                <option value="inactive">⚫ Inactive</option>
                                <option value="terminee">🏁 Terminée</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts spécifiques à cette page -->
    <script>
        // Gestion du modal d'édition d'équipe
        document.addEventListener('DOMContentLoaded', function() {
            const editEquipeModal = document.getElementById('editEquipeModal');
            if (editEquipeModal) {
                editEquipeModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const equipeId = button.getAttribute('data-equipe-id');
                    const equipeNom = button.getAttribute('data-equipe-nom');
                    const equipeCouleur = button.getAttribute('data-equipe-couleur');
                    const equipeStatut = button.getAttribute('data-equipe-statut');
                    
                    // Mettre à jour le modal
                    document.getElementById('editEquipeId').value = equipeId;
                    document.getElementById('editNom').value = equipeNom;
                    document.getElementById('editCouleur').value = equipeCouleur;
                    document.getElementById('editStatut').value = equipeStatut;
                });
            }
        });
        
        // Réinitialisation du mot de passe
        function resetPassword(equipeId, equipeNom) {
            Swal.fire({
                title: '🔑 Réinitialiser le mot de passe',
                text: `Voulez-vous réinitialiser le mot de passe de l'équipe '${equipeNom}' ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, réinitialiser',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="reset_equipe_password">
                        <input type="hidden" name="equipe_id" value="${equipeId}">
                        <input type="hidden" name="equipe_nom" value="${equipeNom}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        
        // Suppression d'équipe
        function deleteEquipe(equipeId, equipeNom) {
            Swal.fire({
                title: '🗑️ Supprimer l\'équipe',
                text: `Voulez-vous vraiment supprimer l'équipe '${equipeNom}' ? Cette action est irréversible.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="delete_equipe">
                        <input type="hidden" name="equipe_id" value="${equipeId}">
                        <input type="hidden" name="equipe_nom" value="${equipeNom}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

    <!-- Modal du Wizard -->
    <?php include 'includes/wizard_modal.php'; ?>

<?php
// Inclure le footer
include 'includes/footer.php';
?>
