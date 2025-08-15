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

// Récupération du lieu avec son énigme et son délai d'indice
$stmt = $pdo->prepare("
    SELECT l.*, e.id as enigme_id, e.type_enigme_id, te.template, te.nom as type_nom,
           COALESCE(l.delai_indice, 6) as delai_indice
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
$indice_start_time = null;
$indice_available = false;

if (!$enigme_resolue && $lieu['enigme_id']) {
    // Créer des clés uniques séparées
    $enigme_session_key = "enigme_start_{$lieu['id']}_{$equipe['id']}";
    $indice_session_key = "indice_start_{$lieu['id']}_{$equipe['id']}";
    
    // Vérifier s'il y a un reset de timer pour cette équipe
    $reset_detected = false;
    
    // Vérifier reset global
    $stmt = $pdo->prepare("SELECT MAX(timestamp) FROM resets_timers WHERE type_reset = 'global'");
    $stmt->execute();
    $reset_global_time = $stmt->fetchColumn();
    
    // Vérifier reset spécifique à l'équipe
    $stmt = $pdo->prepare("SELECT MAX(timestamp) FROM resets_timers WHERE equipe_id = ? AND type_reset = 'equipe'");
    $stmt->execute([$equipe['id']]);
    $reset_equipe_time = $stmt->fetchColumn();
    
    // Si reset détecté, supprimer les sessions et recommencer
    if (($reset_global_time && $reset_global_time > ($_SESSION['last_reset_check'] ?? 0)) || 
        ($reset_equipe_time && $reset_equipe_time > ($_SESSION['last_reset_check'] ?? 0))) {
        $reset_detected = true;
        
        // Supprimer toutes les clés de session liées à cette équipe et ce lieu
        unset($_SESSION[$enigme_session_key]);
        unset($_SESSION[$indice_session_key]);
        
        // Forcer la création de nouvelles sessions avec le timestamp du reset
        $enigme_start_time = time();
        $indice_start_time = time();
        $_SESSION[$enigme_session_key] = $enigme_start_time;
        $_SESSION[$indice_session_key] = $indice_start_time;
        
        // Recalculer la disponibilité de l'indice
        $indice_elapsed_time = 0;
        $indice_available = false;
        
        // Marquer ce reset comme traité
        $_SESSION['last_reset_check'] = max($reset_global_time ?: 0, $reset_equipe_time ?: 0);
        
        // Debug du reset
        error_log("RESET DETECTED - Nouveau timestamp: " . date('H:i:s', $indice_start_time));
        error_log("RESET DETECTED - Indice available: " . ($indice_available ? 'true' : 'false'));
    } else {
        // Gestion normale des timers - CORRECTION PRINCIPALE
        if (!isset($_SESSION[$enigme_session_key])) {
            // Première fois qu'on lance l'énigme
            $_SESSION[$enigme_session_key] = time();
            $enigme_start_time = $_SESSION[$enigme_session_key];
        } else {
            // L'énigme a déjà commencé - RÉCUPÉRER le timestamp existant
            $enigme_start_time = $_SESSION[$enigme_session_key];
        }
        
        // Gestion du timer de l'indice - SÉPARÉ ET PERSISTANT
        if (!isset($_SESSION[$indice_session_key])) {
            // Première fois qu'on lance l'indice - DÉMARRER APRÈS 3 minutes
            $indice_start_time = $enigme_start_time + 180; // 3 minutes après le début de l'énigme
            $_SESSION[$indice_session_key] = $indice_start_time;
        } else {
            // L'indice a déjà commencé - RÉCUPÉRER le timestamp existant
            $indice_start_time = $_SESSION[$indice_session_key];
        }
        
        // Calculer si l'indice est disponible (délai dynamique depuis la BDD)
        $enigme_elapsed_time = time() - $enigme_start_time;
        $delai_indice_secondes = $lieu['delai_indice'] * 60; // Convertir en secondes
        $indice_available = ($enigme_elapsed_time >= $delai_indice_secondes);
    }
    
    // Passer les deux timers au template
    $enigme_elapsed_time = time() - $enigme_start_time;
    
    // Debug final pour vérifier
    error_log("FINAL DEBUG - Indice available: " . ($indice_available ? 'true' : 'false'));
    error_log("FINAL DEBUG - Enigme elapsed: " . $enigme_elapsed_time);
    error_log("FINAL DEBUG - Indice start time: " . date('H:i:s', $indice_start_time));
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
const INDICE_START_TIME = <?php echo $indice_start_time ?: 'null'; ?>;
const INDICE_AVAILABLE = <?php echo $indice_available ? 'true' : 'false'; ?>;
const DELAI_INDICE_SECONDES = <?php echo $lieu['delai_indice'] * 60; ?>; // Délai dynamique en secondes

// Configuration globale de SweetAlert2 pour personnaliser l'apparence
Swal.mixin({
    confirmButtonColor: '#343a40',
    cancelButtonColor: '#6c757d',
    background: '#fff',
    backdrop: `
        rgba(0,0,123,0.4)
        url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%239C92AC' fill-opacity='0.4' fill-rule='evenodd'/%3E%3C/svg%3E")
        left top
        no-repeat
    `
});

// Démarrer le timer seulement si l'énigme n'est pas résolue
<?php if (!$enigme_resolue && $lieu['enigme_id']): ?>
    // Timer principal de l'énigme (12 minutes)
    // startTimer(720, 'timer'); // COMMENTÉ - À réactiver plus tard
    
    // Timer pour l'indice - NE PAS REDÉMARRER SI DÉJÀ ACTIF
    if (!INDICE_AVAILABLE && ENIGME_START_TIME && INDICE_START_TIME) {
        // Calculer le temps restant avant que l'indice soit disponible
        const now = Math.floor(Date.now() / 1000);
        const indiceRemaining = INDICE_START_TIME + 180 - now; // 3 minutes après le début de l'indice
        
        if (indiceRemaining > 0) {
            // Démarrer le timer de l'indice avec le temps restant
            startIndiceTimer(indiceRemaining);
        }
    }
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>