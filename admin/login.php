<?php
session_start();
require_once '../config/connexion.php';

// Si déjà connecté en tant qu'admin, rediriger vers admin.php
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header('Location: admin2.php');
    exit();
}

$error = '';

if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Vérifier les identifiants admin (à configurer selon vos besoins)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: admin2.php');
        exit();
    } else {
        $error = 'Identifiants incorrects';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur - Cyberchasse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex;
            align-items: center;
        }
        .login-card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.3); 
        }
        .card-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-header text-center py-4">
                        <h2><i class="fas fa-shield-alt"></i> Administration</h2>
                        <p class="mb-0">Connexion sécurisée</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fas fa-sign-in-alt"></i> Se connecter
                                </button>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <a href="../index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Retour à l'accueil
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3 text-white">
                    <small>
                        <strong>Identifiants par défaut :</strong><br>
                        Utilisateur: <code>admin</code><br>
                        Mot de passe: <code>admin123</code>
                    </small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
