<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

require_once '../../config/connexion.php';

// Récupération des informations de l'équipe et du lieu actuel
$team_name = $_SESSION['team_name'];
$equipe_id = $_SESSION['equipe_id'];
$lieu_slug = 'fin';

// Récupération du token actuel pour ce lieu
$stmt = $pdo->prepare("
    SELECT ct.*, l.nom as lieu_nom, l.slug as lieu_slug,
           p.nom as parcours_nom, p.description as parcours_description
    FROM cyber_token ct
    JOIN cyber_lieux l ON ct.lieu_id = l.id
    JOIN cyber_parcours p ON ct.parcours_id = p.id
    WHERE ct.equipe_id = ? AND l.slug = ? AND ct.parcours_id = ?
");
$stmt->execute([$equipe_id, $lieu_slug, $_SESSION['parcours_id']]);
$current = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current) {
    header('Location: ../../login.php');
    exit();
}

// Marquer le lieu de fin comme terminé
$stmt = $pdo->prepare("
    UPDATE cyber_token 
    SET statut = 'termine', 
        created_at = CURRENT_TIMESTAMP
    WHERE equipe_id = ? AND lieu_id = ? AND parcours_id = ?
");
$stmt->execute([$equipe_id, $current['lieu_id'], $_SESSION['parcours_id']]);

// Récupération des statistiques du parcours complet avec la nouvelle structure
$stmt = $pdo->prepare("
    SELECT 
        (SELECT COUNT(*) FROM cyber_token WHERE equipe_id = ? AND parcours_id = ? AND statut = 'termine') as total_lieux_visites,
        (SELECT COUNT(*) FROM cyber_token WHERE equipe_id = ? AND parcours_id = ?) as total_lieux_parcours,
        (SELECT COUNT(*) FROM cyber_token WHERE equipe_id = ? AND parcours_id = ? AND statut = 'termine') / 
        (SELECT COUNT(*) FROM cyber_token WHERE equipe_id = ? AND parcours_id = ?) * 100 as progression
");
$stmt->execute([$equipe_id, $_SESSION['parcours_id'], $equipe_id, $_SESSION['parcours_id'], $equipe_id, $_SESSION['parcours_id'], $equipe_id, $_SESSION['parcours_id']]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

include './header.php';
?>

<!-- Inclusion de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class='container mt-4'>
    <div class='row justify-content-center'>
        <div class='col-md-10'>
            <div class='card border-0 shadow-lg'>
                <div class='card-header bg-success text-white text-center py-4'>
                    <h1 class="mb-0">🎉 Félicitations !</h1>
                    <h3>Vous avez terminé la cyberchasse !</h3>
                </div>
                <div class='card-body p-5'>
                    
                    <!-- Message de félicitations -->
                    <div class="text-center mb-5">
                        <div class="alert alert-success border-0 shadow-sm">
                            <i class="fas fa-trophy fa-3x mb-3 text-warning"></i>
                            <h4>🏆 Mission accomplie !</h4>
                            <p class="mb-0">Bravo équipe <strong><?php echo htmlspecialchars($team_name); ?></strong> !</p>
                            <p>Vous avez brillamment relevé tous les défis de cette cyberchasse.</p>
                        </div>
                    </div>

                    <!-- Statistiques du parcours -->
                    <div class="row text-center mb-5">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <i class="fas fa-map-marker-alt fa-2x text-primary mb-2"></i>
                                    <h5 class="card-title"><?php echo $stats['total_lieux_visites'] ?? 0; ?></h5>
                                    <p class="card-text text-muted">Lieux complétés</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <i class="fas fa-route fa-2x text-info mb-2"></i>
                                    <h5 class="card-title"><?php echo $stats['total_lieux_parcours'] ?? 0; ?></h5>
                                    <p class="card-text text-muted">Total du parcours</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                                    <h5 class="card-title"><?php echo round($stats['progression'] ?? 0); ?>%</h5>
                                    <p class="card-text text-muted">Progression totale</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message de fin personnalisé -->
                    <div class="text-center mb-4">
                        <?php if (!empty($current['parcours_description'])): ?>
                            <div class="alert alert-info border-0 shadow-sm">
                                <h5><i class="fas fa-info-circle"></i> Message final</h5>
                                <p class="mb-0"><?php echo htmlspecialchars($current['parcours_description']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Remerciements -->
                    <div class="text-center mt-5 pt-4 border-top">
                        <p class="text-muted">
                            <i class="fas fa-heart text-danger"></i>
                            Merci d'avoir participé à cette cyberchasse !
                            <br>
                            <small>Nous espérons que vous avez appris beaucoup sur la cybersécurité.</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>
