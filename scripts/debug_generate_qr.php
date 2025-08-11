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
echo "<title>Debug - Génération QR Codes</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-light'>";
echo "<div class='container mt-4'>";
echo "<h1>🐛 Debug - Génération QR Codes</h1>";

// Test de la requête principale
echo "<h2>1. Test de la requête principale</h2>";
try {
    $stmt = $pdo->query("
        SELECT p.*, e.nom as equipe_nom, e.couleur as equipe_couleur, l.nom as lieu_nom, l.slug as lieu_slug
        FROM parcours p
        JOIN equipes e ON p.equipe_id = e.id
        JOIN lieux l ON p.lieu_id = l.id
        ORDER BY p.equipe_id, p.ordre_visite
    ");
    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='alert alert-success'>✅ Requête exécutée avec succès</div>";
    echo "<p><strong>Nombre de parcours trouvés :</strong> " . count($parcours) . "</p>";
    
    if (!empty($parcours)) {
        echo "<h3>Premier parcours :</h3>";
        echo "<pre>" . print_r($parcours[0], true) . "</pre>";
        
        echo "<h3>Tous les parcours :</h3>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped'>";
        echo "<thead><tr>";
        echo "<th>ID</th>";
        echo "<th>Équipe ID</th>";
        echo "<th>Équipe Nom</th>";
        echo "<th>Équipe Couleur</th>";
        echo "<th>Lieu ID</th>";
        echo "<th>Lieu Nom</th>";
        echo "<th>Ordre</th>";
        echo "<th>Statut</th>";
        echo "</tr></thead>";
        echo "<tbody>";
        
        foreach ($parcours as $parcour) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($parcour['id']) . "</td>";
            echo "<td>" . htmlspecialchars($parcour['equipe_id']) . "</td>";
            echo "<td>" . htmlspecialchars($parcour['equipe_nom'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($parcour['equipe_couleur'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($parcour['lieu_id']) . "</td>";
            echo "<td>" . htmlspecialchars($parcour['lieu_nom'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($parcour['ordre_visite']) . "</td>";
            echo "<td>" . htmlspecialchars($parcour['statut']) . "</td>";
            echo "</tr>";
        }
        
        echo "</tbody></table>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-warning'>⚠️ Aucun parcours trouvé</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur : " . $e->getMessage() . "</div>";
}

// Test de la table équipes
echo "<h2>2. Test de la table équipes</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM equipes ORDER BY nom");
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='alert alert-success'>✅ Équipes récupérées avec succès</div>";
    echo "<p><strong>Nombre d'équipes :</strong> " . count($equipes) . "</p>";
    
    if (!empty($equipes)) {
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped'>";
        echo "<thead><tr>";
        echo "<th>ID</th>";
        echo "<th>Nom</th>";
        echo "<th>Couleur</th>";
        echo "<th>Mot de passe</th>";
        echo "</tr></thead>";
        echo "<tbody>";
        
        foreach ($equipes as $equipe) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($equipe['id']) . "</td>";
            echo "<td>" . htmlspecialchars($equipe['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($equipe['couleur']) . "</td>";
            echo "<td>" . htmlspecialchars($equipe['mot_de_passe']) . "</td>";
            echo "</tr>";
        }
        
        echo "</tbody></table>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur : " . $e->getMessage() . "</div>";
}

// Test de la table lieux
echo "<h2>3. Test de la table lieux</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM lieux ORDER BY ordre LIMIT 5");
    $lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='alert alert-success'>✅ Lieux récupérés avec succès</div>";
    echo "<p><strong>Nombre de lieux (limité à 5) :</strong> " . count($lieux) . "</p>";
    
    if (!empty($lieux)) {
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped'>";
        echo "<thead><tr>";
        echo "<th>ID</th>";
        echo "<th>Nom</th>";
        echo "<th>Slug</th>";
        echo "<th>Ordre</th>";
        echo "</tr></thead>";
        echo "<tbody>";
        
        foreach ($lieux as $lieu) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($lieu['id']) . "</td>";
            echo "<td>" . htmlspecialchars($lieu['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($lieu['slug']) . "</td>";
            echo "<td>" . htmlspecialchars($lieu['ordre']) . "</td>";
            echo "</tr>";
        }
        
        echo "</tbody></table>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur : " . $e->getMessage() . "</div>";
}

// Test de la table parcours
echo "<h2>4. Test de la table parcours</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM parcours LIMIT 5");
    $parcours_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='alert alert-success'>✅ Parcours bruts récupérés avec succès</div>";
    echo "<p><strong>Nombre de parcours (limité à 5) :</strong> " . count($parcours_raw) . "</p>";
    
    if (!empty($parcours_raw)) {
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped'>";
        echo "<thead><tr>";
        echo "<th>ID</th>";
        echo "<th>Équipe ID</th>";
        echo "<th>Lieu ID</th>";
        echo "<th>Ordre</th>";
        echo "<th>Statut</th>";
        echo "<th>Token</th>";
        echo "</tr></thead>";
        echo "<tbody>";
        
        foreach ($parcours_raw as $parcour) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($parcour['id']) . "</td>";
            echo "<td>" . htmlspecialchars($parcour['equipe_id']) . "</td>";
            echo "<td>" . htmlspecialchars($parcour['lieu_id']) . "</td>";
            echo "<td>" . htmlspecialchars($parcour['ordre_visite']) . "</td>";
            echo "<td>" . htmlspecialchars($parcour['statut']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($parcour['token_acces'], 0, 20)) . "...</td>";
            echo "</tr>";
        }
        
        echo "</tbody></table>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur : " . $e->getMessage() . "</div>";
}

echo "<hr>";
echo "<p><a href='../admin/generate_qr.php' class='btn btn-primary'>← Retour à la génération QR</a></p>";
echo "</div>";
echo "</body>";
echo "</html>";
?>
