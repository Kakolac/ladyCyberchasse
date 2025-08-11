<?php
/**
 * Script de création des headers et footers spécifiques aux lieux
 * Lancez depuis : http://localhost:8888/scripts/create_lieux_headers.php
 */

// Configuration
$baseDir = dirname(__DIR__);
$lieuxDir = $baseDir . '/lieux';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Création Headers Lieux - Cyberchasse</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .success { color: #28a745; }
        .info { color: #17a2b8; }
    </style>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-10'>
                <div class='card'>
                    <div class='card-header bg-primary text-white text-center'>
                        <h2>🏗️ Création des Headers et Footers des Lieux</h2>
                        <p class='mb-0'>Headers et footers spécifiques pour chaque lieu du lycée</p>
                    </div>
                    <div class='card-body'>";

// Vérifier que le répertoire lieux existe
if (!is_dir($lieuxDir)) {
    echo "<div class='alert alert-danger'>❌ Le répertoire 'lieux' n'existe pas !</div>";
    exit;
}

echo "<div class='alert alert-info'>ℹ️ Répertoire 'lieux' trouvé</div>";

// Créer le header principal des lieux (identique à l'original)
$headerPath = $lieuxDir . '/header.php';
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
    <title>Cyberchasse - Escape Game</title>
    <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
    <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
    <link href=\"https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap\" rel=\"stylesheet\">
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
    <link rel=\"stylesheet\" href=\"../styles/style.css\">
</head>
<body>
    <header class=\"bg-header\">
        <div class=\"header-content\">
            <h1>Bienvenue à la Cyberchasse</h1>
            <?php if (isset(\$_SESSION['team_name'])): ?>
                <div class=\"user-info\">
                    <span class=\"team-name\">Équipe: <?php echo htmlspecialchars(\$_SESSION['team_name']); ?></span>
                    <a href=\"../logout.php\" class=\"logout-btn\">Déconnexion</a>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <div class=\"container\">";

if (file_put_contents($headerPath, $headerContent)) {
    echo "<div class='alert alert-success'>✅ Header principal des lieux créé avec succès</div>";
} else {
    echo "<div class='alert alert-danger'>❌ Erreur lors de la création du header principal</div>";
}

// Créer le footer principal des lieux (identique à l'original)
$footerPath = $lieuxDir . '/footer.php';
$footerContent = "</div><!-- Fermeture du container -->
    
    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js\"></script>
</body>
</html>";

if (file_put_contents($footerPath, $footerContent)) {
    echo "<div class='alert alert-success'>✅ Footer principal des lieux créé avec succès</div>";
} else {
    echo "<div class='alert alert-danger'>❌ Erreur lors de la création du footer principal</div>";
}

// Lister tous les sous-répertoires des lieux
$lieux = array_filter(glob($lieuxDir . '/*'), 'is_dir');
$lieux = array_filter($lieux, function($path) use ($lieuxDir) {
    return basename($path) !== basename($lieuxDir);
});

echo "<div class='alert alert-info'>ℹ️ " . count($lieux) . " lieux trouvés</div>";

// Créer des headers et footers spécifiques pour chaque lieu (optionnel)
foreach ($lieux as $lieuPath) {
    $lieuName = basename($lieuPath);
    
    // Créer un header spécifique au lieu
    $lieuHeaderPath = $lieuPath . '/header.php';
    $lieuHeaderContent = "<?php
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

    if (file_put_contents($lieuHeaderPath, $lieuHeaderContent)) {
        echo "<div class='alert alert-success'>✅ Header créé pour $lieuName</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Erreur création header pour $lieuName</div>";
    }
    
    // Créer un footer spécifique au lieu
    $lieuFooterPath = $lieuPath . '/footer.php';
    $lieuFooterContent = "    </div>
    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js\"></script>
</body>
</html>";

    if (file_put_contents($lieuFooterPath, $lieuFooterContent)) {
        echo "<div class='alert alert-success'>✅ Footer créé pour $lieuName</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Erreur création footer pour $lieuName</div>";
    }
}

echo "<div class='alert alert-success mt-4'>
        <h4>🎉 Headers et Footers créés avec succès !</h4>
        <p>Tous les lieux ont maintenant leurs propres headers et footers avec les bons chemins.</p>
    </div>
    
    <div class='text-center mt-4'>
        <a href='../lieux/accueil/' class='btn btn-success btn-lg'>🏠 Accéder à l'accueil des lieux</a>
        <a href='../' class='btn btn-primary btn-lg'> Retour au projet</a>
    </div>
    
    </div></div></div></div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
