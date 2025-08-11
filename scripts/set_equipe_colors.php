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
echo "<title>Définir les Couleurs d'Équipes</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-light'>";
echo "<div class='container mt-4'>";
echo "<h1>🎨 Définir les Couleurs d'Équipes</h1>";

// Traitement de la mise à jour des couleurs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'set_colors') {
        echo "<h2>Mise à jour des couleurs</h2>";
        
        try {
            $updated = 0;
            
            // Mettre à jour les couleurs selon les noms d'équipes
            $equipes_colors = [
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
                'teal' => '#20c997'
            ];
            
            foreach ($equipes_colors as $nom_equipe => $couleur) {
                $stmt = $pdo->prepare("UPDATE equipes SET couleur = ? WHERE LOWER(nom) LIKE ?");
                if ($stmt->execute([$couleur, '%' . strtolower($nom_equipe) . '%'])) {
                    if ($stmt->rowCount() > 0) {
                        echo "<div class='alert alert-success'>✅ Équipe contenant '$nom_equipe' : couleur définie à $couleur</div>";
                        $updated++;
                    }
                }
            }
            
            // Pour les équipes restantes, attribuer des couleurs par défaut
            $stmt = $pdo->query("SELECT * FROM equipes WHERE couleur IS NULL OR couleur = '' ORDER BY nom");
            $equipes_sans_couleur = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($equipes_sans_couleur)) {
                $couleurs_restantes = ['#e91e63', '#9c27b0', '#673ab7', '#3f51b5', '#2196f3', '#00bcd4', '#009688', '#4caf50', '#8bc34a', '#cddc39'];
                $couleur_index = 0;
                
                foreach ($equipes_sans_couleur as $equipe) {
                    $couleur_choisie = $couleurs_restantes[$couleur_index % count($couleurs_restantes)];
                    
                    $stmt = $pdo->prepare("UPDATE equipes SET couleur = ? WHERE id = ?");
                    if ($stmt->execute([$couleur_choisie, $equipe['id']])) {
                        echo "<div class='alert alert-info'>🎨 Équipe <strong>" . htmlspecialchars($equipe['nom']) . "</strong> : couleur définie à $couleur_choisie</div>";
                        $updated++;
                    }
                    $couleur_index++;
                }
            }
            
            if ($updated > 0) {
                echo "<div class='alert alert-success'>🎉 <strong>$updated équipes</strong> ont été mises à jour !</div>";
                echo "<div class='alert alert-info'>🔄 Rechargement dans 3 secondes...</div>";
                echo "<script>setTimeout(function(){ location.reload(); }, 3000);</script>";
            } else {
                echo "<div class='alert alert-warning'>⚠️ Aucune équipe n'a été mise à jour</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>❌ Erreur : " . $e->getMessage() . "</div>";
        }
    }
}

// Afficher l'état actuel
echo "<h2>État actuel des équipes</h2>";
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
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($equipe['id']) . "</td>";
            echo "<td>" . htmlspecialchars($equipe['nom']) . "</td>";
            echo "<td>";
            if (!empty($equipe['couleur'])) {
                echo "<span style='background-color: " . htmlspecialchars($equipe['couleur']) . "; color: white; padding: 4px 8px; border-radius: 4px; font-weight: bold;'>" . htmlspecialchars($equipe['couleur']) . "</span>";
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

// Formulaire pour définir les couleurs
if (empty($_POST['action'])) {
    echo "<h2>Définir les couleurs automatiquement</h2>";
    echo "<form method='POST' class='mb-4'>";
    echo "<input type='hidden' name='action' value='set_colors'>";
    echo "<div class='alert alert-info'>";
    echo "<h5>ℹ️ Comment ça fonctionne</h5>";
    echo "<ul>";
    echo "<li>Les équipes avec des noms contenant 'rouge', 'bleu', 'vert', etc. auront la couleur correspondante</li>";
    echo "<li>Les équipes restantes recevront automatiquement une couleur unique</li>";
    echo "<li>Ce processus est intelligent et évite les doublons</li>";
    echo "</ul>";
    echo "</div>";
    echo "<button type='submit' class='btn btn-success btn-lg'>🎨 Définir les Couleurs Automatiquement</button>";
    echo "</form>";
}

echo "<hr>";
echo "<p><a href='../admin/generate_qr.php' class='btn btn-primary'>← Retour à la génération QR</a></p>";
echo "<p><a href='../admin/admin.php' class='btn btn-secondary'>← Retour à l'administration</a></p>";
echo "</div>";
echo "</body>";
echo "</html>";
?>
