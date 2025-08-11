<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

include './header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h2>👥 Vie Scolaire - Administration des Élèves</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h5>🚨 Alerte RGPD !</h5>
                        <p>Des données personnelles d'élèves ont été compromises ! Votre mission : identifier les bonnes pratiques de protection des données et sécuriser les informations sensibles.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>�� Mission en cours</h5>
                            <p>Explorez la vie scolaire pour :</p>
                            <ul>
                                <li>Identifier les bonnes pratiques RGPD</li>
                                <li>Détecter les violations de confidentialité</li>
                                <li>Apprendre la protection des données</li>
                                <li>Collecter les indices de sécurité</li>
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
                        <h4>🔍 Prêt à enquêter sur la protection des données ?</h4>
                        <a href="enigme.php" class="btn btn-warning btn-lg">�� Commencer l'énigme RGPD</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5>🗺️ Navigation</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="../accueil/" class="list-group-item list-group-item-action">
                            �� Retour à l'accueil
                        </a>
                        <a href="../cdi/" class="list-group-item list-group-item-action">
                            📚 CDI
                        </a>
                        <a href="../salle_info/" class="list-group-item list-group-item-action">
                            �� Salle Informatique
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
                        <div class="progress-bar" role="progressbar" style="width: 50%">50%</div>
                    </div>
                    <small class="text-muted">2/4 lieux explorés</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../js/game-timer.js"></script>
<script>
    startTimer(720, 'timer');
</script>

<?php include './footer.php'; ?>
```

