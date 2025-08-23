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
                // Création d'un nouveau parcours
                $nom = trim($_POST['nom']);
                $description = trim($_POST['description']);
                $statut = $_POST['statut'];
                
                if (!empty($nom)) {
                    try {
                        $stmt = $pdo->prepare("
                            INSERT INTO cyber_parcours (nom, description, statut) 
                            VALUES (?, ?, ?)
                        ");
                        
                        if ($stmt->execute([$nom, $description, $statut])) {
                            $success_message = "Parcours '$nom' créé avec succès !";
                        } else {
                            $error_message = "Erreur lors de la création du parcours";
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "Le nom est obligatoire";
                }
                break;
                
            case 'update':
                // Modification d'un parcours existant
                $parcours_id = (int)$_POST['parcours_id'];
                $nom = trim($_POST['nom']);
                $description = trim($_POST['description']);
                $statut = $_POST['statut'];
                
                if (!empty($nom) && $parcours_id > 0) {
                    try {
                        $stmt = $pdo->prepare("
                            UPDATE cyber_parcours SET nom = ?, description = ?, statut = ? 
                            WHERE id = ?
                        ");
                        
                        if ($stmt->execute([$nom, $description, $statut, $parcours_id])) {
                            $success_message = "Parcours '$nom' modifié avec succès !";
                        } else {
                            $error_message = "Erreur lors de la modification du parcours";
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "Données invalides pour la modification";
                }
                break;
                
            case 'delete':
                // Suppression d'un parcours
                $parcours_id = (int)$_POST['parcours_id'];
                $parcours_nom = $_POST['parcours_nom'];
                
                if ($parcours_id > 0) {
                    try {
                        // Vérifier s'il y a des équipes qui suivent ce parcours
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_equipes_parcours WHERE parcours_id = ?");
                        $stmt->execute([$parcours_id]);
                        $equipes_count = $stmt->fetchColumn();
                        
                        if ($equipes_count > 0) {
                            $error_message = "Impossible de supprimer ce parcours : $equipes_count équipe(s) le suivent actuellement.";
                        } else {
                            // Supprimer d'abord les lieux du parcours
                            $stmt = $pdo->prepare("DELETE FROM cyber_parcours_lieux WHERE parcours_id = ?");
                            $stmt->execute([$parcours_id]);
                            
                            // Puis supprimer le parcours
                            $stmt = $pdo->prepare("DELETE FROM cyber_parcours WHERE id = ?");
                            if ($stmt->execute([$parcours_id])) {
                                $success_message = "Parcours '$parcours_nom' supprimé avec succès !";
                            } else {
                                $error_message = "Erreur lors de la suppression du parcours";
                            }
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "ID de parcours invalide";
                }
                break;
        }
    }
}

// Récupération des parcours existants avec le nombre de lieux et d'équipes
try {
    $stmt = $pdo->query("
        SELECT p.*, 
               (SELECT COUNT(*) FROM cyber_parcours_lieux WHERE parcours_id = p.id) as nb_lieux,
               (SELECT COUNT(*) FROM cyber_equipes_parcours WHERE parcours_id = p.id AND statut IN ('en_cours', 'termine')) as nb_equipes
        FROM cyber_parcours p 
        ORDER BY p.nom
    ");
    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = "Erreur lors de la récupération des parcours : " . $e->getMessage();
    $parcours = [];
}

// Configuration pour le header
$page_title = 'Gestion des Parcours - Administration Cyberchasse';
$breadcrumb_items = [
    ['text' => 'Administration', 'url' => '../../../admin/admin2.php', 'active' => false],
    ['text' => 'Gestion des Parcours', 'url' => 'index.php', 'active' => true]
];

include '../../../admin/includes/header.php';
?>

    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card admin-card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-route"></i> Gestion des Parcours
                        </h4>
                        <div>
                            <a href="../../../admin/admin2.php" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-arrow-left"></i> Retour à l'administration
                            </a>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createParcoursModal">
                                <i class="fas fa-plus"></i> Nouveau Parcours
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <strong>Gérez vos parcours de cyberchasse :</strong> Créez des parcours, assignez des lieux et des équipes, 
                        et générez des tokens d'accès sécurisés pour chaque équipe.
                    </p>
                </div>
            </div>
        </div>
    </div>

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
                    <h5><i class="fas fa-plus"></i> Créer un Nouveau Parcours</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du parcours *</label>
                            <input type="text" class="form-control" id="nom" name="nom" required 
                                   placeholder="ex: Parcours Débutant">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="Description du parcours..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select" id="statut" name="statut">
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                            </select>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer le parcours
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Liste des parcours existants -->
        <div class="col-md-8">
            <div class="card admin-card">
                <div class="card-header">
                    <h5><i class="fas fa-list"></i> Parcours Existants (<?php echo count($parcours); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($parcours)): ?>
                        <div class="alert alert-info">
                            <h6>ℹ️ Aucun parcours trouvé</h6>
                            <p>Créez votre premier parcours en utilisant le formulaire à gauche.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Statut</th>
                                        <th>Lieux</th>
                                        <th>Équipes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($parcours as $parc): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($parc['nom']); ?></strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars(substr($parc['description'], 0, 100)); ?>
                                                    <?php if (strlen($parc['description']) > 100): ?>...<?php endif; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $parc['statut'] === 'actif' ? 'success' : 'secondary'; ?>">
                                                    <?php echo ucfirst($parc['statut']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo $parc['nb_lieux']; ?> lieu(x)
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">
                                                    <?php echo $parc['nb_equipes']; ?> équipe(s)
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-primary text-white" style="cursor: pointer; padding: 8px 12px;" 
                                                          onclick="window.location.href='manage_parcours.php?id=<?php echo $parc['id']; ?>'" 
                                                          title="Gérer le parcours">
                                                        🗺️ Gérer
                                                    </span>
                                                    
                                                    <span class="badge bg-info text-white" style="cursor: pointer; padding: 8px 12px;" 
                                                          onclick="window.location.href='token_manager.php?id=<?php echo $parc['id']; ?>'" 
                                                          title="Gérer les tokens d'accès">
                                                        🔑 Tokens
                                                    </span>
                                                    
                                                    <span class="badge bg-warning text-dark" style="cursor: pointer; padding: 8px 12px;" 
                                                          onclick="editParcours(<?php echo htmlspecialchars(json_encode($parc)); ?>)" 
                                                          title="Modifier">
                                                        ✏️ Modifier
                                                    </span>
                                                    
                                                    <span class="badge bg-danger" style="cursor: pointer; padding: 8px 12px;" 
                                                          onclick="deleteParcours(<?php echo $parc['id']; ?>, '<?php echo htmlspecialchars($parc['nom']); ?>')" 
                                                          title="Supprimer">
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
<div class="modal fade" id="editParcoursModal" tabindex="-1" aria-labelledby="editParcoursModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editParcoursModalLabel">
                    <i class="fas fa-edit"></i> Modifier le Parcours
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="parcours_id" id="editParcoursId">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editNom" class="form-label">Nom du parcours *</label>
                        <input type="text" class="form-control" id="editNom" name="nom" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editStatut" class="form-label">Statut</label>
                        <select class="form-select" id="editStatut" name="statut" required>
                            <option value="actif">Actif</option>
                            <option value="inactif">Inactif</option>
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

<script>
// Fonction de modification
function editParcours(parcours) {
    document.getElementById('editParcoursId').value = parcours.id;
    document.getElementById('editNom').value = parcours.nom;
    document.getElementById('editDescription').value = parcours.description;
    document.getElementById('editStatut').value = parcours.statut;
    
    const modal = new bootstrap.Modal(document.getElementById('editParcoursModal'));
    modal.show();
}

// Fonction de suppression
function deleteParcours(parcoursId, parcoursNom) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le parcours "${parcoursNom}" ?\n\nCette action est irréversible.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="parcours_id" value="${parcoursId}">
            <input type="hidden" name="parcours_nom" value="${parcoursNom}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include '../../../admin/includes/footer.php'; ?>
