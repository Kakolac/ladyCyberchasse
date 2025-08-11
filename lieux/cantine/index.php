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
                <div class="card-header bg-success text-white">
                    <h2>🍽️ Cantine - Sécurité Alimentaire & Numérique</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5>🚨 Alerte Sécurité !</h5>
                        <p>Le système de commande de la cantine a été piraté ! Votre mission : identifier les bonnes pratiques de sécurité numérique et protéger les données des repas.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>�� Mission en cours</h5>
                            <p>Explorez la cantine pour :</p>
                            <ul>
                                <li>Protéger les données de commande</li>
                                <li>Identifier les vulnérabilités</li>
                                <li>Apprendre la sécurité des paiements</li>
                                <li>Collecter les indices de cybersécurité</li>
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
                        <h4>🔒 Prêt à sécuriser la cantine ?</h4>
                        <a href="enigme.php" class="btn btn-success btn-lg">�� Commencer l'énigme cantine</a>
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
                        <a href="../gymnase/" class="list-group-item list-group-item-action">
                            🏃 Gymnase
                        </a>
                        <a href="../cour/" class="list-group-item list-group-item-action">
                            �� Cour de récréation
                        </a>
                    </div>
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

