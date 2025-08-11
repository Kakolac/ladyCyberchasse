<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: login.php');
    exit();
}

// Supprimer la vérification du timeout
// if (isset($_SESSION['start_time'])) {
//     $elapsed_time = time() - $_SESSION['start_time'];
//     if ($elapsed_time > 720) { // 12 minutes en secondes
//         header('Location: timeout.php');
//         exit();
//     }
// }

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Salutations chers cyberchasseurs!</h2>
                </div>
                <div class="card-body">
                    <p>Bienvenue dans l'Escape Game Cyberchasse ! Votre équipe est sur le point de commencer une aventure palpitante pour déjouer les pièges informatiques et collecter des informations secrètes. Assurez-vous de respecter les règles et de travailler ensemble pour réussir votre mission.</p>
                    <p>Vous avez 12 minutes pour chaque lieu, alors restez concentrés et efficaces. N'oubliez pas de valider votre réponse à chaque étape pour valider votre progression.<br>
                
                    Ce jeu se déroulant dans l'espace du lycée, votre comportement doit être exemplaire. <br>
            Soyez irréprochables et amusez-vous bien !</p>
                    <p>Bonne chance, et que la chasse commence !</p>
                    <a href="index.php" class="btn btn-primary"></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>