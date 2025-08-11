<?php
/**
 * Script de création de tous les headers et footers pour tous les lieux
 * Lancez depuis : http://localhost:8888/scripts/create_all_lieux_headers.php
 */

$baseDir = dirname(__DIR__);
$lieuxDir = $baseDir . '/lieux';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Création Tous les Headers Lieux - Cyberchasse</title>
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
                        <h2>🏗️ Création de Tous les Headers et Footers des Lieux</h2>
                        <p class='mb-0'>Création automatique pour tous les lieux du lycée</p>
                    </div>
                    <div class='card-body'>";

// Vérifier que le répertoire lieux existe
if (!is_dir($lieuxDir)) {
    echo "<div class='alert alert-danger'>❌ Le répertoire 'lieux' n'existe pas !</div>";
    exit;
}

echo "<div class='alert alert-info'>ℹ️ Répertoire 'lieux' trouvé</div>";

// Liste des lieux à traiter (exclure le dossier 'lieux' et 'accueil' déjà traité)
$lieux = array_filter(glob($lieuxDir . '/*'), 'is_dir');
$lieux = array_filter($lieux, function($path) use ($lieuxDir) {
    $name = basename($path);
    return $name !== 'lieux' && $name !== 'accueil';
});

echo "<div class='alert alert-info'>ℹ️ " . count($lieux) . " lieux à traiter</div>";

$totalLieux = count($lieux);
$createdCount = 0;

foreach ($lieux as $lieuPath) {
    $lieuName = basename($lieuPath);
    
    echo "<div class='card mb-3'>
            <div class='card-body'>
                <h5 class='card-title'>🏫 $lieuName</h5>";
    
    // Créer le header pour ce lieu
    $headerPath = $lieuPath . '/header.php';
    $headerContent = "<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang=\"fr\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Cyberchasse - " . ucfirst(str_replace('_', ' ', $lieuName)) . "</title>
    <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
    <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
    <link href=\"https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap\" rel=\"stylesheet\">
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
    <link rel=\"stylesheet\" href=\"../../styles/style.css\">
</head>
<body>
    <header class=\"bg-header\">
        <div class=\"header-content\">
            <h1>Bienvenue à la Cyberchasse</h1>
            <?php if (isset(\$_SESSION['team_name'])): ?>
                <div class=\"user-info\">
                    <span class=\"team-name\">Équipe: <?php echo htmlspecialchars(\$_SESSION['team_name']); ?></span>
                    <a href=\"../../logout.php\" class=\"logout-btn\">Déconnexion</a>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <div class=\"container\">";

    if (file_put_contents($headerPath, $headerContent)) {
        echo "<span class='success'>✅ Header créé</span>";
    } else {
        echo "<span class='error'>❌ Erreur création header</span>";
    }
    
    // Créer le footer pour ce lieu
    $footerPath = $lieuPath . '/footer.php';
    $footerContent = "</div><!-- Fermeture du container -->
    
    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js\"></script>
</body>
</html>";

    if (file_put_contents($footerPath, $footerContent)) {
        echo " - <span class='success'>✅ Footer créé</span>";
    } else {
        echo " - <span class='error'>❌ Erreur création footer</span>";
    }
    
    echo "</div></div>";
    
    $createdCount++;
    $progress = round(($createdCount / $totalLieux) * 100);
    
    echo "<script>
        document.getElementById('progressBar').style.width = '$progress%';
        document.getElementById('progressBar').textContent = '$progress%';
    </script>";
}

echo "<div class='alert alert-success mt-4'>
        <h4>🎉 Création terminée !</h4>
        <p><strong>$createdCount</strong> lieux ont maintenant leurs headers et footers.</p>
        <p>Tous les lieux utilisent maintenant des chemins relatifs corrects :</p>
        <ul>
            <li><code>../../styles/style.css</code> pour les styles</li>
            <li><code>../../logout.php</code> pour la déconnexion</li>
            <li><code>../../images/bg.jpg</code> pour l'image de fond</li>
        </ul>
    </div>
    
    <div class='text-center mt-4'>
        <a href='../lieux/accueil/' class='btn btn-success btn-lg'>🏠 Tester l'accueil des lieux</a>
        <a href='../' class='btn btn-primary btn-lg'> Retour au projet</a>
    </div>
    
    </div></div></div></div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
