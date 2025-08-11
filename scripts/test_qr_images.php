<?php
require_once '../config/connexion.php';

// Récupération de l'URL du site depuis l'environnement
$siteUrl = env('URL_SITE', 'http://127.0.0.1:8888');

echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Test Génération Images QR Codes - Cyberchasse</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<style>";
echo ".test-container { padding: 2rem; }";
echo ".qr-test { text-align: center; margin: 2rem 0; padding: 1rem; border: 2px solid #dee2e6; border-radius: 10px; }";
echo ".qr-image { border: 2px solid #dee2e6; border-radius: 10px; margin: 1rem 0; }";
echo ".info-box { background: #f8f9fa; padding: 1rem; border-radius: 5px; margin: 1rem 0; font-family: monospace; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='test-container'>";
echo "<div class='container'>";
echo "<h1 class='text-center mb-4'>🖼️ Test de Génération des Images QR Codes</h1>";

// Test 1: Vérification de la configuration
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>Test 1: Configuration</h3></div>";
echo "<div class='card-body'>";
echo "<div class='alert alert-info'>";
echo "<strong>URL du site:</strong> $siteUrl<br>";
echo "<strong>Bibliothèque GD:</strong> " . (extension_loaded('gd') ? '✅ Disponible' : '❌ Non disponible') . "<br>";
echo "<strong>Fichier .env:</strong> " . (file_exists('../.env') ? '✅ Présent' : '❌ Absent');
echo "</div>";
echo "</div></div>";

// Test 2: Génération de tokens de test
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>Test 2: Génération d'Images QR Codes</h3></div>";
echo "<div class='card-body'>";

$test_data = [
    ['equipe' => 'Rouge', 'lieu' => 'cantine', 'lieu_nom' => 'Cantine', 'ordre' => 1],
    ['equipe' => 'Bleu', 'lieu' => 'cdi', 'lieu_nom' => 'CDI', 'ordre' => 2],
    ['equipe' => 'Vert', 'lieu' => 'cour', 'lieu_nom' => 'Cour', 'ordre' => 3]
];

foreach ($test_data as $index => $data) {
    $token = bin2hex(random_bytes(16));
    $qr_url = "$siteUrl/lieux/access.php?token=$token&lieu=" . $data['lieu'];
    
    echo "<div class='qr-test'>";
    echo "<h5>QR Code Test " . ($index + 1) . " - " . $data['equipe'] . " - " . $data['lieu_nom'] . "</h5>";
    
    echo "<div class='info-box'>";
    echo "<strong>URL générée:</strong><br>$qr_url<br>";
    echo "<strong>Token:</strong> $token";
    echo "</div>";
    
    echo "<div class='qr-image'>";
    echo "<img src='../admin/generate_qr_image.php?token=" . urlencode($token) . "&lieu=" . urlencode($data['lieu']) . "&equipe=" . urlencode($data['equipe']) . "&lieu_nom=" . urlencode($data['lieu_nom']) . "&ordre=" . $data['ordre'] . "' alt='QR Code Test' style='max-width: 300px; height: auto;'>";
    echo "</div>";
    
    echo "<div class='mt-3'>";
    echo "<a href='../admin/generate_qr_image.php?token=" . urlencode($token) . "&lieu=" . urlencode($data['lieu']) . "&equipe=" . urlencode($data['equipe']) . "&lieu_nom=" . urlencode($data['lieu_nom']) . "&ordre=" . $data['ordre'] . "&download=1' class='btn btn-success btn-sm'>💾 Télécharger l'image</a>";
    echo "</div>";
    
    echo "</div>";
}

echo "</div></div>";

// Test 3: Vérification des parcours existants
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>Test 3: Parcours Existants avec Images</h3></div>";
echo "<div class='card-body'>";

try {
    $stmt = $pdo->query("
        SELECT p.*, e.nom as equipe_nom, l.nom as lieu_nom, l.slug as lieu_slug
        FROM parcours p
        JOIN equipes e ON p.equipe_id = e.id
        JOIN lieux l ON p.lieu_id = l.id
        ORDER BY p.equipe_id, p.ordre_visite
        LIMIT 3
    ");
    $parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($parcours)) {
        echo "<div class='alert alert-warning'>⚠️ Aucun parcours trouvé dans la base de données</div>";
        echo "<p>Créez d'abord des parcours via <a href='../admin/parcours.php' target='_blank'>l'interface d'administration</a></p>";
    } else {
        echo "<div class='alert alert-success'>✅ " . count($parcours) . " parcours trouvé(s)</div>";
        
        foreach ($parcours as $parcour) {
            echo "<div class='qr-test'>";
            echo "<h5>" . htmlspecialchars($parcour['equipe_nom']) . " - " . htmlspecialchars($parcour['lieu_nom']) . "</h5>";
            
            echo "<div class='qr-image'>";
            echo "<img src='../admin/generate_qr_image.php?token=" . urlencode($parcour['token_acces']) . "&lieu=" . urlencode($parcour['lieu_slug']) . "&equipe=" . urlencode($parcour['equipe_nom']) . "&lieu_nom=" . urlencode($parcour['lieu_nom']) . "&ordre=" . $parcour['ordre_visite'] . "' alt='QR Code Parcours' style='max-width: 300px; height: auto;'>";
            echo "</div>";
            
            echo "<div class='info-box'>";
            echo "<strong>Token:</strong> " . htmlspecialchars($parcour['token_acces']) . "<br>";
            echo "<strong>Ordre:</strong> " . $parcour['ordre_visite'];
            echo "</div>";
            
            echo "</div>";
        }
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur lors de la récupération des parcours: " . $e->getMessage() . "</div>";
}

echo "</div></div>";

// Test 4: Lien vers l'interface de génération
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>Test 4: Interface de Génération</h3></div>";
echo "<div class='card-body'>";
echo "<div class='text-center'>";
echo "<a href='../admin/generate_qr.php' class='btn btn-primary btn-lg' target='_blank'>�� Ouvrir l'Interface de Génération des QR Codes</a>";
echo "</div>";
echo "</div></div>";

echo "</div></div>";
echo "</body></html>";
