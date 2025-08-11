<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

include './header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h2>📚 CDI - Centre de Documentation et d'Information</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5>🚨 Alerte Sécurité !</h5>
                        <p>Des informations sensibles ont été disséminées dans la documentation du CDI. Votre mission : identifier les documents fiables des fake news et collecter les indices de cybersécurité.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>�� Mission en cours</h5>
                            <p>Explorez les ressources du CDI pour :</p>
                            <ul>
                                <li>Identifier les sources d'information fiables</li>
                                <li>Détecter les tentatives de désinformation</li>
                                <li>Trouver les documents sur la cybersécurité</li>
                                <li>Collecter les indices cachés</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>⏱️ Temps restant</h5>
                            <div id="timer" class="display-4 text-danger"></div>
                            <p class="text-muted">Vous avez 12 minutes pour cette mission</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <h4>�� Prêt à commencer l'enquête ?</h4>
                        <a href="enigme.php" class="btn btn-primary btn-lg">�� Commencer l'énigme</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5>🗺️ Navigation</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="../accueil/" class="list-group-item list-group-item-action">
                            �� Retour à l'accueil
                        </a>
                        <a href="../salle_info/" class="list-group-item list-group-item-action">
                            �� Salle Informatique
                        </a>
                        <a href="../vie_scolaire/" class="list-group-item list-group-item-action">
                            👥 Vie Scolaire
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5>📊 Progression</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: 25%">25%</div>
                    </div>
                    <small class="text-muted">1/4 lieux explorés</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../js/game-timer.js"></script>
<script>
    // Timer spécifique au CDI
    startTimer(720, 'timer'); // 12 minutes = 720 secondes
</script>

<?php include './footer.php'; ?>
```

```css:lieux/cdi/style.css
/* Styles spécifiques au CDI */
.card-header.bg-success {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1, #bee5eb);
    border-left: 4px solid #17a2b8;
}

.options .form-check {
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.options .form-check:hover {
    border-color: #28a745;
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.options .form-check-input:checked + .form-check-label {
    color: #28a745;
    font-weight: bold;
}

.progress {
    height: 25px;
    border-radius: 15px;
}

.progress-bar {
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 15px;
}

/* Animation pour les options */
.form-check {
    animation: fadeInUp 0.6s ease-out;
}

.form-check:nth-child(1) { animation-delay: 0.1s; }
.form-check:nth-child(2) { animation-delay: 0.2s; }
.form-check:nth-child(3) { animation-delay: 0.3s; }
.form-check:nth-child(4) { animation-delay: 0.4s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```

Maintenant, créons un script pour initialiser cette nouvelle structure :

```php:scripts/create_lieux_structure.php
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
        'index.php' => "<?php\nsession_start();\nif (!isset(\$_SESSION['team_name'])) {\n    header('Location: ../../login.php');\n    exit();\n}\ninclude './header.php';\n?>\n\n<div class='container mt-4'>\n    <div class='card'>\n        <div class='card-header'>\n            <h2>🏫 " . ucfirst(str_replace('_', ' ', $lieu)) . "</h2>\n        </div>\n        <div class='card-body'>\n            <p>Page en cours de développement pour le lieu : <strong>$description</strong></p>\n            <a href='../accueil/' class='btn btn-primary'> Retour à l'accueil</a>\n        </div>\n    </div>\n</div>\n\n<?php include './footer.php'; ?>",
        'style.css' => "/* Styles spécifiques à $lieu */\n.card-header {\n    background: linear-gradient(135deg, #007bff, #0056b3);\n    color: white;\n}\n\n.container {\n    padding: 20px;\n}",
        'enigme.php' => "<?php\nsession_start();\nif (!isset(\$_SESSION['team_name'])) {\n    header('Location: ../../login.php');\n   
