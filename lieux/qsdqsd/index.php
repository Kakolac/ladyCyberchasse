<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

require_once '../../config/connexion.php';

// Récupération des informations de l'équipe et du lieu
$team_name = $_SESSION['team_name'];
$lieu_slug = 'qsdqsd';

// Récupération de l'équipe
$stmt = $pdo->prepare("SELECT id FROM equipes WHERE nom = ?");
$stmt->execute([$team_name]);
$equipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipe) {
    header('Location: ../../login.php');
    exit();
}

// Récupération du lieu
$stmt = $pdo->prepare("SELECT id, nom, ordre, description FROM lieux WHERE slug = ?");
$stmt->execute([$lieu_slug]);
$lieu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lieu) {
    header('Location: ../../accueil/');
    exit();
}

// Mise à jour directe du statut du lieu
try {
    // Vérifier si le lieu n'est pas déjà terminé pour éviter les doublons
    $stmt = $pdo->prepare("
        SELECT statut 
        FROM parcours 
        WHERE equipe_id = ? AND lieu_id = ?
    ");
    $stmt->execute([$equipe['id'], $lieu['id']]);
    $parcours = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$parcours || $parcours['statut'] !== 'termine') {
        // Mise à jour du parcours
        $stmt = $pdo->prepare("
            UPDATE parcours 
            SET statut = 'termine', 
                score_obtenu = ?, 
                temps_fin = NOW(),
                temps_ecoule = TIMESTAMPDIFF(SECOND, temps_debut, NOW())
            WHERE equipe_id = ? AND lieu_id = ?
        ");
        
        if ($stmt->execute([10, $equipe['id'], $lieu['id']])) {
            // Vérifier si c'est la fin du parcours
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as total_lieux, 
                       SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) as lieux_termines
                FROM parcours 
                WHERE equipe_id = ?
            ");
            $stmt->execute([$equipe['id']]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si tous les lieux sont terminés
            if ($stats['total_lieux'] > 0 && $stats['lieux_termines'] == $stats['total_lieux']) {
                $stmt = $pdo->prepare("UPDATE parcours SET statut = 'parcours_termine' WHERE equipe_id = ?");
                $stmt->execute([$equipe['id']]);
            }
        }
    }
} catch (Exception $e) {
    error_log("Erreur mise à jour statut lieu: " . $e->getMessage());
}

// Récupération des statistiques du parcours complet
$stmt = $pdo->prepare("
    SELECT 
        (SELECT COUNT(*) FROM parcours WHERE equipe_id = ? AND statut = 'termine') as total_lieux_visites,
        (SELECT COALESCE(SUM(score_obtenu), 0) FROM parcours WHERE equipe_id = ?) as score_total,
        (SELECT COUNT(*) FROM parcours WHERE equipe_id = ? AND statut = 'termine') / 
        (SELECT COUNT(*) FROM lieux WHERE enigme_requise = 1) * 100 as progression
");
$stmt->execute([$equipe['id'], $equipe['id'], $equipe['id']]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

include './header.php';
?>

<!-- Inclusion de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class='container mt-4'>
    <div class='row justify-content-center'>
        <div class='col-md-10'>
            <div class='card border-0 shadow-lg'>
                <div class='card-header text-center py-4'>
                    <h1 class="mb-0">🎉 Bienvenue</h1>
                    <h3>Commencez votre cyberchasse</h3>
                </div>
                <div class='card-body p-5'>
                    <!-- Message de fin personnalisé -->
                    <div class="text-center mb-4">
                        <?php if (!empty($lieu['description'])): ?>
                            <div class="message-box">
                                <h5>
                                    <i class="fas fa-info-circle"></i> 
                                    Instructions
                                </h5>
                                <p class="description-text">
                                    <?php echo htmlspecialchars($lieu['description']); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Section principale -->
                    <div class="text-center mt-5 pt-4 border-top">
                        <p class="welcome-text">
                            <i class="fas fa-heart accent-icon"></i>
                            Bienvenue dans cette cyberchasse !
                            <br>
                            <span class="subtitle-text">
                                Prêt à relever les défis de la cybersécurité ?
                            </span>
                        </p>
                        
                        <!-- Bouton QR Scanner -->
                        <div class="mt-4">
                            <p class="scan-text">
                                Scannez un QR code pour commencer votre aventure
                            </p>
                            <button id="qrScannerBtnMain" class="scan-button">
                                📷 Scanner QR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Connexion du bouton Scanner QR principal au composant existant
document.addEventListener('DOMContentLoaded', function() {
    const qrScannerBtnMain = document.getElementById('qrScannerBtnMain');
    if (qrScannerBtnMain) {
        qrScannerBtnMain.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('📱 Bouton scanner principal cliqué');
            if (typeof openQRScanner === 'function') {
                openQRScanner();
            } else {
                console.error('Fonction openQRScanner non disponible');
            }
        });
    }
});
</script>

<?php include './footer.php'; ?>
