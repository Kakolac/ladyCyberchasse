<?php
require_once '../config/connexion.php';

echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🧪 Test Token Manager - Cyberchasse</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<style>";
echo "body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }";
echo ".test-card { background: rgba(255,255,255,0.95); border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<div class='row justify-content-center'>";
echo "<div class='col-md-8'>";

echo "<div class='test-card p-4 mb-4'>";
echo "<h2 class='text-center mb-4'>🧪 Test du Token Manager</h2>";

// Test 1: Vérifier la connexion à la base
echo "<div class='card mb-3'>";
echo "<div class='card-header bg-primary text-white'>Test 1: Connexion Base de Données</div>";
echo "<div class='card-body'>";

try {
    $pdo->query("SELECT 1");
    echo "<div class='alert alert-success'>✅ Connexion à la base de données réussie</div>";
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur de connexion : " . $e->getMessage() . "</div>";
}

echo "</div></div>";

// Test 2: Vérifier l'existence de la table cyber_token
echo "<div class='card mb-3'>";
echo "<div class='card-header bg-info text-white'>Test 2: Table cyber_token</div>";
echo "<div class='card-body'>";

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'cyber_token'");
    if ($stmt->rowCount() > 0) {
        echo "<div class='alert alert-success'>✅ Table cyber_token existe</div>";
        
        // Vérifier la structure
        $stmt = $pdo->query("DESCRIBE cyber_token");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h6>Structure de la table :</h6>";
        echo "<ul>";
        foreach ($columns as $col) {
            echo "<li><code>{$col['Field']}</code> - {$col['Type']} ({$col['Null']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<div class='alert alert-warning'>⚠️ Table cyber_token n'existe pas</div>";
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur : " . $e->getMessage() . "</div>";
}

echo "</div></div>";

// Test 3: Vérifier les parcours existants
echo "<div class='card mb-3'>";
echo "<div class='card-header bg-success text-white'>Test 3: Parcours Disponibles</div>";
echo "<div class='card-body'>";

try {
    $stmt = $pdo->query("SELECT id, nom, statut FROM cyber_parcours ORDER BY nom");
    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($parcours) > 0) {
        echo "<div class='alert alert-success'>✅ " . count($parcours) . " parcours trouvés</div>";
        echo "<h6>Liste des parcours :</h6>";
        echo "<ul>";
        foreach ($parcours as $parc) {
            echo "<li><strong>{$parc['nom']}</strong> (ID: {$parc['id']}) - <span class='badge bg-" . ($parc['statut'] === 'actif' ? 'success' : 'secondary') . "'>{$parc['statut']}</span></li>";
        }
        echo "</ul>";
        
        echo "<div class='mt-3'>";
        echo "<a href='../admin/modules/parcours/token_manager.php?id={$parcours[0]['id']}' class='btn btn-primary'>";
        echo "<i class='fas fa-key'></i> Tester le Token Manager pour '{$parcours[0]['nom']}'";
        echo "</a>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-warning'>⚠️ Aucun parcours trouvé</div>";
        echo "<p>Créez d'abord un parcours pour tester le token manager.</p>";
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur : " . $e->getMessage() . "</div>";
}

echo "</div></div>";

// Test 4: Vérifier les équipes
echo "<div class='card mb-3'>";
echo "<div class='card-header bg-warning text-dark'>Test 4: Équipes Disponibles</div>";
echo "<div class='card-body'>";

try {
    $stmt = $pdo->query("SELECT id, nom, couleur, statut FROM cyber_equipes ORDER BY nom");
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($equipes) > 0) {
        echo "<div class='alert alert-success'>✅ " . count($equipes) . " équipes trouvées</div>";
        echo "<h6>Liste des équipes :</h6>";
        echo "<ul>";
        foreach ($equipes as $equipe) {
            echo "<li><span style='color: {$equipe['couleur'];'>●</span> <strong>{$equipe['nom']}</strong> (ID: {$equipe['id']}) - <span class='badge bg-" . ($equipe['statut'] === 'active' ? 'success' : 'secondary') . "'>{$equipe['statut']}</span></li>";
        }
        echo "</ul>";
    } else {
        echo "<div class='alert alert-warning'>⚠️ Aucune équipe trouvée</div>";
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur : " . $e->getMessage() . "</div>";
}

echo "</div></div>";

echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";

echo "</body>";
echo "</html>";
?>
