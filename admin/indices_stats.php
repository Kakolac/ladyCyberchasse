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
            
        // NOUVELLE ACTION : Reset timer des indices pour une équipe spécifique
        case 'reset_indice_timer_equipe':
            $equipe_id = $_POST['equipe_id'];
            $equipe_nom = $_POST['equipe_nom'];
            
            try {
                // Vérifier que l'équipe_id est valide
                if (!$equipe_id || $equipe_id === 'null') {
                    throw new Exception("ID d'équipe invalide");
                }
                
                // Supprimer les consultations d'indices
                $stmt = $pdo->prepare("DELETE FROM indices_consultes WHERE equipe_id = ?");
                $stmt->execute([$equipe_id]);
                
                // Réinitialiser les timestamps dans la table parcours
                $stmt = $pdo->prepare("
                    UPDATE parcours 
                    SET enigme_start_time = NOW(), 
                        indice_start_time = DATE_ADD(NOW(), INTERVAL 6 MINUTE)
                    WHERE equipe_id = ? AND statut = 'en_cours'
                ");
                $stmt->execute([$equipe_id]);
                
                $success_message = "Timer des indices reseté pour l'équipe '$equipe_nom'. L'équipe peut maintenant recommencer les énigmes avec un nouveau délai de 6 minutes.";
            } catch (Exception $e) {
                $error_message = "Erreur lors du reset du timer: " . $e->getMessage();
            }
            break;
            
        // NOUVELLE ACTION : Reset timer des indices pour un lieu spécifique
        case 'reset_indice_timer_lieu':
            $lieu_id = $_POST['lieu_id'];
            $lieu_nom = $_POST['lieu_nom'];
            
            try {
                // Vérifier que le lieu_id est valide
                if (!$lieu_id || $lieu_id === 'null') {
                    throw new Exception("ID de lieu invalide");
                }
                
                // Supprimer les consultations d'indices pour ce lieu
                $stmt = $pdo->prepare("
                    DELETE ic FROM indices_consultes ic
                    JOIN parcours p ON ic.equipe_id = p.equipe_id
                    WHERE p.lieu_id = ?
                ");
                $stmt->execute([$lieu_id]);
                
                // Réinitialiser les timestamps dans la table parcours pour ce lieu
                $stmt = $pdo->prepare("
                    UPDATE parcours 
                    SET enigme_start_time = NOW(), 
                        indice_start_time = DATE_ADD(NOW(), INTERVAL 6 MINUTE)
                    WHERE lieu_id = ? AND statut = 'en_cours'
                ");
                $stmt->execute([$lieu_id]);
                
                $success_message = "Timer des indices reseté pour le lieu '$lieu_nom'. Toutes les équipes peuvent maintenant recommencer les énigmes de ce lieu avec un nouveau délai de 6 minutes.";
            } catch (Exception $e) {
                $error_message = "Erreur lors du reset du timer du lieu: " . $e->getMessage();
            }
            break;
            
        // NOUVELLE ACTION : Reset tous les timers
        case 'reset_all_timers':
            try {
                // Supprimer toutes les consultations d'indices
                $stmt = $pdo->query("DELETE FROM indices_consultes");
                $stmt->execute();
                
                // Réinitialiser tous les timestamps dans la table parcours
                $stmt = $pdo->prepare("
                    UPDATE parcours 
                    SET enigme_start_time = NOW(), 
                        indice_start_time = DATE_ADD(NOW(), INTERVAL 6 MINUTE)
                    WHERE statut = 'en_cours'
                ");
                $stmt->execute();
                
                $success_message = "Tous les timers des indices ont été resetés. Toutes les équipes peuvent maintenant recommencer les énigmes avec un nouveau délai de 6 minutes.";
            } catch (Exception $e) {
                $error_message = "Erreur lors du reset global des timers: " . $e->getMessage();
            }
            break;
            
        // NOUVELLE ACTION : Modifier le délai d'indice d'un lieu
        case 'modify_delai_indice':
            $lieu_id = $_POST['lieu_id'];
            $lieu_nom = $_POST['lieu_nom'];
            $nouveau_delai = $_POST['nouveau_delai'];
            
            try {
                // Modifier le délai dans la table lieux
                $stmt = $pdo->prepare("UPDATE lieux SET delai_indice = ? WHERE id = ?");
                $stmt->execute([$nouveau_delai, $lieu_id]);
                
                $success_message = "Délai d'indice modifié pour le lieu '$lieu_nom' : $nouveau_delai minutes.";
            } catch (Exception $e) {
                $error_message = "Erreur lors de la modification du délai: " . $e->getMessage();
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
        SELECT l.id, l.nom as lieu, l.delai_indice, COUNT(*) as nb_indices
        FROM indices_consultes ic
        JOIN lieux l ON ic.lieu_id = l.id
        GROUP BY l.id, l.nom, l.delai_indice
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
    
    // NOUVEAU : État actuel des timers par équipe et lieu (CORRIGÉ avec gestion des NULL)
    $stmt = $pdo->query("
        SELECT 
            e.id as equipe_id,
            e.nom as equipe,
            l.id as lieu_id,
            l.nom as lieu,
            l.delai_indice,
            p.enigme_start_time,
            p.indice_start_time,
            p.statut,
            -- Gestion des NULL pour le temps écoulé
            CASE 
                WHEN p.enigme_start_time IS NULL THEN 0
                ELSE TIMESTAMPDIFF(MINUTE, p.enigme_start_time, NOW())
            END as minutes_ecoulees,
            -- NOUVEAU : Calcul correct du statut de l'indice avec gestion des NULL
            CASE 
                WHEN p.enigme_start_time IS NULL THEN 'Non démarré'
                WHEN TIMESTAMPDIFF(SECOND, p.enigme_start_time, NOW()) >= (l.delai_indice * 60) THEN 'Disponible'
                ELSE 'En attente'
            END as statut_indice,
            -- NOUVEAU : Calcul correct du temps restant avec gestion des NULL
            CASE 
                WHEN p.enigme_start_time IS NULL THEN (l.delai_indice * 60)
                WHEN TIMESTAMPDIFF(SECOND, p.enigme_start_time, NOW()) >= (l.delai_indice * 60) THEN 0
                ELSE (l.delai_indice * 60) - TIMESTAMPDIFF(SECOND, p.enigme_start_time, NOW())
            END as secondes_restantes
        FROM parcours p
        JOIN equipes e ON p.equipe_id = e.id
        JOIN lieux l ON p.lieu_id = l.id
        WHERE p.statut = 'en_cours'
        ORDER BY e.nom, l.nom
    ");
    $etat_timers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des statistiques: " . $e->getMessage();
}

// Configuration pour le header
$page_title = 'Administration des Indices et Timers - Cyberchasse';
$current_page = 'indices_stats';
$breadcrumb_items = [
    ['text' => 'Tableau de bord', 'url' => 'admin.php', 'active' => false],
    ['text' => 'Administration des Indices', 'url' => 'indices_stats.php', 'active' => true]
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
            .timer-status { font-weight: bold; }
            .timer-available { color: #28a745; }
            .timer-waiting { color: #ffc107; }
        </style>

        <!-- En-tête de la page -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-clock"></i> Administration des Indices et Timers</h1>
                <p class="text-muted">Gestion complète des timers d'indice et des consultations</p>
            </div>
            <div>
                <!-- NOUVEAU BOUTON : Reset tous les timers -->
                <form method="POST" style="display: inline;" 
                      onsubmit="return confirm('Êtes-vous sûr de vouloir resetter TOUS les timers des indices ? Toutes les équipes pourront recommencer les énigmes avec un nouveau délai de 6 minutes.')">
                    <input type="hidden" name="action" value="reset_all_timers">
                    <button type="submit" class="btn btn-warning me-2">
                        <i class="fas fa-clock"></i> Reset Tous les Timers
                    </button>
                </form>
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

        <!-- NOUVEAU : État actuel des timers -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card admin-card stats-card">
                    <div class="card-header bg-dark text-white">
                        <h5><i class="fas fa-clock"></i> État Actuel des Timers d'Indice</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($etat_timers)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Aucun timer d'indice actif.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Équipe</th>
                                            <th>Lieu</th>
                                            <th>Délai Configuré</th>
                                            <th>Début Énigme</th>
                                            <th>Indice Disponible</th>
                                            <th>Temps Écoulé</th>
                                            <th>Statut Indice</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($etat_timers as $timer): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($timer['equipe']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($timer['lieu']); ?></td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?php echo $timer['delai_indice']; ?> min
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php 
                                                        if ($timer['enigme_start_time']) {
                                                            echo date('H:i', strtotime($timer['enigme_start_time']));
                                                        } else {
                                                            echo '<span class="text-muted">Non défini</span>';
                                                        }
                                                        ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php 
                                                        if ($timer['indice_start_time']) {
                                                            echo date('H:i', strtotime($timer['indice_start_time']));
                                                        } else {
                                                            echo '<span class="text-muted">Non défini</span>';
                                                        }
                                                        ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo $timer['minutes_ecoulees']; ?> min
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="timer-status <?php echo $timer['statut_indice'] === 'Disponible' ? 'timer-available' : 'timer-waiting'; ?>">
                                                        <?php 
                                                        if ($timer['statut_indice'] === 'Non démarré') {
                                                            echo '<span class="text-muted">Non démarré</span>';
                                                        } elseif ($timer['statut_indice'] === 'En attente') {
                                                            $secondes_restantes = $timer['secondes_restantes'];
                                                            $minutes_restantes = floor($secondes_restantes / 60);
                                                            $secondes = $secondes_restantes % 60;
                                                            echo "Disponible dans {$minutes_restantes}m {$secondes}s";
                                                        } else {
                                                            echo htmlspecialchars($timer['statut_indice']);
                                                        }
                                                        ?>
                                                    </span>
                                                </td>
                                                <td class="action-buttons">
                                                    <!-- Bouton pour modifier le délai du lieu -->
                                                    <button type="button" class="btn btn-primary btn-sm me-1" 
                                                            onclick="modifierDelai(<?php echo $timer['lieu_id'] ?? 'null'; ?>, '<?php echo htmlspecialchars($timer['lieu']); ?>', <?php echo $timer['delai_indice']; ?>)"
                                                            title="Modifier le délai d'indice pour ce lieu">
                                                        <i class="fas fa-edit"></i> Délai
                                                    </button>
                                                    
                                                    <!-- Bouton pour resetter le timer de cette équipe sur ce lieu -->
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Resetter le timer pour <?php echo htmlspecialchars($timer['equipe']); ?> sur <?php echo htmlspecialchars($timer['lieu']); ?> ?')">
                                                        <input type="hidden" name="action" value="reset_indice_timer_equipe">
                                                        <input type="hidden" name="equipe_id" value="<?php echo $timer['equipe_id'] ?? 'null'; ?>">
                                                        <input type="hidden" name="equipe_nom" value="<?php echo htmlspecialchars($timer['equipe']); ?>">
                                                        <button type="submit" class="btn btn-warning btn-sm" title="Resetter le timer pour cette équipe sur ce lieu">
                                                            <i class="fas fa-redo"></i> Reset
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
                                                    <!-- NOUVEAU BOUTON : Reset timer pour cette équipe -->
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir resetter le timer des indices pour l\'équipe <?php echo htmlspecialchars($equipe['equipe']); ?> ? L\'équipe pourra recommencer les énigmes avec un nouveau délai de 6 minutes.')">
                                                        <input type="hidden" name="action" value="reset_indice_timer_equipe">
                                                        <input type="hidden" name="equipe_id" value="<?php echo $equipe['id']; ?>">
                                                        <input type="hidden" name="equipe_nom" value="<?php echo htmlspecialchars($equipe['equipe']); ?>">
                                                        <button type="submit" class="btn btn-info btn-sm me-1" title="Resetter le timer des indices pour cette équipe">
                                                            <i class="fas fa-clock"></i> Reset Timer
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer TOUS les indices consultés par l\'équipe <?php echo htmlspecialchars($equipe['equipe']); ?> ?')">
                                                        <input type="hidden" name="action" value="reset_equipe_indices">
                                                        <input type="hidden" name="equipe_id" value="<?php echo $equipe['id']; ?>">
                                                        <input type="hidden" name="equipe_nom" value="<?php echo htmlspecialchars($equipe['equipe']); ?>">
                                                        <button type="submit" class="btn btn-warning btn-sm me-1" title="Supprimer tous les indices consultés par cette équipe">
                                                            <i class="fas fa-undo"></i> Reset Indices
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
                                            <th>Délai Configuré</th>
                                            <th>Indices Consultés</th>
                                            <th>Pourcentage</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($indices_par_lieu as $lieu): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($lieu['lieu']); ?></strong></td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?php echo $lieu['delai_indice']; ?> min
                                                    </span>
                                                </td>
                                                <td><?php echo $lieu['nb_indices']; ?></td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <?php echo round(($lieu['nb_indices'] / $total_indices) * 100, 1); ?>%
                                                    </span>
                                                </td>
                                                <td class="action-buttons">
                                                    <!-- Bouton pour modifier le délai -->
                                                    <button type="button" class="btn btn-primary btn-sm me-1" 
                                                            onclick="modifierDelai(<?php echo $lieu['id']; ?>, '<?php echo htmlspecialchars($lieu['lieu']); ?>', <?php echo $lieu['delai_indice']; ?>)"
                                                            title="Modifier le délai d'indice pour ce lieu">
                                                        <i class="fas fa-edit"></i> Délai
                                                    </button>
                                                    
                                                    <!-- Bouton pour resetter tous les timers de ce lieu -->
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Resetter tous les timers pour le lieu <?php echo htmlspecialchars($lieu['lieu']); ?> ?')">
                                                        <input type="hidden" name="action" value="reset_indice_timer_lieu">
                                                        <input type="hidden" name="lieu_id" value="<?php echo $lieu['id']; ?>">
                                                        <input type="hidden" name="lieu_nom" value="<?php echo htmlspecialchars($lieu['lieu']); ?>">
                                                        <button type="submit" class="btn btn-warning btn-sm" title="Resetter tous les timers pour ce lieu">
                                                            <i class="fas fa-redo"></i> Reset Lieu
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

<!-- Modal pour modifier le délai d'indice -->
<div class="modal fade" id="modalModifierDelai" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier le Délai d'Indice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="modify_delai_indice">
                    <input type="hidden" name="lieu_id" id="lieu_id_modal">
                    <input type="hidden" name="lieu_nom" id="lieu_nom_modal">
                    
                    <div class="mb-3">
                        <label for="lieu_nom_display" class="form-label">Lieu :</label>
                        <input type="text" class="form-control" id="lieu_nom_display" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nouveau_delai" class="form-label">Nouveau délai (en minutes) :</label>
                        <input type="number" class="form-control" id="nouveau_delai" name="nouveau_delai" 
                               min="1" max="60" required>
                        <small class="text-muted">Délai en minutes avant que l'indice soit disponible</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Modifier le Délai</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fonction pour ouvrir le modal de modification du délai
function modifierDelai(lieuId, lieuNom, delaiActuel) {
    document.getElementById('lieu_id_modal').value = lieuId;
    document.getElementById('lieu_nom_modal').value = lieuNom;
    document.getElementById('lieu_nom_display').value = lieuNom;
    document.getElementById('nouveau_delai').value = delaiActuel;
    
    // Ouvrir le modal
    const modal = new bootstrap.Modal(document.getElementById('modalModifierDelai'));
    modal.show();
}
</script>

<?php include 'includes/footer.php'; ?>
