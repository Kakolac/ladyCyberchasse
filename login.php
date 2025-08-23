<?php
session_start();

// Rediriger si déjà connecté
if (isset($_SESSION['team_name'])) {
    header('Location: scenario.php');
    exit();
}

// Traitement du formulaire de connexion
$error = '';
$equipes_disponibles = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/connexion.php';
    
    $team_name = trim($_POST['team_name'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($team_name) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        try {
            // ✅ CORRECTION : Utiliser la nouvelle table 'cyber_equipes'
            $stmt = $pdo->prepare("
                SELECT * FROM cyber_equipes 
                WHERE nom = ? AND statut = 'active'
            ");
            $stmt->execute([$team_name]);
            $equipe = $stmt->fetch();
            
            if ($equipe && password_verify($password, $equipe['mot_de_passe'])) {
                // Vérifier que l'équipe a un parcours assigné
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) as nb_parcours
                    FROM cyber_equipes_parcours 
                    WHERE equipe_id = ? AND statut = 'en_cours'
                ");
                $stmt->execute([$equipe['id']]);
                $parcours_count = $stmt->fetchColumn();
                
                if ($parcours_count > 0) {
                    // Connexion réussie
                    $_SESSION['team_name'] = $equipe['nom'];
                    $_SESSION['equipe_id'] = $equipe['id'];
                    $_SESSION['equipe_couleur'] = $equipe['couleur'];
                    
                    // Récupérer les informations du parcours actif
                    $stmt = $pdo->prepare("
                        SELECT ep.parcours_id, p.nom as parcours_nom, p.description as parcours_description
                        FROM cyber_equipes_parcours ep
                        JOIN cyber_parcours p ON ep.parcours_id = p.id
                        WHERE ep.equipe_id = ? AND ep.statut = 'en_cours'
                        LIMIT 1
                    ");
                    $stmt->execute([$equipe['id']]);
                    $parcours_info = $stmt->fetch();
                    
                    if ($parcours_info) {
                        $_SESSION['parcours_id'] = $parcours_info['parcours_id'];
                        $_SESSION['parcours_nom'] = $parcours_info['parcours_nom'];
                        $_SESSION['parcours_description'] = $parcours_info['parcours_description'];
                    }
                    
                    // Log de connexion
                    error_log("Connexion réussie pour l'équipe: " . $equipe['nom'] . " (ID: " . $equipe['id'] . ")");
                    
                    header('Location: scenario.php');
                    exit();
                } else {
                    $error = 'Aucun parcours actif assigné à cette équipe. Contactez l\'administrateur.';
                }
            } else {
                if ($equipe && $equipe['statut'] !== 'active') {
                    $error = 'Cette équipe est désactivée. Contactez l\'administrateur.';
                } else {
                    $error = 'Nom d\'équipe ou mot de passe incorrect';
                }
            }
        } catch(PDOException $e) {
            error_log("Erreur de base de données lors de la connexion : " . $e->getMessage());
            $error = 'Erreur technique, veuillez réessayer';
        }
    }
}

// Récupération des équipes disponibles pour l'affichage
try {
    require_once 'config/connexion.php';
    
    $stmt = $pdo->query("
        SELECT e.nom, e.couleur, e.statut,
               CASE 
                   WHEN ep.parcours_id IS NOT NULL THEN 'Parcours assigné'
                   ELSE 'Aucun parcours'
               END as statut_parcours
        FROM cyber_equipes e
        LEFT JOIN cyber_equipes_parcours ep ON e.id = ep.equipe_id AND ep.statut = 'en_cours'
        WHERE e.statut = 'active'
        ORDER BY e.nom
    ");
    $equipes_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    error_log("Erreur lors de la récupération des équipes : " . $e->getMessage());
    $equipes_disponibles = [];
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
                        <?php if (!empty($equipes_disponibles)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Équipe</th>
                                            <th>Statut</th>
                                            <th>Parcours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($equipes_disponibles as $equipe): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge" style="background-color: <?php echo htmlspecialchars($equipe['couleur']); ?>; color: white;">
                                                        <?php echo htmlspecialchars($equipe['nom']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $equipe['statut'] === 'active' ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst($equipe['statut']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo strpos($equipe['statut_parcours'], 'assigné') !== false ? 'info' : 'warning'; ?>">
                                                        <?php echo htmlspecialchars($equipe['statut_parcours']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Aucune équipe disponible pour le moment.</p>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="team_name" class="form-label">Nom de l'équipe</label>
                            <select class="form-select" id="team_name" name="team_name" required>
                                <option value="">Sélectionner une équipe</option>
                                <?php foreach ($equipes_disponibles as $equipe): ?>
                                    <option value="<?php echo htmlspecialchars($equipe['nom']); ?>">
                                        <?php echo htmlspecialchars($equipe['nom']); ?>
                                        <?php if (strpos($equipe['statut_parcours'], 'assigné') !== false): ?>
                                            ✅
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
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
                    
                    <?php if (!empty($equipes_disponibles)): ?>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Les équipes avec ✅ ont un parcours actif assigné.
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
