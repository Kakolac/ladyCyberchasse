<?php
require_once '../config/connexion.php';

// Récupération de l'URL du site depuis l'environnement
$siteUrl = env('URL_SITE', 'http://127.0.0.1:8888');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Système de Validation des Accès - Cyberchasse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .card {
            border: none;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            border-radius: 15px;
        }
        
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            border-bottom: none;
        }
        
        .test-section { 
            margin-bottom: 30px; 
        }
        
        .token-display { 
            background: #f8f9fa; 
            padding: 12px; 
            border-radius: 8px; 
            font-family: 'Courier New', monospace; 
            word-break: break-all; 
            border: 2px solid #e9ecef;
            font-size: 14px;
        }
        
        .url-test {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 12px;
            margin: 15px 0;
            border-left: 6px solid #2196f3;
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.1);
            color: #495057 !important;
        }
        
        .url-test h6 {
            color: #495057 !important;
        }
        
        .url-test .token-display {
            color: #495057 !important;
        }
        
        .url-test p {
            color: #6c757d !important;
        }
        
        .status-success { 
            color: #2e7d32; 
            font-weight: 600;
        }
        
        .status-error { 
            color: #c62828; 
            font-weight: 600;
        }
        
        .status-warning { 
            color: #f57c00; 
            font-weight: 600;
        }
        
        .equipe-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            color: white;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .equipe-rouge { background: linear-gradient(45deg, #ff6b6b, #ee5a52); }
        .equipe-bleu { background: linear-gradient(45deg, #4ecdc4, #44a08d); }
        .equipe-vert { background: linear-gradient(45deg, #45b7d1, #96ceb4); }
        .equipe-jaune { background: linear-gradient(45deg, #f9ca24, #f0932b); }
        
        .statut-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .statut-en_attente { background: #ffc107; color: #000; }
        .statut-en_cours { background: #17a2b8; color: #fff; }
        .statut-termine { background: #28a745; color: #fff; }
        .statut-echec { background: #dc3545; color: #fff; }
        
        .info-card {
            background: rgba(255,255,255,0.95);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 6px solid #4ade80;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .test-button {
            margin: 5px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .copy-button {
            background: #6c757d;
            border: none;
            border-radius: 25px;
            padding: 8px 16px;
            color: white;
            font-weight: 600;
        }
        
        .copy-button:hover {
            background: #5a6268;
            color: white;
        }
        
        .table-custom {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .table-custom th {
            background: #f8f9fa;
            border: none;
            padding: 15px;
            font-weight: 600;
            color: #495057;
        }
        
        .table-custom td {
            padding: 12px 15px;
            border: none;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table-custom tr:hover {
            background: #f8f9fa;
        }
        
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .section-title {
            color: #495057;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #4ade80;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h1 class="text-center mb-2">🧪 Test Système de Validation des Accès</h1>
                        <p class="text-center mb-0 fs-5">ÉTAPE 5 : Validation des tokens et accès aux lieux</p>
                    </div>
                    <div class="card-body">
                        
                        <!-- Test 1: Vérification de la base de données -->
                        <div class="test-section">
                            <h3 class="section-title">📊 Test 1: Vérification de la Base de Données</h3>
                            <div class="info-card">
                                <?php
                                try {
                                    $pdo->query("SELECT 1");
                                    echo "<div class='alert alert-success alert-custom'>";
                                    echo "<h5 class='mb-2'>✅ Connexion à la base de données réussie</h5>";
                                    echo "<p class='mb-0'>Le serveur MySQL est opérationnel et accessible</p>";
                                    echo "</div>";
                                    
                                    // Vérification des tables
                                    $tables = ['equipes', 'lieux', 'parcours', 'sessions_jeu', 'logs_activite'];
                                    echo "<div class='row'>";
                                    foreach ($tables as $table) {
                                        try {
                                            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
                                            $count = $stmt->fetchColumn();
                                            echo "<div class='col-md-4 mb-3'>";
                                            echo "<div class='alert alert-info alert-custom text-center'>";
                                            echo "<h6 class='mb-1'> Table '$table'</h6>";
                                            echo "<span class='fs-4 fw-bold'>$count</span> enregistrement(s)";
                                            echo "</div>";
                                            echo "</div>";
                                        } catch (Exception $e) {
                                            echo "<div class='col-md-4 mb-3'>";
                                            echo "<div class='alert alert-danger alert-custom text-center'>";
                                            echo "<h6 class='mb-1'>❌ Table '$table'</h6>";
                                            echo "<small>Erreur: " . $e->getMessage() . "</small>";
                                            echo "</div>";
                                            echo "</div>";
                                        }
                                    }
                                    echo "</div>";
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger alert-custom'>";
                                    echo "<h5 class='mb-2'>❌ Erreur de connexion</h5>";
                                    echo "<p class='mb-0'>" . $e->getMessage() . "</p>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Test 2: Parcours existants -->
                        <div class="test-section">
                            <h3 class="section-title">🗺️ Test 2: Parcours Existants</h3>
                            <div class="info-card">
                                <?php
                                try {
                                    $stmt = $pdo->query("
                                        SELECT p.*, e.nom as equipe_nom, e.couleur as equipe_couleur, 
                                               l.nom as lieu_nom, l.slug as lieu_slug, l.ordre
                                        FROM parcours p
                                        JOIN equipes e ON p.equipe_id = e.id
                                        JOIN lieux l ON p.lieu_id = l.id
                                        ORDER BY p.equipe_id, p.ordre_visite
                                        LIMIT 20
                                    ");
                                    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if ($parcours) {
                                        echo "<div class='alert alert-success alert-custom'>";
                                        echo "<h5 class='mb-2'>✅ " . count($parcours) . " parcours trouvé(s)</h5>";
                                        echo "<p class='mb-0'>Le système est configuré et prêt à être testé</p>";
                                        echo "</div>";
                                        
                                        echo "<div class='table-responsive'>";
                                        echo "<table class='table table-custom'>";
                                        echo "<thead>";
                                        echo "<tr>";
                                        echo "<th>Équipe</th>";
                                        echo "<th>Lieu</th>";
                                        echo "<th>Ordre</th>";
                                        echo "<th>Statut</th>";
                                        echo "<th>Token</th>";
                                        echo "<th>Actions</th>";
                                        echo "</tr>";
                                        echo "</thead>";
                                        echo "<tbody>";
                                        
                                        foreach ($parcours as $p) {
                                            $equipe_class = 'equipe-' . strtolower($p['equipe_couleur']);
                                            $statut_class = 'statut-' . $p['statut'];
                                            $token_short = substr($p['token_acces'], 0, 8) . '...';
                                            
                                            echo "<tr>";
                                            echo "<td><span class='equipe-badge $equipe_class'>" . htmlspecialchars($p['equipe_nom']) . "</span></td>";
                                            echo "<td><strong>" . htmlspecialchars($p['lieu_nom']) . "</strong><br><small class='text-muted'>/" . $p['lieu_slug'] . "</small></td>";
                                            echo "<td><span class='badge bg-secondary fs-6'>" . $p['ordre_visite'] . "</span></td>";
                                            echo "<td><span class='statut-badge $statut_class'>" . $p['statut'] . "</span></td>";
                                            echo "<td><code class='token-display'>$token_short</code></td>";
                                            echo "<td>";
                                            echo "<button class='btn btn-outline-primary btn-sm test-button' onclick='copyToClipboard(\"" . $p['token_acces'] . "\")'> Copier</button>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                        
                                        echo "</tbody></table>";
                                        echo "</div>";
                                        
                                        echo "<div class='mt-3 text-center'>";
                                        echo "<a href='../admin/parcours.php' class='btn btn-info btn-lg me-2'> Gérer tous les parcours</a>";
                                        echo "<a href='../admin/generate_qr.php' class='btn btn-success btn-lg'> Générer QR codes</a>";
                                        echo "</div>";
                                        
                                    } else {
                                        echo "<div class='alert alert-warning alert-custom'>";
                                        echo "<h5 class='mb-2'>⚠️ Aucun parcours trouvé</h5>";
                                        echo "<p class='mb-0'>Créez d'abord des parcours via l'interface d'administration</p>";
                                        echo "</div>";
                                        
                                        echo "<div class='text-center'>";
                                        echo "<a href='create_test_parcours.php' class='btn btn-primary btn-lg me-2'> Créer des parcours</a>";
                                        echo "<a href='../admin/parcours.php' class='btn btn-info btn-lg'> Gérer les parcours</a>";
                                        echo "</div>";
                                    }
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger alert-custom'>";
                                    echo "<h5 class='mb-2'>❌ Erreur lors de la récupération des parcours</h5>";
                                    echo "<p class='mb-0'>" . $e->getMessage() . "</p>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Test 3: Génération de tokens de test -->
                        <div class="test-section">
                            <h3 class="section-title"> Test 3: Génération de Tokens de Test</h3>
                            <div class="info-card">
                                <?php
                                // Génération de tokens de test
                                $test_tokens = [];
                                for ($i = 1; $i <= 3; $i++) {
                                    $token = bin2hex(random_bytes(16));
                                    $test_tokens[] = $token;
                                    echo "<div class='alert alert-success alert-custom'>";
                                    echo "<h6 class='mb-2'> Token $i généré</h6>";
                                    echo "<div class='token-display'>$token</div>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Test 4: URLs de test -->
                        <div class="test-section">
                            <h3 class="section-title">🔗 Test 4: URLs de Test pour Validation</h3>
                            <div class="info-card">
                                <p class="text-muted mb-3" style="color: #6c757d !important;">Utilisez ces URLs pour tester le système de validation :</p>
                                
                                <?php
                                $lieux_test = ['cantine', 'cdi', 'cour'];
                                
                                foreach ($test_tokens as $index => $token) {
                                    $lieu = $lieux_test[$index % count($lieux_test)];
                                    $url = "$siteUrl/lieux/access.php?token=$token&lieu=$lieu";
                                    
                                    echo "<div class='url-test'>";
                                    echo "<h6 class='mb-3' style='color: #495057 !important;'> Test " . ($index + 1) . " - Lieu: <strong style='color: #2196f3 !important;'>$lieu</strong></h6>";
                                    echo "<div class='token-display mb-3' style='color: #495057 !important;'>Token: $token</div>";
                                    echo "<div class='d-flex flex-wrap gap-2'>";
                                    echo "<a href='$url' target='_blank' class='btn btn-primary test-button'>🧪 Tester l'accès</a>";
                                    echo "<button class='copy-button' onclick='copyToClipboard(\"$url\")'>📋 Copier l'URL</button>";
                                    echo "</div>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Test 5: Test avec parcours réel -->
                        <div class="test-section">
                            <h3 class="section-title">🎯 Test 5: Test avec Parcours Réel</h3>
                            <div class="info-card">
                                <?php
                                try {
                                    // Récupérer un parcours en attente
                                    $stmt = $pdo->query("
                                        SELECT p.*, e.nom as equipe_nom, l.slug as lieu_slug
                                        FROM parcours p
                                        JOIN equipes e ON p.equipe_id = e.id
                                        JOIN lieux l ON p.lieu_id = l.id
                                        WHERE p.statut = 'en_attente'
                                        LIMIT 1
                                    ");
                                    $parcours_test = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if ($parcours_test) {
                                        $url_test = "$siteUrl/lieux/access.php?token=" . $parcours_test['token_acces'] . "&lieu=" . $parcours_test['lieu_slug'];
                                            
                                        echo "<div class='alert alert-info alert-custom'>";
                                        echo "<h5 class='mb-2'>🎯 Parcours de test trouvé :</h5>";
                                        echo "<div class='row'>";
                                        echo "<div class='col-md-6'>";
                                        echo "<p><strong>Équipe:</strong> " . htmlspecialchars($parcours_test['equipe_nom']) . "</p>";
                                        echo "<p><strong>Lieu:</strong> " . htmlspecialchars($parcours_test['lieu_slug']) . "</p>";
                                        echo "</div>";
                                        echo "<div class='col-md-6'>";
                                        echo "<p><strong>Token:</strong> " . $parcours_test['token_acces'] . "</p>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                        
                                        echo "<div class='url-test'>";
                                        echo "<div class='d-flex flex-wrap gap-2'>";
                                        echo "<a href='$url_test' target='_blank' class='btn btn-success btn-lg'>🚀 Tester l'accès réel</a>";
                                        echo "<button class='copy-button' onclick='copyToClipboard(\"$url_test\")'>📋 Copier l'URL</button>";
                                        echo "</div>";
                                        echo "</div>";
                                    } else {
                                        echo "<div class='alert alert-warning alert-custom'>";
                                        echo "<h5 class='mb-2'>⚠️ Aucun parcours en attente trouvé pour le test</h5>";
                                        echo "<p class='mb-0'>Tous les parcours sont peut-être déjà en cours ou terminés</p>";
                                        echo "</div>";
                                    }
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger alert-custom'>";
                                    echo "<h5 class='mb-2'>❌ Erreur lors de la récupération du parcours de test</h5>";
                                    echo "<p class='mb-0'>" . $e->getMessage() . "</p>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Test 6: Vérification des logs -->
                        <div class="test-section">
                            <h3 class="section-title">📝 Test 6: Vérification des Logs d'Activité</h3>
                            <div class="info-card">
                                <?php
                                try {
                                    $stmt = $pdo->query("
                                        SELECT action, COUNT(*) as count, MAX(created_at) as last_action
                                        FROM logs_activite
                                        GROUP BY action
                                        ORDER BY count DESC
                                    ");
                                    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if ($logs) {
                                        echo "<div class='alert alert-success alert-custom'>";
                                        echo "<h5 class='mb-2'>✅ Logs d'activité disponibles</h5>";
                                        echo "<p class='mb-0'>Le système de traçabilité fonctionne correctement</p>";
                                        echo "</div>";
                                        
                                        echo "<div class='table-responsive'>";
                                        echo "<table class='table table-custom'>";
                                        echo "<thead>";
                                        echo "<tr>";
                                        echo "<th>Action</th>";
                                        echo "<th>Nombre</th>";
                                        echo "<th>Dernière action</th>";
                                        echo "</tr>";
                                        echo "</thead>";
                                        echo "<tbody>";
                                        
                                        foreach ($logs as $log) {
                                            echo "<tr>";
                                            echo "<td><span class='badge bg-secondary'>" . htmlspecialchars($log['action']) . "</span></td>";
                                            echo "<td><strong>" . $log['count'] . "</strong></td>";
                                            echo "<td><small>" . $log['last_action'] . "</small></td>";
                                            echo "</tr>";
                                        }
                                        
                                        echo "</tbody></table>";
                                        echo "</div>";
                                    } else {
                                        echo "<div class='alert alert-info alert-custom'>";
                                        echo "<h5 class='mb-2'>ℹ️ Aucun log d'activité enregistré</h5>";
                                        echo "<p class='mb-0'>Les logs apparaîtront après les premiers tests d'accès</p>";
                                        echo "</div>";
                                    }
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger alert-custom'>";
                                    echo "<h5 class='mb-2'>❌ Erreur lors de la vérification des logs</h5>";
                                    echo "<p class='mb-0'>" . $e->getMessage() . "</p>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Instructions de test -->
                        <div class="test-section">
                            <h3 class="section-title">📋 Instructions de Test</h3>
                            <div class="info-card">
                                <div class="row">
                                    <div class="col-md-8">
                                        <ol class="fs-5">
                                            <li><strong>Vérifiez les parcours existants</strong> dans le tableau ci-dessus</li>
                                            <li><strong>Copiez un token valide</strong> depuis un parcours existant</li>
                                            <li><strong>Testez les URLs d'accès</strong> avec les boutons "Tester l'accès"</li>
                                            <li><strong>Vérifiez les redirections</strong> vers les lieux correspondants</li>
                                        </ol>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-info">
                                            <h6> Résultats attendus :</h6>
                                            <ul class="mb-0">
                                                <li>✅ Accès autorisé avec tokens valides</li>
                                                <li>❌ Accès refusé avec tokens invalides</li>
                                                <li>⚠️ Messages d'erreur clairs</li>
                                                <li> Logs d'activité enregistrés</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <a href="fix_parcours_status.php" class="btn btn-warning btn-lg me-2">🔧 Corriger les statuts</a>
                                    <a href="create_test_parcours.php" class="btn btn-primary btn-lg me-2"> Créer des parcours</a>
                                    <a href="../admin/parcours.php" class="btn btn-info btn-lg"> Gérer les parcours</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Créer une notification temporaire
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #28a745;
                    color: white;
                    padding: 15px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                    z-index: 9999;
                    font-weight: 600;
                `;
                notification.textContent = '✅ Copié dans le presse-papiers !';
                document.body.appendChild(notification);
                
                // Supprimer la notification après 2 secondes
                setTimeout(() => {
                    notification.remove();
                }, 2000);
                
                console.log('URL copiée dans le presse-papiers !');
            }, function(err) {
                console.error('Erreur lors de la copie : ', err);
                alert('Erreur lors de la copie : ' + err);
            });
        }
        
        // Amélioration de l'expérience utilisateur
        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter des animations aux cartes
            const cards = document.querySelectorAll('.info-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
