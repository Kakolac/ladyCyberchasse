<?php
require_once '../config/connexion.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nettoyage des Parcours de Test - Cyberchasse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h1>�� Nettoyage des Parcours de Test</h1>
                        <p class="mb-0">Suppression des parcours de test (attention : action irréversible)</p>
                    </div>
                    <div class="card-body">
                        
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_cleanup'])) {
                            try {
                                // Suppression de tous les parcours
                                $stmt = $pdo->prepare("DELETE FROM parcours");
                                $deleted_count = $stmt->execute();
                                
                                // Suppression des sessions de jeu
                                $stmt = $pdo->prepare("DELETE FROM sessions_jeu");
                                $stmt->execute();
                                
                                // Suppression des logs d'activité
                                $stmt = $pdo->prepare("DELETE FROM logs_activite");
                                $stmt->execute();
                                
                                echo "<div class='alert alert-success'>";
                                echo "<h5>🧹 Nettoyage terminé !</h5>";
                                echo "<p>• Parcours supprimés</p>";
                                echo "<p>• Sessions de jeu supprimées</p>";
                                echo "<p>• Logs d'activité supprimés</p>";
                                echo "</div>";
                                
                                echo "<div class='mt-3'>";
                                echo "<a href='create_test_parcours.php' class='btn btn-primary'>🔧 Recréer les parcours de test</a>";
                                echo "<a href='../admin/parcours.php' class='btn btn-info ms-2'>�� Gérer les parcours</a>";
                                echo "</div>";
                                
                            } catch (Exception $e) {
                                echo "<div class='alert alert-danger'>❌ Erreur lors du nettoyage: " . $e->getMessage() . "</div>";
                            }
                        } else {
                            ?>
                            
                            <div class="alert alert-warning">
                                <h5>⚠️ Attention !</h5>
                                <p>Cette action va supprimer <strong>TOUS</strong> les parcours, sessions et logs de la base de données.</p>
                                <p>Cette action est <strong>irréversible</strong> !</p>
                            </div>
                            
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>📊 État actuel de la base de données</h4>
                                </div>
                                <div class="card-body">
                                    <?php
                                    try {
                                        $stmt = $pdo->query("SELECT COUNT(*) FROM parcours");
                                        $parcours_count = $stmt->fetchColumn();
                                        
                                        $stmt = $pdo->query("SELECT COUNT(*) FROM sessions_jeu");
                                        $sessions_count = $stmt->fetchColumn();
                                        
                                        $stmt = $pdo->query("SELECT COUNT(*) FROM logs_activite");
                                        $logs_count = $stmt->fetchColumn();
                                        
                                        echo "<div class='row'>";
                                        echo "<div class='col-md-4'>";
                                        echo "<div class='alert alert-info'>📋 Parcours: $parcours_count</div>";
                                        echo "</div>";
                                        echo "<div class='col-md-4'>";
                                        echo "<div class='alert alert-info'>🎮 Sessions: $sessions_count</div>";
                                        echo "</div>";
                                        echo "<div class='col-md-4'>";
                                        echo "<div class='alert alert-info'>📝 Logs: $logs_count</div>";
                                        echo "</div>";
                                        echo "</div>";
                                        
                                    } catch (Exception $e) {
                                        echo "<div class='alert alert-danger'>❌ Erreur: " . $e->getMessage() . "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <form method="POST" onsubmit="return confirm('Êtes-vous SÛR de vouloir supprimer TOUS les parcours ? Cette action est irréversible !');">
                                <div class="text-center">
                                    <button type="submit" name="confirm_cleanup" class="btn btn-danger btn-lg">
                                        🧹 Confirmer le nettoyage complet
                                    </button>
                                    <a href="create_test_parcours.php" class="btn btn-secondary btn-lg ms-3">
                                        🔧 Retour à la création
                                    </a>
                                </div>
                            </form>
                            
                            <?php
                        }
                        ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
