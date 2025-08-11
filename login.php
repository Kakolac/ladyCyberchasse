<?php
session_start();

// Rediriger si déjà connecté
if (isset($_SESSION['team_name'])) {
    header('Location: scenario.php');
    exit();
}

// Traitement du formulaire de connexion
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/connexion.php';
    
    $team_name = trim($_POST['team_name'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($team_name) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE teamName = ?");
            $stmt->execute([$team_name]);
            $team = $stmt->fetch();
            
            if ($team && password_verify($password, $team['password'])) {
                // Connexion réussie
                $_SESSION['team_name'] = $team['teamName'];
                $_SESSION['team_id'] = $team['id'];
                $_SESSION['start_time'] = time();
                
                // Log de connexion (optionnel)
                error_log("Connexion réussie pour l'équipe: " . $team['teamName']);
                
                header('Location: scenario.php');
                exit();
            } else {
                $error = 'Nom d\'équipe ou mot de passe incorrect';
            }
        } catch(PDOException $e) {
            error_log("Erreur de base de données : " . $e->getMessage());
            $error = 'Erreur technique, veuillez réessayer';
        }
    }
}

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
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="team_name" class="form-label">Nom de l'équipe</label>
                            <input type="text" class="form-control" id="team_name" name="team_name" value="<?php echo htmlspecialchars($_POST['team_name'] ?? ''); ?>" required>
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
