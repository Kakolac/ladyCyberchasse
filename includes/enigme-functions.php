<?php
/**
 * Inclusion des fonctions JavaScript centralisées pour les énigmes
 * Ce fichier élimine la duplication de code entre tous les templates
 */

// Vérifier que les variables nécessaires sont définies
if (!isset($lieu_slug) || !isset($_SESSION['team_name'])) {
    error_log('Variables manquantes pour l\'inclusion des fonctions d\'énigme');
    return;
}

// Récupérer les informations de l'équipe
$team_name = $_SESSION['team_name'];
$equipe_id = $equipe['id'] ?? null;
$lieu_id = $lieu['id'] ?? null;
$enigme_id = $lieu['enigme_id'] ?? null;
?>

<!-- Inclusion du fichier JavaScript centralisé -->
<script src="js/enigme-validation.js"></script>

<!-- Initialisation des variables globales -->
<script>
// Initialisation de l'énigme avec les variables PHP
initEnigme({
    lieu_slug: '<?php echo $lieu_slug; ?>',
    team_name: '<?php echo $team_name; ?>',
    lieu_id: <?php echo $lieu_id ?: 'null'; ?>,
    equipe_id: <?php echo $equipe_id ?: 'null'; ?>,
    enigme_id: <?php echo $enigme_id ?: 'null'; ?>
});
</script>
