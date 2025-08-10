<?php
session_start();
include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="alert alert-warning">
        <h2>Temps écoulé !</h2>
        <p>Votre temps de 2 heures est écoulé.</p>
        <p>Score final : <?php echo isset($_SESSION['score']) ? $_SESSION['score'] : 0; ?> points</p>
        <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
    </div>
</div>

<?php 
session_destroy();
include 'includes/footer.php'; 
?>