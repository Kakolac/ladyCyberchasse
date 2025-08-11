<?php
/**
 * Script de mise à jour des includes dans tous les fichiers index.php des lieux
 * Lancez depuis : http://localhost:8888/scripts/update_all_lieux_includes.php
 */

$baseDir = dirname(__DIR__);
$lieuxDir = $baseDir . '/lieux';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Mise à Jour Includes Lieux - Cyberchasse</title>
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
                        <h2>🔄 Mise à Jour des Includes dans Tous les Lieux</h2>
                        <p class='mb-0'>Modification des chemins d'inclusion pour tous les lieux</p>
                    </div>
                    <div class='card-body'>";

// Vérifier que le répertoire lieux existe
if (!is_dir($lieuxDir)) {
    echo "<div class='alert alert-danger'>❌ Le répertoire 'lieux' n'existe pas !</div>";
    exit;
}

echo "<div class='alert alert-info'>ℹ️ Répertoire 'lieux' trouvé</div>";

// Liste des lieux à traiter
$lieux = array_filter(glob($lieuxDir . '/*'), 'is_dir');
$lieux = array_filter($lieux, function($path) use ($lieuxDir) {
    $name = basename($path);
    return $name !== 'lieux';
});

echo "<div class='alert alert-info'>ℹ️ " . count($lieux) . " lieux à traiter</div>";

$totalLieux = count($lieux);
$updatedCount = 0;

foreach ($lieux as $lieuPath) {
    $lieuName = basename($lieuPath);
    $indexPath = $lieuPath . '/index.php';
    
    echo "<div class='card mb-3'>
            <div class='card-body'>
                <h5 class='card-title'>🏫 $lieuName</h5>";
    
    if (file_exists($indexPath)) {
        // Lire le contenu du fichier
        $content = file_get_contents($indexPath);
        
        // Remplacer les includes
        $oldContent = $content;
        
        // Remplacer include '../../includes/header.php' par include './header.php'
        $content = str_replace("include '../../includes/header.php';", "include './header.php';", $content);
        
        // Remplacer include '../../includes/footer.php' par include './footer.php'
        $content = str_replace("include '../../includes/footer.php';", "include './footer.php';", $content);
        
        // Remplacer include '../header.php' par include './header.php' (si déjà modifié)
        $content = str_replace("include '../header.php';", "include './header.php';", $content);
        
        // Remplacer include '../footer.php' par include './footer.php' (si déjà modifié)
        $content = str_replace("include '../footer.php';", "include './footer.php';", $content);
        
        // Vérifier si des changements ont été faits
        if ($content !== $oldContent) {
            if (file_put_contents($indexPath, $content)) {
                echo "<span class='success'>✅ Includes mis à jour</span>";
                $updatedCount++;
            } else {
                echo "<span class='error'>❌ Erreur mise à jour</span>";
            }
        } else {
            echo "<span class='info'>ℹ️ Aucun changement nécessaire</span>";
        }
    } else {
        echo "<span class='error'>❌ Fichier index.php manquant</span>";
    }
    
    echo "</div></div>";
}

echo "<div class='alert alert-success mt-4'>
        <h4>🎉 Mise à jour terminée !</h4>
        <p><strong>$updatedCount</strong> fichiers ont été mis à jour.</p>
        <p>Tous les lieux utilisent maintenant :</p>
        <ul>
            <li><code>include './header.php';</code> pour le header</li>
            <li><code>include './footer.php';</code> pour le footer</li>
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
