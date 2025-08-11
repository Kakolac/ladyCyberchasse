<?php
require_once '../config/connexion.php';

// Récupération de l'URL du site depuis l'environnement
$siteUrl = env('URL_SITE', 'http://127.0.0.1:8888');

echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Test Génération QR Codes - Cyberchasse</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<script src='https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js'></script>";
echo "<style>";
echo ".test-container { padding: 2rem; }";
echo ".qr-test { text-align: center; margin: 2rem 0; padding: 1rem; border: 2px solid #dee2e6; border-radius: 10px; }";
echo ".token-display { background: #f8f9fa; padding: 1rem; border-radius: 5px; font-family: monospace; margin: 1rem 0; }";
echo ".url-display { background: #e3f2fd; padding: 1rem; border-radius: 5px; font-family: monospace; margin: 1rem 0; word-break: break-all; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='test-container'>";
echo "<div class='container'>";
echo "<h1 class='text-center mb-4'> Test de Génération des QR Codes</h1>";

// Affichage de la configuration
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>⚙️ Configuration</h3></div>";
echo "<div class='card-body'>";
echo "<div class='alert alert-info'>";
echo "<strong>URL du site:</strong> $siteUrl<br>";
echo "<strong>Fichier .env:</strong> " . (file_exists('../.env') ? '✅ Présent' : '❌ Absent') . "<br>";
echo "<strong>Variables chargées:</strong> " . (function_exists('env') ? '✅ Oui' : '❌ Non');
echo "</div>";
echo "</div></div>";

// Test 1: Vérification de la connexion à la base de données
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>Test 1: Connexion Base de Données</h3></div>";
echo "<div class='card-body'>";

try {
    $pdo->query("SELECT 1");
    echo "<div class='alert alert-success'>✅ Connexion à la base de données réussie</div>";
    
    // Vérification des tables
    $tables = ['equipes', 'lieux', 'parcours'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "<div class='alert alert-info'>📊 Table '$table': $count enregistrement(s)</div>";
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>❌ Table '$table': Erreur - " . $e->getMessage() . "</div>";
        }
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur de connexion: " . $e->getMessage() . "</div>";
}
echo "</div></div>";

// Test 2: Génération de tokens de test
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>Test 2: Génération de Tokens</h3></div>";
echo "<div class='card-body'>";

$test_tokens = [];
for ($i = 1; $i <= 3; $i++) {
    $token = bin2hex(random_bytes(16));
    $test_tokens[] = $token;
    echo "<div class='alert alert-success'>�� Token $i généré: $token</div>";
}

echo "</div></div>";

// Test 3: Test de génération de QR codes
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>Test 3: Génération de QR Codes</h3></div>";
echo "<div class='card-body'>";

foreach ($test_tokens as $index => $token) {
    $lieu_test = 'lieu_test_' . ($index + 1);
    $qr_url = "$siteUrl/lieux/access.php?token=$token&lieu=$lieu_test";
    
    echo "<div class='qr-test'>";
    echo "<h5>QR Code Test " . ($index + 1) . "</h5>";
    echo "<div class='url-display'>URL: $qr_url</div>";
    echo "<div id='qr-test-$index' class='mt-3'></div>";
    echo "</div>";
}

echo "</div></div>";

// Test 4: Vérification des parcours existants
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>Test 4: Parcours Existants</h3></div>";
echo "<div class='card-body'>";

try {
    $stmt = $pdo->query("
        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom
        FROM parcours p
        JOIN equipes e ON p.equipe_id = e.id
        JOIN lieux l ON p.lieu_id = l.id
        ORDER BY p.equipe_id, p.ordre_visite
        LIMIT 5
    ");
    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($parcours)) {
        echo "<div class='alert alert-warning'>⚠️ Aucun parcours trouvé dans la base de données</div>";
        echo "<p>Créez d'abord des parcours via <a href='../admin/parcours.php' target='_blank'>l'interface d'administration</a></p>";
    } else {
        echo "<div class='alert alert-success'>✅ " . count($parcours) . " parcours trouvé(s)</div>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-sm'>";
        echo "<thead><tr><th>Équipe</th><th>Lieu</th><th>Ordre</th><th>Token</th></tr></thead>";
        echo "<tbody>";
        foreach ($parcours as $parcour) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($parcour['equipe_nom']) . "</td>";
            echo "<td>" . htmlspecialchars($parcour['lieu_nom']) . "</td>";
            echo "<td>" . $parcour['ordre_visite'] . "</td>";
            echo "<td><code>" . htmlspecialchars($parcour['token_acces']) . "</code></td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur lors de la récupération des parcours: " . $e->getMessage() . "</div>";
}

echo "</div></div>";

// Test 5: Lien vers l'interface de génération
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>Test 5: Interface de Génération</h3></div>";
echo "<div class='card-body'>";
echo "<div class='text-center'>";
echo "<a href='../admin/generate_qr.php' class='btn btn-primary btn-lg' target='_blank'>�� Ouvrir l'Interface de Génération des QR Codes</a>";
echo "</div>";
echo "</div></div>";

echo "</div></div>";

echo "<script>";
echo "// Génération des QR codes de test";
echo "document.addEventListener('DOMContentLoaded', function() {";
echo "const siteUrl = '$siteUrl';";
foreach ($test_tokens as $index => $token) {
    $lieu_test = 'lieu_test_' . ($index + 1);
    $qr_url = "$siteUrl/lieux/access.php?token=$token&lieu=$lieu_test";
    echo "QRCode.toCanvas(document.getElementById('qr-test-$index'), '$qr_url', {";
    echo "width: 150, height: 150, margin: 2";
    echo "}, function (error) { if (error) console.error(error); });";
}
echo "});";
echo "</script>";

echo "</body></html>";
