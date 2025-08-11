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
    <title>Correction des Mots de Passe - Cyberchasse</title>
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
        .password-display {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            border: 2px solid #e9ecef;
            margin: 10px 0;
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
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h1>🔐 Correction des Mots de Passe - Cyberchasse</h1>
                        <p class="mb-0">Diagnostic et correction des problèmes d'authentification</p>
                    </div>
                    <div class="card-body">
                        
                        <!-- Étape 1: Diagnostic des mots de passe -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Étape 1: Diagnostic des Mots de Passe</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    // Vérifier tous les mots de passe des équipes
                                    $stmt = $pdo->query("SELECT id, nom, couleur, mot_de_passe FROM equipes ORDER BY nom");
                                    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    echo "<div class='alert alert-info'>";
                                    echo "<h5>📊 État actuel des mots de passe</h5>";
                                    echo "<p>Vérification de l'état des équipes dans la base de données</p>";
                                    echo "</div>";
                                    
                                    foreach ($equipes as $equipe) {
                                        $equipe_class = 'equipe-' . strtolower($equipe['couleur']);
                                        
                                        echo "<div class='card mb-3'>";
                                        echo "<div class='card-header'>";
                                        echo "<span class='equipe-badge $equipe_class'>" . htmlspecialchars($equipe['nom']) . "</span>";
                                        echo "</div>";
                                        echo "<div class='card-body'>";
                                        
                                        // Vérifier si le mot de passe est hashé
                                        if (password_verify('test', $equipe['mot_de_passe'])) {
                                            echo "<div class='alert alert-danger'>❌ Mot de passe non hashé (vulnérable)</div>";
                                        } else {
                                            echo "<div class='alert alert-success'>✅ Mot de passe correctement hashé</div>";
                                        }
                                        
                                        echo "<p><strong>Hash actuel :</strong></p>";
                                        echo "<div class='password-display'>" . htmlspecialchars($equipe['mot_de_passe']) . "</div>";
                                        
                                        // Tester les mots de passe connus
                                        $passwords_to_test = [
                                            'Egour2023#!', // Rouge
                                            'Uelb2023#!',  // Bleu
                                            'Trev2023#!',  // Vert
                                            'Enuaj2023#!'  // Jaune
                                        ];
                                        
                                        $password_found = false;
                                        foreach ($passwords_to_test as $test_password) {
                                            if (password_verify($test_password, $equipe['mot_de_passe'])) {
                                                echo "<div class='alert alert-success'>✅ Mot de passe valide : <strong>$test_password</strong></div>";
                                                $password_found = true;
                                                break;
                                            }
                                        }
                                        
                                        if (!$password_found) {
                                            echo "<div class='alert alert-warning'>⚠️ Aucun mot de passe connu ne fonctionne</div>";
                                        }
                                        
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                    
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger'>❌ Erreur lors du diagnostic: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Étape 2: Correction des mots de passe -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Étape 2: Correction des Mots de Passe</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_passwords'])) {
                                    try {
                                        echo "<div class='alert alert-info'>🔧 Début de la correction des mots de passe...</div>";
                                        
                                        // Mots de passe corrects
                                        $correct_passwords = [
                                            'Rouge' => 'Egour2023#!',
                                            'Bleu' => 'Uelb2023#!',
                                            'Vert' => 'Trev2023#!',
                                            'Jaune' => 'Enuaj2023#!'
                                        ];
                                        
                                        $updated_count = 0;
                                        
                                        foreach ($correct_passwords as $equipe_nom => $password) {
                                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                                            
                                            $stmt = $pdo->prepare("UPDATE equipes SET mot_de_passe = ? WHERE nom = ?");
                                            if ($stmt->execute([$hashed_password, $equipe_nom])) {
                                                echo "<div class='alert alert-success'>✅ Mot de passe mis à jour pour l'équipe <strong>$equipe_nom</strong> : <code>$password</code></div>";
                                                $updated_count++;
                                            } else {
                                                echo "<div class='alert alert-danger'>❌ Erreur lors de la mise à jour pour l'équipe $equipe_nom</div>";
                                            }
                                        }
                                        
                                        echo "<div class='alert alert-success'>";
                                        echo "<h5>🎉 Correction terminée !</h5>";
                                        echo "<p>$updated_count équipe(s) mise(s) à jour avec succès.</p>";
                                        echo "</div>";
                                        
                                        // Recharger la page après 3 secondes
                                        echo "<script>setTimeout(function(){ location.reload(); }, 3000);</script>";
                                        
                                    } catch (Exception $e) {
                                        echo "<div class='alert alert-danger'>❌ Erreur lors de la correction: " . $e->getMessage() . "</div>";
                                    }
                                } else {
                                    ?>
                                    
                                    <div class="alert alert-warning">
                                        <h5>⚠️ Action requise</h5>
                                        <p>Pour résoudre les problèmes d'authentification, nous devons :</p>
                                        <ul>
                                            <li>Vérifier que tous les mots de passe sont correctement hashés</li>
                                            <li>Mettre à jour les mots de passe avec les valeurs correctes</li>
                                            <li>Permettre la connexion de toutes les équipes</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>🔑 Mots de passe qui seront appliqués :</h6>
                                            <ul>
                                                <li><strong>Rouge :</strong> <code>Egour2023#!</code></li>
                                                <li><strong>Bleu :</strong> <code>Uelb2023#!</code></li>
                                                <li><strong>Vert :</strong> <code>Trev2023#!</code></li>
                                                <li><strong>Jaune :</strong> <code>Enuaj2023#!</code></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>�� Instructions :</h6>
                                            <ol>
                                                <li>Cliquez sur "Corriger les mots de passe"</li>
                                                <li>Attendez la confirmation</li>
                                                <li>Testez la connexion de toutes les équipes</li>
                                            </ol>
                                        </div>
                                    </div>
                                    
                                    <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir corriger tous les mots de passe ? Cela va mettre à jour la base de données.');">
                                        <button type="submit" name="fix_passwords" class="btn btn-warning btn-lg">
                                            🔐 Corriger tous les mots de passe
                                        </button>
                                    </form>
                                    
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Étape 3: Test après correction -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Étape 3: Test après Correction</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!isset($_POST['fix_passwords'])): ?>
                                    <div class="alert alert-info">
                                        <h5>📋 Après la correction, vous pourrez :</h5>
                                        <ol>
                                            <li><strong>Tester la connexion</strong> de toutes les équipes</li>
                                            <li><strong>Vérifier l'authentification</strong> sur la page de login</li>
                                            <li><strong>Continuer les tests</strong> du système de validation</li>
                                        </ol>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <h6>�� URLs de test :</h6>
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
                                        <h5>🎯 Test des mots de passe corrigés :</h5>
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
