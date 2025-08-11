<?php
session_start();
require_once '../config/connexion.php';

// Vérification de l'authentification (pour l'instant, on laisse ouvert pour les tests)
// TODO: Ajouter une authentification admin sécurisée

// Récupération des équipes
$stmt = $pdo->query("SELECT * FROM equipes ORDER BY nom");
$equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des lieux
$stmt = $pdo->query("SELECT * FROM lieux ORDER BY ordre");
$lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des parcours existants
$stmt = $pdo->query("
    SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
    FROM parcours p
    JOIN equipes e ON p.equipe_id = e.id
    JOIN lieux l ON p.lieu_id = l.id
    ORDER BY p.equipe_id, p.ordre_visite
");
$parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_parcours':
                // Création d'un nouveau parcours
                $equipe_id = $_POST['equipe_id'];
                $lieu_id = $_POST['lieu_id'];
                $ordre = $_POST['ordre'];
                
                // Génération d'un token unique
                $token = bin2hex(random_bytes(16));
                
                $stmt = $pdo->prepare("
                    INSERT INTO parcours (equipe_id, lieu_id, ordre_visite, token_acces, statut)
                    VALUES (?, ?, ?, ?, 'en_attente')
                ");
                
                if ($stmt->execute([$equipe_id, $lieu_id, $ordre, $token])) {
                    $success_message = "Parcours créé avec succès !";
                    // Recharger les parcours
                    $stmt = $pdo->query("
                        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
                        FROM parcours p
                        JOIN equipes e ON p.equipe_id = e.id
                        JOIN lieux l ON p.lieu_id = l.id
                        ORDER BY p.equipe_id, p.ordre_visite
                    ");
                    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $error_message = "Erreur lors de la création du parcours";
                }
                break;
                
            case 'delete_parcours':
                $parcours_id = $_POST['parcours_id'];
                $stmt = $pdo->prepare("DELETE FROM parcours WHERE id = ?");
                if ($stmt->execute([$parcours_id])) {
                    $success_message = "Parcours supprimé avec succès !";
                    // Recharger les parcours
                    $stmt = $pdo->query("
                        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
                        FROM parcours p
                        JOIN equipes e ON p.equipe_id = e.id
                        JOIN lieux l ON p.lieu_id = l.id
                        ORDER BY p.equipe_id, p.ordre_visite
                    ");
                    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $error_message = "Erreur lors de la suppression";
                }
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des Parcours - Cyberchasse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .card-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
        }
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background: #f8fafc;
            border: none;
            font-weight: 600;
        }
        .token-cell {
            font-family: monospace;
            font-size: 0.8rem;
            background: #f1f5f9;
            padding: 0.5rem;
            border-radius: 5px;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-en_attente { background: #fef3c7; color: #92400e; }
        .status-en_cours { background: #dbeafe; color: #1e40af; }
        .status-termine { background: #d1fae5; color: #065f46; }
        .status-echec { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">�� Administration des Parcours</h1>
                    <p class="mb-0 mt-2">Gérez les parcours des équipes pour la cyberchasse</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="../index.php" class="btn btn-outline-light">← Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✅ <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ❌ <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Création d'un nouveau parcours -->
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">➕ Créer un nouveau parcours</h3>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <input type="hidden" name="action" value="create_parcours">
                    
                    <div class="col-md-4">
                        <label for="equipe_id" class="form-label">Équipe</label>
                        <select class="form-select" name="equipe_id" required>
                            <option value="">Sélectionner une équipe</option>
                            <?php foreach ($equipes as $equipe): ?>
                                <option value="<?php echo $equipe['id']; ?>">
                                    <?php echo htmlspecialchars($equipe['nom']); ?> (<?php echo htmlspecialchars($equipe['couleur']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="lieu_id" class="form-label">Lieu</label>
                        <select class="form-select" name="lieu_id" required>
                            <option value="">Sélectionner un lieu</option>
                            <?php foreach ($lieux as $lieu): ?>
                                <option value="<?php echo $lieu['id']; ?>">
                                    <?php echo htmlspecialchars($lieu['nom']); ?> (Ordre: <?php echo $lieu['ordre']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="ordre" class="form-label">Ordre</label>
                        <input type="number" class="form-control" name="ordre" min="1" required>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Créer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Affichage des parcours existants -->
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">�� Parcours existants</h3>
            </div>
            <div class="card-body">
                <?php if (empty($parcours)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">Aucun parcours créé pour le moment.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Équipe</th>
                                    <th>Lieu</th>
                                    <th>Ordre</th>
                                    <th>Token d'accès</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($parcours as $parcour): ?>
                                    <tr>
                                        <td>
                                            <span class="badge" style="background-color: <?php echo $parcour['equipe_nom'] === 'Rouge' ? '#ef4444' : ($parcour['equipe_nom'] === 'Bleu' ? '#3b82f6' : ($parcour['equipe_nom'] === 'Vert' ? '#10b981' : '#f59e0b')); ?>; color: white;">
                                                <?php echo htmlspecialchars($parcour['equipe_nom']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($parcour['lieu_nom']); ?></strong>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($parcour['lieu_slug']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo $parcour['ordre_visite']; ?></span>
                                        </td>
                                        <td>
                                            <div class="token-cell"><?php echo htmlspecialchars($parcour['token_acces']); ?></div>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $parcour['statut']; ?>">
                                                <?php 
                                                switch($parcour['statut']) {
                                                    case 'en_attente': echo '⏳ En attente'; break;
                                                    case 'en_cours': echo '▶️ En cours'; break;
                                                    case 'termine': echo '✅ Terminé'; break;
                                                    case 'echec': echo '❌ Échec'; break;
                                                    default: echo $parcour['statut'];
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce parcours ?')">
                                                <input type="hidden" name="action" value="delete_parcours">
                                                <input type="hidden" name="parcours_id" value="<?php echo $parcour['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">��️ Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">�� Statistiques</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-primary"><?php echo count($equipes); ?></h4>
                            <p class="text-muted mb-0">Équipes</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-success"><?php echo count($lieux); ?></h4>
                            <p class="text-muted mb-0">Lieux</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-info"><?php echo count($parcours); ?></h4>
                            <p class="text-muted mb-0">Parcours créés</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-warning"><?php echo count(array_filter($parcours, function($p) { return $p['statut'] === 'en_attente'; })); ?></h4>
                            <p class="text-muted mb-0">En attente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
