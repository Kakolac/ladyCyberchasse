<?php
session_start();
require_once '../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Correction des Couleurs d'Équipes</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-light'>";
echo "<div class='container mt-4'>";
echo "<h1>🎨 Correction des Couleurs d'Équipes</h1>";

// Vérifier l'état actuel des équipes
echo "<h2>1. État actuel des équipes</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM equipes ORDER BY nom");
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($equipes)) {
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped'>";
        echo "<thead><tr>";
        echo "<th>ID</th>";
        echo "<th>Nom</th>";
        echo "<th>Couleur actuelle</th>";
        echo "<th>Statut</th>";
        echo "</tr></thead>";
        echo "<tbody>";
        
        foreach ($equipes as $equipe) {
            $couleur = $equipe['couleur'] ?? 'Non définie';
            $status = !empty($equipe['couleur']) ? '✅ OK' : '⚠️ Manquante';
            $couleur_display = !empty($equipe['couleur']) ? $equipe['couleur'] : 'Non définie';
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($equipe['id']) . "</td>";
            echo "<td>" . htmlspecialchars($equipe['nom']) . "</td>";
            echo "<td>";
            if (!empty($equipe['couleur'])) {
                echo "<span style='background-color: " . htmlspecialchars($equipe['couleur']) . "; color: white; padding: 4px 8px; border-radius: 4px;'>" . htmlspecialchars($equipe['couleur']) . "</span>";
            } else {
                echo "<span class='text-muted'>Non définie</span>";
            }
            echo "</td>";
            echo "<td>" . $status . "</td>";
            echo "</tr>";
        }
        
        echo "</tbody></table>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-warning'>Aucune équipe trouvée</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
}

// Définir des couleurs par défaut pour les équipes
$couleurs_defaut = [
    'rouge' => '#dc3545',
    'bleu' => '#007bff',
    'vert' => '#28a745',
    'jaune' => '#ffc107',
    'orange' => '#fd7e14',
    'violet' => '#6f42c1',
    'cyan' => '#17a2b8',
    'rose' => '#e83e8c',
    'gris' => '#6c757d',
    'indigo' => '#6610f2'
];

echo "<h2>2. Couleurs par défaut disponibles</h2>";
echo "<div class='row'>";
foreach ($couleurs_defaut as $nom => $couleur) {
    echo "<div class='col-md-3 mb-2'>";
    echo "<div style='background-color: " . $couleur . "; color: white; padding: 10px; border-radius: 8px; text-align: center;'>";
    echo "<strong>" . ucfirst($nom) . "</strong><br>";
    echo "<small>" . $couleur . "</small>";
    echo "</div>";
    echo "</div>";
}
echo "</div>";

// Traitement de la correction des couleurs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'fix_colors') {
        echo "<h2>3. Correction des couleurs</h2>";
        
        try {
            // Récupérer toutes les équipes sans couleur
            $stmt = $pdo->query("SELECT * FROM equipes WHERE couleur IS NULL OR couleur = '' ORDER BY nom");
            $equipes_sans_couleur = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($equipes_sans_couleur)) {
                $couleurs_keys = array_keys($couleurs_defaut);
                $couleur_index = 0;
                
                foreach ($equipes_sans_couleur as $equipe) {
                    $couleur_choisie = $couleurs_defaut[$couleurs_keys[$couleur_index % count($couleurs_keys)]];
                    
                    $stmt = $pdo->prepare("UPDATE equipes SET couleur = ? WHERE id = ?");
                    if ($stmt->execute([$couleur_choisie, $equipe['id']])) {
                        echo "<div class='alert alert-success'>✅ Équipe <strong>" . htmlspecialchars($equipe['nom']) . "</strong> : couleur définie à " . $couleur_choisie . "</div>";
                    } else {
                        echo "<div class='alert alert-danger'>❌ Erreur lors de la mise à jour de l'équipe " . htmlspecialchars($equipe['nom']) . "</div>";
                    }
                    
                    $couleur_index++;
                }
                
                echo "<div class='alert alert-info'>🔄 Rechargement des données...</div>";
                echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";
                
            } else {
                echo "<div class='alert alert-success'>✅ Toutes les équipes ont déjà une couleur définie !</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>❌ Erreur : " . $e->getMessage() . "</div>";
        }
    }
}

// Formulaire pour corriger les couleurs
if (empty($_POST['action'])) {
    echo "<h2>3. Corriger les couleurs manquantes</h2>";
    echo "<form method='POST' class='mb-4'>";
    echo "<input type='hidden' name='action' value='fix_colors'>";
    echo "<div class='alert alert-info'>";
    echo "<h5>ℹ️ Information</h5>";
    echo "<p>Ce script va automatiquement attribuer des couleurs aux équipes qui n'en ont pas.</p>";
    echo "<p>Les couleurs seront choisies dans la palette prédéfinie ci-dessus.</p>";
    echo "</div>";
    echo "<button type='submit' class='btn btn-warning btn-lg'>🎨 Corriger les Couleurs Manquantes</button>";
    echo "</form>";
}

echo "<hr>";
echo "<p><a href='../admin/generate_qr.php' class='btn btn-primary'>← Retour à la génération QR</a></p>";
echo "<p><a href='../admin/admin.php' class='btn btn-secondary'>← Retour à l'administration</a></p>";
echo "</div>";
echo "</body>";
echo "</html>";
?>
