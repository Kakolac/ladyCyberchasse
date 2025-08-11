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
                <div class="card-header bg-info text-white">
                    <h2>�� Salle Informatique - Cybersécurité</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h5>🚨 Alerte Mots de Passe !</h5>
                        <p>Des comptes utilisateurs ont été compromis ! Votre mission : tester la force des mots de passe et apprendre les bonnes pratiques de cybersécurité.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>�� Mission en cours</h5>
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
                        <a href="enigme.php" class="btn btn-info btn-lg">�� Commencer l'énigme cybersécurité</a>
                    </div>
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
                            �� Retour à l'accueil
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

<script src="../../js/game-timer.js"></script>
<script>
    startTimer(720, 'timer');
</script>

<?php include './footer.php'; ?>
```

