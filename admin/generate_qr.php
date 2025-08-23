<?php
session_start();
require_once '../config/connexion.php';
require_once '../config/env.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Récupération de l'URL du site depuis l'environnement
$siteUrl = env('URL_SITE', 'http://127.0.0.1:8888');

// Récupération des tokens avec toutes les informations
$stmt = $pdo->query("
    SELECT t.*, e.nom as equipe_nom, e.couleur as equipe_couleur, l.nom as lieu_nom, l.slug as lieu_slug, l.ordre
    FROM cyber_token t
    JOIN equipes e ON t.equipe_id = e.id
    JOIN cyber_lieux l ON t.lieu_id = l.id
    ORDER BY t.equipe_id, l.ordre
");
$tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des équipes et lieux pour les filtres
$stmt = $pdo->query("SELECT * FROM equipes WHERE statut = 'active' ORDER BY nom");
$equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM cyber_lieux WHERE statut = 'actif' ORDER BY ordre");
$lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'generate_token':
                $lieu_id = $_POST['lieu_id'];
                $equipe_id = $_POST['equipe_id'];
                $new_token = bin2hex(random_bytes(16));
                
                // Vérifier si un token existe déjà pour cette équipe/lieu
                $stmt = $pdo->prepare("SELECT id FROM cyber_token WHERE equipe_id = ? AND lieu_id = ?");
                $stmt->execute([$equipe_id, $lieu_id]);
                
                if ($stmt->fetch()) {
                    // Mettre à jour le token existant
                    $stmt = $pdo->prepare("UPDATE cyber_token SET token_acces = ?, statut = 'en_cours' WHERE equipe_id = ? AND lieu_id = ?");
                    $stmt->execute([$new_token, $equipe_id, $lieu_id]);
                } else {
                    // Créer un nouveau token
                    $stmt = $pdo->prepare("INSERT INTO cyber_token (equipe_id, lieu_id, token_acces, statut) VALUES (?, ?, ?, 'en_cours')");
                    $stmt->execute([$equipe_id, $lieu_id, $new_token]);
                }
                
                $success_message = "Token généré avec succès !";
                
                // Recharger les tokens
                $stmt = $pdo->query("
                    SELECT t.*, e.nom as equipe_nom, e.couleur as equipe_couleur, l.nom as lieu_nom, l.slug as lieu_slug, l.ordre
                    FROM cyber_token t
                    JOIN equipes e ON t.equipe_id = e.id
                    JOIN cyber_lieux l ON t.lieu_id = l.id
                    ORDER BY t.equipe_id, l.ordre
                ");
                $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
                
            case 'regenerate_token':
                $token_id = $_POST['token_id'];
                $new_token = bin2hex(random_bytes(16));
                
                $stmt = $pdo->prepare("UPDATE cyber_token SET token_acces = ?, created_at = NOW() WHERE id = ?");
                if ($stmt->execute([$new_token, $token_id])) {
                    $success_message = "Token régénéré avec succès !";
                    // Recharger les tokens
                    $stmt = $pdo->query("
                        SELECT t.*, e.nom as equipe_nom, e.couleur as equipe_couleur, l.nom as lieu_nom, l.slug as lieu_slug, l.ordre
                        FROM cyber_token t
                        JOIN equipes e ON t.equipe_id = e.id
                        JOIN cyber_lieux l ON t.lieu_id = l.id
                        ORDER BY t.equipe_id, l.ordre
                    ");
                    $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $error_message = "Erreur lors de la régénération du token";
                }
                break;
                
            case 'delete_token':
                $token_id = $_POST['token_id'];
                
                $stmt = $pdo->prepare("DELETE FROM cyber_token WHERE id = ?");
                if ($stmt->execute([$token_id])) {
                    $success_message = "Token supprimé avec succès !";
                    // Recharger les tokens
                    $stmt = $pdo->query("
                        SELECT t.*, e.nom as equipe_nom, e.couleur as equipe_couleur, l.nom as lieu_nom, l.slug as lieu_slug, l.ordre
                        FROM cyber_token t
                        JOIN equipes e ON t.equipe_id = e.id
                        JOIN cyber_lieux l ON t.lieu_id = l.id
                        ORDER BY t.equipe_id, l.ordre
                    ");
                    $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $error_message = "Erreur lors de la suppression du token";
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
        SELECT t.*, e.nom as equipe_nom, e.couleur as equipe_couleur, l.nom as lieu_nom, l.slug as lieu_slug, l.ordre
        FROM cyber_token t
        JOIN equipes e ON t.equipe_id = e.id
        JOIN cyber_lieux l ON t.lieu_id = l.id
        $where_clause
        ORDER BY t.equipe_id, l.ordre
    ");
    $stmt->execute($params);
    $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php
// Définir le titre de la page pour le header
$page_title = "Gestion des Tokens d'Accès";
$breadcrumb_items = [
    ['url' => 'admin2.php', 'text' => 'Administration', 'active' => false],
    ['url' => '#', 'text' => 'Gestion des Tokens', 'active' => true]
];

// Inclure le header commun
include 'includes/header.php';
?>

<!-- Styles CSS spécifiques à cette page -->
<style>
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
    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-weight: 700;
        color: white;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        margin-bottom: 1rem;
        display: inline-block;
        font-size: 1.2rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: 2px solid rgba(255,255,255,0.3);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        min-width: 120px;
    }
    .filters-section {
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 2rem;
    }
    .generate-section {
        background: #f0f9ff;
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
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .status-en_cours {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    .status-termine {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
    }
    @media print {
        .no-print { display: none !important; }
        .qr-container { page-break-inside: avoid; }
        .qr-code img { max-width: 200px !important; }
    }
</style>

<!-- Titre de la page -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header text-center">
                <h1 class="mb-0">
                    <i class="fas fa-key"></i> Gestion des Tokens d'Accès
                </h1>
                <p class="mb-0 mt-2">Générez et gérez les tokens d'accès pour chaque équipe et lieu</p>
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

    <!-- Section de génération de tokens -->
    <div class="generate-section">
        <h4>🔑 Générer un Nouveau Token</h4>
        <form method="POST" class="row g-3">
            <input type="hidden" name="action" value="generate_token">
            
            <div class="col-md-4">
                <label for="equipe_id" class="form-label">Équipe</label>
                <select class="form-select" name="equipe_id" required>
                    <option value="">Sélectionner une équipe</option>
                    <?php foreach ($equipes as $equipe): ?>
                        <option value="<?php echo $equipe['id']; ?>">
                            <?php echo htmlspecialchars($equipe['nom']); ?>
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
            
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Générer Token</button>
            </div>
        </form>
    </div>

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

    <?php if (empty($tokens)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <h4 class="text-muted">Aucun token trouvé</h4>
                <p class="text-muted">Générez votre premier token en utilisant le formulaire ci-dessus</p>
            </div>
        </div>
    <?php else: ?>
        <!-- Affichage des tokens -->
        <div class="row">
            <?php foreach ($tokens as $token): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="qr-container">
                        <?php
                        // Définir une couleur par défaut si aucune couleur n'est définie
                        $couleur_equipe = !empty($token['equipe_couleur']) ? $token['equipe_couleur'] : '#007bff';
                        
                        // Définir la couleur du texte selon la luminosité de l'arrière-plan
                        $is_light = false;
                        if (strpos($couleur_equipe, '#fff') !== false || 
                            strpos($couleur_equipe, 'white') !== false || 
                            strpos($couleur_equipe, '#ffffff') !== false ||
                            strpos($couleur_equipe, '#f0f0f0') !== false ||
                            strpos($couleur_equipe, '#e0e0e0') !== false) {
                            $is_light = true;
                        }
                        
                        $text_color = $is_light ? '#333' : 'white';
                        $text_shadow = $is_light ? 'none' : '2px 2px 4px rgba(0,0,0,0.5)';
                        $border_color = $is_light ? '#333' : 'rgba(255,255,255,0.3)';
                        ?>
                        <div class="equipe-badge" style="background-color: <?php echo $couleur_equipe; ?>; color: <?php echo $text_color; ?>; text-shadow: <?php echo $text_shadow; ?>; border-color: <?php echo $border_color; ?>;">
                            <strong><?php echo htmlspecialchars($token['equipe_nom']); ?></strong>
                        </div>
                        
                        <h5 class="mt-3"><?php echo htmlspecialchars($token['lieu_nom']); ?></h5>
                        <p class="text-muted mb-2">Ordre: <?php echo $token['ordre']; ?></p>
                        
                        <!-- Statut du token -->
                        <div class="mb-3">
                            <span class="status-badge status-<?php echo $token['statut']; ?>">
                                <?php echo ucfirst($token['statut']); ?>
                            </span>
                        </div>
                        
                        <div class="qr-code">
                            <img src="generate_qr_image.php?token=<?php echo urlencode($token['token_acces']); ?>&lieu=<?php echo urlencode($token['lieu_slug']); ?>&equipe=<?php echo urlencode($token['equipe_nom']); ?>&lieu_nom=<?php echo urlencode($token['lieu_nom']); ?>&ordre=<?php echo $token['ordre']; ?>" 
                                 alt="QR Code pour <?php echo htmlspecialchars($token['equipe_nom']); ?> - <?php echo htmlspecialchars($token['lieu_nom']); ?>"
                                 style="width: 200px; height: 200px;">
                        </div>
                        
                        <div class="token-info">
                            <strong>Token:</strong><br>
                            <?php echo htmlspecialchars($token['token_acces']); ?>
                        </div>
                        
                        <div class="url-info">
                            <strong>URL générée:</strong><br>
                            <code><?php echo $siteUrl; ?>/lieux/access.php?token=<?php echo $token['token_acces']; ?>&lieu=<?php echo $token['lieu_slug']; ?></code>
                        </div>
                        
                        <div class="qr-actions no-print">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="regenerate_token">
                                <input type="hidden" name="token_id" value="<?php echo $token['id']; ?>">
                                <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Régénérer ce token ?')">
                                    🔄 Régénérer
                                </button>
                            </form>
                            
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce token ?')">
                                <input type="hidden" name="action" value="delete_token">
                                <input type="hidden" name="token_id" value="<?php echo $token['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    🗑️ Supprimer
                                </button>
                            </form>
                            
                            <a href="generate_qr_image.php?token=<?php echo urlencode($token['token_acces']); ?>&lieu=<?php echo urlencode($token['lieu_slug']); ?>&equipe=<?php echo urlencode($token['equipe_nom']); ?>&lieu_nom=<?php echo urlencode($token['lieu_nom']); ?>&ordre=<?php echo $token['ordre']; ?>&download=1" 
                               class="btn btn-success btn-sm">
                                💾 Télécharger
                            </button>
                            
                            <button class="btn btn-outline-primary btn-sm" onclick="printSingleQR(<?php echo $token['id']; ?>)">
                                🖨️ Imprimer
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    // Fonction d'impression individuelle
    function printSingleQR(tokenId) {
        const qrContainer = document.querySelector(`#qr-${tokenId}`).closest('.qr-container');
        const printWindow = window.open('', '_blank');
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>QR Code - Token ${tokenId}</title>
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

<?php
// Inclure le footer commun
include 'includes/footer.php';
?>
