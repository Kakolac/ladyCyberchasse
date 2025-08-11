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
                <div class="card-header bg-danger text-white">
                    <h2>⚡ Laboratoire de Physique - Cryptographie</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5>🔐 Alerte Cryptographie !</h5>
                        <p>Des messages secrets ont été interceptés ! Votre mission : utiliser les principes physiques pour décoder des messages cryptés et comprendre la cryptographie.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>�� Mission en cours</h5>
                            <p>Explorez le laboratoire de physique pour :</p>
                            <ul>
                                <li>Décoder des messages avec des principes physiques</li>
                                <li>Comprendre la cryptographie quantique</li>
                                <li>Utiliser les ondes pour la transmission</li>
                                <li>Résoudre des énigmes scientifiques</li>
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
                        <h4>🔬 Prêt à décoder avec la physique ?</h4>
                        <a href="enigme.php" class="btn btn-danger btn-lg">�� Commencer l'énigme physique</a>
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
                        <a href="../salle_info/" class="list-group-item list-group-item-action">
                            �� Salle Informatique
                        </a>
                        <a href="../labo_chimie/" class="list-group-item list-group-item-action">
                            🧪 Laboratoire de Chimie
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
                        <div class="progress-bar" role="progressbar" style="width: 100%">100%</div>
                    </div>
                    <small class="text-muted">4/4 lieux explorés</small>
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

