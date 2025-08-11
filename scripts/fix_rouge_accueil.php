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
    <title>Correction Statut Équipe Rouge - Accueil - Cyberchasse</title>
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
        .status-fixed { 
            background: rgba(76, 175, 80, 0.1); 
            padding: 15px; 
            border-radius: 8px; 
            margin: 10px 0;
            border-left: 4px solid #4caf50;
        }
        .status-problem { 
            background: rgba(244, 67, 54, 0.1); 
            padding: 15px; 
            border-radius: 8px; 
            margin: 10px 0;
            border-left: 4px solid #f44336;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h1>🔧 Correction Statut Équipe Rouge - Accueil</h1>
                        <p class="mb-0">Résolution du problème d'ordre de visite pour l'équipe Rouge</p>
                    </div>
                    <div class="card-body">
                        
                        <!-- Étape 1: Diagnostic du problème -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Étape 1: Diagnostic du Problème</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    // Vérifier le statut actuel de l'équipe Rouge pour l'Accueil
                                    $stmt = $pdo->prepare("
                                        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
                                        FROM parcours p
                                        JOIN equipes e ON p.equipe_id = e.id
                                        JOIN lieux l ON p.lieu_id = l.id
                                        WHERE e.nom = 'Rouge' AND l.slug = 'accueil'
                                    ");
                                    $stmt->execute();
                                    $parcours_accueil = $stmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if ($parcours_accueil) {
                                        echo "<div class='alert alert-info'>";
                                        echo "<h5>📊 État actuel du parcours Équipe Rouge → Accueil</h5>";
                                        echo "<div class='row'>";
                                        echo "<div class='col-md-6'>";
                                        echo "<p><strong>Équipe:</strong> " . htmlspecialchars($parcours_accueil['equipe_nom']) . "</p>";
                                        echo "<p><strong>Lieu:</strong> " . htmlspecialchars($parcours_accueil['lieu_nom']) . "</p>";
                                        echo "<p><strong>Ordre:</strong> " . $parcours_accueil['ordre_visite'] . "</p>";
                                        echo "</div>";
                                        echo "<div class='col-md-6'>";
                                        echo "<p><strong>Statut actuel:</strong> <span class='badge bg-warning'>" . $parcours_accueil['statut'] . "</span></p>";
                                        echo "<p><strong>Token:</strong> <code>" . substr($parcours_accueil['token_acces'], 0, 8) . "...</code></p>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                        
                                        if ($parcours_accueil['statut'] === 'en_cours') {
                                            echo "<div class='alert alert-warning'>";
                                            echo "<h5>⚠️ Problème détecté !</h5>";
                                            echo "<p>Le statut 'En cours' bloque la progression vers les étapes suivantes.</p>";
                                            echo "<p>Le système attend un statut 'Terminé' pour permettre l'accès à l'étape 2 (Cantine).</p>";
                                            echo "</div>";
                                        }
                                    } else {
                                        echo "<div class='alert alert-danger'>❌ Parcours Équipe Rouge → Accueil non trouvé</div>";
                                    }
                                    
                                    // Vérifier tous les parcours de l'équipe Rouge
                                    echo "<h5 class='mt-4'>📋 Tous les parcours de l'équipe Rouge :</h5>";
                                    $stmt = $pdo->prepare("
                                        SELECT p.*, l.nom as lieu_nom, l.slug as lieu_slug
                                        FROM parcours p
                                        JOIN lieux l ON p.lieu_id = l.id
                                        WHERE p.equipe_id = (SELECT id FROM equipes WHERE nom = 'Rouge')
                                        ORDER BY p.ordre_visite
                                    ");
                                    $stmt->execute();
                                    $parcours_rouge = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    echo "<div class='table-responsive'>";
                                    echo "<table class='table table-striped'>";
                                    echo "<thead><tr><th>Ordre</th><th>Lieu</th><th>Statut</th><th>Problème</th></tr></thead>";
                                    echo "<tbody>";
                                    
                                    foreach ($parcours_rouge as $p) {
                                        $status_class = $p['statut'] === 'en_cours' ? 'warning' : 
                                                      ($p['statut'] === 'termine' ? 'success' : 'secondary');
                                        $probleme = $p['statut'] === 'en_cours' ? 'Bloque la progression' : 
                                                   ($p['statut'] === 'termine' ? 'OK' : 'En attente');
                                        
                                        echo "<tr>";
                                        echo "<td><strong>" . $p['ordre_visite'] . "</strong></td>";
                                        echo "<td>" . htmlspecialchars($p['lieu_nom']) . "</td>";
                                        echo "<td><span class='badge bg-$status_class'>" . $p['statut'] . "</span></td>";
                                        echo "<td>" . $probleme . "</td>";
                                        echo "</tr>";
                                    }
                                    
                                    echo "</tbody></table>";
                                    echo "</div>";
                                    
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger'>❌ Erreur lors du diagnostic: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Étape 2: Correction du statut -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Étape 2: Correction du Statut</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_status'])) {
                                    try {
                                        echo "<div class='alert alert-info'>🔧 Début de la correction...</div>";
                                        
                                        // 1. Marquer l'Accueil comme terminé
                                        $stmt = $pdo->prepare("
                                            UPDATE parcours 
                                            SET statut = 'termine', temps_fin = CURRENT_TIMESTAMP 
                                            WHERE equipe_id = (SELECT id FROM equipes WHERE nom = 'Rouge') 
                                            AND lieu_id = (SELECT id FROM lieux WHERE slug = 'accueil')
                                        ");
                                        $stmt->execute();
                                        $affected = $stmt->rowCount();
                                        
                                        if ($affected > 0) {
                                            echo "<div class='status-fixed'>✅ Statut de l'Accueil changé de 'En cours' à 'Terminé'</div>";
                                        } else {
                                            echo "<div class='status-problem'>⚠️ Aucun changement effectué</div>";
                                        }
                                        
                                        // 2. Nettoyer les sessions de jeu pour l'équipe Rouge
                                        $stmt = $pdo->prepare("
                                            DELETE FROM sessions_jeu 
                                            WHERE equipe_id = (SELECT id FROM equipes WHERE nom = 'Rouge')
                                        ");
                                        $stmt->execute();
                                        echo "<div class='status-fixed'>✅ Sessions de jeu de l'équipe Rouge nettoyées</div>";
                                        
                                        // 3. Vérifier le nouveau statut
                                        $stmt = $pdo->prepare("
                                            SELECT p.statut
                                            FROM parcours p
                                            JOIN equipes e ON p.equipe_id = e.id
                                            JOIN lieux l ON p.lieu_id = l.id
                                            WHERE e.nom = 'Rouge' AND l.slug = 'accueil'
                                        ");
                                        $stmt->execute();
                                        $nouveau_statut = $stmt->fetchColumn();
                                        
                                        echo "<div class='alert alert-success'>";
                                        echo "<h5>🎉 Correction terminée !</h5>";
                                        echo "<p>Le statut de l'Accueil est maintenant : <strong>$nouveau_statut</strong></p>";
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
                                        <p>Pour résoudre le problème d'ordre de visite, nous devons :</p>
                                        <ul>
                                            <li>Changer le statut de l'<strong>Accueil</strong> de "En cours" à "Terminé"</li>
                                            <li>Nettoyer les sessions de jeu de l'équipe Rouge</li>
                                            <li>Permettre l'accès à l'étape 2 (Cantine)</li>
                                        </ul>
                                    </div>
                                    
                                    <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir marquer l\'Accueil comme terminé ? Cela va permettre la progression vers la Cantine.');">
                                        <button type="submit" name="fix_status" class="btn btn-warning btn-lg">
                                            🔧 Marquer l'Accueil comme terminé
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
                                <?php if (!isset($_POST['fix_status'])): ?>
                                    <div class="alert alert-info">
                                        <h5>📋 Après la correction, vous pourrez :</h5>
                                        <ol>
                                            <li><strong>Tester l'accès à la Cantine</strong> avec le token existant</li>
                                            <li><strong>Vérifier la progression</strong> étape par étape</li>
                                            <li><strong>Continuer le parcours</strong> normalement</li>
                                        </ol>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <h6>🔗 URL de test pour la Cantine (après correction) :</h6>
                                        <div class="alert alert-info">
                                            <code>http://localhost:8888/lieux/access.php?token=836d790e633a1fd07d6cbf4e7275e522&lieu=cantine</code>
                                        </div>
                                        <p class="text-muted">Cette URL fonctionnera une fois l'Accueil marqué comme terminé.</p>
                                    </div>
                                    
                                <?php else: ?>
                                    <div class="mt-3">
                                        <h5>🎯 Test du système corrigé :</h5>
                                        <a href="http://localhost:8888/lieux/access.php?token=836d790e633a1fd07d6cbf4e7275e522&lieu=cantine" target="_blank" class="btn btn-success btn-lg">
                                            🚀 Tester l'accès à la Cantine
                                        </a>
                                        <a href="test_access_validation.php" class="btn btn-info btn-lg ms-2">
                                            🧪 Vérifier le système
                                        </a>
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
