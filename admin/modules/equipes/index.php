<?php
session_start();
require_once '../../../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../../../admin/login.php');
    exit();
}

$success_message = '';
$error_message = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Création d'une nouvelle équipe
                $nom = trim($_POST['nom']);
                $description = trim($_POST['description']);
                $couleur = trim($_POST['couleur']);
                $mot_de_passe = trim($_POST['mot_de_passe']);
                $email_contact = trim($_POST['email_contact']);
                
                // Génération automatique du slug
                $slug = strtolower(trim($nom));
                $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
                $slug = preg_replace('/[\s-]+/', '_', $slug);
                $slug = trim($slug, '_');
                
                if (!empty($nom) && !empty($couleur) && !empty($mot_de_passe)) {
                    try {
                        // Vérifier si le nom ou le slug existe déjà
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_equipes WHERE nom = ? OR slug = ?");
                        $stmt->execute([$nom, $slug]);
                        
                        if ($stmt->fetchColumn() > 0) {
                            $error_message = "Une équipe avec ce nom existe déjà";
                        } else {
                            // Hasher le mot de passe
                            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                            
                            $stmt = $pdo->prepare("
                                INSERT INTO cyber_equipes (nom, slug, description, couleur, mot_de_passe, email_contact) 
                                VALUES (?, ?, ?, ?, ?, ?)
                            ");
                            
                            if ($stmt->execute([$nom, $slug, $description, $couleur, $mot_de_passe_hash, $email_contact])) {
                                $success_message = "Équipe '$nom' créée avec succès !";
                            } else {
                                $error_message = "Erreur lors de la création de l'équipe";
                            }
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "Le nom, la couleur et le mot de passe sont obligatoires";
                }
                break;
                
            case 'update':
                // Modification d'une équipe existante
                $equipe_id = (int)$_POST['equipe_id'];
                $nom = trim($_POST['nom']);
                $description = trim($_POST['description']);
                $couleur = trim($_POST['couleur']);
                $email_contact = trim($_POST['email_contact']);
                $statut = $_POST['statut'];
                
                if (!empty($nom) && !empty($couleur) && $equipe_id > 0) {
                    try {
                        // Vérifier si le nom existe déjà pour une autre équipe
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_equipes WHERE nom = ? AND id != ?");
                        $stmt->execute([$nom, $equipe_id]);
                        
                        if ($stmt->fetchColumn() > 0) {
                            $error_message = "Une autre équipe avec ce nom existe déjà";
                        } else {
                            $stmt = $pdo->prepare("
                                UPDATE cyber_equipes SET nom = ?, description = ?, couleur = ?, email_contact = ?, statut = ? 
                                WHERE id = ?
                            ");
                            
                            if ($stmt->execute([$nom, $description, $couleur, $email_contact, $statut, $equipe_id])) {
                                $success_message = "Équipe '$nom' modifiée avec succès !";
                            } else {
                                $error_message = "Erreur lors de la modification de l'équipe";
                            }
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "Données invalides pour la modification";
                }
                break;
                
            case 'delete':
                // Suppression d'une équipe
                $equipe_id = (int)$_POST['equipe_id'];
                $equipe_nom = $_POST['equipe_nom'];
                
                if ($equipe_id > 0) {
                    try {
                        // Vérifier si l'équipe a un parcours actif
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_equipes_parcours WHERE equipe_id = ? AND statut = 'en_cours'");
                        $stmt->execute([$equipe_id]);
                        
                        if ($stmt->fetchColumn() > 0) {
                            $error_message = "Impossible de supprimer cette équipe : elle a un parcours en cours";
                        } else {
                            $stmt = $pdo->prepare("DELETE FROM cyber_equipes WHERE id = ?");
                            if ($stmt->execute([$equipe_id])) {
                                $success_message = "Équipe '$equipe_nom' supprimée avec succès !";
                            } else {
                                $error_message = "Erreur lors de la suppression de l'équipe";
                            }
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "ID d'équipe invalide";
                }
                break;
        }
    }
}

// Récupération des équipes existantes
try {
    $stmt = $pdo->query("
        SELECT e.*, 
               COUNT(ep.id) as nb_parcours_actifs
        FROM cyber_equipes e 
        LEFT JOIN cyber_equipes_parcours ep ON e.id = ep.equipe_id AND ep.statut = 'en_cours'
        GROUP BY e.id 
        ORDER BY e.nom
    ");
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = "Erreur lors de la récupération des équipes : " . $e->getMessage();
    $equipes = [];
}

// Configuration pour le header
$page_title = 'Gestion des Équipes - Administration Cyberchasse';
$breadcrumb_items = [
    ['text' => 'Administration', 'url' => '../../../admin/admin2.php', 'active' => false],
    ['text' => 'Gestion des Équipes', 'url' => 'index.php', 'active' => true]
];

include '../../../admin/includes/header.php';
?>

<div class="container-fluid">
    <!-- Messages de succès/erreur -->
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Formulaire de création -->
        <div class="col-md-4">
            <div class="card admin-card">
                <div class="card-header">
                    <h5><i class="fas fa-plus"></i> Créer une Nouvelle Équipe</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom de l'équipe *</label>
                            <input type="text" class="form-control" id="nom" name="nom" required 
                                   placeholder="ex: Rouge, Bleu, Vert...">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"
                                      placeholder="Description de l'équipe..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="couleur" class="form-label">Couleur *</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="couleur" name="couleur" 
                                       value="#007bff" required>
                                <input type="text" class="form-control" id="couleur_hex" placeholder="#007bff" 
                                       pattern="^#[0-9A-Fa-f]{6}$" required>
                            </div>
                            <small class="text-muted">Choisissez une couleur distinctive pour l'équipe</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mot_de_passe" class="form-label">Mot de passe *</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                            <small class="text-muted">Mot de passe pour l'accès de l'équipe</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email_contact" class="form-label">Email de contact</label>
                            <input type="email" class="form-control" id="email_contact" name="email_contact" 
                                   placeholder="contact@equipe.com">
                            <small class="text-muted">Email pour contacter l'équipe</small>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer l'équipe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Liste des équipes existantes -->
        <div class="col-md-8">
            <div class="card admin-card">
                <div class="card-header">
                    <h5><i class="fas fa-list"></i> Équipes Existantes (<?php echo count($equipes); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($equipes)): ?>
                        <div class="alert alert-info">
                            <h6>ℹ️ Aucune équipe trouvée</h6>
                            <p>Créez votre première équipe en utilisant le formulaire à gauche.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Équipe</th>
                                        <th>Description</th>
                                        <th>Contact</th>
                                        <th>Statut</th>
                                        <th>Parcours</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($equipes as $equipe): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2" style="width: 20px; height: 20px; background-color: <?php echo $equipe['couleur']; ?>; border-radius: 50%;"></div>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($equipe['nom']); ?></strong>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($equipe['slug']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars(substr($equipe['description'], 0, 50)); ?>
                                                    <?php if (strlen($equipe['description']) > 50): ?>...<?php endif; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($equipe['email_contact']): ?>
                                                    <small><?php echo htmlspecialchars($equipe['email_contact']); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $equipe['statut'] === 'active' ? 'success' : 
                                                        ($equipe['statut'] === 'disqualifiee' ? 'danger' : 'secondary'); 
                                                ?>">
                                                    <?php echo ucfirst($equipe['statut']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($equipe['nb_parcours_actifs'] > 0): ?>
                                                    <span class="badge bg-warning"><?php echo $equipe['nb_parcours_actifs']; ?> actif(s)</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Aucun</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-warning text-dark" style="cursor: pointer; padding: 8px 12px;" 
                                                          onclick="editEquipe(<?php echo htmlspecialchars(json_encode($equipe)); ?>)" title="Modifier">
                                                        ✏️ Modifier
                                                    </span>
                                                    
                                                    <span class="badge bg-danger" style="cursor: pointer; padding: 8px 12px;" 
                                                          onclick="deleteEquipe(<?php echo $equipe['id']; ?>, '<?php echo htmlspecialchars($equipe['nom']); ?>')" title="Supprimer">
                                                        🗑️ Supprimer
                                                    </span>
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
    </div>
</div>

<!-- Modal de modification -->
<div class="modal fade" id="editEquipeModal" tabindex="-1" aria-labelledby="editEquipeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEquipeModalLabel">
                    <i class="fas fa-edit"></i> Modifier l'Équipe
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="equipe_id" id="editEquipeId">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editNom" class="form-label">Nom de l'équipe *</label>
                            <input type="text" class="form-control" id="editNom" name="nom" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editCouleur" class="form-label">Couleur *</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="editCouleur" name="couleur" required>
                                <input type="text" class="form-control" id="editCouleurHex" placeholder="#007bff" 
                                       pattern="^#[0-9A-Fa-f]{6}$" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editEmailContact" class="form-label">Email de contact</label>
                            <input type="email" class="form-control" id="editEmailContact" name="email_contact">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editStatut" class="form-label">Statut</label>
                            <select class="form-select" id="editStatut" name="statut" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="disqualifiee">Disqualifiée</option>
                            </select>
                        </div>
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

<script>
// Synchronisation couleur picker et input hex
document.getElementById('couleur').addEventListener('input', function() {
    document.getElementById('couleur_hex').value = this.value;
});

document.getElementById('couleur_hex').addEventListener('input', function() {
    if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
        document.getElementById('couleur').value = this.value;
    }
});

// Fonction de modification
function editEquipe(equipe) {
    document.getElementById('editEquipeId').value = equipe.id;
    document.getElementById('editNom').value = equipe.nom;
    document.getElementById('editDescription').value = equipe.description;
    document.getElementById('editCouleur').value = equipe.couleur;
    document.getElementById('editCouleurHex').value = equipe.couleur;
    document.getElementById('editEmailContact').value = equipe.email_contact;
    document.getElementById('editStatut').value = equipe.statut;
    
    const modal = new bootstrap.Modal(document.getElementById('editEquipeModal'));
    modal.show();
}

// Fonction de suppression
function deleteEquipe(equipeId, equipeNom) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'équipe "${equipeNom}" ?\n\nCette action est irréversible.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="equipe_id" value="${equipeId}">
            <input type="hidden" name="equipe_nom" value="${equipeNom}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include '../../../admin/includes/footer.php'; ?>
