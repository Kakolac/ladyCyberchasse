<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}
include '../../includes/header.php';
?>

<div class='container mt-4'>
    <div class='card'>
        <div class='card-header'>
            <h2>🔍 Énigme - Accueil</h2>
        </div>
        <div class='card-body'>
            <p>Énigme en cours de développement pour ce lieu.</p>
            <a href='index.php' class='btn btn-secondary'>🏠 Retour au lieu</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>