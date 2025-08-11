<?php
require_once '../config/connexion.php';

// Récupération de l'URL du site depuis l'environnement
$siteUrl = env('URL_SITE', 'http://127.0.0.1:8888');

// Fonction pour logger dans la console
function consoleLog($message, $type = 'info') {
    $color = $type === 'error' ? '#ef4444' : ($type === 'success' ? '#4ade80' : '#60a5fa');
    echo "<script>console.log('%c[CREATE_TEST_PARCOURS] $message', 'color: $color; font-weight: bold;');</script>";
}

// Fonction pour logger les détails de la base
function logDatabaseState($pdo, $message) {
    consoleLog($message);
    echo "<script>console.group('📊 État de la base de données');</script>";
    
    try {
        // Log des équipes
        $stmt = $pdo->query("SELECT COUNT(*) FROM equipes");
        $equipes_count = $stmt->fetchColumn();
        consoleLog("👥 Équipes: $equipes_count", 'info');
        
        // Log des lieux
        $stmt = $pdo->query("SELECT COUNT(*) FROM lieux");
        $lieux_count = $stmt->fetchColumn();
        consoleLog("📍 Lieux: $lieux_count", 'info');
        
        // Log des parcours
        $stmt = $pdo->query("SELECT COUNT(*) FROM parcours");
        $parcours_count = $stmt->fetchColumn();
        consoleLog("🗺️ Parcours: $parcours_count", 'info');
        
        // Log des sessions
        $stmt = $pdo->query("SELECT COUNT(*) FROM sessions_jeu");
        $sessions_count = $stmt->fetchColumn();
        consoleLog("🎮 Sessions: $sessions_count", 'info');
        
        // Log des logs d'activité
        $stmt = $pdo->query("SELECT COUNT(*) FROM logs_activite");
        $logs_count = $stmt->fetchColumn();
        consoleLog("�� Logs d'activité: $logs_count", 'info');
        
    } catch (Exception $e) {
        consoleLog("❌ Erreur lors de la vérification: " . $e->getMessage(), 'error');
    }
    
    echo "<script>console.groupEnd();</script>";
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création des Parcours de Test - Cyberchasse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        .parcours-item { 
            background: rgba(255,255,255,0.1); 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 15px; 
            border-left: 4px solid #4ade80;
        }
        .token-display { 
            background: rgba(0,0,0,0.2); 
            padding: 8px; 
            border-radius: 5px; 
            font-family: monospace; 
            word-break: break-all; 
            margin: 10px 0;
        }
        .url-test {
            background: #e3f2fd;
            padding: 12px;
            border-radius: 6px;
            margin: 8px 0;
            border-left: 4px solid #2196f3;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h1>🔧 Création des Parcours de Test</h1>
                        <p class="mb-0">Génération automatique des parcours pour tester le système de validation</p>
                    </div>
                    <div class="card-body">
                        
                        <!-- Étape 1: Vérification de la base de données -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Étape 1: Vérification de la Base de Données</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                consoleLog("🚀 Début de la vérification de la base de données");
                                
                                try {
                                    $pdo->query("SELECT 1");
                                    consoleLog("✅ Connexion à la base de données réussie", 'success');
                                    echo "<div class='alert alert-success'>✅ Connexion à la base de données réussie</div>";
                                    
                                    // Vérification des tables
                                    $tables = ['equipes', 'lieux', 'parcours', 'sessions_jeu', 'logs_activite'];
                                    foreach ($tables as $table) {
                                        try {
                                            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
                                            $count = $stmt->fetchColumn();
                                            consoleLog("📊 Table '$table': $count enregistrement(s)", 'info');
                                            echo "<div class='alert alert-info'>📊 Table '$table': $count enregistrement(s)</div>";
                                        } catch (Exception $e) {
                                            consoleLog("❌ Table '$table': Erreur - " . $e->getMessage(), 'error');
                                            echo "<div class='alert alert-danger'>❌ Table '$table': Erreur - " . $e->getMessage() . "</div>";
                                        }
                                    }
                                    
                                    // Log de l'état initial de la base
                                    logDatabaseState($pdo, "📊 État initial de la base de données");
                                    
                                } catch (Exception $e) {
                                    consoleLog("❌ Erreur de connexion: " . $e->getMessage(), 'error');
                                    echo "<div class='alert alert-danger'>❌ Erreur de connexion: " . $e->getMessage() . "</div>";
                                    exit();
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Étape 2: Création des parcours de test -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Étape 2: Création des Parcours de Test</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                consoleLog("🔧 Début de la création des parcours de test");
                                
                                try {
                                    // Récupération des équipes
                                    $stmt = $pdo->query("SELECT * FROM equipes ORDER BY nom");
                                    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    consoleLog("👥 Équipes trouvées: " . count($equipes), 'info');
                                    
                                    // Récupération des lieux
                                    $stmt = $pdo->query("SELECT * FROM lieux ORDER BY ordre LIMIT 5");
                                    $lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    consoleLog("📍 Lieux trouvés: " . count($lieux), 'info');
                                    
                                    if (empty($equipes) || empty($lieux)) {
                                        consoleLog("⚠️ Aucune équipe ou lieu trouvé. Exécutez d'abord init_database.php", 'error');
                                        echo "<div class='alert alert-warning'>⚠️ Aucune équipe ou lieu trouvé. Exécutez d'abord init_database.php</div>";
                                        exit();
                                    }
                                    
                                    echo "<div class='alert alert-info'>📋 Création des parcours pour " . count($equipes) . " équipes et " . count($lieux) . " lieux</div>";
                                    
                                    $parcours_crees = [];
                                    $total_parcours_crees = 0;
                                    
                                    // Création des parcours pour chaque équipe
                                    foreach ($equipes as $equipe) {
                                        consoleLog("🏃 Traitement de l'équipe: " . $equipe['nom'] . " (" . $equipe['couleur'] . ")");
                                        echo "<div class='parcours-item'>";
                                        echo "<h5>🏃 Équipe: " . htmlspecialchars($equipe['nom']) . " (" . htmlspecialchars($equipe['couleur']) . ")</h5>";
                                        
                                        $ordre = 1;
                                        foreach ($lieux as $lieu) {
                                            consoleLog("  📍 Lieu $ordre: " . $lieu['nom'] . " (slug: " . $lieu['slug'] . ")");
                                            
                                            // Vérifier si le parcours existe déjà
                                            $stmt = $pdo->prepare("
                                                SELECT id FROM parcours 
                                                WHERE equipe_id = ? AND lieu_id = ?
                                            ");
                                            $stmt->execute([$equipe['id'], $lieu['id']]);
                                            
                                            if (!$stmt->fetch()) {
                                                // Génération d'un token unique
                                                $token = bin2hex(random_bytes(16));
                                                consoleLog("    🔑 Token généré: " . substr($token, 0, 8) . "...", 'success');
                                                
                                                $stmt = $pdo->prepare("
                                                    INSERT INTO parcours (equipe_id, lieu_id, ordre_visite, token_acces, statut)
                                                    VALUES (?, ?, ?, ?, 'en_attente')
                                                ");
                                                
                                                if ($stmt->execute([$equipe['id'], $lieu['id'], $ordre, $token])) {
                                                    consoleLog("    ✅ Parcours créé avec succès", 'success');
                                                    echo "<div class='alert alert-success'>✅ Lieu " . $ordre . ": " . htmlspecialchars($lieu['nom']) . "</div>";
                                                    
                                                    $parcours_crees[] = [
                                                        'equipe' => $equipe['nom'],
                                                        'lieu' => $lieu['slug'],
                                                        'token' => $token,
                                                        'ordre' => $ordre
                                                    ];
                                                    $total_parcours_crees++;
                                                } else {
                                                    consoleLog("    ❌ Erreur création parcours", 'error');
                                                    echo "<div class='alert alert-danger'>❌ Erreur création parcours pour " . htmlspecialchars($lieu['nom']) . "</div>";
                                                }
                                            } else {
                                                consoleLog("    ℹ️ Parcours déjà existant", 'info');
                                                echo "<div class='alert alert-warning'>ℹ️ Parcours déjà existant pour " . htmlspecialchars($lieu['nom']) . "</div>";
                                            }
                                            
                                            $ordre++;
                                        }
                                        echo "</div>";
                                    }
                                    
                                    consoleLog("🎉 Création terminée. Total parcours créés: $total_parcours_crees", 'success');
                                    echo "<div class='alert alert-success'>🎉 Création des parcours terminée ! $total_parcours_crees parcours créés</div>";
                                    
                                    // Log de l'état final de la base
                                    logDatabaseState($pdo, "📊 État final de la base de données après création");
                                    
                                } catch (Exception $e) {
                                    consoleLog("❌ Erreur lors de la création des parcours: " . $e->getMessage(), 'error');
                                    echo "<div class='alert alert-danger'>❌ Erreur lors de la création des parcours: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Étape 3: Affichage des parcours créés -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Étape 3: Parcours Créés et URLs de Test</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($parcours_crees)): ?>
                                    <div class="alert alert-success">
                                        <h5>🎯 Parcours créés avec succès !</h5>
                                        <p>Utilisez ces URLs pour tester le système de validation :</p>
                                    </div>
                                    
                                    <?php foreach ($parcours_crees as $parcour): ?>
                                        <div class='url-test'>
                                            <h6>🏃 Équipe: <?php echo htmlspecialchars($parcour['equipe']); ?> - Lieu: <?php echo htmlspecialchars($parcour['lieu']); ?> (Ordre: <?php echo $parcour['ordre']; ?>)</h6>
                                            <div class='token-display'>Token: <?php echo $parcour['token']; ?></div>
                                            <div class='mt-2'>
                                                <a href='<?php echo $siteUrl; ?>/lieux/access.php?token=<?php echo $parcour['token']; ?>&lieu=<?php echo $parcour['lieu']; ?>' target='_blank' class='btn btn-primary btn-sm'>🧪 Tester l'accès</a>
                                                <button class='btn btn-outline-secondary btn-sm ms-2' onclick='copyToClipboard("<?php echo $siteUrl; ?>/lieux/access.php?token=<?php echo $parcour['token']; ?>&lieu=<?php echo $parcour['lieu']; ?>")'>📋 Copier l'URL</button>
                                            </div>
                                        </div>
                                        
                                        <?php
                                        // Log de l'URL de test
                                        $test_url = $siteUrl . "/lieux/access.php?token=" . $parcour['token'] . "&lieu=" . $parcour['lieu'];
                                        consoleLog("🔗 URL de test générée: $test_url", 'info');
                                        ?>
                                    <?php endforeach; ?>
                                    
                                    <div class="mt-4">
                                        <h5>📋 Instructions de test :</h5>
                                        <ol>
                                            <li><strong>Cliquez sur "Tester l'accès"</strong> pour chaque parcours</li>
                                            <li><strong>Vérifiez la redirection</strong> vers le lieu correspondant</li>
                                            <li><strong>Testez le script de validation</strong> : <a href="test_access_validation.php" class="btn btn-info btn-sm">🧪 Tester la validation</a></li>
                                        </ol>
                                    </div>
                                    
                                <?php else: ?>
                                    <div class="alert alert-warning">⚠️ Aucun parcours créé pour le moment</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Étape 4: Vérification finale -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Étape 4: Vérification Finale</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM parcours");
                                    $total_parcours = $stmt->fetchColumn();
                                    
                                    consoleLog("📊 Vérification finale - Total parcours: $total_parcours", 'info');
                                    echo "<div class='alert alert-info'>📊 Total des parcours dans la base : $total_parcours</div>";
                                    
                                    if ($total_parcours > 0) {
                                        consoleLog("🎉 Le système de validation est maintenant prêt !", 'success');
                                        echo "<div class='alert alert-success'>🎉 Le système de validation est maintenant prêt !</div>";
                                        echo "<div class='mt-3'>";
                                        echo "<a href='test_access_validation.php' class='btn btn-success btn-lg'>🧪 Tester le système de validation</a>";
                                        echo "<a href='../admin/parcours.php' class='btn btn-info btn-lg ms-2'>📝 Gérer les parcours</a>";
                                        echo "</div>";
                                    } else {
                                        consoleLog("⚠️ Aucun parcours trouvé. Vérifiez la création des parcours.", 'error');
                                        echo "<div class='alert alert-warning'>⚠️ Aucun parcours trouvé. Vérifiez la création des parcours.</div>";
                                    }
                                    
                                } catch (Exception $e) {
                                    consoleLog("❌ Erreur lors de la vérification: " . $e->getMessage(), 'error');
                                    echo "<div class='alert alert-danger'>❌ Erreur lors de la vérification: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Log de démarrage de la page
        console.log('%c[CREATE_TEST_PARCOURS] 🚀 Page chargée - Script de création des parcours de test', 'color: #4ade80; font-weight: bold; font-size: 14px;');
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                console.log('[CREATE_TEST_PARCOURS] 📋 URL copiée dans le presse-papiers:', text);
                alert('URL copiée dans le presse-papiers !');
            }, function(err) {
                console.error('[CREATE_TEST_PARCOURS] ❌ Erreur lors de la copie : ', err);
            });
        }
        
        // Log de fin de chargement
        window.addEventListener('load', function() {
            console.log('%c[CREATE_TEST_PARCOURS] ✅ Page complètement chargée', 'color: #4ade80; font-weight: bold;');
        });
    </script>
</body>
</html>
