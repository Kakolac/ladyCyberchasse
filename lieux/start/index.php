<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}
//templateLieuDemarrage
require_once '../../config/connexion.php';

// Récupération des informations de l'équipe et du lieu actuel
$team_name = $_SESSION['team_name'];
$lieu_slug = 'start';

// Récupération de l'équipe et du lieu actuel
$stmt = $pdo->prepare("
    SELECT e.id as equipe_id, p.id as parcours_id, l.id as lieu_id 
    FROM equipes e
    JOIN parcours p ON e.id = p.equipe_id
    JOIN lieux l ON p.lieu_id = l.id
    WHERE e.nom = ? AND l.slug = ?
");
$stmt->execute([$team_name, $lieu_slug]);
$current = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current) {
    header('Location: ../../login.php');
    exit();
}

// Marquer le lieu actuel (direction) comme terminé
$stmt = $pdo->prepare("
    UPDATE parcours 
    SET statut = 'termine',
        temps_debut = CURRENT_TIMESTAMP,
        temps_fin = CURRENT_TIMESTAMP,
        score_obtenu = 10
    WHERE id = ?
");
$stmt->execute([$current['parcours_id']]);

// Appel à update_parcours_status.php pour le lieu actuel
$ch = curl_init('../../update_parcours_status.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'lieu' => $lieu_slug,
    'team' => $team_name,
    'success' => true,
    'score' => 10
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$result = curl_exec($ch);
curl_close($ch);

// Trouver le prochain lieu dans l'ordre
$stmt = $pdo->prepare("
    SELECT l.*, p.token_acces, p.ordre_visite
    FROM lieux l
    JOIN parcours p ON l.id = p.lieu_id
    WHERE p.equipe_id = ? 
    AND p.statut = 'en_attente'
    ORDER BY p.ordre_visite ASC
    LIMIT 1
");
$stmt->execute([$current['equipe_id']]);
$prochain_lieu = $stmt->fetch(PDO::FETCH_ASSOC);

// Si pas de prochain lieu, rediriger vers la page de fin
if (!$prochain_lieu) {
    header('Location: ../TemplateLieuFin/');
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
                        
                        <?php if ($prochain_lieu['qrcodeObligatoire']): ?>
                            <!-- Bouton QR Scanner -->
                            <div class="mt-4">
                                <p class="scan-text">
                                    Scannez le QR code pour accéder au prochain lieu
                                </p>
                                <button id="qrScannerBtnMain" class="scan-button">
                                    📷 Scanner QR
                                </button>
                            </div>
                        <?php else: ?>
                            <!-- Lien direct -->
                            <div class="mt-4">
                                <p class="scan-text">
                                    Cliquez sur le bouton pour accéder au prochain lieu
                                </p>
                                <a href="../../lieux/access.php?token=<?php echo urlencode($prochain_lieu['token_acces']); ?>&lieu=<?php echo urlencode($prochain_lieu['slug']); ?>" 
                                   class="btn btn-primary btn-lg">
                                    🚪 Accéder au lieu
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($prochain_lieu['qrcodeObligatoire']): ?>
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
<?php endif; ?>

<?php include './footer.php'; ?>
