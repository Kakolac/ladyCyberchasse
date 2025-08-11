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
echo "<title>Vérification des Couleurs d'Équipes</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-light'>";
echo "<div class='container mt-4'>";
echo "<h1>🔍 Vérification des Couleurs d'Équipes</h1>";

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
        echo "<th>Type de couleur</th>";
        echo "<th>Statut</th>";
        echo "</tr></thead>";
        echo "<tbody>";
        
        foreach ($equipes as $equipe) {
            $couleur = $equipe['couleur'] ?? 'Non définie';
            $is_hex = preg_match('/^#[0-9A-Fa-f]{6}$/', $couleur);
            $type = $is_hex ? 'Hexadécimal' : 'Texte';
            $status = $is_hex ? '✅ OK' : '⚠️ À corriger';
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($equipe['id']) . "</td>";
            echo "<td>" . htmlspecialchars($equipe['nom']) . "</td>";
            echo "<td>";
            if (!empty($equipe['couleur'])) {
                if ($is_hex) {
                    echo "<span style='background-color: " . htmlspecialchars($equipe['couleur']) . "; color: white; padding: 4px 8px; border-radius: 4px; font-weight: bold;'>" . htmlspecialchars($equipe['couleur']) . "</span>";
                } else {
                    echo "<span class='text-muted'>" . htmlspecialchars($equipe['couleur']) . "</span>";
                }
            } else {
                echo "<span class='text-muted'>Non définie</span>";
            }
            echo "</td>";
            echo "<td>" . $type . "</td>";
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

// Traitement de la correction des couleurs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'fix_colors') {
        echo "<h2>2. Correction des couleurs</h2>";
        
        try {
            $updated = 0;
            
            // Mapping des noms de couleurs vers les valeurs hexadécimales
            $couleurs_mapping = [
                'rouge' => '#dc3545',
                'bleu' => '#007bff',
                'vert' => '#28a745',
                'jaune' => '#ffc107',
                'orange' => '#fd7e14',
                'violet' => '#6f42c1',
                'cyan' => '#17a2b8',
                'rose' => '#e83e8c',
                'gris' => '#6c757d',
                'indigo' => '#6610f2',
                'marron' => '#795548',
                'teal' => '#20c997',
                'noir' => '#343a40',
                'blanc' => '#f8f9fa'
            ];
            
            // Corriger les couleurs qui sont des noms au lieu de valeurs hexadécimales
            foreach ($couleurs_mapping as $nom_couleur => $couleur_hex) {
                $stmt = $pdo->prepare("UPDATE equipes SET couleur = ? WHERE LOWER(couleur) = ?");
                if ($stmt->execute([$couleur_hex, strtolower($nom_couleur)])) {
                    if ($stmt->rowCount() > 0) {
                        echo "<div class='alert alert-success'>✅ Couleur '$nom_couleur' convertie en $couleur_hex</div>";
                        $updated++;
                    }
                }
            }
            
            // Pour les équipes sans couleur, attribuer une couleur par défaut
            $stmt = $pdo->query("SELECT * FROM equipes WHERE couleur IS NULL OR couleur = '' ORDER BY nom");
            $equipes_sans_couleur = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($equipes_sans_couleur)) {
                $couleurs_defaut = ['#e91e63', '#9c27b0', '#673ab7', '#3f51b5', '#2196f3', '#00bcd4', '#009688', '#4caf50', '#8bc34a', '#cddc39'];
                $couleur_index = 0;
                
                foreach ($equipes_sans_couleur as $equipe) {
                    $couleur_choisie = $couleurs_defaut[$couleur_index % count($couleurs_defaut)];
                    
                    $stmt = $pdo->prepare("UPDATE equipes SET couleur = ? WHERE id = ?");
                    if ($stmt->execute([$couleur_choisie, $equipe['id']])) {
                        echo "<div class='alert alert-info'>🎨 Équipe <strong>" . htmlspecialchars($equipe['nom']) . "</strong> : couleur définie à $couleur_choisie</div>";
                        $updated++;
                    }
                    $couleur_index++;
                }
            }
            
            if ($updated > 0) {
                echo "<div class='alert alert-success'>🎉 <strong>$updated mises à jour</strong> effectuées !</div>";
                echo "<div class='alert alert-info'>🔄 Rechargement dans 3 secondes...</div>";
                echo "<script>setTimeout(function(){ location.reload(); }, 3000);</script>";
            } else {
                echo "<div class='alert alert-warning'>⚠️ Aucune mise à jour nécessaire</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>❌ Erreur : " . $e->getMessage() . "</div>";
        }
    }
}

// Formulaire pour corriger les couleurs
if (empty($_POST['action'])) {
    echo "<h2>2. Corriger les couleurs</h2>";
    echo "<form method='POST' class='mb-4'>";
    echo "<input type='hidden' name='action' value='fix_colors'>";
    echo "<div class='alert alert-info'>";
    echo "<h5>ℹ️ Ce que fait ce script</h5>";
    echo "<ul>";
    echo "<li>Convertit les noms de couleurs (rouge, bleu, vert...) en valeurs hexadécimales</li>";
    echo "<li>Attribue des couleurs aux équipes qui n'en ont pas</li>";
    echo "<li>Assure que toutes les couleurs sont au format #RRGGBB</li>";
    echo "</ul>";
    echo "</div>";
    echo "<button type='submit' class='btn btn-warning btn-lg'>🔧 Corriger les Couleurs</button>";
    echo "</form>";
}

echo "<hr>";
echo "<p><a href='../admin/generate_qr.php' class='btn btn-primary'>← Retour à la génération QR</a></p>";
echo "<p><a href='../admin/admin.php' class='btn btn-secondary'>← Retour à l'administration</a></p>";
echo "</div>";
echo "</body>";
echo "</html>";
?>
