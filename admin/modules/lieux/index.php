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
                // Création d'un nouveau lieu
                $nom = trim($_POST['nom']);
                $description = trim($_POST['description']);
                $temps_limite = (int)$_POST['temps_limite'];
                $statut = $_POST['statut'];
                $delai_indice = (int)$_POST['delai_indice'];
                $type_lieu = $_POST['type_lieu'];
                $qrcodeObligatoire = isset($_POST['qrcodeObligatoire']) ? 1 : 0;
                
                // Génération automatique du slug
                $slug = strtolower(trim($nom));
                $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
                $slug = preg_replace('/[\s-]+/', '_', $slug);
                $slug = trim($slug, '_');
                
                if (!empty($nom)) {
                    try {
                        $stmt = $pdo->prepare("
                            INSERT INTO cyber_lieux (
                                nom, slug, description, temps_limite, 
                                statut, delai_indice, type_lieu, qrcodeObligatoire
                            ) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                        ");
                        
                        if ($stmt->execute([$nom, $slug, $description, $temps_limite,
                            $statut, $delai_indice, $type_lieu, $qrcodeObligatoire])) {
                            
                            $lieu_id = $pdo->lastInsertId();
                            
                            // Lancer automatiquement la génération des fichiers
                            ob_start();
                            include 'creer-lieux.php';
                            ob_end_clean();
                            
                            $success_message = "Lieu '$nom' créé avec succès !";
                        } else {
                            $error_message = "Erreur lors de la création du lieu";
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "Le nom est obligatoire";
                }
                break;
                
            case 'update':
                // Modification d'un lieu existant
                $lieu_id = (int)$_POST['lieu_id'];
                $nom = trim($_POST['nom']);
                $description = trim($_POST['description']);
                $temps_limite = (int)$_POST['temps_limite'];
                $statut = $_POST['statut'];
                $delai_indice = (int)$_POST['delai_indice'];
                $type_lieu = $_POST['type_lieu'];
                $qrcodeObligatoire = isset($_POST['qrcodeObligatoire']) ? 1 : 0;
                
                if (!empty($nom) && $lieu_id > 0) {
                    try {
                        $stmt = $pdo->prepare("
                            UPDATE cyber_lieux SET 
                                nom = ?, description = ?, temps_limite = ?, 
                                statut = ?, delai_indice = ?, type_lieu = ?, qrcodeObligatoire = ?
                            WHERE id = ?
                        ");
                        
                        if ($stmt->execute([$nom, $description, $temps_limite,
                            $statut, $delai_indice, $type_lieu, $qrcodeObligatoire, $lieu_id])) {
                            $success_message = "Lieu '$nom' modifié avec succès !";
                        } else {
                            $error_message = "Erreur lors de la modification du lieu";
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "Données invalides pour la modification";
                }
                break;
                
            case 'delete':
                // Suppression d'un lieu
                $lieu_id = (int)$_POST['lieu_id'];
                $lieu_nom = $_POST['lieu_nom'];
                $lieu_slug = $_POST['lieu_slug'];
                
                if ($lieu_id > 0) {
                    try {
                        // 1. Supprimer le lieu de la base de données
                        $stmt = $pdo->prepare("DELETE FROM cyber_lieux WHERE id = ?");
                        if ($stmt->execute([$lieu_id])) {
                            
                            // 2. Supprimer le dossier et les fichiers physiques
                            $lieu_dir = "../../../lieux/$lieu_slug";
                            if (is_dir($lieu_dir)) {
                                // Supprimer tous les fichiers du dossier
                                $files = glob("$lieu_dir/*");
                                foreach ($files as $file) {
                                    if (is_file($file)) {
                                        unlink($file);
                                    }
                                }
                                // Supprimer le dossier vide
                                rmdir($lieu_dir);
                            }
                            
                            $success_message = "Lieu '$lieu_nom' supprimé avec succès !";
                        } else {
                            $error_message = "Erreur lors de la suppression du lieu";
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "ID de lieu invalide";
                }
                break;

            case 'affecter_enigme':
                // Affectation d'une énigme à un lieu
                $lieu_id = (int)$_POST['lieu_id'];
                $enigme_id = (int)$_POST['enigme_id'];
                
                if ($lieu_id > 0 && $enigme_id > 0) {
                    try {
                        $stmt = $pdo->prepare("UPDATE cyber_lieux SET enigme_id = ? WHERE id = ?");
                        if ($stmt->execute([$enigme_id, $lieu_id])) {
                            $success_message = "Énigme affectée au lieu avec succès !";
                        } else {
                            $error_message = "Erreur lors de l'affectation de l'énigme";
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "Données invalides pour l'affectation";
                }
                break;
        }
    }
}

// Récupération des lieux existants avec le titre de l'énigme
try {
    $stmt = $pdo->query("
        SELECT l.*, e.titre as enigme_titre 
        FROM cyber_lieux l 
        LEFT JOIN enigmes e ON l.enigme_id = e.id 
        ORDER BY l.nom
    ");
    $lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = "Erreur lors de la récupération des lieux : " . $e->getMessage();
    $lieux = [];
}

// Récupération des énigmes disponibles pour affectation
try {
    $stmt = $pdo->query("
        SELECT e.id, e.titre, e.actif, te.nom as type_nom 
        FROM enigmes e 
        JOIN types_enigmes te ON e.type_enigme_id = te.id 
        WHERE e.actif = 1 
        ORDER BY e.titre
    ");
    $enigmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $enigmes = [];
}

// Configuration pour le header
$page_title = 'Gestion des Lieux';
$breadcrumb_items = [
    ['text' => 'Administration', 'url' => '../../../admin/admin2.php', 'active' => false],
    ['text' => 'Gestion des Lieux', 'url' => 'index.php', 'active' => true]
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
                    <h5><i class="fas fa-plus"></i> Créer un Nouveau Lieu</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du lieu *</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="temps_limite" class="form-label">Temps limite (minutes)</label>
                                    <input type="number" class="form-control" id="temps_limite" name="temps_limite" min="0" value="5">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delai_indice" class="form-label">Délai indice (min)</label>
                                    <input type="number" class="form-control" id="delai_indice" name="delai_indice" min="0" value="6">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type_lieu" class="form-label">Type de lieu</label>
                                    <select class="form-select" id="type_lieu" name="type_lieu">
                                        <option value="standard">Standard</option>
                                        <option value="demarrage">Démarrage</option>
                                        <option value="fin">Fin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="statut" class="form-label">Statut</label>
                                    <select class="form-select" id="statut" name="statut">
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="qrcodeObligatoire" name="qrcodeObligatoire" checked>
                                <label class="form-check-label" for="qrcodeObligatoire">
                                    QR Code obligatoire
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer le lieu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Liste des lieux existants -->
        <div class="col-md-8">
            <div class="card admin-card">
                <div class="card-header">
                    <h5><i class="fas fa-list"></i> Lieux Existants (<?php echo count($lieux); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($lieux)): ?>
                        <div class="alert alert-info">
                            <h6>ℹ️ Aucun lieu trouvé</h6>
                            <p>Créez votre premier lieu en utilisant le formulaire à gauche.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nom</th>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th>Temps</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lieux as $lieu): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($lieu['nom']); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($lieu['slug']); ?></small>
                                                <?php if ($lieu['enigme_id'] && $lieu['enigme_titre']): ?>
                                                    <br><span class="badge bg-success" style="cursor: pointer;" 
                                                             onclick="voirEnigme(<?php echo $lieu['enigme_id']; ?>)" 
                                                             title="Cliquer pour voir les détails de l'énigme">
                                                        🧩 <?php echo htmlspecialchars($lieu['enigme_titre']); ?>
                                                    </span>
                                                <?php elseif ($lieu['enigme_id']): ?>
                                                    <br><span class="badge bg-warning" style="cursor: pointer;" 
                                                             onclick="voirEnigme(<?php echo $lieu['enigme_id']; ?>)" 
                                                             title="Cliquer pour voir les détails de l'énigme">
                                                         Énigme #<?php echo $lieu['enigme_id']; ?> (titre non trouvé)
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $lieu['type_lieu'] === 'fin' ? 'success' : ($lieu['type_lieu'] === 'demarrage' ? 'warning' : 'info'); ?>">
                                                    <?php echo ucfirst($lieu['type_lieu']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $lieu['statut'] === 'actif' ? 'success' : 'secondary'; ?>">
                                                    <?php echo ucfirst($lieu['statut']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?php echo $lieu['temps_limite'] > 0 ? $lieu['temps_limite'] . ' min' : 'Illimité'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-warning text-dark" style="cursor: pointer; padding: 8px 12px;" 
                                                          onclick="editLieu(<?php echo htmlspecialchars(json_encode($lieu)); ?>)" title="Modifier">
                                                        ✏️ Modifier
                                                    </span>
                                                    
                                                    <span class="badge bg-danger" style="cursor: pointer; padding: 8px 12px;" 
                                                          onclick="deleteLieu(<?php echo $lieu['id']; ?>, '<?php echo htmlspecialchars($lieu['nom']); ?>', '<?php echo htmlspecialchars($lieu['slug']); ?>')" title="Supprimer">
                                                        🗑️ Supprimer
                                                    </span>
                                                    
                                                    <?php if ($lieu['type_lieu'] === 'standard'): ?>
                                                        <span class="badge bg-info text-dark" style="cursor: pointer; padding: 8px 12px;" 
                                                              onclick="affecterEnigme(<?php echo $lieu['id']; ?>, '<?php echo htmlspecialchars($lieu['nom']); ?>')" title="Affecter Énigme">
                                                            🧩 Énigme
                                                        </span>
                                                    <?php endif; ?>
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
<div class="modal fade" id="editLieuModal" tabindex="-1" aria-labelledby="editLieuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLieuModalLabel">
                    <i class="fas fa-edit"></i> Modifier le Lieu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="lieu_id" id="editLieuId">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editNom" class="form-label">Nom du lieu *</label>
                            <input type="text" class="form-control" id="editNom" name="nom" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editTempsLimite" class="form-label">Temps limite (minutes)</label>
                            <input type="number" class="form-control" id="editTempsLimite" name="temps_limite" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editDelaiIndice" class="form-label">Délai indice (min)</label>
                            <input type="number" class="form-control" id="editDelaiIndice" name="delai_indice" min="0" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editTypeLieu" class="form-label">Type de lieu</label>
                            <select class="form-select" id="editTypeLieu" name="type_lieu" required>
                                <option value="standard">Standard</option>
                                <option value="demarrage">Démarrage</option>
                                <option value="fin">Fin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editStatut" class="form-label">Statut</label>
                            <select class="form-select" id="editStatut" name="statut" required>
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editQrcodeObligatoire" name="qrcodeObligatoire">
                            <label class="form-check-label" for="editQrcodeObligatoire">
                                QR Code obligatoire
                            </label>
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

<!-- Modal de sélection d'énigme -->
<div class="modal fade" id="enigmeModal" tabindex="-1" aria-labelledby="enigmeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enigmeModalLabel">
                    <i class="fas fa-puzzle-piece"></i> Affecter une Énigme
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="affecter_enigme">
                <input type="hidden" name="lieu_id" id="enigmeLieuId">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="enigme_id" class="form-label">Sélectionner une énigme</label>
                        <select class="form-select" name="enigme_id" id="enigme_id" required>
                            <option value="">Choisir une énigme...</option>
                            <?php foreach ($enigmes as $enigme): ?>
                                <option value="<?php echo $enigme['id']; ?>">
                                    <?php echo htmlspecialchars($enigme['titre']); ?> 
                                    <span class="text-muted">(<?php echo htmlspecialchars($enigme['type_nom']); ?>)</span>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <?php if (empty($enigmes)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Aucune énigme disponible. Créez d'abord des énigmes dans le module de gestion des énigmes.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong><?php echo count($enigmes); ?> énigme(s) disponible(s)</strong><br>
                            Sélectionnez l'énigme que vous souhaitez affecter à ce lieu.
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info" <?php echo empty($enigmes) ? 'disabled' : ''; ?>>
                        <i class="fas fa-link"></i> Affecter l'énigme
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de détails de l'énigme -->
<div class="modal fade" id="enigmeDetailsModal" tabindex="-1" aria-labelledby="enigmeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enigmeDetailsModalLabel">
                    <i class="fas fa-puzzle-piece"></i> Détails de l'Énigme
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="enigmeDetailsContent">
                <!-- Le contenu sera chargé dynamiquement -->
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction de modification
function editLieu(lieu) {
    document.getElementById('editLieuId').value = lieu.id;
    document.getElementById('editNom').value = lieu.nom;
    document.getElementById('editDescription').value = lieu.description;
    document.getElementById('editTempsLimite').value = lieu.temps_limite;
    document.getElementById('editDelaiIndice').value = lieu.delai_indice;
    document.getElementById('editTypeLieu').value = lieu.type_lieu;
    document.getElementById('editStatut').value = lieu.statut;
    document.getElementById('editQrcodeObligatoire').checked = lieu.qrcodeObligatoire == 1;
    
    const modal = new bootstrap.Modal(document.getElementById('editLieuModal'));
    modal.show();
}

// Fonction d'affectation d'énigme
function affecterEnigme(lieuId, lieuNom) {
    document.getElementById('enigmeLieuId').value = lieuId;
    
    // Mettre à jour le titre du modal
    document.getElementById('enigmeModalLabel').innerHTML = `<i class="fas fa-puzzle-piece"></i> Affecter une Énigme à "${lieuNom}"`;
    
    const modal = new bootstrap.Modal(document.getElementById('enigmeModal'));
    modal.show();
}

// Fonction de suppression
function deleteLieu(lieuId, lieuNom, lieuSlug) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le lieu "${lieuNom}" ?\n\nCette action supprimera également tous les fichiers associés et est irréversible.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="lieu_id" value="${lieuId}">
            <input type="hidden" name="lieu_nom" value="${lieuNom}">
            <input type="hidden" name="lieu_slug" value="${lieuSlug}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Fonction pour voir les détails d'une énigme
function voirEnigme(enigmeId) {
    // Mettre à jour le titre du modal
    document.getElementById('enigmeDetailsModalLabel').innerHTML = `<i class="fas fa-puzzle-piece"></i> Détails de l'Énigme #${enigmeId}`;
    
    // Afficher le modal
    const modal = new bootstrap.Modal(document.getElementById('enigmeDetailsModal'));
    modal.show();
    
    // Charger les détails de l'énigme via AJAX
    fetch(`get_enigme_details.php?id=${enigmeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const enigme = data.enigme;
                let content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle"></i> Informations générales</h6>
                            <table class="table table-bordered">
                                <tr><td><strong>ID :</strong></td><td>${enigme.id}</td></tr>
                                <tr><td><strong>Titre :</strong></td><td>${enigme.titre}</td></tr>
                                <tr><td><strong>Type :</strong></td><td>${enigme.type_nom}</td></tr>
                                <tr><td><strong>Statut :</strong></td><td><span class="badge bg-${enigme.actif ? 'success' : 'secondary'}">${enigme.actif ? 'Active' : 'Inactive'}</span></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-puzzle-piece"></i> Contenu de l'énigme</h6>
                            <div class="card">
                                <div class="card-body">
                                    ${formatEnigmeContent(enigme.donnees)}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('enigmeDetailsContent').innerHTML = content;
            } else {
                document.getElementById('enigmeDetailsContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Erreur lors du chargement de l'énigme : ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('enigmeDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Erreur de connexion : ${error.message}
                </div>
            `;
        });
}

// Fonction pour formater le contenu de l'énigme selon son type
function formatEnigmeContent(donnees) {
    try {
        const data = JSON.parse(donnees);
        let html = '';
        
        if (data.question) {
            html += `<p><strong>Question :</strong> ${data.question}</p>`;
        }
        
        if (data.contexte) {
            html += `<p><strong>Contexte :</strong> ${data.contexte}</p>`;
        }
        
        if (data.indice) {
            html += `<p><strong>Indice :</strong> ${data.indice}</p>`;
        }
        
        if (data.reponse_correcte) {
            html += `<p><strong>Réponse correcte :</strong> <span class="badge bg-success">${data.reponse_correcte}</span></p>`;
        }
        
        if (data.options) {
            html += `<p><strong>Options :</strong></p><ul>`;
            Object.entries(data.options).forEach(([key, value]) => {
                html += `<li><strong>${key} :</strong> ${value}</li>`;
            });
            html += `</ul>`;
        }
        
        return html || '<p class="text-muted">Aucun contenu disponible</p>';
    } catch (e) {
        return '<p class="text-muted">Erreur lors du formatage du contenu</p>';
    }
}
</script>

<?php include '../../../admin/includes/footer.php'; ?>
