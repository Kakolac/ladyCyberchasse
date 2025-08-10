<?php
session_start();
include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Connexion</h2>
                </div>
                <div class="card-body">
                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            switch($_GET['error']) {
                                case '1':
                                    echo "Nom d'équipe ou mot de passe incorrect";
                                    break;
                                default:
                                    echo "Une erreur s'est produite";
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="verify.php" method="POST">
                        <div class="mb-3">
                            <label for="team_name" class="form-label">Nom de l'équipe</label>
                            <input type="text" class="form-control" id="team_name" name="team_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>