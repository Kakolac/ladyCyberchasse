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
$equipe_id = $_SESSION['equipe_id'];

// Récupération du token actuel pour ce lieu
$stmt = $pdo->prepare("
    SELECT ct.*, l.nom as lieu_nom, l.slug as lieu_slug, l.description as lieu_description,
           p.nom as parcours_nom, p.description as parcours_description,
           e.id as enigme_id, e.type_enigme_id, e.donnees, 
           te.template, te.nom as type_nom,
           COALESCE(l.delai_indice, 6) as delai_indice
    FROM cyber_token ct
    JOIN cyber_lieux l ON ct.lieu_id = l.id
    JOIN cyber_parcours p ON ct.parcours_id = p.id
    LEFT JOIN enigmes e ON l.enigme_id = e.id 
    LEFT JOIN types_enigmes te ON e.type_enigme_id = te.id
    WHERE ct.equipe_id = ? AND l.slug = ? AND ct.parcours_id = ?
");

$stmt->execute([$equipe_id, $lieu_slug, $_SESSION['parcours_id']]);
$current = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current) {
    header('Location: login.php');
    exit();
}

// Vérification si l'énigme est déjà résolue
$enigme_resolue = ($current['statut'] === 'termine');

// VÉRIFICATION SIMPLE : QR Code obligatoire ou pas
$qr_code_obligatoire = ($current['qrcodeObligatoire'] == 1);

// Gestion du timing pour les indices - LOGIQUE SIMPLIFIÉE
$enigme_elapsed_time = 0;
$indice_available = false;
$remaining_time = 0;

if (!$enigme_resolue && $current['enigme_id']) {
    // Vérifier si c'est la première fois qu'on utilise ce token
    if (!$current['temps_debut']) {
        // Première fois - enregistrer le timestamp
        $stmt = $pdo->prepare("
            UPDATE cyber_token 
            SET temps_debut = NOW()
            WHERE equipe_id = ? AND lieu_id = ? AND parcours_id = ?
        ");
        $stmt->execute([$equipe_id, $current['lieu_id'], $_SESSION['parcours_id']]);
        
        // Récupérer le timestamp qu'on vient de créer
        $temps_debut = time();
    } else {
        // Pas la première fois - utiliser le timestamp existant
        $temps_debut = strtotime($current['temps_debut']);
    }
    
    // Calculer la disponibilité de l'indice basée sur temps_debut
    $enigme_elapsed_time = time() - $temps_debut;
    $delai_indice_secondes = $current['delai_indice'] * 60;
    $indice_available = ($enigme_elapsed_time >= $delai_indice_secondes);
    $remaining_time = max(0, $delai_indice_secondes - $enigme_elapsed_time);
}

// Variables supplémentaires nécessaires pour les templates
$lieu_slug_for_template = $lieu_slug;
$equipe_id_for_template = $equipe_id;
$lieu_id_for_template = $current['lieu_id'] ?? null;
$enigme_id_for_template = $current['enigme_id'] ?? null;

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
                    <h2>🔍 Énigme - <?php echo htmlspecialchars($current['lieu_nom']); ?></h2>
                    <?php if ($current['type_nom']): ?>
                        <small>Type : <?php echo htmlspecialchars($current['type_nom']); ?></small>
                    <?php endif; ?>
                </div>
                <div class='card-body'>
                    
                    <?php
                    // Récupérer le contexte depuis les données JSON de l'énigme
                    $contexte = "Résolvez cette énigme de cybersécurité pour progresser dans votre mission et débloquer le prochain lieu !";
                    if (!empty($current['donnees'])) {
                        $donnees_enigme = json_decode($current['donnees'], true);
                        if (json_last_error() === JSON_ERROR_NONE && isset($donnees_enigme['contexte'])) {
                            $contexte = $donnees_enigme['contexte'];
                        }
                    }
                    ?>

                    <?php if ($enigme_resolue): ?>
                        <!-- Énigme déjà résolue -->
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h4>🎉 Bravo !</h4>
                            <p>Vous avez déjà résolu l'énigme de ce lieu.</p>
                        </div>
                        
                        <div class="text-center">
                            <a href="lieux/<?php echo $lieu_slug; ?>/" class="btn btn-dark btn-lg">
                                <i class="fas fa-arrow-left"></i> Retour au lieu
                            </a>
                        </div>
                        
                    <?php elseif ($current['enigme_id']): ?>
                        <!-- Énigme à résoudre -->
                        <div class='alert alert-info'>
                            <h5>🎯 Contexte</h5>
                            <p><?php echo htmlspecialchars($contexte); ?></p>
                        </div>
                        
                        <?php
                        // Inclusion du template spécifique au type d'énigme
                        $template_path = "templates/enigmes/{$current['template']}.php";
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
        url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-5 3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 3 3zm12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%239C92AC' fill-opacity='0.4' fill-rule='evenodd'/%3E%3C/svg%3E")
        left top
        no-repeat
    `
});

// La gestion des timers est maintenant centralisée dans les templates d'énigmes
// via enigme-functions.php
</script>

<?php include 'includes/footer.php'; ?>