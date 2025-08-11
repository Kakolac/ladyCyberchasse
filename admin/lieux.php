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
    }
}

// Récupération des lieux avec leurs énigmes
try {
    $stmt = $pdo->query("SELECT * FROM lieux ORDER BY ordre, nom");
    $lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des lieux: " . $e->getMessage();
}

// Configuration pour le header - CORRECTION ICI
$page_title = 'Gestion des Lieux - Administration Cyberchasse';
$current_page = 'lieux';
$breadcrumb_items = [
    ['text' => 'Tableau de bord', 'url' => 'admin.php', 'active' => false],
    ['text' => 'Gestion des Lieux', 'url' => 'lieux.php', 'active' => true]
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
            .lieu-card { transition: transform 0.3s ease; }
            .lieu-card:hover { transform: translateY(-5px); }
            .enigme-preview { background: rgba(0,0,0,0.05); border-radius: 8px; padding: 15px; }
            .option-correct { border-left: 4px solid #28a745; background: rgba(40, 167, 69, 0.1); }
            .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
            .btn-close { filter: invert(1); }
        </style>

        <!-- En-tête de la page -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-map-marker-alt"></i> Gestion des Lieux</h1>
                <p class="text-muted">Administrer les lieux et leurs énigmes de la cyberchasse</p>
            </div>
            <div>
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
                        <h3><?php echo count(array_filter($lieux, function($l) { return !empty($l['enigme_texte']); })); ?></h3>
                        <p class="mb-0">Énigmes Configurées</p>
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
                                        
                                        <?php if (!empty($lieu['enigme_texte'])): ?>
                                            <div class="enigme-preview mb-3">
                                                <h6><i class="fas fa-puzzle-piece text-info"></i> Énigme configurée</h6>
                                                <p class="mb-2"><strong><?php echo htmlspecialchars($lieu['enigme_texte']); ?></strong></p>
                                                
                                                <?php 
                                                $options = json_decode($lieu['options_enigme'], true);
                                                if ($options): 
                                                ?>
                                                    <div class="options-preview">
                                                        <?php foreach ($options as $key => $option): ?>
                                                            <div class="option-item <?php echo ($key === $lieu['reponse_enigme']) ? 'option-correct' : ''; ?> p-2 mb-1 rounded">
                                                                <small>
                                                                    <strong><?php echo $key; ?>)</strong> 
                                                                    <?php echo htmlspecialchars($option); ?>
                                                                    <?php if ($key === $lieu['reponse_enigme']): ?>
                                                                        <span class="badge bg-success">✓ Correcte</span>
                                                                    <?php endif; ?>
                                                                </small>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Aucune énigme configurée</strong>
                                                <br>
                                                <small>Cliquez sur "Configurer" pour ajouter une énigme</small>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="d-grid">
                                            <button type="button" class="btn btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editEnigmeModal"
                                                    data-lieu-id="<?php echo $lieu['id']; ?>"
                                                    data-lieu-nom="<?php echo htmlspecialchars($lieu['nom']); ?>"
                                                    data-reponse="<?php echo htmlspecialchars($lieu['reponse_enigme'] ?? ''); ?>"
                                                    data-enigme="<?php echo htmlspecialchars($lieu['enigme_texte'] ?? ''); ?>"
                                                    data-options="<?php echo htmlspecialchars($lieu['options_enigme'] ?? '{}'); ?>">
                                                <i class="fas fa-edit"></i> 
                                                <?php echo !empty($lieu['enigme_texte']) ? 'Modifier' : 'Configurer'; ?> l'énigme
                                            </button>
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

    <!-- Modal d'édition d'énigme -->
    <div class="modal fade" id="editEnigmeModal" tabindex="-1" aria-labelledby="editEnigmeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEnigmeModalLabel">
                        <i class="fas fa-puzzle-piece"></i> Configuration de l'Énigme
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_enigme">
                        <input type="hidden" name="lieu_id" id="editLieuId">
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="editLieuNom" class="form-label">Lieu</label>
                                <input type="text" class="form-control" id="editLieuNom" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="editReponse" class="form-label">Réponse correcte</label>
                                <select class="form-select" name="reponse_enigme" id="editReponse" required>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editEnigme" class="form-label">Texte de l'énigme</label>
                            <textarea class="form-control" name="enigme_texte" id="editEnigme" rows="3" required 
                                      placeholder="Posez votre question de cybersécurité ici..."></textarea>
                        </div>
                        
                        <h6 class="mb-3">Options de réponse</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editOptionA" class="form-label">Option A</label>
                                    <input type="text" class="form-control" name="option_a" id="editOptionA" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editOptionB" class="form-label">Option B</label>
                                    <input type="text" class="form-control" name="option_b" id="editOptionB" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editOptionC" class="form-label">Option C</label>
                                    <input type="text" class="form-control" name="option_c" id="editOptionC" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editOptionD" class="form-label">Option D</label>
                                    <input type="text" class="form-control" name="option_d" id="editOptionD" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Conseil :</strong> Assurez-vous que la réponse correcte correspond bien à l'option sélectionnée ci-dessus.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer l'énigme
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts spécifiques à cette page -->
    <script>
        // Gestion du modal d'édition d'énigme
        document.addEventListener('DOMContentLoaded', function() {
            const editEnigmeModal = document.getElementById('editEnigmeModal');
            if (editEnigmeModal) {
                editEnigmeModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const lieuId = button.getAttribute('data-lieu-id');
                    const lieuNom = button.getAttribute('data-lieu-nom');
                    const reponse = button.getAttribute('data-reponse');
                    const enigme = button.getAttribute('data-enigme');
                    const options = JSON.parse(button.getAttribute('data-options'));
                    
                    // Mettre à jour le modal
                    document.getElementById('editLieuId').value = lieuId;
                    document.getElementById('editLieuNom').value = lieuNom;
                    document.getElementById('editReponse').value = reponse || 'A';
                    document.getElementById('editEnigme').value = enigme || '';
                    
                    // Mettre à jour les options
                    if (options && Object.keys(options).length > 0) {
                        document.getElementById('editOptionA').value = options.A || '';
                        document.getElementById('editOptionB').value = options.B || '';
                        document.getElementById('editOptionC').value = options.C || '';
                        document.getElementById('editOptionD').value = options.D || '';
                    }
                });
            }
        });
    </script>

<?php
// Inclure le footer
include 'includes/footer.php';
?>
