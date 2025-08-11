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
            // ✅ CORRECTION : Utiliser la table 'equipes' au lieu de 'users'
            $stmt = $pdo->prepare("SELECT * FROM equipes WHERE nom = ?");
            $stmt->execute([$team_name]);
            $equipe = $stmt->fetch();
            
            if ($equipe && password_verify($password, $equipe['mot_de_passe'])) {
                // Connexion réussie
                $_SESSION['team_name'] = $equipe['nom'];
                $_SESSION['team_id'] = $equipe['id'];
                $_SESSION['team_color'] = $equipe['couleur'];
                
                // Log de connexion (optionnel)
                error_log("Connexion réussie pour l'équipe: " . $equipe['nom']);
                
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
                    <h2>🔐 Connexion - Cyberchasse</h2>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <h5>❌ Erreur de connexion</h5>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info">
                        <h6>📋 Équipes disponibles :</h6>
                        <ul class="mb-0">
                            <li><strong>Rouge :</strong> Egour2023#!</li>
                            <li><strong>Bleu :</strong> Uelb2023#!</li>
                            <li><strong>Vert :</strong> Trev2023#!</li>
                            <li><strong>Jaune :</strong> Enuaj2023#!</li>
                        </ul>
                    </div>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="team_name" class="form-label">Nom de l'équipe</label>
                            <select class="form-select" id="team_name" name="team_name" required>
                                <option value="">Sélectionner une équipe</option>
                                <option value="Rouge">Rouge</option>
                                <option value="Bleu">Bleu</option>
                                <option value="Vert">Vert</option>
                                <option value="Jaune">Jaune</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">🚀 Se connecter</button>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            Utilisez les identifiants fournis par l'organisateur
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
