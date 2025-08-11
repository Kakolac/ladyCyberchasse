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
                <div class="card-header bg-primary text-white">
                    <h2>�� Gymnase - Sécurité des Objets Connectés</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h5>�� Alerte IoT !</h5>
                        <p>Les objets connectés du gymnase (montres, bracelets) ont été compromis ! Votre mission : identifier les risques de sécurité des objets connectés et les bonnes pratiques.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>�� Mission en cours</h5>
                            <p>Explorez le gymnase pour :</p>
                            <ul>
                                <li>Identifier les risques IoT</li>
                                <li>Protéger les données de santé</li>
                                <li>Apprendre la sécurité des objets connectés</li>
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
                        <h4>📱 Prêt à sécuriser les objets connectés ?</h4>
                        <a href="enigme.php" class="btn btn-primary btn-lg">�� Commencer l'énigme gymnase</a>
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
                        <a href="../cantine/" class="list-group-item list-group-item-action">
                            🍽️ Cantine
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

