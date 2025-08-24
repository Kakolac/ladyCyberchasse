<?php
session_start();
require_once '../../../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../../../admin/login.php');
    exit();
}

// Récupération de l'ID du parcours
$parcours_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($parcours_id <= 0) {
    header('Location: index.php');
    exit();
}

// Récupération des informations du parcours
try {
    $stmt = $pdo->prepare("SELECT * FROM cyber_parcours WHERE id = ?");
    $stmt->execute([$parcours_id]);
    $parcours = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$parcours) {
        header('Location: index.php');
        exit();
    }
} catch (Exception $e) {
    die("Erreur lors de la récupération du parcours : " . $e->getMessage());
}

// Récupération des tokens avec QR codes pour ce parcours
try {
    $stmt = $pdo->prepare("
        SELECT ct.*, e.nom as equipe_nom, e.couleur as equipe_couleur, 
               l.nom as lieu_nom, l.slug as lieu_slug, 
               pl.ordre as lieu_ordre
        FROM cyber_token ct
        JOIN cyber_equipes e ON ct.equipe_id = e.id
        JOIN cyber_lieux l ON ct.lieu_id = l.id
        JOIN cyber_parcours_lieux pl ON ct.parcours_id = pl.parcours_id AND ct.lieu_id = pl.lieu_id
        WHERE ct.parcours_id = ?
        ORDER BY ct.equipe_id, ct.ordre_visite
    ");
    $stmt->execute([$parcours_id]);
    $tokens_qr = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erreur lors de la récupération des tokens : " . $e->getMessage());
}

// Configuration pour le header
$page_title = 'Impression QR Codes - ' . htmlspecialchars($parcours['nom']);
$breadcrumb_items = [
    ['text' => 'Administration', 'url' => '../../../admin/admin2.php', 'active' => false],
    ['text' => 'Gestion des Parcours', 'url' => 'index.php', 'active' => false],
    ['text' => 'Gestion du Parcours - ' . htmlspecialchars($parcours['nom']), 'url' => 'manage_parcours.php?id=' . $parcours_id, 'active' => false],
    ['text' => 'Impression QR Codes', 'url' => 'print_qr_codes.php?id=' . $parcours_id, 'active' => true]
];

include '../../../admin/includes/header.php';
?>

<!-- Styles CSS optimisés pour l'impression -->
<style>
    /* Styles d'écran */
    .print-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        text-align: center;
    }
    
    .print-header h1 {
        margin: 0;
        font-size: 2.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .print-header p {
        margin: 0.5rem 0 0 0;
        font-size: 1.2rem;
        opacity: 0.9;
    }
    
    .qr-card {
        border: 2px solid #333;
        border-radius: 15px;
        background: white;
        padding: 1.5rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        transition: transform 0.2s ease-in-out;
    }
    
    .qr-card:hover {
        transform: translateY(-5px);
    }
    
    .equipe-header {
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        color: white;
        font-weight: bold;
        font-size: 1.3rem;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    .qr-code {
        margin: 1.5rem 0;
        padding: 1rem;
        background: white;
        border-radius: 10px;
        display: inline-block;
    }
    
    .qr-code img {
        border: 3px solid #ddd;
        border-radius: 10px;
        max-width: 100%;
        height: auto;
    }
    
    .lieu-info {
        margin-top: 1rem;
    }
    
    .lieu-nom {
        font-size: 1.4rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .lieu-ordre {
        color: #666;
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    
    .print-actions {
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        text-align: center;
    }
    
    .btn-print {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 10px;
        font-size: 1.2rem;
        font-weight: bold;
        margin: 0 0.5rem;
        cursor: pointer;
        transition: transform 0.2s ease-in-out;
    }
    
    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }
    
    .btn-back {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        font-size: 1rem;
        text-decoration: none;
        display: inline-block;
        margin: 0 0.5rem;
        transition: transform 0.2s ease-in-out;
    }
    
    .btn-back:hover {
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }
    
    /* Styles d'impression - OPTIMISÉ POUR 2 RANGÉES PAR PAGE */
    @media print {
        /* Masquer UNIQUEMENT les éléments non nécessaires */
        .print-actions, .btn-back, .breadcrumb, .navbar, .sidebar {
            display: none !important;
        }
        
        /* FORCER l'affichage des couleurs et dégradés */
        .print-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
            color: white !important;
            padding: 1rem !important; /* Réduit de 2rem à 1rem */
            margin-bottom: 1rem !important; /* Réduit de 2rem à 1rem */
            border-radius: 15px !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
            page-break-after: always !important;
        }
        
        .print-header h1 {
            font-size: 1.8rem !important; /* Réduit de 2.5rem à 1.8rem */
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3) !important;
            margin: 0 !important;
        }
        
        .print-header p {
            font-size: 1rem !important; /* Réduit de 1.2rem à 1rem */
            opacity: 0.9 !important;
            margin: 0.2rem 0 0 0 !important; /* Réduit les marges */
        }
        
        .qr-card {
            border: 2px solid #333 !important;
            border-radius: 10px !important; /* Réduit de 15px à 10px */
            background: white !important;
            padding: 0.8rem !important; /* Réduit de 1.5rem à 0.8rem */
            text-align: center !important;
            margin-bottom: 1rem !important; /* Réduit de 2rem à 1rem */
            box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important; /* Réduit l'ombre */
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
            page-break-inside: avoid !important;
        }
        
        .equipe-header {
            padding: 0.6rem !important; /* Réduit de 1rem à 0.6rem */
            border-radius: 8px !important; /* Réduit de 10px à 8px */
            margin-bottom: 0.8rem !important; /* Réduit de 1.5rem à 0.8rem */
            color: white !important;
            font-weight: bold !important;
            font-size: 1.1rem !important; /* Réduit de 1.3rem à 1.1rem */
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5) !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .qr-code {
            margin: 0.8rem 0 !important; /* Réduit de 1.5rem à 0.8rem */
            padding: 0.5rem !important; /* Réduit de 1rem à 0.5rem */
            background: white !important;
            border-radius: 8px !important; /* Réduit de 10px à 8px */
            display: inline-block !important;
        }
        
        .qr-code img {
            border: 2px solid #ddd !important; /* Réduit de 3px à 2px */
            border-radius: 8px !important; /* Réduit de 10px à 8px */
            max-width: 150px !important; /* Réduit de 200px à 150px */
            height: auto !important;
        }
        
        .lieu-info {
            margin-top: 0.5rem !important; /* Réduit de 1rem à 0.5rem */
        }
        
        .lieu-nom {
            font-size: 1.1rem !important; /* Réduit de 1.4rem à 1.1rem */
            font-weight: bold !important;
            color: #333 !important;
            margin-bottom: 0.3rem !important; /* Réduit de 0.5rem à 0.3rem */
        }
        
        .lieu-ordre {
            color: #666 !important;
            font-size: 0.9rem !important; /* Réduit de 1rem à 0.9rem */
            margin-bottom: 0.5rem !important; /* Réduit de 1rem à 0.5rem */
        }
        
        /* Mise en page optimisée pour 2 rangées par page */
        .col-md-6, .col-lg-4 {
            width: 33.33% !important;
            float: left !important;
        }
        
        .row {
            margin: 0 !important;
            page-break-inside: avoid !important;
        }
        
        .mb-4 {
            margin-bottom: 1rem !important; /* Réduit de 2rem à 1rem */
        }
        
        /* Sauts de page après 2 rangées (6 cartes) */
        .col-lg-4:nth-child(6n) {
            page-break-after: always !important;
        }
        
        /* Marges de page réduites */
        @page {
            margin: 0.8cm !important; /* Réduit de 1cm à 0.8cm */
        }
        
        /* Garder le fond blanc */
        body {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        /* S'assurer que les images sont visibles */
        img {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Règles globales pour forcer les couleurs */
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

<!-- En-tête de la page -->
<div class="print-header">
    <h1><i class="fas fa-qrcode"></i> QR Codes du Parcours</h1>
    <p><strong><?php echo htmlspecialchars($parcours['nom']); ?></strong></p>
    <p>Généré le <?php echo date('d/m/Y à H:i'); ?></p>
</div>

<!-- Actions d'impression -->
<div class="print-actions">
    <h4>🖨️ Actions d'impression</h4>
    <p class="mb-3">Imprimez tous les QR codes ou utilisez Ctrl+P</p>
    <button onclick="window.print()" class="btn-print">
        <i class="fas fa-print"></i> Imprimer tous les QR codes
    </button>
    <a href="manage_parcours.php?id=<?php echo $parcours_id; ?>" class="btn-back">
        <i class="fas fa-arrow-left"></i> Retour à la gestion
    </a>
</div>

<?php if (empty($tokens_qr)): ?>
    <div class="alert alert-info text-center py-5">
        <h4 class="text-muted">Aucun QR code trouvé</h4>
        <p class="text-muted">
            Aucun token n'a été généré pour ce parcours.<br>
            Vous devez d'abord générer des tokens dans la gestion du parcours.
        </p>
        <a href="token_manager.php?id=<?php echo $parcours_id; ?>" class="btn btn-primary">
            <i class="fas fa-key"></i> Gérer les Tokens
        </a>
    </div>
<?php else: ?>
    <!-- Affichage des QR codes -->
    <div class="row">
        <?php foreach ($tokens_qr as $token): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="qr-card">
                    <!-- En-tête de l'équipe avec sa couleur -->
                    <div class="equipe-header" style="background-color: <?php echo $token['equipe_couleur']; ?>;">
                        <?php echo htmlspecialchars($token['equipe_nom']); ?>
                    </div>
                    
                    <!-- QR Code -->
                    <div class="qr-code">
                        <img src="../qr_codes/generate_image.php?token=<?php echo urlencode($token['token_acces']); ?>&lieu=<?php echo urlencode($token['lieu_slug']); ?>&equipe=<?php echo urlencode($token['equipe_nom']); ?>&lieu_nom=<?php echo htmlspecialchars($token['lieu_nom']); ?>&ordre=<?php echo $token['lieu_ordre']; ?>&parcours=<?php echo urlencode($parcours['nom']); ?>" 
                             alt="QR Code pour <?php echo htmlspecialchars($token['equipe_nom']); ?> - <?php echo htmlspecialchars($token['lieu_nom']); ?>"
                             style="width: 200px; height: 200px;">
                    </div>
                    
                    <!-- Informations du lieu -->
                    <div class="lieu-info">
                        <div class="lieu-nom"><?php echo htmlspecialchars($token['lieu_nom']); ?></div>
                        <div class="lieu-ordre">Ordre: <?php echo $token['lieu_ordre']; ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
// Fonction pour optimiser l'impression
function optimizePrint() {
    // Masquer les éléments non nécessaires à l'impression
    const elementsToHide = document.querySelectorAll('.print-actions, .btn-back, .breadcrumb, .navbar, .sidebar');
    elementsToHide.forEach(el => el.style.display = 'none');
    
    // Lancer l'impression
    window.print();
    
    // Remettre les éléments visibles après l'impression
    setTimeout(() => {
        elementsToHide.forEach(el => el.style.display = '');
    }, 1000);
}

// Écouter l'événement d'impression
window.addEventListener('beforeprint', function() {
    console.log('Impression en cours...');
});

window.addEventListener('afterprint', function() {
    console.log('Impression terminée');
});
</script>

<?php include '../../../admin/includes/footer.php'; ?>
