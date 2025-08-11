<?php
session_start();
require_once '../../config/connexion.php';

// Vérification de la session
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

// Vérification du token de validation
$token_validation = $_GET['token_validation'] ?? '';
if (empty($token_validation)) {
    header('Location: ../../accueil/');
    exit();
}

// Vérifier que le token de validation appartient à l'équipe connectée
try {
    $stmt = $pdo->prepare("
        SELECT s.*, p.equipe_id, e.nom as equipe_nom
        FROM sessions_jeu s
        JOIN parcours p ON s.equipe_id = p.equipe_id
        JOIN equipes e ON p.equipe_id = e.id
        WHERE s.token_validation = ? AND s.statut = 'active'
    ");
    $stmt->execute([$token_validation]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$session) {
        // Token invalide ou session expirée
        header('Location: ../../accueil/');
        exit();
    }
    
    // Vérifier que l'équipe connectée correspond à la session
    if ($session['equipe_nom'] !== $_SESSION['team_name']) {
        // Tentative de fraude : équipe différente
        error_log("TENTATIVE DE FRAUDE: Équipe {$_SESSION['team_name']} tente d'utiliser la session de l'équipe {$session['equipe_nom']}");
        header('Location: ../../accueil/');
        exit();
    }
    
    // Vérifier que le lieu correspond
    if ($session['lieu_id'] != 3) { // ID du CDI
        // Tentative d'accès au mauvais lieu
        header('Location: ../../accueil/');
        exit();
    }
    
} catch (Exception $e) {
    error_log("Erreur de validation de session: " . $e->getMessage());
    header('Location: ../../accueil/');
    exit();
}

include './header.php';
?>

<!-- Reste du contenu de la page CDI -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2>📚 CDI - Centre de Documentation et d'Information</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>🔒 Accès sécurisé validé</h5>
                        <p>Bienvenue dans le CDI, équipe <strong><?php echo htmlspecialchars($_SESSION['team_name']); ?></strong> !</p>
                        <p>Votre session est validée et sécurisée.</p>
                    </div>
                    
                    <!-- Contenu de l'énigme CDI -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5> Mission en cours</h5>
                            <p>Explorez le CDI pour :</p>
                            <ul>
                                <li>Identifier les sources fiables</li>
                                <li>Détecter les fake news</li>
                                <li>Apprendre la vérification d'information</li>
                                <li>Collecter les indices de cybersécurité</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>⏱️ Temps restant</h5>
                            <div id="timer" class="display-4 text-warning"></div>
                            <p class="text-muted">Vous avez 7 minutes pour cette mission</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <h4>🔒 Prêt à sécuriser le CDI ?</h4>
                        <a href="enigme.php" class="btn btn-primary btn-lg"> Commencer l'énigme CDI</a>
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
                        <a href="../../accueil/" class="list-group-item list-group-item-action">
                             Retour à l'accueil
                        </a>
                        <a href="../cantine/" class="list-group-item list-group-item-action">
                            🍽️ Cantine
                        </a>
                        <a href="../cour/" class="list-group-item list-group-item-action">
                             Cour de récréation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../js/game-timer.js"></script>
<script>
    startTimer(420, 'timer'); // 7 minutes = 420 secondes
</script>

<?php include './footer.php'; ?>
