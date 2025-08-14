<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/connexion.php';

// Récupération du lieu depuis l'URL
$lieu_slug = $_GET['lieu'] ?? '';
if (empty($lieu_slug)) {
    header('Location: accueil/');
    exit();
}

// Récupération des informations de l'équipe et du lieu
$team_name = $_SESSION['team_name'];

// Récupération de l'équipe
$stmt = $pdo->prepare("SELECT id FROM equipes WHERE nom = ?");
$stmt->execute([$team_name]);
$equipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipe) {
    header('Location: login.php');
    exit();
}

// Récupération du lieu avec son énigme
$stmt = $pdo->prepare("
    SELECT l.*, e.id as enigme_id, e.type_enigme_id, te.template, te.nom as type_nom
    FROM lieux l 
    LEFT JOIN enigmes e ON l.enigme_id = e.id 
    LEFT JOIN types_enigmes te ON e.type_enigme_id = te.id
    WHERE l.slug = ?
");
$stmt->execute([$lieu_slug]);
$lieu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lieu) {
    header('Location: accueil/');
    exit();
}

// Vérification si l'énigme est déjà résolue
$stmt = $pdo->prepare("SELECT * FROM parcours WHERE equipe_id = ? AND lieu_id = ?");
$stmt->execute([$equipe['id'], $lieu['id']]);
$parcours = $stmt->fetch(PDO::FETCH_ASSOC);
$enigme_resolue = ($parcours && $parcours['statut'] === 'termine');

// Gestion du timing pour les indices
$enigme_start_time = null;
$indice_available = false;

if (!$enigme_resolue && $lieu['enigme_id']) {
    // Créer une clé unique pour cette session d'énigme
    $enigme_session_key = "enigme_start_{$lieu['id']}_{$equipe['id']}";
    
    // Vérifier si l'énigme a déjà commencé
    if (!isset($_SESSION[$enigme_session_key])) {
        // Première fois qu'on lance l'énigme
        $_SESSION[$enigme_session_key] = time();
        $enigme_start_time = time();
    } else {
        // L'énigme a déjà commencé
        $enigme_start_time = $_SESSION[$enigme_session_key];
    }
    
    // Calculer si l'indice est disponible (6 minutes = 360 secondes)
    $elapsed_time = time() - $enigme_start_time;
    $indice_available = ($elapsed_time >= 360);
}

// Inclusion du header
include 'includes/header.php';
?>

<!-- Inclusion de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class='container mt-4'>
    <div class='row justify-content-center'>
        <div class='col-md-10'>
            <div class='card'>
                <div class='card-header bg-dark text-white'>
                    <h2>🔍 Énigme - <?php echo htmlspecialchars($lieu['nom']); ?></h2>
                    <?php if ($lieu['type_nom']): ?>
                        <small>Type : <?php echo htmlspecialchars($lieu['type_nom']); ?></small>
                    <?php endif; ?>
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
                            <a href="lieux/<?php echo $lieu_slug; ?>/" class="btn btn-dark btn-lg">
                                <i class="fas fa-arrow-left"></i> Retour au lieu
                            </a>
                        </div>
                        
                    <?php elseif ($lieu['enigme_id']): ?>
                        <!-- Énigme à résoudre -->
                        <div class='alert alert-info'>
                            <h5>🎯 Contexte</h5>
                            <p>Résolvez cette énigme de cybersécurité pour progresser dans votre mission et débloquer le prochain lieu !</p>
                        </div>
                        
                        <?php
                        // Inclusion du template spécifique au type d'énigme
                        $template_path = "templates/enigmes/{$lieu['template']}.php";
                        if (file_exists($template_path)) {
                            include $template_path;
                        } else {
                            echo '<div class="alert alert-warning">Template d\'énigme non trouvé</div>';
                        }
                        ?>
                        
                    <?php else: ?>
                        <!-- Aucune énigme configurée -->
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h4>⚠️ Aucune énigme configurée</h4>
                            <p>Ce lieu n'a pas encore d'énigme configurée.</p>
                        </div>
                        
                        <div class="text-center">
                            <a href="lieux/<?php echo $lieu_slug; ?>/" class="btn btn-dark btn-lg">
                                <i class="fas fa-arrow-left"></i> Retour au lieu
                            </a>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales pour l'énigme
const LIEU_SLUG = '<?php echo $lieu_slug; ?>';
const TEAM_NAME = '<?php echo $_SESSION["team_name"]; ?>';
const ENIGME_RESOLUE = <?php echo $enigme_resolue ? 'true' : 'false'; ?>;

// Variables pour le timing des indices
const ENIGME_START_TIME = <?php echo $enigme_start_time ?: 'null'; ?>;
const INDICE_AVAILABLE = <?php echo $indice_available ? 'true' : 'false'; ?>;
const INDICE_DELAY = 180; // 3 minutes en secondes

// Démarrer le timer seulement si l'énigme n'est pas résolue
<?php if (!$enigme_resolue && $lieu['enigme_id']): ?>
    startTimer(720, 'timer');
    
    // Démarrer le timer pour l'indice si pas encore disponible
    if (!INDICE_AVAILABLE && ENIGME_START_TIME) {
        startIndiceTimer();
    }
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>