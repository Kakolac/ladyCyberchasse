<?php
/**
 * Script de test final pour vérifier tous les lieux
 * Lancez depuis : http://localhost:8888/scripts/test_final_lieux.php
 */

$baseDir = dirname(__DIR__);
$lieuxDir = $baseDir . '/lieux';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Final Tous les Lieux - Cyberchasse</title>
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
                        <h2>🧪 Test Final de Tous les Lieux</h2>
                        <p class='mb-0'>Vérification complète de la structure et des chemins</p>
                    </div>
                    <div class='card-body'>";

// Vérifier que le répertoire lieux existe
if (!is_dir($lieuxDir)) {
    echo "<div class='alert alert-danger'>❌ Le répertoire 'lieux' n'existe pas !</div>";
    exit;
}

echo "<div class='alert alert-info'>ℹ️ Répertoire 'lieux' trouvé</div>";

// Liste des lieux à vérifier
$lieux = array_filter(glob($lieuxDir . '/*'), 'is_dir');
$lieux = array_filter($lieux, function($path) use ($lieuxDir) {
    $name = basename($path);
    return $name !== 'lieux';
});

echo "<div class='alert alert-info'>ℹ️ " . count($lieux) . " lieux à vérifier</div>";

$totalLieux = count($lieux);
$validLieux = 0;

foreach ($lieux as $lieuPath) {
    $lieuName = basename($lieuPath);
    $indexPath = $lieuPath . '/index.php';
    $headerPath = $lieuPath . '/header.php';
    $footerPath = $lieuPath . '/footer.php';
    
    echo "<div class='card mb-3'>
            <div class='card-body'>
                <h5 class='card-title'>🏫 $lieuName</h5>";
    
    $lieuValid = true;
    
    // Vérifier index.php
    if (file_exists($indexPath)) {
        echo "<span class='success'>✅ index.php</span>";
    } else {
        echo "<span class='error'>❌ index.php manquant</span>";
        $lieuValid = false;
    }
    
    echo " - ";
    
    // Vérifier header.php
    if (file_exists($headerPath)) {
        echo "<span class='success'>✅ header.php</span>";
    } else {
        echo "<span class='error'>❌ header.php manquant</span>";
        $lieuValid = false;
    }
    
    echo " - ";
    
    // Vérifier footer.php
    if (file_exists($footerPath)) {
        echo "<span class='success'>✅ footer.php</span>";
    } else {
        echo "<span class='error'>❌ footer.php manquant</span>";
        $lieuValid = false;
    }
    
    // Vérifier les chemins dans index.php
    if (file_exists($indexPath)) {
        $content = file_get_contents($indexPath);
        if (strpos($content, "include './header.php';") !== false && 
            strpos($content, "include './footer.php';") !== false) {
            echo " - <span class='success'>✅ chemins corrects</span>";
        } else {
            echo " - <span class='error'>❌ chemins incorrects</span>";
            $lieuValid = false;
        }
    }
    
    if ($lieuValid) {
        $validLieux++;
    }
    
    echo "</div></div>";
}

echo "<div class='alert alert-success mt-4'>
        <h4>🎉 Test final terminé !</h4>
        <p><strong>$validLieux</strong> sur <strong>$totalLieux</strong> lieux sont correctement configurés.</p>
    </div>";
    
if ($validLieux === $totalLieux) {
    echo "<div class='alert alert-success'>
            <h5>🎯 Tous les lieux sont prêts !</h5>
            <p>Vous pouvez maintenant tester la navigation entre tous les lieux.</p>
        </div>";
} else {
    echo "<div class='alert alert-warning'>
            <h5>⚠️ Certains lieux nécessitent une attention</h5>
            <p>Relancez les scripts de création si nécessaire.</p>
        </div>";
}

echo "<h4 class='mt-4'>🔗 Liens de test</h4>
<div class='row'>
    <div class='col-md-6'>
        <div class='list-group'>
            <a href='../lieux/accueil/' class='list-group-item list-group-item-action' target='_blank'>
                🏠 Accueil des lieux
            </a>
            <a href='../lieux/cdi/' class='list-group-item list-group-item-action' target='_blank'>
                📚 CDI
            </a>
            <a href='../lieux/salle_info/' class='list-group-item list-group-item-action' target='_blank'>
                💻 Salle Informatique
            </a>
        </div>
    </div>
    <div class='col-md-6'>
        <div class='list-group'>
            <a href='../lieux/vie_scolaire/' class='list-group-item list-group-item-action' target='_blank'>
                👥 Vie Scolaire
            </a>
            <a href='../lieux/labo_physique/' class='list-group-item list-group-item-action' target='_blank'>
                ⚡ Laboratoire de Physique
            </a>
            <a href='../lieux/cantine/' class='list-group-item list-group-item-action' target='_blank'>
                🍽️ Cantine
            </a>
        </div>
    </div>
</div>

<div class='text-center mt-4'>
    <a href='../lieux/accueil/' class='btn btn-success btn-lg' target='_blank'>🏠 Tester l'accueil</a>
    <a href='create_all_lieux_headers.php' class='btn btn-primary btn-lg'>🔄 Recréer les headers</a>
    <a href='../' class='btn btn-secondary btn-lg'> Retour au projet</a>
</div>

</div></div></div></div>

<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
