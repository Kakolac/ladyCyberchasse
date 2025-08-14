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
        case 'delete_indice':
            $indice_id = $_POST['indice_id'];
            
            $stmt = $pdo->prepare("DELETE FROM indices_consultes WHERE id = ?");
            if ($stmt->execute([$indice_id])) {
                $success_message = "Consultation d'indice supprimée avec succès !";
            } else {
                $error_message = "Erreur lors de la suppression de la consultation d'indice";
            }
            break;
            
        case 'reset_equipe_indices':
            $equipe_id = $_POST['equipe_id'];
            $equipe_nom = $_POST['equipe_nom'];
            
            $stmt = $pdo->prepare("DELETE FROM indices_consultes WHERE equipe_id = ?");
            if ($stmt->execute([$equipe_id])) {
                $success_message = "Tous les indices consultés par l'équipe '$equipe_nom' ont été supprimés !";
            } else {
                $error_message = "Erreur lors de la suppression des indices de l'équipe";
            }
            break;
    }
}

// Récupération des statistiques des indices
try {
    // Statistiques globales
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM indices_consultes");
    $total_indices = $stmt->fetchColumn();
    
    // Indices par équipe
    $stmt = $pdo->query("
        SELECT e.id, e.nom as equipe, COUNT(*) as nb_indices
        FROM indices_consultes ic
        JOIN equipes e ON ic.equipe_id = e.id
        GROUP BY e.id, e.nom
        ORDER BY nb_indices DESC
    ");
    $indices_par_equipe = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Indices par lieu
    $stmt = $pdo->query("
        SELECT l.nom as lieu, COUNT(*) as nb_indices
        FROM indices_consultes ic
        JOIN lieux l ON ic.lieu_id = l.id
        GROUP BY l.id, l.nom
        ORDER BY nb_indices DESC
    ");
    $indices_par_lieu = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Indices par énigme
    $stmt = $pdo->query("
        SELECT e.titre as enigme, te.nom as type, COUNT(*) as nb_indices
        FROM indices_consultes ic
        JOIN enigmes e ON ic.enigme_id = e.id
        JOIN types_enigmes te ON e.type_enigme_id = te.id
        GROUP BY e.id, e.titre, te.nom
        ORDER BY nb_indices DESC
    ");
    $indices_par_enigme = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Détail des consultations récentes avec ID pour suppression
    $stmt = $pdo->query("
        SELECT ic.id, e.nom as equipe, l.nom as lieu, en.titre as enigme, ic.timestamp
        FROM indices_consultes ic
        JOIN equipes e ON ic.equipe_id = e.id
        JOIN lieux l ON ic.lieu_id = l.id
        JOIN enigmes en ON ic.enigme_id = en.id
        ORDER BY ic.timestamp DESC
        LIMIT 20
    ");
    $consultations_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des statistiques: " . $e->getMessage();
}

// Configuration pour le header
$page_title = 'Statistiques des Indices - Administration Cyberchasse';
$current_page = 'indices_stats';
$breadcrumb_items = [
    ['text' => 'Tableau de bord', 'url' => 'admin.php', 'active' => false],
    ['text' => 'Statistiques des Indices', 'url' => 'indices_stats.php', 'active' => true]
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
            .stats-card { transition: transform 0.3s ease; }
            .stats-card:hover { transform: translateY(-5px); }
            .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
            .btn-close { filter: invert(1); }
            .table-responsive { border-radius: 10px; }
            .action-buttons { white-space: nowrap; }
        </style>

        <!-- En-tête de la page -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-chart-bar"></i> Statistiques des Indices</h1>
                <p class="text-muted">Analyse de l'utilisation des indices par les équipes</p>
            </div>
            <div>
                <a href="admin.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
            </div>
        </div>

        <!-- Statistiques globales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card admin-card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-lightbulb fa-3x mb-3"></i>
                        <h3><?php echo $total_indices; ?></h3>
                        <p class="mb-0">Total Indices Consultés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h3><?php echo count($indices_par_equipe); ?></h3>
                        <p class="mb-0">Équipes Utilisant les Indices</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                        <h3><?php echo count($indices_par_lieu); ?></h3>
                        <p class="mb-0">Lieux avec Indices Consultés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-warning text-dark">
                    <div class="card-body text-center">
                        <i class="fas fa-puzzle-piece fa-3x mb-3"></i>
                        <h3><?php echo count($indices_par_enigme); ?></h3>
                        <p class="mb-0">Énigmes avec Indices Consultés</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détail des statistiques -->
        <div class="row">
            <!-- Indices par équipe -->
            <div class="col-lg-6 mb-4">
                <div class="card admin-card stats-card">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-users"></i> Indices par Équipe</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($indices_par_equipe)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Aucune consultation d'indice enregistrée.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Équipe</th>
                                            <th>Indices Consultés</th>
                                            <th>Pourcentage</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($indices_par_equipe as $equipe): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($equipe['equipe']); ?></strong></td>
                                                <td><?php echo $equipe['nb_indices']; ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo round(($equipe['nb_indices'] / $total_indices) * 100, 1); ?>%
                                                    </span>
                                                </td>
                                                <td class="action-buttons">
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer TOUS les indices consultés par l\'équipe <?php echo htmlspecialchars($equipe['equipe']); ?> ?')">
                                                        <input type="hidden" name="action" value="reset_equipe_indices">
                                                        <input type="hidden" name="equipe_id" value="<?php echo $equipe['id']; ?>">
                                                        <input type="hidden" name="equipe_nom" value="<?php echo htmlspecialchars($equipe['equipe']); ?>">
                                                        <button type="submit" class="btn btn-warning btn-sm" title="Reset tous les indices de cette équipe">
                                                            <i class="fas fa-undo"></i> Reset
                                                        </button>
                                                    </form>
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

            <!-- Indices par lieu -->
            <div class="col-lg-6 mb-4">
                <div class="card admin-card stats-card">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-map-marker-alt"></i> Indices par Lieu</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($indices_par_lieu)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Aucune consultation d'indice enregistrée.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Lieu</th>
                                            <th>Indices Consultés</th>
                                            <th>Pourcentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($indices_par_lieu as $lieu): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($lieu['lieu']); ?></strong></td>
                                                <td><?php echo $lieu['nb_indices']; ?></td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <?php echo round(($lieu['nb_indices'] / $total_indices) * 100, 1); ?>%
                                                    </span>
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

        <!-- Indices par énigme -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card admin-card stats-card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-puzzle-piece"></i> Indices par Énigme</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($indices_par_enigme)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Aucune consultation d'indice enregistrée.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Énigme</th>
                                            <th>Type</th>
                                            <th>Indices Consultés</th>
                                            <th>Pourcentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($indices_par_enigme as $enigme): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($enigme['enigme']); ?></strong></td>
                                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($enigme['type']); ?></span></td>
                                                <td><?php echo $enigme['nb_indices']; ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo round(($enigme['nb_indices'] / $total_indices) * 100, 1); ?>%
                                                    </span>
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

        <!-- Consultations récentes avec actions -->
        <div class="row">
            <div class="col-12">
                <div class="card admin-card stats-card">
                    <div class="card-header bg-warning text-dark">
                        <h5><i class="fas fa-clock"></i> Consultations Récentes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($consultations_recentes)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Aucune consultation d'indice enregistrée.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Équipe</th>
                                            <th>Lieu</th>
                                            <th>Énigme</th>
                                            <th>Date/Heure</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($consultations_recentes as $consultation): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($consultation['equipe']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($consultation['lieu']); ?></td>
                                                <td><?php echo htmlspecialchars($consultation['enigme']); ?></td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y H:i', strtotime($consultation['timestamp'])); ?>
                                                    </small>
                                                </td>
                                                <td class="action-buttons">
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette consultation d\'indice ?')">
                                                        <input type="hidden" name="action" value="delete_indice">
                                                        <input type="hidden" name="indice_id" value="<?php echo $consultation['id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Supprimer cette consultation d'indice">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
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

<?php include 'includes/footer.php'; ?>
