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
$lieu_slug = 'standard';

// Récupération du token actuel pour ce lieu
$stmt = $pdo->prepare("
    SELECT ct.*, l.nom as lieu_nom, l.slug as lieu_slug, l.qrcodeObligatoire,
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

// Vérification si l'énigme est déjà résolue
$enigme_resolue = ($current['statut'] === 'termine');

// RÉCUPÉRATION DU PROCHAIN LIEU dans l'ordre du parcours
$stmt = $pdo->prepare("
    SELECT l.nom as prochain_lieu_nom, l.slug as prochain_lieu_slug, 
           l.qrcodeObligatoire as prochain_qr_obligatoire
    FROM cyber_token ct
    JOIN cyber_lieux l ON ct.lieu_id = l.id
    WHERE ct.equipe_id = ? AND ct.parcours_id = ? AND ct.statut = 'en_attente'
    ORDER BY ct.ordre_visite ASC
    LIMIT 1
");
$stmt->execute([$equipe_id, $_SESSION['parcours_id']]);
$prochain_lieu = $stmt->fetch(PDO::FETCH_ASSOC);

// VÉRIFICATION : QR Code obligatoire pour le PROCHAIN lieu
$qr_code_obligatoire = ($prochain_lieu && $prochain_lieu['prochain_qr_obligatoire'] == 1);

include './header.php';
?>

<!-- Inclusion de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class='container mt-4'>
    <div class='row'>
        <div class='col-md-8'>
            <div class='card'>
                <div class='card-header bg-dark text-white'>
                    <h2>👔 <?php echo htmlspecialchars($current['lieu_nom']); ?></h2>
                </div>
                <div class='card-body'>
                    
                    <?php if ($enigme_resolue): ?>
                        <!-- Énigme déjà résolue -->
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h4>🎉 Bravo !</h4>
                            <p>Vous avez déjà résolu l'énigme de ce lieu.</p>
                        </div>
                        
                        <div class="text-center">
                            <p class="mb-3">Vous avez maintenant la possibilité de voyager jusqu'à votre prochaine destination</p>
                            
                            <?php if ($prochain_lieu): ?>
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <strong>Prochain lieu :</strong> <?php echo htmlspecialchars($prochain_lieu['prochain_lieu_nom']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- AFFICHAGE CONDITIONNEL : Scanner QR seulement si le PROCHAIN lieu l'exige -->
                            <?php if ($qr_code_obligatoire): ?>
                                <button id="qrScannerBtnMain" class="qr-scanner-btn">
                                    📷 Scanner QR pour accéder au prochain lieu
                                </button>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check"></i>
                                    <strong>Accès direct :</strong> Le prochain lieu ne nécessite pas de QR code
                                </div>
                            <?php endif; ?>
                        </div>
                        
                    <?php else: ?>
                        <!-- Énigme à résoudre -->
                        <div class='alert alert-info'>
                            <?php if (!empty($current['lieu_description'])): ?>
                                <p class="mb-0"><?php echo htmlspecialchars($current['lieu_description']); ?></p>
                            <?php else: ?>
                                <p class="mb-0">Aucune description disponible pour ce lieu.</p>
                            <?php endif; ?>
                        </div>
                        
                        <hr>
                        
                        <div class='text-center'>
                            <h4> Prêt à commencer l'enquête ?</h4>
                            <a href='../../enigme_launcher.php?lieu=<?php echo $lieu_slug; ?>' class='btn btn-dark btn-lg'> Commencer l'énigme</a>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Connexion du bouton Scanner QR principal au composant existant
// SEULEMENT si le PROCHAIN lieu nécessite un QR code
<?php if ($qr_code_obligatoire): ?>
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
<?php endif; ?>
</script>

<?php include './footer.php'; ?>