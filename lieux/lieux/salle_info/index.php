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
            <h2>🏫 Salle info</h2>
        </div>
        <div class='card-body'>
            <p>Page en cours de développement pour le lieu : <strong>Salle informatique - Cybersécurité</strong></p>
            <a href='../accueil/' class='btn btn-primary'> Retour à l'accueil</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>