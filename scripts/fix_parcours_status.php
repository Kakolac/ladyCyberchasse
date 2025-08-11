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
    <title>Correction des Statuts des Parcours - Cyberchasse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        .status-fixed { 
            background: rgba(76, 175, 80, 0.1); 
            padding: 10px; 
            border-radius: 5px; 
            margin: 5px 0;
            border-left: 4px solid #4caf50;
        }
        .status-problem { 
            background: rgba(244, 67, 54, 0.1); 
            padding: 10px; 
            border-radius: 5px; 
            margin: 5px 0;
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
                        <h1>🔧 Correction des Statuts des Parcours</h1>
                        <p class="mb-0">Résolution du problème d'ordre de visite</p>
                    </div>
                    <div class="card-body">
                        
                        <!-- Étape 1: Diagnostic des problèmes -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Étape 1: Diagnostic des Problèmes</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    // Vérifier les parcours avec statut 'en_cours'
                                    $stmt = $pdo->query("
                                        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
                                        FROM parcours p
                                        JOIN equipes e ON p.equipe_id = e.id
                                        JOIN lieux l ON p.lieu_id = l.id
                                        WHERE p.statut = 'en_cours'
                                        ORDER BY p.equipe_id, p.ordre_visite
                                    ");
                                    $parcours_en_cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if (!empty($parcours_en_cours)) {
                                        echo "<div class='alert alert-warning'>";
                                        echo "<h5>⚠️ Problème détecté : " . count($parcours_en_cours) . " parcours en statut 'en_cours'</h5>";
                                        echo "<p>Ces parcours bloquent la progression car le système attend des statuts 'termine'</p>";
                                        echo "</div>";
                                        
                                        echo "<div class='table-responsive'>";
                                        echo "<table class='table table-striped'>";
                                        echo "<thead><tr><th>Équipe</th><th>Lieu</th><th>Ordre</th><th>Statut</th><th>Problème</th></tr></thead>";
                                        echo "<tbody>";
                                        
                                        foreach ($parcours_en_cours as $p) {
                                            echo "<tr>";
                                            echo "<td><strong>" . htmlspecialchars($p['equipe_nom']) . "</strong></td>";
                                            echo "<td>" . htmlspecialchars($p['lieu_nom']) . "</td>";
                                            echo "<td>" . $p['ordre_visite'] . "</td>";
                                            echo "<td><span class='badge bg-warning'>" . $p['statut'] . "</span></td>";
                                            echo "<td>Bloque la progression</td>";
                                            echo "</tr>";
                                        }
                                        
                                        echo "</tbody></table>";
                                        echo "</div>";
                                    } else {
                                        echo "<div class='alert alert-success'>✅ Aucun parcours en statut 'en_cours' trouvé</div>";
                                    }
                                    
                                    // Vérifier la progression des équipes
                                    echo "<h5 class='mt-4'>📊 Progression des équipes :</h5>";
                                    $stmt = $pdo->query("
                                        SELECT e.nom as equipe_nom, 
                                               COUNT(CASE WHEN p.statut = 'termine' THEN 1 END) as lieux_termines,
                                               COUNT(CASE WHEN p.statut = 'en_cours' THEN 1 END) as lieux_en_cours,
                                               COUNT(CASE WHEN p.statut = 'en_attente' THEN 1 END) as lieux_en_attente,
                                               COUNT(*) as total_lieux
                                        FROM equipes e
                                        LEFT JOIN parcours p ON e.id = p.equipe_id
                                        GROUP BY e.id, e.nom
                                        ORDER BY e.nom
                                    ");
                                    $progression = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($progression as $prog) {
                                        $status_class = $prog['lieux_en_cours'] > 0 ? 'warning' : 'success';
                                        echo "<div class='alert alert-$status_class'>";
                                        echo "<strong>" . htmlspecialchars($prog['equipe_nom']) . "</strong> : ";
                                        echo $prog['lieux_termines'] . " terminés, ";
                                        echo $prog['lieux_en_cours'] . " en cours, ";
                                        echo $prog['lieux_en_attente'] . " en attente ";
                                        echo "(" . $prog['total_lieux'] . " total)";
                                        echo "</div>";
                                    }
                                    
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger'>❌ Erreur lors du diagnostic: " . $e->getMessage() . "</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Étape 2: Correction automatique -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Étape 2: Correction Automatique</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_status'])) {
                                    try {
                                        echo "<div class='alert alert-info'>🔧 Début de la correction automatique...</div>";
                                        
                                        // 1. Remettre tous les parcours en 'en_attente'
                                        $stmt = $pdo->prepare("UPDATE parcours SET statut = 'en_attente' WHERE statut = 'en_cours'");
                                        $updated = $stmt->execute();
                                        $affected = $stmt->rowCount();
                                        
                                        echo "<div class='status-fixed'>✅ $affected parcours remis en statut 'en_attente'</div>";
                                        
                                        // 2. Nettoyer les sessions de jeu
                                        $stmt = $pdo->prepare("DELETE FROM sessions_jeu");
                                        $stmt->execute();
                                        echo "<div class='status-fixed'>✅ Sessions de jeu nettoyées</div>";
                                        
                                        // 3. Nettoyer les logs d'activité
                                        $stmt = $pdo->prepare("DELETE FROM logs_activite");
                                        $stmt->execute();
                                        echo "<div class='status-fixed'>✅ Logs d'activité nettoyés</div>";
                                        
                                        echo "<div class='alert alert-success'>🎉 Correction terminée ! Le système est maintenant prêt.</div>";
                                        
                                        // Recharger la page pour montrer le nouvel état
                                        echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";
                                        
                                    } catch (Exception $e) {
                                        echo "<div class='alert alert-danger'>❌ Erreur lors de la correction: " . $e->getMessage() . "</div>";
                                    }
                                } else {
                                    ?>
                                    
                                    <div class="alert alert-warning">
                                        <h5>⚠️ Action requise</h5>
                                        <p>Pour résoudre le problème d'ordre de visite, nous devons :</p>
                                        <ul>
                                            <li>Remettre tous les parcours en statut 'en_attente'</li>
                                            <li>Nettoyer les sessions de jeu bloquées</li>
                                            <li>Réinitialiser les logs d'activité</li>
                                        </ul>
                                    </div>
                                    
                                    <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir corriger les statuts ? Cela va réinitialiser la progression.');">
                                        <button type="submit" name="fix_status" class="btn btn-warning btn-lg">
                                            🔧 Corriger automatiquement les statuts
                                        </button>
                                    </form>
                                    
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Étape 3: Vérification après correction -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>Étape 3: Vérification et Test</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!isset($_POST['fix_status'])): ?>
                                    <div class="alert alert-info">
                                        <h5>📋 Après la correction, vous pourrez :</h5>
                                        <ol>
                                            <li><strong>Tester le système de validation</strong> : <a href="test_access_validation.php" class="btn btn-primary btn-sm">🧪 Tester la validation</a></li>
                                            <li><strong>Gérer les parcours</strong> : <a href="../admin/parcours.php" class="btn btn-info btn-sm"> Gérer les parcours</a></li>
                                            <li><strong>Générer des QR codes</strong> : <a href="../admin/generate_qr.php" class="btn btn-success btn-sm">�� Générer QR codes</a></li>
                                        </ol>
                                    </div>
                                <?php else: ?>
                                    <div class="mt-3">
                                        <h5>🎯 Test du système corrigé :</h5>
                                        <a href="test_access_validation.php" class="btn btn-success btn-lg">🧪 Tester le système de validation</a>
                                        <a href="../admin/parcours.php" class="btn btn-info btn-lg ms-2"> Gérer les parcours</a>
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
