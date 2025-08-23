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
$lieu_slug = 'start';

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

// Marquer le lieu actuel comme terminé
$stmt = $pdo->prepare("
    UPDATE cyber_token 
    SET statut = 'termine', 
        created_at = CURRENT_TIMESTAMP
    WHERE equipe_id = ? AND lieu_id = ? AND parcours_id = ?
");
$stmt->execute([$equipe_id, $current['lieu_id'], $_SESSION['parcours_id']]);

// Trouver le prochain lieu dans l'ordre
$stmt = $pdo->prepare("
    SELECT l.*, ct.token_acces, ct.ordre_visite
    FROM cyber_lieux l
    JOIN cyber_token ct ON l.id = ct.lieu_id
    WHERE ct.equipe_id = ? 
    AND ct.parcours_id = ?
    AND ct.statut = 'en_attente'
    ORDER BY ct.ordre_visite ASC
    LIMIT 1
");
$stmt->execute([$equipe_id, $_SESSION['parcours_id']]);
$prochain_lieu = $stmt->fetch(PDO::FETCH_ASSOC);

// Si pas de prochain lieu, rediriger vers la page de fin
if (!$prochain_lieu) {
    header('Location: ../fin/');
    exit();
}

include './header.php';
?>

<div class='container mt-4'>
    <div class='row justify-content-center'>
        <div class='col-md-10'>
            <div class='card border-0 shadow-lg'>
                <div class='card-header text-center py-4'>
                    <h1 class="mb-0">🎉 Bienvenue</h1>
                    <h3>Commencez votre cyberchasse</h3>
                </div>
                <div class='card-body p-5'>
                    <!-- Message de bienvenue -->
                    <div class="text-center mb-4">
                        <div class="message-box">
                            <h5>
                                <i class="fas fa-info-circle"></i> 
                                Prochain lieu : <?php echo htmlspecialchars($prochain_lieu['nom']); ?>
                            </h5>
                            <?php if (!empty($prochain_lieu['description'])): ?>
                                <p class="description-text">
                                    <?php echo htmlspecialchars($prochain_lieu['description']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
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
                                Scannez le QR code pour accéder au prochain lieu
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
