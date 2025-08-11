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
        <link rel="stylesheet" href="../styles/style.css">
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
                            <div class="alert alert-warning">
                                <h5>🔐 Vous devez être connecté</h5>
                                <p>Pour accéder aux lieux, vous devez d'abord vous connecter avec votre équipe.</p>
                            </div>
                            
                            <div class="mt-4">
                                <a href="../login.php" class="btn btn-primary">🔐 Se connecter</a>
                                <a href="../accueil/" class="btn btn-secondary"> Retour à l'accueil</a>
                            </div>
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

// Fonction pour logger l'activité
function logActivity($pdo, $equipe_id, $lieu_id, $action, $details = '') {
    $stmt = $pdo->prepare("
        INSERT INTO logs_activite (equipe_id, lieu_id, action, details, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $equipe_id,
        $lieu_id,
        $action,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ]);
}

// Fonction pour créer une session de jeu
function createGameSession($pdo, $equipe_id, $lieu_id, $token) {
    $session_id = bin2hex(random_bytes(16));
    $token_validation = bin2hex(random_bytes(16));
    
    $stmt = $pdo->prepare("
        INSERT INTO sessions_jeu (equipe_id, lieu_id, session_id, token_validation, statut, temps_restant)
        VALUES (?, ?, ?, ?, 'active', 0)
    ");
    
    if ($stmt->execute([$equipe_id, $lieu_id, $session_id, $token_validation])) {
        return $token_validation;
    }
    return false;
}

try {
    // 1. Validation du token d'accès AVEC VÉRIFICATION DE L'ÉQUIPE
    $stmt = $pdo->prepare("
        SELECT p.*, e.nom as equipe_nom, e.couleur as equipe_couleur, 
               l.nom as lieu_nom, l.slug as lieu_slug, l.temps_limite, l.ordre
        FROM parcours p
        JOIN equipes e ON p.equipe_id = e.id
        JOIN lieux l ON p.lieu_id = l.id
        WHERE p.token_acces = ? AND l.slug = ? AND p.statut IN ('en_attente', 'en_cours')
    ");
    
    $stmt->execute([$token, $lieu]);
    $parcours = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$parcours) {
        // Token invalide ou lieu non autorisé
        logActivity($pdo, null, null, 'acces_refuse', "Token invalide: $token pour lieu: $lieu");
        
        http_response_code(403);
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Accès Refusé - Cyberchasse</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="../styles/style.css">
        </head>
        <body>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h3> Accès Refusé</h3>
                            </div>
                            <div class="card-body text-center">
                                <div class="alert alert-danger">
                                    <h5>❌ Token invalide ou lieu non autorisé</h5>
                                    <p>Le token fourni n'est pas valide pour accéder à ce lieu.</p>
                                    <p><strong>Lieu demandé :</strong> <?php echo htmlspecialchars($lieu); ?></p>
                                    <p><strong>Token :</strong> <?php echo htmlspecialchars(substr($token, 0, 8)) . '...'; ?></p>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="../accueil/" class="btn btn-primary"> Retour à l'accueil</a>
                                    <a href="../login.php" class="btn btn-secondary">🔐 Se connecter</a>
                                </div>
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
    
    // 🔍 DEBUG : Log des informations de session et de parcours
    error_log("DEBUG ACCESS: Token=$token, Lieu=$lieu");
    error_log("DEBUG ACCESS: Session team_name=" . ($_SESSION['team_name'] ?? 'NULL'));
    error_log("DEBUG ACCESS: Parcours equipe_nom=" . ($parcours['equipe_nom'] ?? 'NULL'));
    error_log("DEBUG ACCESS: Comparaison: '{$parcours['equipe_nom']}' !== '{$_SESSION['team_name']}'");
    
    // 🔒 NOUVEAU : VÉRIFICATION STRICTE ÉQUIPE-TOKEN (OBLIGATOIRE)
    if ($parcours['equipe_nom'] !== $_SESSION['team_name']) {
        // Tentative d'utilisation d'un token d'une autre équipe
        error_log("SECURITY BREACH: Équipe {$_SESSION['team_name']} tente d'utiliser le token de l'équipe {$parcours['equipe_nom']}");
        
        logActivity($pdo, $_SESSION['equipe_id'] ?? null, null, 'tentative_fraude', "Équipe {$_SESSION['team_name']} tente d'utiliser le token de l'équipe {$parcours['equipe_nom']}");
        
        http_response_code(403);
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Accès Interdit - Cyberchasse</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="../styles/style.css">
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
                                <div class="alert alert-danger">
                                    <h5>❌ Tentative d'accès non autorisée</h5>
                                    <p>Ce token appartient à l'équipe <strong><?php echo htmlspecialchars($parcours['equipe_nom']); ?></strong>.</p>
                                    <p>Vous êtes connecté avec l'équipe <strong><?php echo htmlspecialchars($_SESSION['team_name']); ?></strong>.</p>
                                    <p><strong>Chaque équipe ne peut utiliser que ses propres tokens !</strong></p>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="../accueil/" class="btn btn-primary"> Retour à l'accueil</a>
                                    <a href="../admin/parcours.php" class="btn btn-info">📋 Voir mon parcours</a>
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted">
                                        Cette tentative a été enregistrée dans les logs de sécurité.
                                    </small>
                                </div>
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
    
    // 2. Vérification de l'ordre de visite (MODIFIÉ)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as lieux_termines
        FROM parcours 
        WHERE equipe_id = ? AND statut = 'termine'
    ");
    $stmt->execute([$parcours['equipe_id']]);
    $lieux_termines = $stmt->fetchColumn();
    
    // Si le lieu actuel est "en_cours", permettre l'accès
    if ($parcours['statut'] === 'en_cours') {
        // Reprise de la session existante
        echo "<div class='alert alert-info'>🔄 Reprise de la session en cours...</div>";
    } else {
        // Vérifier l'ordre normal pour les nouveaux accès
        if ($lieux_termines < ($parcours['ordre_visite'] - 1)) {
            // L'équipe n'a pas encore visité les lieux précédents
            logActivity($pdo, $parcours['equipe_id'], $parcours['lieu_id'], 'acces_premature', "Ordre de visite non respecté");
            
            ?>
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Ordre de Visite - Cyberchasse</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="../styles/style.css">
            </head>
            <body>
                <div class="container mt-5">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h3>⚠️ Ordre de Visite</h3>
                                </div>
                                <div class="card-body text-center">
                                    <div class="alert alert-warning">
                                        <h5> Vous devez d'abord visiter les lieux précédents</h5>
                                        <p>Ce lieu est l'étape <strong><?php echo $parcours['ordre_visite']; ?></strong> de votre parcours.</p>
                                        <p>Vous avez visité <strong><?php echo $lieux_termines; ?></strong> lieu(x) sur <?php echo $parcours['ordre_visite']; ?>.</p>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <a href="../accueil/" class="btn btn-primary"> Retour à l'accueil</a>
                                        <a href="../admin/parcours.php" class="btn btn-info">📋 Voir mon parcours</a>
                                    </div>
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
    }
    
    // 3. Mise à jour du statut du parcours (seulement si pas déjà "en_cours")
    if ($parcours['statut'] !== 'en_cours') {
        $stmt = $pdo->prepare("
            UPDATE parcours 
            SET statut = 'en_cours', temps_debut = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->execute([$parcours['id']]);
    }
    
    // 4. Création ou reprise de la session de jeu
    $token_validation = createGameSession($pdo, $parcours['equipe_id'], $parcours['lieu_id'], $token);
    
    // 5. Log de l'accès réussi
    logActivity($pdo, $parcours['equipe_id'], $parcours['lieu_id'], 'acces_reussi', "Accès validé via token");
    
    // 6. Mise à jour de la session utilisateur
    $_SESSION['equipe_id'] = $parcours['equipe_id'];
    $_SESSION['equipe_couleur'] = $parcours['equipe_couleur'];
    $_SESSION['lieu_actuel'] = $parcours['lieu_slug'];
    $_SESSION['token_validation'] = $token_validation;
    $_SESSION['temps_limite'] = $parcours['temps_limite'] * 60; // Conversion en secondes
    $_SESSION['debut_session'] = time();
    
    // 7. Redirection vers le lieu avec le token de validation
    $redirect_url = "./$lieu/?token_validation=" . urlencode($token_validation);
    header("Location: $redirect_url");
    exit();
    
} catch (PDOException $e) {
    // Erreur de base de données
    error_log("Erreur de validation d'accès : " . $e->getMessage());
    
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Erreur - Cyberchasse</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../styles/style.css">
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
                            <div class="alert alert-danger">
                                <h5>❌ Une erreur est survenue</h5>
                                <p>Impossible de valider votre accès pour le moment.</p>
                                <p><small class="text-muted">Erreur technique : <?php echo htmlspecialchars($e->getMessage()); ?></small></p>
                            </div>
                            
                            <div class="mt-4">
                                <a href="../accueil/" class="btn btn-primary"> Retour à l'accueil</a>
                                <a href="../admin/parcours.php" class="btn btn-secondary">📋 Voir mon parcours</a>
                            </div>
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
