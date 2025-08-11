<?php
session_start();
require_once '../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Récupération des équipes
$stmt = $pdo->query("SELECT * FROM equipes ORDER BY nom");
$equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des lieux
$stmt = $pdo->query("SELECT * FROM lieux ORDER BY ordre");
$lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des parcours existants
$stmt = $pdo->query("
    SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
    FROM parcours p
    JOIN equipes e ON p.equipe_id = e.id
    JOIN lieux l ON p.lieu_id = l.id
    ORDER BY p.equipe_id, p.ordre_visite
");
$parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_parcours':
                // Création d'un nouveau parcours
                $equipe_id = $_POST['equipe_id'];
                $lieu_id = $_POST['lieu_id'];
                $ordre = $_POST['ordre'];
                
                // Génération d'un token unique
                $token = bin2hex(random_bytes(16));
                
                $stmt = $pdo->prepare("
                    INSERT INTO parcours (equipe_id, lieu_id, ordre_visite, token_acces, statut)
                    VALUES (?, ?, ?, ?, 'en_attente')
                ");
                
                if ($stmt->execute([$equipe_id, $lieu_id, $ordre, $token])) {
                    $success_message = "Parcours créé avec succès !";
                    // Recharger les parcours
                    $stmt = $pdo->query("
                        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
                        FROM parcours p
                        JOIN equipes e ON p.equipe_id = e.id
                        JOIN lieux l ON p.lieu_id = l.id
                        ORDER BY p.equipe_id, p.ordre_visite
                    ");
                    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $error_message = "Erreur lors de la création du parcours";
                }
                break;
                
            case 'update_status':
                // Mise à jour du statut d'un parcours
                $parcours_id = $_POST['parcours_id'];
                $new_status = $_POST['new_status'];
                
                $stmt = $pdo->prepare("UPDATE parcours SET statut = ? WHERE id = ?");
                if ($stmt->execute([$new_status, $parcours_id])) {
                    $success_message = "Statut mis à jour avec succès !";
                    // Recharger les parcours
                    $stmt = $pdo->query("
                        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
                        FROM parcours p
                        JOIN equipes e ON p.equipe_id = e.id
                        JOIN lieux l ON p.lieu_id = l.id
                        ORDER BY p.equipe_id, p.ordre_visite
                    ");
                    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $error_message = "Erreur lors de la mise à jour du statut";
                }
                break;
                
            case 'regenerate_token':
                $parcours_id = $_POST['parcours_id'];
                $new_token = bin2hex(random_bytes(16));
                
                $stmt = $pdo->prepare("UPDATE parcours SET token_acces = ? WHERE id = ?");
                if ($stmt->execute([$new_token, $parcours_id])) {
                    $success_message = "Token régénéré avec succès !";
                    // Recharger les parcours
                    $stmt = $pdo->query("
                        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
                        FROM parcours p
                        JOIN equipes e ON p.equipe_id = e.id
                        JOIN lieux l ON p.lieu_id = l.id
                        ORDER BY p.equipe_id, p.ordre_visite
                    ");
                    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $error_message = "Erreur lors de la régénération du token";
                }
                break;
                
            case 'delete_parcours':
                $parcours_id = $_POST['parcours_id'];
                
                $stmt = $pdo->prepare("DELETE FROM parcours WHERE id = ?");
                if ($stmt->execute([$parcours_id])) {
                    $success_message = "Parcours supprimé avec succès !";
                    // Recharger les parcours
                    $stmt = $pdo->query("
                        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
                        FROM parcours p
                        JOIN equipes e ON p.equipe_id = e.id
                        JOIN lieux l ON p.lieu_id = l.id
                        ORDER BY p.equipe_id, p.ordre_visite
                    ");
                    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $error_message = "Erreur lors de la suppression du parcours";
                }
                break;
            
            case 'delete_all_equipe_parcours':
                $equipe_id = $_POST['equipe_id'];
                
                // Récupérer le nom de l'équipe depuis la base de données
                $stmt = $pdo->prepare("SELECT nom FROM equipes WHERE id = ?");
                $stmt->execute([$equipe_id]);
                $equipe = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($equipe) {
                    $equipe_nom = $equipe['nom'];
                    
                    // Supprimer tous les parcours de cette équipe
                    $stmt = $pdo->prepare("DELETE FROM parcours WHERE equipe_id = ?");
                    if ($stmt->execute([$equipe_id])) {
                        $success_message = "Tous les parcours de l'équipe '{$equipe_nom}' ont été supprimés avec succès !";
                        // Recharger les parcours
                        $stmt = $pdo->query("
                            SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
                            FROM parcours p
                            JOIN equipes e ON p.equipe_id = e.id
                            JOIN lieux l ON p.lieu_id = l.id
                            ORDER BY p.equipe_id, p.ordre_visite
                        ");
                        $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $error_message = "Erreur lors de la suppression des parcours de l'équipe";
                    }
                } else {
                    $error_message = "Équipe non trouvée";
                }
                break;
        }
    }
}

// Filtres
$equipe_filter = $_GET['equipe'] ?? '';
$lieu_filter = $_GET['lieu'] ?? '';

// Filtrer les parcours
$filtered_parcours = $parcours;
if ($equipe_filter) {
    $filtered_parcours = array_filter($parcours, function($p) use ($equipe_filter) {
        return strpos(strtolower($p['equipe_nom']), strtolower($equipe_filter)) !== false;
    });
}
if ($lieu_filter) {
    $filtered_parcours = array_filter($filtered_parcours, function($p) use ($lieu_filter) {
        return strpos(strtolower($p['lieu_nom']), strtolower($lieu_filter)) !== false;
    });
}

?>
<?php
// Définir le titre de la page pour le header
$page_title = "Gestion des Parcours";
$breadcrumb_items = [
    ['url' => 'admin.php', 'text' => 'Administration', 'active' => false],
    ['url' => '#', 'text' => 'Gestion des Parcours', 'active' => true]
];

// Inclure le header commun
include 'includes/header.php';
?>

<!-- Styles CSS spécifiques à cette page -->
<style>
    .card {
        border: none;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        border-radius: 15px;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 15px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
    }
    .status-en_attente { background: #ffc107; color: #000; }
    .status-en_cours { background: #17a2b8; color: #fff; }
    .status-termine { background: #28a745; color: #fff; }
    .status-echec { background: #dc3545; color: #fff; }
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
    .token-display {
        background: #f8f9fa;
        padding: 8px;
        border-radius: 5px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        word-break: break-all;
        border: 1px solid #e9ecef;
    }
    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    .btn-sm {
        padding: 4px 8px;
        font-size: 11px;
    }
</style>

<!-- Titre de la page -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h1 class="mb-2">🗺️ Gestion des Parcours - Cyberchasse</h1>
                <p class="mb-0">Administration des parcours des équipes</p>
            </div>
            <div class="card-body">
                        
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

                        <!-- Actions rapides -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>⚡ Actions Rapides</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>🗑️ Supprimer tous les parcours d'une équipe</h5>
                                        <p class="text-muted">Attention : Cette action supprime définitivement tous les parcours d'une équipe sélectionnée.</p>
                                        
                                        <form method="POST" class="row g-3" onsubmit="return confirm('⚠️ ATTENTION : Vous êtes sur le point de supprimer TOUS les parcours de cette équipe. Cette action est irréversible !\n\nÊtes-vous absolument sûr de vouloir continuer ?');">
                                            <input type="hidden" name="action" value="delete_all_equipe_parcours">
                                            
                                            <div class="col-md-8">
                                                <select class="form-select" name="equipe_id" id="equipeSelect" required>
                                                    <option value="">Sélectionner une équipe</option>
                                                    <?php foreach ($equipes as $equipe): ?>
                                                        <option value="<?php echo $equipe['id']; ?>">
                                                            <?php echo htmlspecialchars($equipe['nom']); ?> (<?php echo htmlspecialchars($equipe['couleur']); ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-danger w-100" id="deleteAllBtn" disabled>
                                                    🗑️ Supprimer tout
                                                </button>
                                            </div>
                                        </form>
                                        
                                        <small class="text-danger">
                                            <strong>⚠️ Attention :</strong> Cette action est irréversible et supprimera tous les parcours, tokens et données associés à l'équipe sélectionnée.
                                        </small>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5>📊 Statistiques rapides</h5>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="card bg-primary text-white text-center p-3">
                                                    <h4><?php echo count($parcours); ?></h4>
                                                    <small>Total Parcours</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card bg-success text-white text-center p-3">
                                                    <h4><?php echo count(array_filter($parcours, function($p) { return $p['statut'] === 'termine'; })); ?></h4>
                                                    <small>Terminés</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Création d'un nouveau parcours -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>➕ Créer un Nouveau Parcours</h3>
                            </div>
                            <div class="card-body">
                                <form method="POST" class="row g-3">
                                    <input type="hidden" name="action" value="create_parcours">
                                    
                                    <div class="col-md-4">
                                        <label for="equipe_id" class="form-label">Équipe</label>
                                        <select class="form-select" name="equipe_id" required>
                                            <option value="">Sélectionner une équipe</option>
                                            <?php foreach ($equipes as $equipe): ?>
                                                <option value="<?php echo $equipe['id']; ?>">
                                                    <?php echo htmlspecialchars($equipe['nom']); ?> (<?php echo htmlspecialchars($equipe['couleur']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label for="lieu_id" class="form-label">Lieu</label>
                                        <select class="form-select" name="lieu_id" required>
                                            <option value="">Sélectionner un lieu</option>
                                            <?php foreach ($lieux as $lieu): ?>
                                                <option value="<?php echo $lieu['id']; ?>">
                                                    <?php echo htmlspecialchars($lieu['nom']); ?> (Ordre: <?php echo $lieu['ordre']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label for="ordre" class="form-label">Ordre</label>
                                        <input type="number" class="form-control" name="ordre" min="1" required>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-success w-100">➕ Créer</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Filtres -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>🔍 Filtres</h3>
                            </div>
                            <div class="card-body">
                                <form method="GET" class="row g-3">
                                    <div class="col-md-4">
                                        <label for="equipe_filter" class="form-label">Filtrer par équipe</label>
                                        <input type="text" class="form-control" name="equipe" value="<?php echo htmlspecialchars($equipe_filter); ?>" placeholder="Nom de l'équipe">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="lieu_filter" class="form-label">Filtrer par lieu</label>
                                        <input type="text" class="form-control" name="lieu" value="<?php echo htmlspecialchars($lieu_filter); ?>" placeholder="Nom du lieu">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">🔍 Filtrer</button>
                                            <a href="parcours.php" class="btn btn-secondary"> Réinitialiser</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Liste des parcours -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3> Parcours Existants (<?php echo count($filtered_parcours); ?>)</h3>
                                <div>
                                    <a href="../scripts/create_test_parcours.php" class="btn btn-info btn-sm">🔧 Créer parcours de test</a>
                                    <a href="../scripts/fix_parcours_status.php" class="btn btn-warning btn-sm"> Corriger statuts</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (empty($filtered_parcours)): ?>
                                    <div class="alert alert-info">
                                        <h5>ℹ️ Aucun parcours trouvé</h5>
                                        <p>Créez des parcours en utilisant le formulaire ci-dessus ou utilisez le script de création automatique.</p>
                                        <a href="../scripts/create_test_parcours.php" class="btn btn-primary"> Créer des parcours de test</a>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Équipe</th>
                                                    <th>Lieu</th>
                                                    <th>Ordre</th>
                                                    <th>Token d'accès</th>
                                                    <th>Statut</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($filtered_parcours as $parcour): ?>
                                                    <tr>
                                                        <td>
                                                            <span class="equipe-badge equipe-<?php echo strtolower($parcour['equipe_couleur'] ?? 'default'); ?>">
                                                                <?php echo htmlspecialchars($parcour['equipe_nom']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($parcour['lieu_nom']); ?></strong>
                                                            <br><small class="text-muted">/<?php echo $parcour['lieu_slug']; ?></small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary fs-6"><?php echo $parcour['ordre_visite']; ?></span>
                                                        </td>
                                                        <td>
                                                            <div class="token-display">
                                                                <?php echo htmlspecialchars($parcour['token_acces']); ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="status-badge status-<?php echo $parcour['statut']; ?>">
                                                                <?php echo $parcour['statut']; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="action-buttons">
                                                                <!-- Modification du statut -->
                                                                <button type="button" class="btn btn-warning btn-sm" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#statusModal" 
                                                                        data-parcours-id="<?php echo $parcour['id']; ?>"
                                                                        data-current-status="<?php echo $parcour['statut']; ?>"
                                                                        data-equipe="<?php echo htmlspecialchars($parcour['equipe_nom']); ?>"
                                                                        data-lieu="<?php echo htmlspecialchars($parcour['lieu_nom']); ?>">
                                                                    🔄 Statut
                                                                </button>
                                                                
                                                                <!-- Régénération du token -->
                                                                <form method="POST" style="display: inline;" 
                                                                      onsubmit="return confirm('Régénérer le token pour ce parcours ?');">
                                                                    <input type="hidden" name="action" value="regenerate_token">
                                                                    <input type="hidden" name="parcours_id" value="<?php echo $parcour['id']; ?>">
                                                                    <button type="submit" class="btn btn-info btn-sm">🔑 Token</button>
                                                                </form>
                                                                
                                                                <!-- Suppression -->
                                                                <form method="POST" style="display: inline;" 
                                                                      onsubmit="return confirm('Supprimer ce parcours ? Cette action est irréversible.');">
                                                                    <input type="hidden" name="action" value="delete_parcours">
                                                                    <input type="hidden" name="parcours_id" value="<?php echo $parcour['id']; ?>">
                                                                    <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                                                </form>
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
        </div>
    </div>

    <!-- Modal pour modifier le statut -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> Modifier le Statut</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="parcours_id" id="modalParcoursId">
                        
                        <div class="mb-3">
                            <label class="form-label">Parcours :</label>
                            <div id="modalParcoursInfo" class="alert alert-info"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_status" class="form-label">Nouveau statut :</label>
                            <select class="form-select" name="new_status" id="new_status" required>
                                <option value="en_attente">⏳ En attente</option>
                                <option value="en_cours">▶️ En cours</option>
                                <option value="termine">✅ Terminé</option>
                                <option value="echec">❌ Échec</option>
                            </select>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6>⚠️ Attention :</h6>
                            <ul class="mb-0">
                                <li><strong>En attente :</strong> Lieu non visité</li>
                                <li><strong>En cours :</strong> Lieu en cours de visite</li>
                                <li><strong>Terminé :</strong> Lieu visité avec succès</li>
                                <li><strong>Échec :</strong> Lieu échoué</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">🔄 Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Gestion du modal pour modifier le statut
        document.addEventListener('DOMContentLoaded', function() {
            const statusModal = document.getElementById('statusModal');
            if (statusModal) {
                statusModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const parcoursId = button.getAttribute('data-parcours-id');
                    const currentStatus = button.getAttribute('data-current-status');
                    const equipe = button.getAttribute('data-equipe');
                    const lieu = button.getAttribute('data-lieu');
                    
                    // Mettre à jour le modal
                    document.getElementById('modalParcoursId').value = parcoursId;
                    document.getElementById('modalParcoursInfo').innerHTML = 
                        `<strong>${equipe}</strong> → <strong>${lieu}</strong>`;
                    document.getElementById('new_status').value = currentStatus;
                });
            }
            
            // Gestion du bouton de suppression de tous les parcours d'une équipe
            const equipeSelect = document.getElementById('equipeSelect');
            const deleteAllBtn = document.getElementById('deleteAllBtn');
            
            if (equipeSelect && deleteAllBtn) {
                equipeSelect.addEventListener('change', function() {
                    if (this.value) {
                        const selectedOption = this.options[this.selectedIndex];
                        deleteAllBtn.disabled = false;
                        deleteAllBtn.textContent = `🗑️ Supprimer tout (${selectedOption.text})`;
                    } else {
                        deleteAllBtn.disabled = true;
                        deleteAllBtn.textContent = '🗑️ Supprimer tout';
                    }
                });
            }
        });
        
        // Auto-fermeture des alertes après 5 secondes
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

<?php
// Inclure le footer commun
include 'includes/footer.php';
?>
