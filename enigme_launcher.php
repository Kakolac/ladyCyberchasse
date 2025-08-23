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
    SELECT l.*, e.id as enigme_id, e.type_enigme_id, e.donnees, te.template, te.nom as type_nom,
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

// Gestion du timing pour les indices - NOUVELLE LOGIQUE BDD
$enigme_start_time = null;
$indice_start_time = null;
$indice_available = false;
$enigme_elapsed_time = 0;
$indice_elapsed_time = 0;

if (!$enigme_resolue && $lieu['enigme_id']) {
    // Récupérer ou créer les timestamps depuis la BDD
    $stmt = $pdo->prepare("
        SELECT enigme_start_time, indice_start_time, statut 
        FROM parcours 
        WHERE equipe_id = ? AND lieu_id = ?
    ");
    $stmt->execute([$equipe['id'], $lieu['id']]);
    $parcours_timing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$parcours_timing || !$parcours_timing['enigme_start_time']) {
        // Première fois - créer l'enregistrement avec timestamps
        $enigme_start_time = time();
        $indice_start_time = $enigme_start_time + ($lieu['delai_indice'] * 60);
        
        $stmt = $pdo->prepare("
            INSERT INTO parcours (equipe_id, lieu_id, enigme_start_time, indice_start_time, statut) 
            VALUES (?, ?, FROM_UNIXTIME(?), FROM_UNIXTIME(?), 'en_cours')
            ON DUPLICATE KEY UPDATE 
            enigme_start_time = VALUES(enigme_start_time),
            indice_start_time = VALUES(indice_start_time)
        ");
        $stmt->execute([$equipe['id'], $lieu['id'], $enigme_start_time, $indice_start_time]);
        
        echo "<!-- Debug: Nouveau parcours créé avec timestamps -->";
    } else {
        // Récupérer les timestamps existants
        $enigme_start_time = strtotime($parcours_timing['enigme_start_time']);
        $indice_start_time = strtotime($parcours_timing['indice_start_time']);
        
        // Vérifier si le timer n'est pas trop ancien (plus de 24h)
        $max_timer_age = 24 * 60 * 60; // 24 heures
        if ((time() - $enigme_start_time) > $max_timer_age) {
            // Timer trop ancien, le réinitialiser
            $enigme_start_time = time();
            $indice_start_time = $enigme_start_time + ($lieu['delai_indice'] * 60);
            
            $stmt = $pdo->prepare("
                UPDATE parcours 
                SET enigme_start_time = FROM_UNIXTIME(?), 
                    indice_start_time = FROM_UNIXTIME(?)
                WHERE equipe_id = ? AND lieu_id = ?
            ");
            $stmt->execute([$enigme_start_time, $indice_start_time, $equipe['id'], $lieu['id']]);
            
            echo "<!-- Debug: Timer réinitialisé car trop ancien -->";
        }
    }
    
    // Calculer la disponibilité de l'indice
    $enigme_elapsed_time = time() - $enigme_start_time;
    $delai_indice_secondes = $lieu['delai_indice'] * 60;
    $indice_available = ($enigme_elapsed_time >= $delai_indice_secondes);
    $remaining_time = max(0, $delai_indice_secondes - $enigme_elapsed_time);
    
    // Calculer le temps écoulé depuis le début de l'indice
    $indice_elapsed_time = time() - $indice_start_time;
    
    // Debug
    error_log("TIMING BDD - Enigme start: " . date('H:i:s', $enigme_start_time));
    error_log("TIMING BDD - Indice start: " . date('H:i:s', $indice_start_time));
    error_log("TIMING BDD - Indice available: " . ($indice_available ? 'true' : 'false'));
    error_log("TIMING BDD - Remaining time: " . $remaining_time . "s");
}

// Passer les deux timers au template
$enigme_elapsed_time = time() - $enigme_start_time;
$indice_elapsed_time = time() - $indice_start_time;
    
// CENTRALISER LA GESTION DES INDICES - FORCER TOUJOURS INDISPONIBLE
$indice_available = false;
$remaining_time = $delai_indice_secondes; // Garder le délai pour l'affichage
    
// Debug final pour vérifier
error_log("FINAL DEBUG - Indice available: " . ($indice_available ? 'true' : 'false'));
error_log("FINAL DEBUG - Enigme elapsed: " . $enigme_elapsed_time);
error_log("FINAL DEBUG - Indice start time: " . date('H:i:s', $indice_start_time));

// Définir des valeurs par défaut pour la compatibilité
$enigme_session_key = "enigme_start_{$lieu['id']}_{$equipe['id']}";
$indice_session_key = "indice_start_{$lieu['id']}_{$equipe['id']}";

// CRÉER UN TABLEAU DE VARIABLES À PASSER AUX TEMPLATES
$timing_vars = [
    'enigme_start_time' => $enigme_start_time,
    'indice_start_time' => $indice_start_time,
    'enigme_elapsed_time' => $enigme_elapsed_time,
    'indice_elapsed_time' => $indice_elapsed_time,
    'indice_available' => $indice_available,
    'remaining_time' => $remaining_time,
    'delai_indice_secondes' => $delai_indice_secondes,
    'enigme_session_key' => $enigme_session_key,
    'indice_session_key' => $indice_session_key
];

// Extraire les variables pour les rendre disponibles dans le scope global
extract($timing_vars);

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
                        <?php
                        // Récupérer le contexte depuis les données JSON de l'énigme
                        $contexte = "Résolvez cette énigme de cybersécurité pour progresser dans votre mission et débloquer le prochain lieu !";
                        if (!empty($lieu['donnees'])) {
                            $donnees_enigme = json_decode($lieu['donnees'], true);
                            if (json_last_error() === JSON_ERROR_NONE && isset($donnees_enigme['contexte'])) {
                                $contexte = $donnees_enigme['contexte'];
                            }
                        }
                        ?>
                        <div class='alert alert-info'>
                            <h5>🎯 Contexte</h5>
                            <p><?php echo htmlspecialchars($contexte); ?></p>
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
// Configuration globale de SweetAlert2 pour personnaliser l'apparence
Swal.mixin({
    confirmButtonColor: '#343a40',
    cancelButtonColor: '#6c757d',
    background: '#fff',
    backdrop: `
        rgba(0,0,123,0.4)
        url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-5 3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%239C92AC' fill-opacity='0.4' fill-rule='evenodd'/%3E%3C/svg%3E")
        left top
        no-repeat
    `
});

// La gestion des timers est maintenant centralisée dans les templates d'énigmes
// via enigme-functions.php
</script>

<?php include 'includes/footer.php'; ?>