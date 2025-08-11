<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h2>🔍 Énigme CDI - Chasse aux Fake News</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>📖 Contexte</h5>
                        <p>Le CDI a été infiltré par des informations trompeuses sur la cybersécurité. Votre équipe doit identifier les vraies informations des fausses pour accéder au prochain niveau.</p>
                    </div>
                    
                    <div class="enigme-content">
                        <h4>🎯 Question principale</h4>
                        <p class="lead">Parmi ces affirmations sur la cybersécurité, laquelle est <strong>FAUSSE</strong> ?</p>
                        
                        <div class="options mt-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option1" value="A">
                                <label class="form-check-label" for="option1">
                                    <strong>A)</strong> Un mot de passe fort doit contenir au moins 12 caractères avec des majuscules, minuscules, chiffres et symboles.
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option2" value="B">
                                <label class="form-check-label" for="option2">
                                    <strong>B)</strong> La double authentification (2FA) ajoute une couche de sécurité supplémentaire en demandant un code en plus du mot de passe.
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option3" value="C">
                                <label class="form-check-label" for="option3">
                                    <strong>C)</strong> Il est sécurisé de partager ses identifiants de connexion avec ses amis de confiance.
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option4" value="D">
                                <label class="form-check-label" for="option4">
                                    <strong>D)</strong> Les mises à jour de sécurité des logiciels doivent être installées dès qu'elles sont disponibles.
                                </label>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-success btn-lg" onclick="validateAnswer()">
                                ✅ Valider ma réponse
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateAnswer() {
    const selectedAnswer = document.querySelector('input[name="answer"]:checked');
    
    if (!selectedAnswer) {
        alert('⚠️ Veuillez sélectionner une réponse avant de valider.');
        return;
    }
    
    const answer = selectedAnswer.value;
    
    // La réponse correcte est C (partager ses identifiants n'est jamais sécurisé)
    if (answer === 'C') {
        // Sauvegarder la progression
        saveProgress('cdi', true);
        
        // Afficher le succès
        Swal.fire({
            title: '🎉 Bravo !',
            text: 'Vous avez identifié la fausse information ! Partager ses identifiants n\'est jamais sécurisé.',
            icon: 'success',
            confirmButtonText: 'Continuer l\'aventure'
        }).then((result) => {
            window.location.href = '../salle_info/';
        });
    } else {
        // Réponse incorrecte
        Swal.fire({
            title: '❌ Réponse incorrecte',
            text: 'Essayez de réfléchir aux bonnes pratiques de cybersécurité...',
            icon: 'error',
            confirmButtonText: 'Réessayer'
        });
    }
}

function saveProgress(lieu, success) {
    // Envoyer la progression au serveur
    fetch('../../save_time.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lieu: lieu,
            success: success,
            team: '<?php echo $_SESSION["team_name"]; ?>'
        })
    });
}
</script>

<?php include '../../includes/footer.php'; ?>
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
        'index.php' => "<?php\nsession_start();\nif (!isset(\$_SESSION['team_name'])) {\n    header('Location: ../../login.php');\n    exit();\n}\ninclude '../../includes/header.php';\n?>\n\n<div class='container mt-4'>\n    <div class='card'>\n        <div class='card-header'>\n            <h2>🏫 " . ucfirst(str_replace('_', ' ', $lieu)) . "</h2>\n        </div>\n        <div class='card-body'>\n            <p>Page en cours de développement pour le lieu : <strong>$description</strong></p>\n            <a href='../accueil/' class='btn btn-primary'> Retour à l'accueil</a>\n        </div>\n    </div>\n</div>\n\n<?php include '../../includes/footer.php'; ?>",
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
        <a href='../' class='btn btn-primary btn-lg'> Retour au projet</a>
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
```

## Résumé de la nouvelle structure

J'ai créé une organisation complète des lieux d'un lycée avec :

### ️ **Structure créée :**
- **20 lieux** organisés par zones logiques
- **Fichiers de base** pour chaque lieu (index.php, enigme.php, style.css)
- **Navigation cohérente** entre les lieux
- **Système de progression** pour les équipes

### 🎯 **Lieux clés développés :**
- **Accueil** : Point de départ avec navigation
- **CDI** : Énigme sur la détection de fake news
- **Structure complète** pour tous les autres lieux

###  **Organisation des fichiers :**
- Un répertoire par lieu
- Fichiers spécifiques à chaque lieu
- Composants réutilisables (header, footer, styles)
- Script d'initialisation automatique

### 🚀 **Pour lancer la création :**
```
http://localhost:8888/scripts/create_lieux_structure.php
```

Cette structure respecte vos règles de :
- ✅ Documentation dans `/docs`
- ✅ Scripts lancés depuis le front
- ✅ Interface visuellement attrayante
- ✅ Préservation des fonctionnalités existantes

Voulez-vous que je continue avec le développement d'autres lieux spécifiques ou que je me concentre sur un aspect particulier de cette organisation ?
