<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

require_once '../../config/connexion.php';

// Récupération des informations de l'équipe et du lieu
$team_name = $_SESSION['team_name'];
$lieu_slug = 'salle_info';

// Récupération de l'équipe
$stmt = $pdo->prepare("SELECT id FROM equipes WHERE nom = ?");
$stmt->execute([$team_name]);
$equipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipe) {
    header('Location: ../../login.php');
    exit();
}

// Récupération du lieu
$stmt = $pdo->prepare("SELECT id, nom, ordre FROM lieux WHERE slug = ?");
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

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h2>💻 <?php echo htmlspecialchars($lieu['nom']); ?> - Cybersécurité</h2>
                </div>
                <div class="card-body">
                    
                    <?php if ($enigme_resolue): ?>
                        <!-- Énigme déjà résolue -->
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h4>🎉 Bravo !</h4>
                            <p>Vous avez déjà résolu l'énigme de ce lieu.</p>
                            <p class="mb-0"><strong>Score obtenu :</strong> <?php echo $parcours['score_obtenu'] ?? 0; ?> points</p>
                        </div>
                        
                        <div class="text-center">
                            <a href="../accueil/" class="btn btn-info btn-lg">
                                <i class="fas fa-home"></i> Retour à l'accueil
                            </a>
                        </div>
                        
                    <?php else: ?>
                        <!-- Énigme à résoudre -->
                        <div class="alert alert-danger">
                            <h5>🚨 Alerte Mots de Passe !</h5>
                            <p>Des comptes utilisateurs ont été compromis ! Votre mission : tester la force des mots de passe et apprendre les bonnes pratiques de cybersécurité.</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5> Mission en cours</h5>
                                <p>Explorez la salle informatique pour :</p>
                                <ul>
                                    <li>Tester la force des mots de passe</li>
                                    <li>Décrypter des messages secrets</li>
                                    <li>Apprendre la cryptographie</li>
                                    <li>Identifier les vulnérabilités</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>⏱️ Temps restant</h5>
                                <div id="timer" class="display-4 text-danger"></div>
                                <p class="text-muted">Vous avez 12 minutes pour cette mission</p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="text-center">
                            <h4>🔐 Prêt à tester la cybersécurité ?</h4>
                            <a href="enigme.php" class="btn btn-info btn-lg"> Commencer l'énigme cybersécurité</a>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>🗺️ Navigation</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="../accueil/" class="list-group-item list-group-item-action">
                             Retour à l'accueil
                        </a>
                        <a href="../cdi/" class="list-group-item list-group-item-action">
                            📚 CDI
                        </a>
                        <a href="../vie_scolaire/" class="list-group-item list-group-item-action">
                            👥 Vie Scolaire
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5>📊 Progression</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: 75%">75%</div>
                    </div>
                    <small class="text-muted">3/4 lieux explorés</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Démarrer le timer seulement si l'énigme n'est pas résolue
<?php if (!$enigme_resolue): ?>
    startTimer(720, 'timer');
</script>
<?php endif; ?>

<?php include './footer.php'; ?>
```

