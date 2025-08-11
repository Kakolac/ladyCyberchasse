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
    <title>Debug Sécurité des Tokens - Cyberchasse</title>
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
        .token-test {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
        }
        .security-issue {
            background: #f8d7da;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h1>🚨 Debug Sécurité des Tokens - Cyberchasse</h1>
                        <p class="mb-0">Diagnostic de la faille de sécurité équipe-token</p>
                    </div>
                    <div class="card-body">
                        
                        <!-- Test 1: Vérification des tokens problématiques -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>🔍 Test 1: Vérification des Tokens Problématiques</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    // Token de l'équipe Rouge pour CDI
                                    $token_rouge = '7ec311f2bb156e5cdd5defd948dd01eb';
                                    $lieu_cdi = 'cdi';
                                    
                                    // Token de l'équipe Bleue pour CDI
                                    $token_bleu = '856bd9e3c7c8ecbe918898dcdf34914d';
                                    
                                    echo "<h5>🧪 Test du token Rouge : $token_rouge</h5>";
                                    
                                    $stmt = $pdo->prepare("
                                        SELECT p.*, e.nom as equipe_nom, e.couleur as equipe_couleur, 
                                               l.nom as lieu_nom, l.slug as lieu_slug, l.ordre
                                        FROM parcours p
                                        JOIN equipes e ON p.equipe_id = e.id
                                        JOIN lieux l ON p.lieu_id = l.id
                                        WHERE p.token_acces = ? AND l.slug = ?
                                    ");
                                    
                                    $stmt->execute([$token_rouge, $lieu_cdi]);
                                    $parcours_rouge = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if ($parcours_rouge) {
                                        echo "<div class='token-test'>";
                                        echo "<strong>✅ Token Rouge trouvé :</strong><br>";
                                        echo "Équipe: {$parcours_rouge['equipe_nom']}<br>";
                                        echo "Lieu: {$parcours_rouge['lieu_nom']} ({$parcours_rouge['lieu_slug']})<br>";
                                        echo "Ordre: {$parcours_rouge['ordre_visite']}<br>";
                                        echo "Statut: {$parcours_rouge['statut']}<br>";
                                        echo "</div>";
                                    } else {
                                        echo "<div class='security-issue'>❌ Token Rouge NON trouvé !</div>";
                                    }
                                    
                                    echo "<hr>";
                                    
                                    echo "<h5>🎭 Test du token Bleu : $token_bleu</h5>";
                                    
                                    $stmt->execute([$token_bleu, $lieu_cdi]);
                                    $parcours_bleu = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if ($parcours_bleu) {
                                        echo "<div class='token-test'>";
                                        echo "<strong>✅ Token Bleu trouvé :</strong><br>";
                                        echo "Équipe: {$parcours_bleu['equipe_nom']}<br>";
                                        echo "Lieu: {$parcours_bleu['lieu_nom']} ({$parcours_bleu['lieu_slug']})<br>";
                                        echo "Ordre: {$parcours_bleu['ordre_visite']}<br>";
                                        echo "Statut: {$parcours_bleu['statut']}<br>";
                                        echo "</div>";
                                    } else {
                                        echo "<div class='security-issue'>❌ Token Bleu NON trouvé !</div>";
                                    }
                                    
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger'>❌ Erreur: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Test 2: Simulation de la vérification de sécurité -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>🎭 Test 2: Simulation de la Vérification de Sécurité</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    echo "<h5>🎭 Simulation : Équipe Rouge tente d'utiliser le token Bleu</h5>";
                                    
                                    // Simuler une session Rouge
                                    $session_equipe = 'Rouge';
                                    $token_test = $token_bleu;
                                    $lieu_test = $lieu_cdi;
                                    
                                    echo "<div class='token-test'>";
                                    echo "<strong>Session simulée :</strong> Équipe $session_equipe<br>";
                                    echo "<strong>Token testé :</strong> $token_test<br>";
                                    echo "<strong>Lieu demandé :</strong> $lieu_test<br>";
                                    echo "</div>";
                                    
                                    // Récupérer le parcours du token
                                    $stmt = $pdo->prepare("
                                        SELECT p.*, e.nom as equipe_nom, e.couleur as equipe_couleur, 
                                               l.nom as lieu_nom, l.slug as lieu_slug, l.ordre
                                        FROM parcours p
                                        JOIN equipes e ON p.equipe_id = e.id
                                        JOIN lieux l ON p.lieu_id = l.id
                                        WHERE p.token_acces = ? AND l.slug = ?
                                    ");
                                    
                                    $stmt->execute([$token_test, $lieu_test]);
                                    $parcours_test = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if ($parcours_test) {
                                        echo "<div class='token-test'>";
                                        echo "<strong>Token trouvé dans la base :</strong><br>";
                                        echo "Équipe propriétaire: {$parcours_test['equipe_nom']}<br>";
                                        echo "Équipe connectée: $session_equipe<br>";
                                        echo "</div>";
                                        
                                        // Test de la vérification de sécurité
                                        if ($parcours_test['equipe_nom'] !== $session_equipe) {
                                            echo "<div class='security-issue'>";
                                            echo "<strong>🚨 PROBLÈME DE SÉCURITÉ DÉTECTÉ !</strong><br>";
                                            echo "L'équipe $session_equipe tente d'utiliser le token de l'équipe {$parcours_test['equipe_nom']}<br>";
                                            echo "Cette tentative devrait être BLOQUÉE !<br>";
                                            echo "</div>";
                                            
                                            // Vérifier pourquoi la vérification échoue
                                            echo "<h6>🔍 Diagnostic de la vérification :</h6>";
                                            echo "<div class='token-test'>";
                                            echo "Comparaison : '{$parcours_test['equipe_nom']}' !== '$session_equipe'<br>";
                                            echo "Résultat : " . ($parcours_test['equipe_nom'] !== $session_equipe ? 'TRUE (différents)' : 'FALSE (identiques)') . "<br>";
                                            echo "Longueur Rouge : " . strlen($parcours_test['equipe_nom']) . " caractères<br>";
                                            echo "Longueur session : " . strlen($session_equipe) . " caractères<br>";
                                            echo "Codes ASCII Rouge : ";
                                            for ($i = 0; $i < strlen($parcours_test['equipe_nom']); $i++) {
                                                echo ord($parcours_test['equipe_nom'][$i]) . " ";
                                            }
                                            echo "<br>Codes ASCII session : ";
                                            for ($i = 0; $i < strlen($session_equipe); $i++) {
                                                echo ord($session_equipe[$i]) . " ";
                                            }
                                            echo "</div>";
                                            
                                        } else {
                                            echo "<div class='alert alert-success'>✅ Vérification de sécurité OK</div>";
                                        }
                                    } else {
                                        echo "<div class='security-issue'>❌ Token non trouvé dans la base</div>";
                                    }
                                    
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger'>❌ Erreur: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Test 3: Vérification de la base de données -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>📊 Test 3: Vérification de la Base de Données</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    echo "<h5>🔍 Vérification des données des équipes :</h5>";
                                    
                                    $stmt = $pdo->query("SELECT id, nom, couleur, LENGTH(nom) as nom_length FROM equipes ORDER BY nom");
                                    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($equipes as $equipe) {
                                        echo "<div class='token-test'>";
                                        echo "<strong>Équipe {$equipe['nom']}:</strong><br>";
                                        echo "ID: {$equipe['id']}<br>";
                                        echo "Nom: '{$equipe['nom']}'<br>";
                                        echo "Longueur: {$equipe['nom_length']} caractères<br>";
                                        echo "Couleur: {$equipe['couleur']}<br>";
                                        echo "</div>";
                                    }
                                    
                                    echo "<h5 class='mt-3'>🔍 Vérification des parcours CDI :</h5>";
                                    
                                    $stmt = $pdo->prepare("
                                        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom
                                        FROM parcours p
                                        JOIN equipes e ON p.equipe_id = e.id
                                        JOIN lieux l ON p.lieu_id = l.id
                                        WHERE l.slug = 'cdi'
                                        ORDER BY e.nom
                                    ");
                                    $stmt->execute();
                                    $parcours_cdi = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($parcours_cdi as $p) {
                                        echo "<div class='token-test'>";
                                        echo "<strong>Parcours CDI - Équipe {$p['equipe_nom']}:</strong><br>";
                                        echo "Token: " . substr($p['token_acces'], 0, 8) . "...<br>";
                                        echo "Statut: {$p['statut']}<br>";
                                        echo "Ordre: {$p['ordre_visite']}<br>";
                                        echo "</div>";
                                    }
                                    
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger'>❌ Erreur: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Test 4: Correction de la sécurité -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>🚨 Test 4: Correction de la Sécurité</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <h5>⚠️ Problème identifié</h5>
                                    <p>La vérification de sécurité équipe-token ne fonctionne pas correctement.</p>
                                    <p>Il faut vérifier le code de <code>lieux/access.php</code> et s'assurer que :</p>
                                    <ul>
                                        <li>La session utilisateur est bien vérifiée</li>
                                        <li>La comparaison équipe-token est stricte</li>
                                        <li>Les tokens sont bien liés aux équipes</li>
                                    </ul>
                                </div>
                                
                                <div class="mt-3">
                                    <h6>🔗 URLs de test :</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="../admin/parcours.php" class="btn btn-info btn-lg w-100 mb-2"> Gérer les parcours</a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="../login.php" class="btn btn-primary btn-lg w-100 mb-2">🔐 Page de connexion</a>
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
