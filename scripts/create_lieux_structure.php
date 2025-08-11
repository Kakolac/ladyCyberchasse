<?php
/**
 * Script de création de la structure des lieux du lycée
 * Lancez depuis : http://localhost:8888/scripts/create_lieux_structure.php
 */

// Configuration
$baseDir = dirname(__DIR__);
$lieuxDir = $baseDir . '/lieux';

// Liste des lieux à créer
$lieux = [
    'accueil' => 'Hall d\'entrée - Point de départ',
    'vie_scolaire' => 'Vie scolaire - Administration des élèves',
    'cdi' => 'Centre de Documentation et d\'Information',
    'salle_info' => 'Salle informatique - Cybersécurité',
    'labo_physique' => 'Laboratoire de physique',
    'labo_chimie' => 'Laboratoire de chimie',
    'labo_svt' => 'Laboratoire SVT',
    'salle_arts' => 'Salle d\'arts plastiques',
    'salle_musique' => 'Salle de musique',
    'gymnase' => 'Gymnase - Sport',
    'cantine' => 'Restaurant scolaire',
    'internat' => 'Internat',
    'infirmerie' => 'Infirmerie',
    'salle_profs' => 'Salle des professeurs',
    'direction' => 'Direction du lycée',
    'secretariat' => 'Secrétariat',
    'salle_reunion' => 'Salle de réunion',
    'atelier_techno' => 'Atelier technologique',
    'salle_langues' => 'Salle de langues',
    'cour' => 'Cour de récréation'
];

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Création Structure Lieux - Cyberchasse</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .progress { height: 25px; border-radius: 15px; }
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
                        <h2>🏗️ Création de la Structure des Lieux</h2>
                        <p class='mb-0'>Organisation de l\'application Cyberchasse par lieux du lycée</p>
                    </div>
                    <div class='card-body'>";

// Créer le répertoire principal des lieux
if (!is_dir($lieuxDir)) {
    if (mkdir($lieuxDir, 0755, true)) {
        echo "<div class='alert alert-success'>✅ Répertoire principal 'lieux' créé avec succès</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Erreur lors de la création du répertoire principal</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-info'>ℹ️ Répertoire principal 'lieux' existe déjà</div>";
}

echo "<div class='progress mb-4'>
        <div class='progress-bar progress-bar-striped progress-bar-animated' role='progressbar' style='width: 0%' id='progressBar'>0%</div>
    </div>";

$totalLieux = count($lieux);
$createdCount = 0;

foreach ($lieux as $lieu => $description) {
    $lieuPath = $lieuxDir . '/' . $lieu;
    
    echo "<div class='card mb-3'>
            <div class='card-body'>
                <h5 class='card-title'>🏫 $lieu</h5>
                <p class='card-text'>$description</p>";
    
    // Créer le répertoire du lieu
    if (!is_dir($lieuPath)) {
        if (mkdir($lieuPath, 0755, true)) {
            echo "<span class='success'>✅ Répertoire créé</span>";
        } else {
            echo "<span class='error'>❌ Erreur création répertoire</span>";
            continue;
        }
    } else {
        echo "<span class='info'>ℹ️ Répertoire existe déjà</span>";
    }
    
    // Créer les fichiers de base
    $files = [
        'index.php' => "<?php\nsession_start();\nif (!isset(\$_SESSION['team_name'])) {\n    header('Location: ../../login.php');\n    exit();\n}\ninclude '../../includes/header.php';\n?>\n\n<div class='container mt-4'>\n    <div class='card'>\n        <div class='card-header'>\n            <h2>🏫 " . ucfirst(str_replace('_', ' ', $lieu)) . "</h2>\n        </div>\n        <div class='card-body'>\n            <p>Page en cours de développement pour le lieu : <strong>$description</strong></p>\n            <a href='../accueil/' class='btn btn-primary'>�� Retour à l'accueil</a>\n        </div>\n    </div>\n</div>\n\n<?php include '../../includes/footer.php'; ?>",
        'style.css' => "/* Styles spécifiques à $lieu */\n.card-header {\n    background: linear-gradient(135deg, #007bff, #0056b3);\n    color: white;\n}\n\n.container {\n    padding: 20px;\n}",
        'enigme.php' => "<?php\nsession_start();\nif (!isset(\$_SESSION['team_name'])) {\n    header('Location: ../../login.php');\n    exit();\n}\ninclude '../../includes/header.php';\n?>\n\n<div class='container mt-4'>\n    <div class='card'>\n        <div class='card-header'>\n            <h2>🔍 Énigme - " . ucfirst(str_replace('_', ' ', $lieu)) . "</h2>\n        </div>\n        <div class='card-body'>\n            <p>Énigme en cours de développement pour ce lieu.</p>\n            <a href='index.php' class='btn btn-secondary'>🏠 Retour au lieu</a>\n        </div>\n    </div>\n</div>\n\n<?php include '../../includes/footer.php'; ?>"
    ];
    
    $filesCreated = 0;
    foreach ($files as $filename => $content) {
        $filepath = $lieuPath . '/' . $filename;
        if (!file_exists($filepath)) {
            if (file_put_contents($filepath, $content)) {
                $filesCreated++;
            }
        }
    }
    
    if ($filesCreated > 0) {
        echo " - <span class='success'>$filesCreated fichiers créés</span>";
    } else {
        echo " - <span class='info'>Fichiers existent déjà</span>";
    }
    
    echo "</div></div>";
    
    $createdCount++;
    $progress = round(($createdCount / $totalLieux) * 100);
    
    echo "<script>
        document.getElementById('progressBar').style.width = '$progress%';
        document.getElementById('progressBar').textContent = '$progress%';
    </script>";
    
    // Petite pause pour l'effet visuel
    usleep(100000); // 0.1 seconde
}

echo "<div class='alert alert-success mt-4'>
        <h4>🎉 Structure créée avec succès !</h4>
        <p><strong>$createdCount</strong> lieux ont été créés avec leurs fichiers de base.</p>
        <p>Vous pouvez maintenant personnaliser chaque lieu avec des énigmes spécifiques.</p>
    </div>
    
    <div class='text-center mt-4'>
        <a href='../lieux/accueil/' class='btn btn-success btn-lg'>🏠 Accéder à l'accueil</a>
        <a href='../' class='btn btn-primary btn-lg'>�� Retour au projet</a>
    </div>
    
    </div></div></div></div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>

echo "<div class='alert alert-info mt-4'>
        <h4>📋 Prochaines étapes</h4>
        <ul>
            <li>Personnaliser les énigmes de chaque lieu</li>
            <li>Ajouter des styles spécifiques</li>
            <li>Configurer la navigation entre lieux</li>
            <li>Tester la progression des équipes</li>
        </ul>
    </div>";
?>
