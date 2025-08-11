<?php
/**
 * Script de test des headers et footers des lieux
 * Lancez depuis : http://localhost:8888/scripts/test_lieux_headers.php
 */

$baseDir = dirname(__DIR__);
$lieuxDir = $baseDir . '/lieux';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Headers Lieux - Cyberchasse</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
    </style>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-10'>
                <div class='card'>
                    <div class='card-header bg-primary text-white text-center'>
                        <h2>🧪 Test des Headers et Footers des Lieux</h2>
                        <p class='mb-0'>Vérification de la structure et des chemins</p>
                    </div>
                    <div class='card-body'>";

// Vérifier les fichiers principaux
echo "<h4>📁 Fichiers principaux</h4>";

$headerPath = $lieuxDir . '/header.php';
$footerPath = $lieuxDir . '/footer.php';

if (file_exists($headerPath)) {
    echo "<div class='alert alert-success'>✅ Header principal trouvé : $headerPath</div>";
} else {
    echo "<div class='alert alert-danger'>❌ Header principal manquant : $headerPath</div>";
}

if (file_exists($footerPath)) {
    echo "<div class='alert alert-success'>✅ Footer principal trouvé : $footerPath</div>";
} else {
    echo "<div class='alert alert-danger'>❌ Footer principal manquant : $footerPath</div>";
}

// Vérifier la structure des lieux
echo "<h4 class='mt-4'>🏫 Structure des lieux</h4>";

$lieux = array_filter(glob($lieuxDir . '/*'), 'is_dir');
$lieux = array_filter($lieux, function($path) use ($lieuxDir) {
    return basename($path) !== basename($lieuxDir);
});

echo "<div class='alert alert-info'>ℹ️ " . count($lieux) . " lieux trouvés</div>";

foreach ($lieux as $lieuPath) {
    $lieuName = basename($lieuPath);
    $lieuHeaderPath = $lieuPath . '/header.php';
    $lieuFooterPath = $lieuPath . '/footer.php';
    
    echo "<div class='card mb-2'>
            <div class='card-body'>
                <h6>🏫 $lieuName</h6>";
    
    if (file_exists($lieuHeaderPath)) {
        echo "<span class='success'>✅ Header</span>";
    } else {
        echo "<span class='error'>❌ Header manquant</span>";
    }
    
    echo " - ";
    
    if (file_exists($lieuFooterPath)) {
        echo "<span class='success'>✅ Footer</span>";
    } else {
        echo "<span class='error'>❌ Footer manquant</span>";
    }
    
    echo "</div></div>";
}

// Test de la page d'accueil
echo "<h4 class='mt-4'>🔗 Test de la page d'accueil</h4>";
echo "<div class='alert alert-info'>
        <strong>Page d'accueil des lieux :</strong><br>
        <a href='../lieux/accueil/' target='_blank'>http://localhost:8888/lieux/accueil/</a>
    </div>";

echo "<div class='alert alert-warning'>
        <strong>Vérifications à faire :</strong>
        <ul>
            <li>L'image de fond bg.jpg s'affiche-t-elle ?</li>
            <li>Les styles CSS s'appliquent-ils correctement ?</li>
            <li>La navigation fonctionne-t-elle ?</li>
            <li>La déconnexion fonctionne-t-elle ?</li>
        </ul>
    </div>";

echo "<div class='text-center mt-4'>
        <a href='../lieux/accueil/' class='btn btn-success btn-lg' target='_blank'>🏠 Tester la page d'accueil</a>
        <a href='create_lieux_headers.php' class='btn btn-primary btn-lg'>🔄 Recréer les headers</a>
        <a href='../' class='btn btn-secondary btn-lg'> Retour au projet</a>
    </div>
    
    </div></div></div></div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
