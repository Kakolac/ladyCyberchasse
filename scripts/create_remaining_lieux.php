<?php
/**
 * Script de création des lieux restants du lycée
 * Lancez depuis : http://localhost:8888/scripts/create_remaining_lieux.php
 */

// Configuration
$baseDir = dirname(__DIR__);
$lieuxDir = $baseDir . '/lieux';

// Lieux restants à créer
$lieuxRestants = [
    'labo_chimie' => [
        'title' => 'Laboratoire de chimie',
        'color' => 'secondary',
        'icon' => '🧪',
        'description' => 'Chimie et sécurité des produits'
    ],
    'labo_svt' => [
        'title' => 'Laboratoire SVT',
        'color' => 'success',
        'icon' => '🌱',
        'description' => 'Biologie et éthique numérique'
    ],
    'salle_arts' => [
        'title' => 'Salle d\'arts plastiques',
        'color' => 'warning',
        'icon' => '🎨',
        'description' => 'Art numérique et propriété intellectuelle'
    ],
    'salle_musique' => [
        'title' => 'Salle de musique',
        'color' => 'info',
        'icon' => '🎵',
        'description' => 'Musique numérique et droits d\'auteur'
    ],
    'internat' => [
        'title' => 'Internat',
        'color' => 'secondary',
        'icon' => '🏠',
        'description' => 'Sécurité des réseaux résidentiels'
    ],
    'infirmerie' => [
        'title' => 'Infirmerie',
        'color' => 'danger',
        'icon' => '🏥',
        'description' => 'Protection des données médicales'
    ],
    'salle_profs' => [
        'title' => 'Salle des professeurs',
        'color' => 'warning',
        'icon' => '👨‍🏫',
        'description' => 'Sécurité des données pédagogiques'
    ],
    'direction' => [
        'title' => 'Direction du lycée',
        'color' => 'dark',
        'icon' => '👔',
        'description' => 'Gouvernance et sécurité informatique'
    ],
    'secretariat' => [
        'title' => 'Secrétariat',
        'color' => 'info',
        'icon' => '📋',
        'description' => 'Gestion administrative et cybersécurité'
    ],
    'salle_reunion' => [
        'title' => 'Salle de réunion',
        'color' => 'secondary',
        'icon' => '🤝',
        'description' => 'Confidentialité des réunions'
    ],
    'atelier_techno' => [
        'title' => 'Atelier technologique',
        'color' => 'primary',
        'icon' => '🔧',
        'description' => 'Sécurité industrielle et IoT'
    ],
    'salle_langues' => [
        'title' => 'Salle de langues',
        'color' => 'success',
        'icon' => '🌍',
        'description' => 'Communication internationale sécurisée'
    ],
    'cour' => [
        'title' => 'Cour de récréation',
        'color' => 'warning',
        'icon' => '🌳',
        'description' => 'Sécurité des espaces publics'
    ]
];

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Création Lieux Restants - Cyberchasse</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); margin-bottom: 20px; }
        .progress { height: 25px; border-radius: 15px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        .lieu-card { transition: transform 0.3s ease; }
        .lieu-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-12'>
                <div class='card'>
                    <div class='card-header bg-success text-white text-center'>
                        <h2>🏗️ Création des Lieux Restants</h2>
                        <p class='mb-0'>Compléter l\'application Cyberchasse avec 13 lieux supplémentaires</p>
                    </div>
                    <div class='card-body'>";

echo "<div class='alert alert-info'>
        <h5>📊 État actuel</h5>
        <p>Vous avez déjà <strong>7 lieux</strong> créés. Nous allons créer les <strong>13 lieux restants</strong> pour compléter votre application.</p>
    </div>";

echo "<div class='progress mb-4'>
        <div class='progress-bar progress-bar-striped progress-bar-animated' role='progressbar' style='width: 0%' id='progressBar'>0%</div>
    </div>";

$totalLieux = count($lieuxRestants);
$createdCount = 0;

foreach ($lieuxRestants as $lieu => $config) {
    $lieuPath = $lieuxDir . '/' . $lieu;
    
    echo "<div class='card lieu-card'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-md-8'>
                        <h5 class='card-title'>{$config['icon']} {$config['title']}</h5>
                        <p class='card-text'>{$config['description']}</p>";
    
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
        'index.php' => generateIndexContent($lieu, $config),
        'enigme.php' => generateEnigmeContent($lieu, $config),
        'style.css' => generateStyleContent($lieu, $config)
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
    
    echo "</div>
                    <div class='col-md-4 text-end'>
                        <span class='badge bg-{$config['color']} fs-6'>{$config['icon']} {$lieu}</span>
                    </div>
                </div>
            </div>
        </div>";
    
    $createdCount++;
    $progress = round(($createdCount / $totalLieux) * 100);
    
    echo "<script>
        document.getElementById('progressBar').style.width = '$progress%';
        document.getElementById('progressBar').textContent = '$progress%';
    </script>";
    
    usleep(50000); // 0.05 seconde
}

echo "<div class='alert alert-success mt-4'>
        <h4>�� Tous les lieux créés avec succès !</h4>
        <p><strong>$createdCount</strong> lieux supplémentaires ont été créés.</p>
        <p>Votre application Cyberchasse est maintenant complète avec <strong>20 lieux</strong> !</p>
    </div>
    
    <div class='row mt-4'>
        <div class='col-md-6'>
            <h5>📋 Lieux créés dans cette session :</h5>
            <ul class='list-group'>";

foreach ($lieuxRestants as $lieu => $config) {
    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
            {$config['icon']} {$config['title']}
            <span class='badge bg-{$config['color']} rounded-pill'>{$lieu}</span>
          </li>";
}

echo "</ul>
        </div>
        <div class='col-md-6'>
            <h5>🏆 Application complète :</h5>
            <div class='alert alert-success'>
                <strong>20 lieux</strong> créés avec succès !<br>
                Chaque lieu contient :<br>
                • Page d'accueil<br>
                • Énigme thématique<br>
                • Styles personnalisés<br>
                • Navigation intégrée
            </div>
        </div>
    </div>
    
    <div class='text-center mt-4'>
        <a href='../lieux/accueil/' class='btn btn-success btn-lg'>🏠 Accéder à l'accueil</a>
        <a href='../' class='btn btn-primary btn-lg'> Retour au projet</a>
    </div>
    
    </div></div></div></div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";

// Fonctions de génération de contenu
function generateIndexContent($lieu, $config) {
    $lieuFormatted = ucfirst(str_replace('_', ' ', $lieu));
    $color = $config['color'];
    $icon = $config['icon'];
    $title = $config['title'];
    
    return "<?php
session_start();
if (!isset(\$_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

include '../../includes/header.php';
?>

<div class='container mt-4'>
    <div class='row'>
        <div class='col-md-8'>
            <div class='card'>
                <div class='card-header bg-{$color} text-white'>
                    <h2>{$icon} {$title}</h2>
                </div>
                <div class='card-body'>
                    <div class='alert alert-info'>
                        <h5>🚨 Mission Cybersécurité</h5>
                        <p>Explorez ce lieu pour résoudre une énigme de cybersécurité et progresser dans votre mission !</p>
                    </div>
                    
                    <div class='row'>
                        <div class='col-md-6'>
                            <h5>�� Mission en cours</h5>
                            <p>Votre objectif :</p>
                            <ul>
                                <li>Résoudre l'énigme du lieu</li>
                                <li>Collecter des indices</li>
                                <li>Progresser dans la cyberchasse</li>
                                <li>Apprendre la cybersécurité</li>
                            </ul>
                        </div>
                        <div class='col-md-6'>
                            <h5>⏱️ Temps restant</h5>
                            <div id='timer' class='display-4 text-danger'></div>
                            <p class='text-muted'>Vous avez 12 minutes pour cette mission</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class='text-center'>
                        <h4>�� Prêt à commencer l'enquête ?</h4>
                        <a href='enigme.php' class='btn btn-{$color} btn-lg'>{$icon} Commencer l'énigme</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class='col-md-4'>
            <div class='card'>
                <div class='card-header bg-primary text-white'>
                    <h5>🗺️ Navigation</h5>
                </div>
                <div class='card-body'>
                    <div class='list-group'>
                        <a href='../accueil/' class='list-group-item list-group-item-action'>
                            �� Retour à l'accueil
                        </a>
                        <a href='../cdi/' class='list-group-item list-group-item-action'>
                            📚 CDI
                        </a>
                        <a href='../salle_info/' class='list-group-item list-group-item-action'>
                            �� Salle Informatique
                        </a>
                    </div>
                </div>
            </div>
            
            <div class='card mt-3'>
                <div class='card-header bg-secondary text-white'>
                    <h5>📊 Progression</h5>
                </div>
                <div class='card-body'>
                    <div class='progress mb-2'>
                        <div class='progress-bar' role='progressbar' style='width: 25%'>25%</div>
                    </div>
                    <small class='text-muted'>Progression en cours...</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src='../../js/game-timer.js'></script>
<script>
    startTimer(720, 'timer');
</script>

<?php include '../../includes/footer.php'; ?>";
}

function generateEnigmeContent($lieu, $config) {
    $lieuFormatted = ucfirst(str_replace('_', ' ', $lieu));
    $color = $config['color'];
    $icon = $config['icon'];
    $title = $config['title'];
    
    return "<?php
session_start();
if (!isset(\$_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

include '../../includes/header.php';
?>

<div class='container mt-4'>
    <div class='row justify-content-center'>
        <div class='col-md-10'>
            <div class='card'>
                <div class='card-header bg-{$color} text-white'>
                    <h2>🔍 Énigme - {$title}</h2>
                </div>
                <div class='card-body'>
                    <div class='alert alert-info'>
                        <h5>🎯 Contexte</h5>
                        <p>Résolvez cette énigme de cybersécurité pour progresser dans votre mission et débloquer le prochain lieu !</p>
                    </div>
                    
                    <div class='enigme-content'>
                        <h4>🎯 Question principale</h4>
                        <p class='lead'>Quelle est la <strong>BONNE</strong> pratique de cybersécurité ?</p>
                        
                        <div class='options mt-4'>
                            <div class='form-check mb-3'>
                                <input class='form-check-input' type='radio' name='answer' id='option1' value='A'>
                                <label class='form-check-label' for='option1'>
                                    <strong>A)</strong> Partager ses mots de passe avec ses amis de confiance.
                                </label>
                            </div>
                            
                            <div class='form-check mb-3'>
                                <input class='form-check-input' type='radio' name='answer' id='option2' value='B'>
                                <label class='form-check-label' for='option2'>
                                    <strong>B)</strong> Installer les mises à jour de sécurité dès qu'elles sont disponibles.
                                </label>
                            </div>
                            
                            <div class='form-check mb-3'>
                                <input class='form-check-input' type='radio' name='answer' id='option3' value='C'>
                                <label class='form-check-label' for='option3'>
                                    <strong>C)</strong> Cliquer sur tous les liens reçus par email.
                                </label>
                            </div>
                            
                            <div class='form-check mb-3'>
                                <input class='form-check-input' type='radio' name='answer' id='option4' value='D'>
                                <label class='form-check-label' for='option4'>
                                    <strong>D)</strong> Désactiver l'antivirus pour améliorer les performances.
                                </label>
                            </div>
                        </div>
                        
                        <div class='text-center mt-4'>
                            <button type='button' class='btn btn-{$color} btn-lg' onclick='validateAnswer()'>
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
    const selectedAnswer = document.querySelector('input[name=\"answer\"]:checked');
    
    if (!selectedAnswer) {
        alert('⚠️ Veuillez sélectionner une réponse avant de valider.');
        return;
    }
    
    const answer = selectedAnswer.value;
    
    // La réponse correcte est B (installer les mises à jour)
    if (answer === 'B') {
        saveProgress('{$lieu}', true);
        
        Swal.fire({
            title: '🎉 Bravo !',
            text: 'Vous avez résolu l\'énigme ! Les mises à jour de sécurité sont essentielles.',
            icon: 'success',
            confirmButtonText: 'Continuer l\'aventure'
        }).then((result) => {
            window.location.href = '../accueil/';
        });
    } else {
        Swal.fire({
            title: '❌ Réponse incorrecte',
            text: 'Réfléchissez aux bonnes pratiques de cybersécurité...',
            icon: 'error',
            confirmButtonText: 'Réessayer'
        });
    }
}

function saveProgress(lieu, success) {
    fetch('../../save_time.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lieu: lieu,
            success: success,
            team: '<?php echo \$_SESSION[\"team_name\"]; ?>'
        })
    });
}
</script>

<?php include '../../includes/footer.php'; ?>";
}

function generateStyleContent($lieu, $config) {
    $color = $config['color'];
    
    return "/* Styles spécifiques à {$lieu} */
.card-header.bg-{$color} {
    background: linear-gradient(135deg, var(--bs-{$color}), var(--bs-{$color}-dark)) !important;
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
    border-color: var(--bs-{$color});
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.options .form-check-input:checked + .form-check-label {
    color: var(--bs-{$color});
    font-weight: bold;
}

.progress {
    height: 25px;
    border-radius: 15px;
}

.progress-bar {
    background: linear-gradient(135deg, var(--bs-{$color}), var(--bs-{$color}-dark));
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

/* Responsive design */
@media (max-width: 768px) {
    .container {
        padding: 10px;
    }
    
    .card-body {
        padding: 15px;
    }
}";
}
?>
