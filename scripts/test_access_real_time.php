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
    <title>Test Accès en Temps Réel - Cyberchasse</title>
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
        .test-result {
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            font-weight: 600;
        }
        .test-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .test-failure { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .test-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .token-display {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            border: 1px solid #e9ecef;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h1>🔍 Test Accès en Temps Réel - Cyberchasse</h1>
                        <p class="mb-0">Simulation exacte de lieux/access.php pour identifier la faille</p>
                    </div>
                    <div class="card-body">
                        
                        <!-- Test 1: Simulation de session -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>�� Test 1: Simulation de Session</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                // Simuler une session Rouge
                                $_SESSION['team_name'] = 'Rouge';
                                $_SESSION['equipe_id'] = 1;
                                
                                echo "<div class='test-result test-success'>";
                                echo "<h5>✅ Session simulée créée</h5>";
                                echo "<p><strong>Équipe connectée :</strong> {$_SESSION['team_name']}</p>";
                                echo "<p><strong>ID équipe :</strong> {$_SESSION['equipe_id']}</p>";
                                echo "</div>";
                                ?>
                            </div>
                        </div>

                        <!-- Test 2: Test avec token Rouge -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>🔑 Test 2: Test avec Token Rouge (Valide)</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    $token_rouge = '7ec311f2bb156e5cdd5defd948dd01eb';
                                    $lieu_cdi = 'cdi';
                                    
                                    echo "<h5>🧪 Test du token Rouge : $token_rouge</h5>";
                                    
                                    // 1. Validation du token d'accès
                                    $stmt = $pdo->prepare("
                                        SELECT p.*, e.nom as equipe_nom, e.couleur as equipe_couleur, 
                                               l.nom as lieu_nom, l.slug as lieu_slug, l.temps_limite, l.ordre
                                        FROM parcours p
                                        JOIN equipes e ON p.equipe_id = e.id
                                        JOIN lieux l ON p.lieu_id = l.id
                                        WHERE p.token_acces = ? AND l.slug = ? AND p.statut IN ('en_attente', 'en_cours')
                                    ");
                                    
                                    $stmt->execute([$token_rouge, $lieu_cdi]);
                                    $parcours = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if ($parcours) {
                                        echo "<div class='test-result test-success'>";
                                        echo "<h6>✅ Token Rouge trouvé dans la base</h6>";
                                        echo "<p><strong>Équipe propriétaire :</strong> {$parcours['equipe_nom']}</p>";
                                        echo "<p><strong>Équipe connectée :</strong> {$_SESSION['team_name']}</p>";
                                        echo "<p><strong>Lieu :</strong> {$parcours['lieu_nom']} ({$parcours['lieu_slug']})</p>";
                                        echo "<p><strong>Statut :</strong> {$parcours['statut']}</p>";
                                        echo "</div>";
                                        
                                        // Test de la vérification de sécurité
                                        echo "<h6>🔒 Test de la vérification de sécurité :</h6>";
                                        echo "<div class='token-display'>";
                                        echo "Comparaison : '{$parcours['equipe_nom']}' !== '{$_SESSION['team_name']}'<br>";
                                        echo "Résultat : " . ($parcours['equipe_nom'] !== $_SESSION['team_name'] ? 'TRUE (différents)' : 'FALSE (identiques)') . "<br>";
                                        echo "Type équipe_nom : " . gettype($parcours['equipe_nom']) . "<br>";
                                        echo "Type session_team : " . gettype($_SESSION['team_name']) . "<br>";
                                        echo "</div>";
                                        
                                        if ($parcours['equipe_nom'] !== $_SESSION['team_name']) {
                                            echo "<div class='test-result test-failure'>❌ Vérification de sécurité ÉCHOUÉE - Accès refusé</div>";
                                        } else {
                                            echo "<div class='test-result test-success'>✅ Vérification de sécurité OK - Accès autorisé</div>";
                                        }
                                        
                                    } else {
                                        echo "<div class='test-result test-failure'>❌ Token Rouge NON trouvé dans la base</div>";
                                    }
                                    
                                } catch (Exception $e) {
                                    echo "<div class='test-result test-failure'>❌ Erreur: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Test 3: Test avec token Bleu (Faille de sécurité) -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>🚨 Test 3: Test avec Token Bleu (Faille de Sécurité)</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    $token_bleu = '856bd9e3c7c8ecbe918898dcdf34914d';
                                    
                                    echo "<h5>�� Test du token Bleu : $token_bleu</h5>";
                                    
                                    // 1. Validation du token d'accès
                                    $stmt = $pdo->prepare("
                                        SELECT p.*, e.nom as equipe_nom, e.couleur as equipe_couleur, 
                                               l.nom as lieu_nom, l.slug as lieu_slug, l.temps_limite, l.ordre
                                        FROM parcours p
                                        JOIN equipes e ON p.equipe_id = e.id
                                        JOIN lieux l ON p.lieu_id = l.id
                                        WHERE p.token_acces = ? AND l.slug = ? AND p.statut IN ('en_attente', 'en_cours')
                                    ");
                                    
                                    $stmt->execute([$token_bleu, $lieu_cdi]);
                                    $parcours = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if ($parcours) {
                                        echo "<div class='test-result test-warning'>";
                                        echo "<h6>⚠️ Token Bleu trouvé dans la base</h6>";
                                        echo "<p><strong>Équipe propriétaire :</strong> {$parcours['equipe_nom']}</p>";
                                        echo "<p><strong>Équipe connectée :</strong> {$_SESSION['team_name']}</p>";
                                        echo "<p><strong>Lieu :</strong> {$parcours['lieu_nom']} ({$parcours['lieu_slug']})</p>";
                                        echo "<p><strong>Statut :</strong> {$parcours['statut']}</p>";
                                        echo "</div>";
                                        
                                        // Test de la vérification de sécurité
                                        echo "<h6>🔒 Test de la vérification de sécurité :</h6>";
                                        echo "<div class='token-display'>";
                                        echo "Comparaison : '{$parcours['equipe_nom']}' !== '{$_SESSION['team_name']}'<br>";
                                        echo "Résultat : " . ($parcours['equipe_nom'] !== $_SESSION['team_name'] ? 'TRUE (différents)' : 'FALSE (identiques)') . "<br>";
                                        echo "Type équipe_nom : " . gettype($parcours['equipe_nom']) . "<br>";
                                        echo "Type session_team : " . gettype($_SESSION['team_name']) . "<br>";
                                        echo "</div>";
                                        
                                        if ($parcours['equipe_nom'] !== $_SESSION['team_name']) {
                                            echo "<div class='test-result test-success'>✅ Vérification de sécurité OK - Accès BLOQUÉ</div>";
                                        } else {
                                            echo "<div class='test-result test-failure'>❌ Vérification de sécurité ÉCHOUÉE - Accès autorisé (FAILLE !)</div>";
                                        }
                                        
                                    } else {
                                        echo "<div class='test-result test-failure'>❌ Token Bleu NON trouvé dans la base</div>";
                                    }
                                    
                                } catch (Exception $e) {
                                    echo "<div class='test-result test-failure'>❌ Erreur: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Test 4: Vérification des sessions -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>📋 Test 4: Vérification des Sessions</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                echo "<h5>🔍 État des sessions :</h5>";
                                echo "<div class='token-display'>";
                                echo "<strong>Session ID :</strong> " . session_id() . "<br>";
                                echo "<strong>Session status :</strong> " . session_status() . "<br>";
                                echo "<strong>Session data :</strong><br>";
                                foreach ($_SESSION as $key => $value) {
                                    echo "  - $key : " . (is_string($value) ? "'$value'" : $value) . "<br>";
                                }
                                echo "</div>";
                                
                                echo "<h5 class='mt-3'>🔍 Test de session_start() :</h5>";
                                if (session_status() === PHP_SESSION_ACTIVE) {
                                    echo "<div class='test-result test-success'>✅ Session active</div>";
                                } else {
                                    echo "<div class='test-result test-failure'>❌ Session inactive</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Test 5: Test de connexion réelle -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>🎯 Test 5: Test de Connexion Réelle</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h5>📋 Instructions de test :</h5>
                                    <ol>
                                        <li><strong>Ouvrez un nouvel onglet</strong> et connectez-vous avec l'équipe Rouge</li>
                                        <li><strong>Revenez ici</strong> et rechargez cette page</li>
                                        <li><strong>Vérifiez</strong> que la session est bien détectée</li>
                                        <li><strong>Testez</strong> l'accès avec le token Bleu</li>
                                    </ol>
                                </div>
                                
                                <div class="mt-3">
                                    <h6> URLs de test :</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="../login.php" class="btn btn-primary btn-lg w-100 mb-2">🔐 Se connecter</a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../lieux/access.php?token=856bd9e3c7c8ecbe918898dcdf34914d&lieu=cdi" class="btn btn-danger btn-lg w-100 mb-2">🚨 Tester faille de sécurité</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
