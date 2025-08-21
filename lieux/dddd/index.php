<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

require_once '../../config/connexion.php';

// Récupération des informations de l'équipe et du lieu
$team_name = $_SESSION['team_name'];
$lieu_slug = 'dddd';

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

// Récupération du parcours de l'équipe pour ce lieu
$stmt = $pdo->prepare("SELECT * FROM parcours WHERE equipe_id = ? AND lieu_id = ?");
$stmt->execute([$equipe['id'], $lieu['id']]);
$parcours = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérification si l'énigme est déjà résolue
$enigme_resolue = ($parcours && $parcours['statut'] === 'termine');

include './header.php';
?>

<!-- Inclusion de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class='container mt-4'>
    <div class='row'>
        <div class='col-md-8'>
            <div class='card'>
                <div class='card-header bg-dark text-white'>
                    <h2>👔 <?php echo htmlspecialchars($lieu['nom']); ?></h2>
                </div>
                <div class='card-body'>
                    
                    <?php if ($enigme_resolue): ?>
                        <!-- Énigme déjà résolue -->
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h4>🎉 Bravo !</h4>
                            <p>Vous avez déjà résolu l'énigme de ce lieu.</p>
                            <p class="mb-0"><strong>Score obtenu :</strong> <?php echo $parcours['score_obtenu'] ?? 0; ?> points</p>
                        </div>
                        
                        <div class="text-center">
                            <p class="mb-3">Vous avez maintenant la possibilité de voyager jusqu'à votre prochaine destination</p>
                            <button id="qrScannerBtnMain" class="qr-scanner-btn">
                                📷 Scanner QR
                            </button>
                        </div>
                        
                    <?php else: ?>
                        <!-- Énigme à résoudre -->
                        <div class='alert alert-info'>
                            <?php if (!empty($lieu['description'])): ?>
                                <p class="mb-0"><?php echo htmlspecialchars($lieu['description']); ?></p>
                            <?php else: ?>
                                <p class="mb-0">Aucune description disponible pour ce lieu.</p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- <div class='row'>
                            <div class='col-md-6'>
                                <h5> Mission en cours</h5>
                                <p>Votre objectif :</p>
                                <ul>
                                    <li>Résoudre l'énigme du lieu</li>
                                    <li>Collecter des indices</li>
                                    <li>Progresser dans la cyberchasse</li>
                                    <li>Apprendre la cybersécurité</li>
                                </ul>
                            </div>
                            <div class='col-md-6'>
                                <h5>⏱️ Temps restant</h5>
                                <div id='timer' class='display-4 text-danger'></div>
                                <p class='text-muted'>Vous avez 12 minutes pour cette mission</p>
                            </div>
                        </div> -->
                        
                        <hr>
                        
                        <div class='text-center'>
                            <h4> Prêt à commencer l'enquête ?</h4>
                            <a href='../../enigme_launcher.php?lieu=<?php echo $lieu_slug; ?>' class='btn btn-dark btn-lg'> Commencer l'énigme</a>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
        
        <!-- <div class='col-md-4'>
            <div class='card'>
                <div class='card-header bg-primary text-white'>
                    <h5>🗺️ Navigation</h5>
                </div>
                <div class='card-body'>
                    <div class='list-group'>
                        <a href='../accueil/' class='list-group-item list-group-item-action'>
                             Retour à l'accueil
                        </a>
                        <a href='../cdi/' class='list-group-item list-group-item-action'>
                            📚 CDI
                        </a>
                        <a href='../salle_info/' class='list-group-item list-group-item-action'>
                             Salle Informatique
                        </a>
                    </div>
                </div>
            </div>
            
            <div class='card mt-3'>
                <div class='card-header bg-secondary text-white'>
                    <h5>📊 Progression</h5>
                </div>
                <div class='card-body'>
                    <div class='progress mb-2'>
                        <div class='progress-bar' role='progressbar' style='width: 25%'>25%</div>
                    </div>
                    <small class='text-muted'>Progression en cours...</small>
                </div>
            </div>
        </div> -->
    </div>
</div>

<script>
// Démarrer le timer seulement si l'énigme n'est pas résolue
<?php if (!$enigme_resolue): ?>
    startTimer(720, 'timer');
<?php endif; ?>

// Connexion du bouton Scanner QR principal au composant existant
document.addEventListener('DOMContentLoaded', function() {
    const qrScannerBtnMain = document.getElementById('qrScannerBtnMain');
    if (qrScannerBtnMain) {
        qrScannerBtnMain.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('📱 Bouton scanner principal cliqué');
            // Utiliser la fonction globale du composant QR scanner
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