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
    <title>Debug Authentification - Cyberchasse</title>
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
        .debug-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .test-result {
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
            font-weight: 600;
        }
        .test-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .test-failure { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h1>🐛 Debug Authentification - Cyberchasse</h1>
                        <p class="mb-0">Diagnostic approfondi du problème d'authentification</p>
                    </div>
                    <div class="card-body">
                        
                        <!-- Étape 1: Vérification de la base de données -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>🔍 Étape 1: Vérification de la Base de Données</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    echo "<h5>📊 Connexion à la base de données :</h5>";
                                    $pdo->query("SELECT 1");
                                    echo "<div class='test-result test-success'>✅ Connexion à la base de données réussie</div>";
                                    
                                    // Vérifier la table equipes
                                    echo "<h5 class='mt-3'>📋 Table 'equipes' :</h5>";
                                    $stmt = $pdo->query("DESCRIBE equipes");
                                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    echo "<div class='debug-info'>";
                                    echo "<strong>Structure de la table :</strong><br>";
                                    foreach ($columns as $col) {
                                        echo "• {$col['Field']}: {$col['Type']} ({$col['Null']})<br>";
                                    }
                                    echo "</div>";
                                    
                                    // Vérifier le contenu de la table equipes
                                    $stmt = $pdo->query("SELECT * FROM equipes ORDER BY nom");
                                    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    echo "<h5 class='mt-3'>👥 Contenu de la table 'equipes' :</h5>";
                                    foreach ($equipes as $equipe) {
                                        echo "<div class='debug-info'>";
                                        echo "<strong>ID:</strong> {$equipe['id']}<br>";
                                        echo "<strong>Nom:</strong> {$equipe['nom']}<br>";
                                        echo "<strong>Couleur:</strong> {$equipe['couleur']}<br>";
                                        echo "<strong>Mot de passe (hashé):</strong> {$equipe['mot_de_passe']}<br>";
                                        echo "<strong>Longueur du hash:</strong> " . strlen($equipe['mot_de_passe']) . " caractères<br>";
                                        echo "<strong>Statut:</strong> {$equipe['statut']}<br>";
                                        echo "</div>";
                                    }
                                    
                                } catch (Exception $e) {
                                    echo "<div class='test-result test-failure'>❌ Erreur: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Étape 2: Test des mots de passe -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>🔑 Étape 2: Test des Mots de Passe</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    echo "<h5>🧪 Test de vérification des mots de passe :</h5>";
                                    
                                    $passwords_to_test = [
                                        'Rouge' => 'Egour2023#!',
                                        'Bleu' => 'Uelb2023#!',
                                        'Vert' => 'Trev2023#!',
                                        'Jaune' => 'Enuaj2023#!'
                                    ];
                                    
                                    foreach ($passwords_to_test as $equipe_nom => $password) {
                                        echo "<h6> Test de l'équipe <strong>$equipe_nom</strong> :</h6>";
                                        
                                        // Récupérer le hash de la base
                                        $stmt = $pdo->prepare("SELECT mot_de_passe FROM equipes WHERE nom = ?");
                                        $stmt->execute([$equipe_nom]);
                                        $stored_hash = $stmt->fetchColumn();
                                        
                                        if ($stored_hash) {
                                            echo "<div class='debug-info'>";
                                            echo "<strong>Mot de passe testé :</strong> $password<br>";
                                            echo "<strong>Hash stocké :</strong> $stored_hash<br>";
                                            
                                            // Test de vérification
                                            $is_valid = password_verify($password, $stored_hash);
                                            
                                            if ($is_valid) {
                                                echo "<div class='test-result test-success'>✅ Mot de passe VALIDE pour $equipe_nom</div>";
                                            } else {
                                                echo "<div class='test-result test-failure'>❌ Mot de passe INVALIDE pour $equipe_nom</div>";
                                                
                                                // Test avec hash direct
                                                $direct_hash = password_hash($password, PASSWORD_DEFAULT);
                                                echo "<div class='debug-info'>";
                                                echo "<strong>Hash direct généré :</strong> $direct_hash<br>";
                                                echo "<strong>Test avec hash direct :</strong> " . (password_verify($password, $direct_hash) ? '✅ VALIDE' : '❌ INVALIDE') . "<br>";
                                                echo "</div>";
                                            }
                                            echo "</div>";
                                        } else {
                                            echo "<div class='test-result test-failure'>❌ Équipe $equipe_nom non trouvée dans la base</div>";
                                        }
                                        
                                        echo "<hr>";
                                    }
                                    
                                } catch (Exception $e) {
                                    echo "<div class='test-result test-failure'>❌ Erreur lors du test: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Étape 3: Test de connexion simulé -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>🎮 Étape 3: Test de Connexion Simulé</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    echo "<h5>🧪 Simulation du processus de connexion :</h5>";
                                    
                                    // Simuler la connexion de l'équipe Bleu
                                    $equipe_nom = 'Bleu';
                                    $password = 'Uelb2023#!';
                                    
                                    echo "<h6> Simulation connexion équipe <strong>$equipe_nom</strong> :</h6>";
                                    
                                    // 1. Recherche de l'équipe
                                    $stmt = $pdo->prepare("SELECT * FROM equipes WHERE nom = ?");
                                    $stmt->execute([$equipe_nom]);
                                    $equipe = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if ($equipe) {
                                        echo "<div class='test-result test-success'>✅ Équipe trouvée dans la base</div>";
                                        echo "<div class='debug-info'>";
                                        echo "<strong>ID:</strong> {$equipe['id']}<br>";
                                        echo "<strong>Nom:</strong> {$equipe['nom']}<br>";
                                        echo "<strong>Hash stocké:</strong> {$equipe['mot_de_passe']}<br>";
                                        echo "</div>";
                                        
                                        // 2. Vérification du mot de passe
                                        $password_valid = password_verify($password, $equipe['mot_de_passe']);
                                        
                                        if ($password_valid) {
                                            echo "<div class='test-result test-success'>✅ Mot de passe vérifié avec succès</div>";
                                            
                                            // 3. Simulation de création de session
                                            echo "<div class='test-result test-success'>✅ Connexion réussie - Session créée</div>";
                                            
                                        } else {
                                            echo "<div class='test-result test-failure'>❌ Échec de la vérification du mot de passe</div>";
                                            
                                            // Test avec différents encodages
                                            echo "<h6>🔍 Tests supplémentaires :</h6>";
                                            
                                            // Test avec hash direct
                                            $new_hash = password_hash($password, PASSWORD_DEFAULT);
                                            echo "<div class='debug-info'>";
                                            echo "<strong>Nouveau hash généré :</strong> $new_hash<br>";
                                            echo "<strong>Test avec nouveau hash :</strong> " . (password_verify($password, $new_hash) ? '✅ VALIDE' : '❌ INVALIDE') . "<br>";
                                            echo "</div>";
                                            
                                            // Test avec l'ancien hash
                                            echo "<div class='debug-info'>";
                                            echo "<strong>Test avec hash stocké :</strong> " . (password_verify($password, $equipe['mot_de_passe']) ? '✅ VALIDE' : '❌ INVALIDE') . "<br>";
                                            echo "</div>";
                                        }
                                        
                                    } else {
                                        echo "<div class='test-result test-failure'>❌ Équipe $equipe_nom non trouvée</div>";
                                    }
                                    
                                } catch (Exception $e) {
                                    echo "<div class='test-result test-failure'>❌ Erreur lors de la simulation: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Étape 4: Correction forcée -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>🔧 Étape 4: Correction Forcée des Mots de Passe</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['force_fix'])) {
                                    try {
                                        echo "<div class='alert alert-info'>🔧 Début de la correction forcée...</div>";
                                        
                                        // Supprimer et recréer toutes les équipes
                                        $pdo->exec("DELETE FROM equipes");
                                        echo "<div class='test-result test-success'>✅ Anciennes équipes supprimées</div>";
                                        
                                        // Recréer les équipes avec des mots de passe frais
                                        $equipes_data = [
                                            ['Rouge', 'red', 'Egour2023#!'],
                                            ['Bleu', 'blue', 'Uelb2023#!'],
                                            ['Vert', 'green', 'Trev2023#!'],
                                            ['Jaune', 'yellow', 'Enuaj2023#!']
                                        ];
                                        
                                        $stmt = $pdo->prepare("INSERT INTO equipes (nom, couleur, mot_de_passe, statut) VALUES (?, ?, ?, 'active')");
                                        
                                        foreach ($equipes_data as $equipe) {
                                            $hashed_password = password_hash($equipe[2], PASSWORD_DEFAULT);
                                            $stmt->execute([$equipe[0], $equipe[1], $hashed_password]);
                                            echo "<div class='test-result test-success'>✅ Équipe {$equipe[0]} recréée avec le mot de passe : {$equipe[2]}</div>";
                                        }
                                        
                                        echo "<div class='alert alert-success'>";
                                        echo "<h5>🎉 Correction forcée terminée !</h5>";
                                        echo "<p>Toutes les équipes ont été recréées avec des mots de passe frais.</p>";
                                        echo "</div>";
                                        
                                        // Recharger la page après 3 secondes
                                        echo "<script>setTimeout(function(){ location.reload(); }, 3000);</script>";
                                        
                                    } catch (Exception $e) {
                                        echo "<div class='alert alert-danger'>❌ Erreur lors de la correction forcée: " . $e->getMessage() . "</div>";
                                    }
                                } else {
                                    ?>
                                    
                                    <div class="alert alert-warning">
                                        <h5>⚠️ Solution radicale</h5>
                                        <p>Si les tests précédents montrent des problèmes persistants, cette option va :</p>
                                        <ul>
                                            <li><strong>Supprimer</strong> toutes les équipes existantes</li>
                                            <li><strong>Recréer</strong> les équipes avec des mots de passe frais</li>
                                            <li><strong>Résoudre</strong> définitivement les problèmes d'authentification</li>
                                        </ul>
                                        <p><strong>Attention :</strong> Cette action est irréversible et supprimera tous les parcours existants.</p>
                                    </div>
                                    
                                    <form method="POST" onsubmit="return confirm('ÊTES-VOUS SÛR ? Cette action va supprimer toutes les équipes et parcours existants !');">
                                        <button type="submit" name="force_fix" class="btn btn-danger btn-lg">
                                            ��️ Correction forcée (SUPPRIME TOUT)
                                        </button>
                                    </form>
                                    
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Étape 5: Test après correction -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>🎯 Étape 5: Test après Correction</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!isset($_POST['force_fix'])): ?>
                                    <div class="alert alert-info">
                                        <h5>📋 Après la correction, vous pourrez :</h5>
                                        <ol>
                                            <li><strong>Tester la connexion</strong> de toutes les équipes</li>
                                            <li><strong>Vérifier l'authentification</strong> sur la page de login</li>
                                            <li><strong>Recréer les parcours</strong> si nécessaire</li>
                                        </ol>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <h6> URLs de test :</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <a href="../login.php" class="btn btn-primary btn-lg w-100 mb-2">🔐 Page de connexion</a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="../admin/parcours.php" class="btn btn-info btn-lg w-100 mb-2"> Gérer les parcours</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                <?php else: ?>
                                    <div class="mt-3">
                                        <h5>🎯 Test des équipes recréées :</h5>
                                        <a href="../login.php" class="btn btn-success btn-lg me-2">🔐 Tester la connexion</a>
                                        <a href="../admin/parcours.php" class="btn btn-info btn-lg"> Gérer les parcours</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
