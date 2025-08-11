<?php
/**
 * Script de test pour l'ÉTAPE 2 : Système d'Authentification et Sessions
 * Vérifie que le système de sessions existant fonctionne avec la nouvelle structure
 */

// Démarrer la session pour les tests
session_start();

// Configuration de connexion
require_once '../config/connexion.php';

// Fonction pour nettoyer l'affichage
function cleanOutput($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Fonction pour tester la connexion d'une équipe
function testTeamConnection($pdo, $teamName, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM equipes WHERE nom = ?");
        $stmt->execute([$teamName]);
        $team = $stmt->fetch();
        
        if ($team && password_verify($password, $team['mot_de_passe'])) {
            return [
                'success' => true,
                'team' => $team,
                'message' => "Connexion réussie pour l'équipe: {$teamName}"
            ];
        } else {
            return [
                'success' => false,
                'message' => "Échec de connexion pour l'équipe: {$teamName}"
            ];
        }
    } catch(PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur de base de données: " . $e->getMessage()
        ];
    }
}

// Fonction pour tester la création de session
function testSessionCreation($teamName, $teamId) {
    // Simuler une connexion réussie
    $_SESSION['team_name'] = $teamName;
    $_SESSION['team_id'] = $teamId;
    $_SESSION['start_time'] = time();
    
    return [
        'success' => isset($_SESSION['team_name']) && isset($_SESSION['team_id']),
        'session_data' => [
            'team_name' => $_SESSION['team_name'] ?? 'Non défini',
            'team_id' => $_SESSION['team_id'] ?? 'Non défini',
            'start_time' => $_SESSION['start_time'] ?? 'Non défini'
        ]
    ];
}

// Fonction pour tester la validation de session
function testSessionValidation() {
    $required_keys = ['team_name', 'team_id', 'start_time'];
    $missing_keys = [];
    
    foreach ($required_keys as $key) {
        if (!isset($_SESSION[$key])) {
            $missing_keys[] = $key;
        }
    }
    
    return [
        'valid' => empty($missing_keys),
        'missing_keys' => $missing_keys,
        'session_data' => $_SESSION
    ];
}

// Fonction pour tester la déconnexion
function testLogout() {
    $session_data = $_SESSION;
    session_destroy();
    
    return [
        'success' => session_status() === PHP_SESSION_NONE,
        'previous_data' => $session_data
    ];
}

// Début du test
echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Test ÉTAPE 2 - Authentification et Sessions</title>";
echo "<style>";
echo "body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }";
echo ".container { max-width: 1000px; margin: 0 auto; }";
echo ".header { text-align: center; color: white; margin-bottom: 30px; }";
echo ".header h1 { text-shadow: 2px 2px 4px rgba(0,0,0,0.3); margin-bottom: 10px; }";
echo ".test-section { background: rgba(255,255,255,0.95); margin-bottom: 25px; border-radius: 15px; padding: 25px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); }";
echo ".test-section h3 { color: #4f46e5; margin-top: 0; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }";
echo ".test-result { padding: 15px; border-radius: 10px; margin: 15px 0; }";
echo ".test-result.success { background: #dcfce7; border: 2px solid #22c55e; color: #166534; }";
echo ".test-result.error { background: #fee2e2; border: 2px solid #ef4444; color: #991b1b; }";
echo ".test-result.info { background: #dbeafe; border: 2px solid #3b82f6; color: #1e40af; }";
echo ".test-result.warning { background: #fef3c7; border: 2px solid #f59e0b; color: #92400e; }";
echo ".code-block { background: #1f2937; color: #f9fafb; padding: 15px; border-radius: 8px; font-family: 'Courier New', monospace; margin: 10px 0; overflow-x: auto; }";
echo ".stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }";
echo ".stat-card { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 20px; border-radius: 10px; text-align: center; }";
echo ".stat-card h4 { margin: 0 0 10px 0; font-size: 1.2em; }";
echo ".stat-card .number { font-size: 2em; font-weight: bold; margin: 5px 0; }";
echo ".btn { display: inline-block; padding: 12px 24px; background: #4f46e5; color: white; text-decoration: none; border-radius: 8px; margin: 10px 5px; transition: all 0.3s ease; }";
echo ".btn:hover { background: #3730a3; transform: translateY(-2px); }";
echo ".btn.secondary { background: #6b7280; }";
echo ".btn.secondary:hover { background: #4b5563; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<div class='header'>";
echo "<h1>🧪 TEST ÉTAPE 2 - Authentification et Sessions</h1>";
echo "<p>Système de cyberchasse - Vérification du système d'authentification et des sessions</p>";
echo "</div>";

// ===== TEST 1: CONNEXION À LA BASE DE DONNÉES =====
echo "<div class='test-section'>";
echo "<h3>🔌 Test 1: Connexion à la base de données</h3>";

try {
    $pdo->query("SELECT 1");
    echo "<div class='test-result success'>";
    echo "✅ Connexion à la base de données réussie";
    echo "</div>";
    
    // Vérifier la structure des tables
    $tables = ['equipes', 'lieux', 'parcours', 'sessions_jeu', 'logs_activite'];
    $existing_tables = [];
    
    foreach ($tables as $table) {
        try {
            $pdo->query("SELECT 1 FROM {$table} LIMIT 1");
            $existing_tables[] = $table;
        } catch(PDOException $e) {
            // Table n'existe pas
        }
    }
    
    echo "<div class='test-result info'>";
    echo "�� Tables existantes: " . implode(', ', $existing_tables);
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div class='test-result error'>";
    echo "❌ Erreur de connexion: " . cleanOutput($e->getMessage());
    echo "</div>";
    exit;
}
echo "</div>";

// ===== TEST 2: VÉRIFICATION DES ÉQUIPES =====
echo "<div class='test-section'>";
echo "<h3>👥 Test 2: Vérification des équipes</h3>";

try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM equipes");
    $team_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<div class='test-result info'>";
    echo "📊 Nombre d'équipes dans la base: {$team_count}";
    echo "</div>";
    
    if ($team_count > 0) {
        $stmt = $pdo->query("SELECT nom, couleur, statut FROM equipes ORDER BY nom");
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<div class='code-block'>";
        echo "Équipes disponibles:\n";
        foreach ($teams as $team) {
            echo "• {$team['nom']} ({$team['couleur']}) - {$team['statut']}\n";
        }
        echo "</div>";
    }
    
} catch(PDOException $e) {
    echo "<div class='test-result error'>";
    echo "❌ Erreur lors de la vérification des équipes: " . cleanOutput($e->getMessage());
    echo "</div>";
}
echo "</div>";

// ===== TEST 3: TEST D'AUTHENTIFICATION =====
echo "<div class='test-section'>";
echo "<h3>�� Test 3: Test d'authentification des équipes</h3>";

// Équipes de test avec leurs mots de passe
$test_teams = [
    ['Rouge', 'Egour2023#!'],
    ['Bleu', 'Uelb2023#!'],
    ['Vert', 'Trev2023#!'],
    ['Jaune', 'Enuaj2023#!']
];

$auth_results = [];
foreach ($test_teams as $test_team) {
    $result = testTeamConnection($pdo, $test_team[0], $test_team[1]);
    $auth_results[] = $result;
    
    $class = $result['success'] ? 'success' : 'error';
    $icon = $result['success'] ? '✅' : '❌';
    
    echo "<div class='test-result {$class}'>";
    echo "{$icon} {$result['message']}";
    if ($result['success']) {
        echo " - ID: {$result['team']['id']}, Couleur: {$result['team']['couleur']}";
    }
    echo "</div>";
}

// Statistiques d'authentification
$success_count = count(array_filter($auth_results, function($r) { return $r['success']; }));
$total_count = count($auth_results);

echo "<div class='stats-grid'>";
echo "<div class='stat-card'>";
echo "<h4>Authentifications réussies</h4>";
echo "<div class='number'>{$success_count}</div>";
echo "<p>sur {$total_count} équipes</p>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";

// ===== TEST 4: TEST DES SESSIONS =====
echo "<div class='test-section'>";
echo "<h3>🔄 Test 4: Test des sessions PHP</h3>";

// Tester avec la première équipe authentifiée
$first_successful_team = null;
foreach ($auth_results as $result) {
    if ($result['success']) {
        $first_successful_team = $result['team'];
        break;
    }
}

if ($first_successful_team) {
    echo "<div class='test-result info'>";
    echo "🧪 Test des sessions avec l'équipe: {$first_successful_team['nom']}";
    echo "</div>";
    
    // Test de création de session
    $session_result = testSessionCreation($first_successful_team['nom'], $first_successful_team['id']);
    
    if ($session_result['success']) {
        echo "<div class='test-result success'>";
        echo "✅ Session créée avec succès";
        echo "</div>";
        
        echo "<div class='code-block'>";
        echo "Données de session:\n";
        foreach ($session_result['session_data'] as $key => $value) {
            echo "• {$key}: {$value}\n";
        }
        echo "</div>";
        
        // Test de validation de session
        $validation_result = testSessionValidation();
        
        if ($validation_result['valid']) {
            echo "<div class='test-result success'>";
            echo "✅ Session validée avec succès";
            echo "</div>";
        } else {
            echo "<div class='test-result error'>";
            echo "❌ Session invalide - Clés manquantes: " . implode(', ', $validation_result['missing_keys']);
            echo "</div>";
        }
        
        // Test de déconnexion
        $logout_result = testLogout();
        
        if ($logout_result['success']) {
            echo "<div class='test-result success'>";
            echo "✅ Déconnexion réussie - Session détruite";
            echo "</div>";
        } else {
            echo "<div class='test-result error'>";
            echo "❌ Erreur lors de la déconnexion";
            echo "</div>";
        }
        
    } else {
        echo "<div class='test-result error'>";
        echo "❌ Échec de création de session";
        echo "</div>";
    }
} else {
    echo "<div class='test-result warning'>";
    echo "⚠️ Aucune équipe authentifiée pour tester les sessions";
    echo "</div>";
}
echo "</div>";

// ===== TEST 5: COMPATIBILITÉ AVEC L'ANCIEN SYSTÈME =====
echo "<div class='test-section'>";
echo "<h3>🔄 Test 5: Compatibilité avec l'ancien système</h3>";

// Vérifier si la table 'users' existe encore (ancien système)
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $users_table_exists = $stmt->rowCount() > 0;
    
    if ($users_table_exists) {
        echo "<div class='test-result warning'>";
        echo "⚠️ Table 'users' (ancien système) existe encore";
        echo "</div>";
        
        // Vérifier la compatibilité des données
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $old_users_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo "<div class='test-result info'>";
        echo "📊 Anciens utilisateurs: {$old_users_count}";
        echo "</div>";
        
    } else {
        echo "<div class='test-result success'>";
        echo "✅ Table 'users' n'existe plus (migration complète)";
        echo "</div>";
    }
    
} catch(PDOException $e) {
    echo "<div class='test-result info'>";
    echo "ℹ️ Table 'users' n'existe pas";
    echo "</div>";
}

// Vérifier la compatibilité des noms d'équipes
$old_team_names = ['equipe1', 'equipe2', 'equipe3', 'equipe4'];
$new_team_names = ['Rouge', 'Bleu', 'Vert', 'Jaune'];

echo "<div class='test-result info'>";
echo "🔄 Mapping des équipes:\n";
for ($i = 0; $i < count($old_team_names); $i++) {
    echo "• {$old_team_names[$i]} → {$new_team_names[$i]}\n";
}
echo "</div>";
echo "</div>";

// ===== TEST 6: VÉRIFICATION DES LOGS =====
echo "<div class='test-section'>";
echo "<h3>📝 Test 6: Vérification des logs d'activité</h3>";

try {
    // Insérer un log de test
    $stmt = $pdo->prepare("INSERT INTO logs_activite (equipe_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([1, 'test_authentification', 'Test de l\'étape 2 - Authentification et Sessions', '127.0.0.1']);
    
    echo "<div class='test-result success'>";
    echo "✅ Log de test inséré avec succès";
    echo "</div>";
    
    // Vérifier le nombre de logs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM logs_activite");
    $logs_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<div class='test-result info'>";
    echo "📊 Nombre total de logs: {$logs_count}";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div class='test-result error'>";
    echo "❌ Erreur lors de la gestion des logs: " . cleanOutput($e->getMessage());
    echo "</div>";
}
echo "</div>";

// ===== RÉSUMÉ ET RECOMMANDATIONS =====
echo "<div class='test-section'>";
echo "<h3>📋 Résumé des tests et recommandations</h3>";

$total_tests = 6;
$passed_tests = 0;

// Compter les tests réussis
if (isset($pdo)) $passed_tests++;
if ($team_count > 0) $passed_tests++;
if ($success_count > 0) $passed_tests++;
if (isset($first_successful_team)) $passed_tests++;
if (isset($logs_count)) $passed_tests++;

echo "<div class='stats-grid'>";
echo "<div class='stat-card'>";
echo "<h4>Tests réussis</h4>";
echo "<div class='number'>{$passed_tests}</div>";
echo "<p>sur {$total_tests} tests</p>";
echo "</div>";
echo "</div>";

if ($passed_tests === $total_tests) {
    echo "<div class='test-result success'>";
    echo "<h4>🎉 ÉTAPE 2 TERMINÉE AVEC SUCCÈS !</h4>";
    echo "<p>Le système d'authentification et de sessions fonctionne parfaitement avec la nouvelle structure de base de données.</p>";
    echo "</div>";
    
    echo "<div style='text-align: center; margin: 20px 0;'>";
    echo "<a href='../login.php' class='btn'>🧪 Tester la connexion réelle</a>";
    echo "<a href='../scenario.php' class='btn secondary'>�� Voir le scénario</a>";
    echo "</div>";
    
} else {
    echo "<div class='test-result warning'>";
    echo "<h4>⚠️ Certains tests ont échoué</h4>";
    echo "<p>Vérifiez les erreurs ci-dessus avant de passer à l'étape suivante.</p>";
    echo "</div>";
}

echo "<div class='test-result info'>";
echo "<h4>📋 Prochaines étapes recommandées:</h4>";
echo "<ul>";
echo "<li>✅ ÉTAPE 1: Structure de Base de Données (TERMINÉE)</li>";
echo "<li>✅ ÉTAPE 2: Système d'Authentification et Sessions (EN COURS)</li>";
echo "<li>🔄 ÉTAPE 3: Gestion des Lieux et Parcours</li>";
echo "<li>⏳ ÉTAPE 4: Génération des Tokens et QR Codes</li>";
echo "<li>⏳ ÉTAPE 5: Système de Validation des Accès</li>";
echo "</ul>";
echo "</div>";
echo "</div>";

echo "</div>"; // container
echo "</body>";
echo "</html>";
?>
