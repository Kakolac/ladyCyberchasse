<?php
session_start();
require_once '../config/connexion.php';

// Récupération des paramètres
$token = $_GET['token'] ?? '';
$lieu = $_GET['lieu'] ?? '';

// Vérification des paramètres requis
if (empty($token) || empty($lieu)) {
    http_response_code(400);
    die('Paramètres manquants : token et lieu requis');
}

// Vérification de la session utilisateur
if (!isset($_SESSION['team_name'])) {
    http_response_code(401);
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Non Connecté - Cyberchasse</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h3>⚠️ Non Connecté</h3>
                        </div>
                        <div class="card-body text-center">
                            <p>Vous devez être connecté pour accéder aux lieux.</p>
                            <a href="../login.php" class="btn btn-primary">🔐 Se connecter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

try {
    // 1. Validation du token d'accès
    $stmt = $pdo->prepare("
        SELECT ct.*, e.nom as equipe_nom, l.slug as lieu_slug
        FROM cyber_token ct
        JOIN cyber_equipes e ON ct.equipe_id = e.id
        JOIN cyber_lieux l ON ct.lieu_id = l.id
        WHERE ct.token_acces = ? AND l.slug = ?
    ");
    
    $stmt->execute([$token, $lieu]);
    $token_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$token_data) {
        http_response_code(403);
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Accès Refusé - Cyberchasse</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h3>❌ Accès Refusé</h3>
                            </div>
                            <div class="card-body text-center">
                                <p>Token invalide ou lieu non autorisé.</p>
                                <a href="../accueil/" class="btn btn-primary"> Retour à l'accueil</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
    
    // 2. Vérification que l'équipe connectée correspond au token
    if ($token_data['equipe_nom'] !== $_SESSION['team_name']) {
        http_response_code(403);
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Accès Interdit - Cyberchasse</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h3> Accès Interdit</h3>
                            </div>
                            <div class="card-body text-center">
                                <p>Ce token appartient à l'équipe <strong><?php echo htmlspecialchars($token_data['equipe_nom']); ?></strong>.</p>
                                <p>Vous êtes connecté avec l'équipe <strong><?php echo htmlspecialchars($_SESSION['team_name']); ?></strong>.</p>
                                <a href="../accueil/" class="btn btn-primary"> Retour à l'accueil</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
    
    // 3. Mise à jour du statut du token
    $stmt = $pdo->prepare("UPDATE cyber_token SET statut = 'en_cours' WHERE id = ?");
    $stmt->execute([$token_data['id']]);
    
    // 4. Redirection vers le lieu
    $redirect_url = "./$lieu/";
    header("Location: $redirect_url");
    exit();
    
} catch (PDOException $e) {
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Erreur - Cyberchasse</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h3> Erreur Système</h3>
                        </div>
                        <div class="card-body text-center">
                            <p>Une erreur est survenue. Veuillez réessayer.</p>
                            <a href="../accueil/" class="btn btn-primary"> Retour à l'accueil</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>
