<?php
// Script de remplacement des headers des lieux
$lieuxDir = '../lieux/';

// Vérifier si c'est un POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'start') {
    processHeaderReplacement();
    exit;
}

// Interface HTML
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remplacement des Headers des Lieux</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        .warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h1>🔄 Restauration des Headers des Lieux</h1>
                <p class="mb-0">Script de restauration avec la structure user-info</p>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5>🎯 Objectif</h5>
                    <p>Remplacer tous les headers des lieux par le header.php avec la structure user-info (nom de l'équipe + bouton déconnexion).</p>
                </div>
                
                <div class="alert alert-warning">
                    <h5>⚠️ Attention</h5>
                    <p>Ce script va <strong>SUPPRIMER</strong> tous les header.php existants et les remplacer par le header avec structure user-info. Assurez-vous d'avoir une sauvegarde !</p>
                </div>
                
                <div class="text-center mb-4">
                    <button id="startBtn" class="btn btn-danger btn-lg">
                        🚨 Démarrer la Restauration
                    </button>
                </div>
                
                <div id="results" class="mt-4"></div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('startBtn').addEventListener('click', function() {
        if (confirm('⚠️ ATTENTION ! Ce script va SUPPRIMER tous les header.php des lieux et les remplacer par le header avec structure user-info. Êtes-vous sûr de vouloir continuer ?')) {
            this.disabled = true;
            this.innerHTML = '⏳ Restauration en cours...';
            startRestoration();
        }
    });

    function startRestoration() {
        const resultsDiv = document.getElementById('results');
        resultsDiv.innerHTML = '<div class="alert alert-info">⏳ Restauration en cours...</div>';
        
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=start'
        })
        .then(response => response.text())
        .then(data => {
            resultsDiv.innerHTML = data;
        })
        .catch(error => {
            resultsDiv.innerHTML = '<div class="alert alert-danger">Erreur: ' + error.message + '</div>';
        });
    }
    </script>
</body>
</html>

<?php
function processHeaderReplacement() {
    global $lieuxDir;
    
    echo '<div class="alert alert-info">✅ Début de la restauration des headers avec structure user-info</div>';
    
    if (!is_dir($lieuxDir)) {
        echo '<div class="alert alert-danger">❌ Erreur: Le dossier lieux n\'existe pas</div>';
        return;
    }
    
    $lieux = scandir($lieuxDir);
    $totalLieux = 0;
    $updatedCount = 0;
    $errorCount = 0;
    
    foreach ($lieux as $lieu) {
        if ($lieu === '.' || $lieu === '..' || !is_dir($lieuxDir . $lieu) || $lieu === 'lieux') {
            continue;
        }
        
        $totalLieux++;
        $headerFile = $lieuxDir . $lieu . '/header.php';
        
        echo '<div class="mb-3">';
        echo '<strong>📍 ' . $lieu . '</strong><br>';
        
        try {
            // Vérifier si le header existe déjà
            if (file_exists($headerFile)) {
                echo '<span class="info">ℹ️ Header existant détecté, suppression...</span><br>';
                
                // Supprimer l'ancien header
                if (unlink($headerFile)) {
                    echo '<span class="success">✅ Ancien header supprimé</span><br>';
                } else {
                    echo '<span class="error">❌ Erreur lors de la suppression</span><br>';
                    $errorCount++;
                    continue;
                }
            }
            
            // Créer le nouveau header avec structure user-info
            $headerContent = '<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyberchasse - ' . ucfirst(str_replace('_', ' ', $lieu)) . '</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles/style.css">
</head>
<body>
    <header class="bg-header">
        <div class="header-content">
            <h1>Bienvenue à la Cyberchasse</h1>
            <?php if (isset($_SESSION[\'team_name\'])): ?>
                <div class="user-info">
                    <span class="team-name">Équipe: <?php echo htmlspecialchars($_SESSION[\'team_name\']); ?></span>
                    <a href="../../logout.php" class="logout-btn">Déconnexion</a>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <div class="container">';
            
            // Créer le nouveau header
            if (file_put_contents($headerFile, $headerContent)) {
                echo '<span class="success">✅ Nouveau header avec structure user-info créé avec succès</span>';
                $updatedCount++;
            } else {
                echo '<span class="error">❌ Erreur lors de la création du nouveau header</span>';
                $errorCount++;
            }
            
        } catch (Exception $e) {
            echo '<span class="error">❌ Erreur: ' . htmlspecialchars($e->getMessage()) . '</span>';
            $errorCount++;
        }
        
        echo '</div>';
    }
    
    // Résumé final
    echo '<div class="alert alert-info mt-4">';
    echo '<h5>📊 Résumé de l\'opération</h5>';
    echo '<p><strong>Total des lieux traités:</strong> ' . $totalLieux . '</p>';
    echo '<p><strong>Restaurations réussies:</strong> <span class="success">' . $updatedCount . '</span></p>';
    echo '<p><strong>Erreurs:</strong> <span class="error">' . $errorCount . '</span></p>';
    echo '</div>';
    
    if ($errorCount === 0) {
        echo '<div class="alert alert-success">';
        echo '<h5>🎉 Restauration terminée avec succès !</h5>';
        echo '<p>Tous les headers des lieux ont été restaurés avec la structure user-info.</p>';
        echo '<p><strong>Structure restaurée :</strong> Nom de l\'équipe + bouton déconnexion dans user-info</p>';
        echo '</div>';
        
        echo '<div class="text-center mt-4 mb-4">';
        echo '<a href="../lieux/direction/" class="btn btn-primary btn-lg me-3">👔 Tester sur la direction</a>';
        echo '<a href="../lieux/accueil/" class="btn btn-secondary btn-lg me-3">🏠 Tester sur l\'accueil</a>';
        echo '<a href="../admin/" class="btn btn-outline-light btn-lg">⚙️ Administration</a>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning">';
        echo '<h5>⚠️ Restauration terminée avec des erreurs</h5>';
        echo '<p>Certains lieux n\'ont pas pu être restaurés. Vérifiez les messages d\'erreur ci-dessus.</p>';
        echo '</div>';
    }
}
?>