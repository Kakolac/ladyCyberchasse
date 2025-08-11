<?php
session_start();
require_once '../config/connexion.php';
require_once '../config/env.php';

// Récupération de l'URL du site depuis l'environnement
$siteUrl = env('URL_SITE', 'http://127.0.0.1:8888');

// Vérification de l'authentification (pour l'instant, on laisse ouvert pour les tests)
// TODO: Ajouter une authentification admin sécurisée

// Récupération des parcours avec toutes les informations
$stmt = $pdo->query("
    SELECT p.*, e.nom as equipe_nom, e.couleur as equipe_couleur, l.nom as lieu_nom, l.slug as lieu_slug
    FROM parcours p
    JOIN equipes e ON p.equipe_id = e.id
    JOIN lieux l ON p.lieu_id = l.id
    ORDER BY p.equipe_id, p.ordre_visite
");
$parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des équipes et lieux pour les filtres
$stmt = $pdo->query("SELECT * FROM equipes ORDER BY nom");
$equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM lieux ORDER BY ordre");
$lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'regenerate_token':
                $parcours_id = $_POST['parcours_id'];
                $new_token = bin2hex(random_bytes(16));
                
                $stmt = $pdo->prepare("UPDATE parcours SET token_acces = ? WHERE id = ?");
                if ($stmt->execute([$new_token, $parcours_id])) {
                    $success_message = "Token régénéré avec succès !";
                    // Recharger les parcours
                    $stmt = $pdo->query("
                        SELECT p.*, e.nom as equipe_nom, e.couleur as equipe_couleur, l.nom as lieu_nom, l.slug as lieu_slug
                        FROM parcours p
                        JOIN equipes e ON p.equipe_id = e.id
                        JOIN lieux l ON p.lieu_id = l.id
                        ORDER BY p.equipe_id, p.ordre_visite
                    ");
                    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $error_message = "Erreur lors de la régénération du token";
                }
                break;
        }
    }
}

// Filtres
$equipe_filter = $_GET['equipe'] ?? '';
$lieu_filter = $_GET['lieu'] ?? '';

if ($equipe_filter || $lieu_filter) {
    $where_conditions = [];
    $params = [];
    
    if ($equipe_filter) {
        $where_conditions[] = "e.id = ?";
        $params[] = $equipe_filter;
    }
    
    if ($lieu_filter) {
        $where_conditions[] = "l.id = ?";
        $params[] = $lieu_filter;
    }
    
    $where_clause = "WHERE " . implode(" AND ", $where_conditions);
    
    $stmt = $pdo->prepare("
        SELECT p.*, e.nom as equipe_nom, e.couleur as equipe_couleur, l.nom as lieu_nom, l.slug as lieu_slug
        FROM parcours p
        JOIN equipes e ON p.equipe_id = e.id
        JOIN lieux l ON p.lieu_id = l.id
        $where_clause
        ORDER BY p.equipe_id, p.ordre_visite
    ");
    $stmt->execute($params);
    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Génération des QR Codes - Cyberchasse</title>
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
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
        }
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
        }
        .qr-container {
            text-align: center;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 1rem 0;
        }
        .qr-code {
            margin: 1rem auto;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            display: inline-block;
        }
        .qr-code img {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            max-width: 100%;
            height: auto;
        }
        .token-info {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            font-family: monospace;
            font-size: 0.9rem;
        }
        .url-info {
            background: #e0f2fe;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            font-family: monospace;
            font-size: 0.9rem;
            word-break: break-all;
        }
        .equipe-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            color: white;
        }
        .filters-section {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        .print-section {
            background: #e0f2fe;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            text-align: center;
        }
        .qr-actions {
            margin-top: 1rem;
        }
        .qr-actions .btn {
            margin: 0.25rem;
        }
        @media print {
            .no-print { display: none !important; }
            .qr-container { page-break-inside: avoid; }
            .qr-code img { max-width: 200px !important; }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">🎯 Génération des QR Codes</h1>
                    <p class="mb-0 mt-2">Générez et imprimez les QR codes pour chaque parcours</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="parcours.php" class="btn btn-outline-light">← Retour aux parcours</a>
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

        <!-- Filtres -->
        <div class="filters-section">
            <h4>🔍 Filtres</h4>
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="equipe" class="form-label">Filtrer par équipe</label>
                    <select class="form-select" name="equipe">
                        <option value="">Toutes les équipes</option>
                        <?php foreach ($equipes as $equipe): ?>
                            <option value="<?php echo $equipe['id']; ?>" <?php echo $equipe_filter == $equipe['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($equipe['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="lieu" class="form-label">Filtrer par lieu</label>
                    <select class="form-select" name="lieu">
                        <option value="">Tous les lieux</option>
                        <?php foreach ($lieux as $lieu): ?>
                            <option value="<?php echo $lieu['id']; ?>" <?php echo $lieu_filter == $lieu['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lieu['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                    <a href="generate_qr.php" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>

        <!-- Bouton d'impression -->
        <div class="print-section no-print">
            <h5>🖨️ Impression</h5>
            <p class="mb-2">Imprimez tous les QR codes affichés ou utilisez Ctrl+P</p>
            <button onclick="window.print()" class="btn btn-primary">Imprimer tous les QR codes</button>
        </div>

        <?php if (empty($parcours)): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <h4 class="text-muted">Aucun parcours trouvé</h4>
                    <p class="text-muted">Créez d'abord des parcours dans la section "Administration des Parcours"</p>
                    <a href="parcours.php" class="btn btn-primary">Créer des parcours</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Affichage des QR codes -->
            <div class="row">
                <?php foreach ($parcours as $parcour): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="qr-container">
                            <div class="equipe-badge" style="background-color: <?php echo $parcour['equipe_couleur']; ?>;">
                                <?php echo htmlspecialchars($parcour['equipe_nom']); ?>
                            </div>
                            
                            <h5 class="mt-3"><?php echo htmlspecialchars($parcour['lieu_nom']); ?></h5>
                            <p class="text-muted mb-2">Ordre: <?php echo $parcour['ordre_visite']; ?></p>
                            
                            <div class="qr-code">
                                <img src="generate_qr_image.php?token=<?php echo urlencode($parcour['token_acces']); ?>&lieu=<?php echo urlencode($parcour['lieu_slug']); ?>&equipe=<?php echo urlencode($parcour['equipe_nom']); ?>&lieu_nom=<?php echo urlencode($parcour['lieu_nom']); ?>&ordre=<?php echo $parcour['ordre_visite']; ?>" 
                                     alt="QR Code pour <?php echo htmlspecialchars($parcour['equipe_nom']); ?> - <?php echo htmlspecialchars($parcour['lieu_nom']); ?>"
                                     style="width: 200px; height: 200px;">
                            </div>
                            
                            <div class="token-info">
                                <strong>Token:</strong><br>
                                <?php echo htmlspecialchars($parcour['token_acces']); ?>
                            </div>
                            
                            <div class="url-info">
                                <strong>URL générée:</strong><br>
                                <code><?php echo $siteUrl; ?>/lieux/access.php?token=<?php echo $parcour['token_acces']; ?>&lieu=<?php echo $parcour['lieu_slug']; ?></code>
                            </div>
                            
                            <div class="qr-actions no-print">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="regenerate_token">
                                    <input type="hidden" name="parcours_id" value="<?php echo $parcour['id']; ?>">
                                    <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Régénérer ce token ?')">
                                        🔄 Régénérer
                                    </button>
                                </form>
                                
                                <a href="generate_qr_image.php?token=<?php echo urlencode($parcour['token_acces']); ?>&lieu=<?php echo urlencode($parcour['lieu_slug']); ?>&equipe=<?php echo urlencode($parcour['equipe_nom']); ?>&lieu_nom=<?php echo urlencode($parcour['lieu_nom']); ?>&ordre=<?php echo $parcour['ordre_visite']; ?>&download=1" 
                                   class="btn btn-success btn-sm">
                                    💾 Télécharger
                                </a>
                                
                                <button class="btn btn-outline-primary btn-sm" onclick="printSingleQR(<?php echo $parcour['id']; ?>)">
                                    🖨️ Imprimer
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction d'impression individuelle
        function printSingleQR(parcourId) {
            const qrContainer = document.querySelector(`#qr-${parcourId}`).closest('.qr-container');
            const printWindow = window.open('', '_blank');
            
            printWindow.document.write(`
                <html>
                    <head>
                        <title>QR Code - Parcours ${parcourId}</title>
                        <style>
                            body { font-family: Arial, sans-serif; text-align: center; padding: 2rem; }
                            .qr-container { max-width: 400px; margin: 0 auto; }
                            .equipe-badge { 
                                background: #007bff; 
                                color: white; 
                                padding: 0.5rem 1rem; 
                                border-radius: 20px; 
                                display: inline-block; 
                                margin-bottom: 1rem; 
                            }
                            .qr-code { margin: 1rem 0; }
                            .qr-code img { max-width: 200px; height: auto; }
                            .token-info { 
                                background: #f8f9fa; 
                                padding: 1rem; 
                                border-radius: 5px; 
                                font-family: monospace; 
                                margin: 1rem 0; 
                            }
                            .url-info { 
                                background: #e3f2fd; 
                                padding: 1rem; 
                                border-radius: 5px; 
                                font-family: monospace; 
                                margin: 1rem 0; 
                                word-break: break-all; 
                            }
                        </style>
                    </head>
                    <body>
                        ${qrContainer.outerHTML}
                    </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }
    </script>
</body>
</html>
