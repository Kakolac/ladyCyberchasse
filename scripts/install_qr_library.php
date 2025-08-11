<?php
echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Installation Bibliothèque QR Code - Cyberchasse</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<style>";
echo ".install-container { padding: 2rem; }";
echo ".step { background: #f8f9fa; padding: 1.5rem; border-radius: 10px; margin: 1rem 0; }";
echo ".code-block { background: #e9ecef; padding: 1rem; border-radius: 5px; font-family: monospace; margin: 1rem 0; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='install-container'>";
echo "<div class='container'>";
echo "<h1 class='text-center mb-4'>�� Installation de la Bibliothèque QR Code</h1>";

// Vérification de l'environnement
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>�� Vérification de l'Environnement</h3></div>";
echo "<div class='card-body'>";

echo "<div class='step'>";
echo "<h5>1. Vérification de PHP</h5>";
echo "<p><strong>Version PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Extensions GD:</strong> " . (extension_loaded('gd') ? '✅ Disponible' : '❌ Non disponible') . "</p>";
echo "<p><strong>Extensions cURL:</strong> " . (extension_loaded('curl') ? '✅ Disponible' : '❌ Non disponible') . "</p>";
echo "</div>";

echo "<div class='step'>";
echo "<h5>2. Vérification de Composer</h5>";
if (file_exists('../composer.json')) {
    echo "<p>✅ Fichier composer.json trouvé</p>";
    $composerJson = json_decode(file_get_contents('../composer.json'), true);
    if ($composerJson) {
        echo "<p><strong>Nom du projet:</strong> " . ($composerJson['name'] ?? 'Non défini') . "</p>";
        echo "<p><strong>Description:</strong> " . ($composerJson['description'] ?? 'Non définie') . "</p>";
    }
} else {
    echo "<p>❌ Fichier composer.json non trouvé</p>";
}

if (file_exists('../vendor/autoload.php')) {
    echo "<p>✅ Dossier vendor trouvé</p>";
} else {
    echo "<p>❌ Dossier vendor non trouvé</p>";
}
echo "</div>";

echo "</div></div>";

// Instructions d'installation
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>📋 Instructions d'Installation</h3></div>";
echo "<div class='card-body'>";

echo "<div class='step'>";
echo "<h5>Option 1: Installation via Composer (Recommandée)</h5>";
echo "<p>Si vous avez Composer installé, exécutez cette commande dans le terminal :</p>";
echo "<div class='code-block'>";
echo "cd /Users/adrien/Documents/ladyciber<br>";
echo "composer require endroid/qr-code";
echo "</div>";
echo "</div>";

echo "<div class='step'>";
echo "<h5>Option 2: Installation manuelle</h5>";
echo "<p>Si vous n'avez pas Composer, téléchargez et installez manuellement :</p>";
echo "<ol>";
echo "<li>Téléchargez <a href='https://github.com/endroid/qr-code/releases' target='_blank'>Endroid QR Code</a></li>";
echo "<li>Extrayez dans le dossier <code>vendor/endroid/qr-code</code></li>";
echo "<li>Créez un fichier <code>vendor/autoload.php</code></li>";
echo "</ol>";
echo "</div>";

echo "<div class='step'>";
echo "<h5>Option 3: Utilisation du service en ligne (Temporaire)</h5>";
echo "<p>En attendant, le système utilise un service en ligne pour générer les QR codes.</p>";
echo "<p><strong>Note:</strong> Cette solution n'est pas idéale pour la production.</p>";
echo "</div>";

echo "</div></div>";

// Test de la bibliothèque
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>🧪 Test de la Bibliothèque</h3></div>";
echo "<div class='card-body'>";

echo "<div class='step'>";
echo "<h5>Test de génération de QR code</h5>";

// Test avec la bibliothèque Endroid si disponible
if (file_exists('../vendor/autoload.php')) {
    try {
        require_once '../vendor/autoload.php';
        
        if (class_exists('\Endroid\QrCode\QrCode')) {
            echo "<p>✅ Bibliothèque Endroid QR Code disponible</p>";
            
            // Test de génération
            $testQr = new \Endroid\QrCode\QrCode('https://example.com');
            $testQr->setSize(100);
            $testQr->setMargin(10);
            
            echo "<p>✅ Test de génération réussi</p>";
            echo "<p><strong>Status:</strong> Bibliothèque fonctionnelle</p>";
            
        } else {
            echo "<p>❌ Classe Endroid QR Code non trouvée</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Erreur lors du test: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>⚠️ Dossier vendor non trouvé - Installez d'abord la bibliothèque</p>";
}

echo "</div>";

echo "</div></div>";

// Actions recommandées
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>🚀 Actions Recommandées</h3></div>";
echo "<div class='card-body'>";

echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<h5>Si la bibliothèque est installée:</h5>";
echo "<a href='../admin/generate_qr.php' class='btn btn-success w-100 mb-2'>🎯 Tester la Génération des QR Codes</a>";
echo "<a href='test_qr_images.php' class='btn btn-primary w-100 mb-2'>�� Tests des Images</a>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<h5>Si la bibliothèque n'est pas installée:</h5>";
echo "<a href='#install' class='btn btn-warning w-100 mb-2'>�� Installer la Bibliothèque</a>";
echo "<a href='../admin/generate_qr.php' class='btn btn-info w-100 mb-2'>🔄 Tester avec le Service en Ligne</a>";
echo "</div>";
echo "</div>";

echo "</div></div>";

// Liens utiles
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>🔗 Liens Utiles</h3></div>";
echo "<div class='card-body'>";
echo "<ul>";
echo "<li><a href='https://github.com/endroid/qr-code' target='_blank'>Endroid QR Code sur GitHub</a></li>";
echo "<li><a href='https://getcomposer.org/' target='_blank'>Composer - Gestionnaire de Dépendances PHP</a></li>";
echo "<li><a href='https://api.qrserver.com/' target='_blank'>Service QR Code en ligne (fallback)</a></li>";
echo "</ul>";
echo "</div></div>";

echo "</div></div>";
echo "</body></html>";
