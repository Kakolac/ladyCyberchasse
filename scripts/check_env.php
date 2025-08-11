<?php
echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Vérification Configuration - Cyberchasse</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<style>";
echo ".config-container { padding: 2rem; }";
echo ".env-var { background: #f8f9fa; padding: 1rem; border-radius: 5px; margin: 0.5rem 0; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='config-container'>";
echo "<div class='container'>";
echo "<h1 class='text-center mb-4'>⚙️ Vérification de la Configuration</h1>";

// Vérification du fichier .env
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>📁 Fichier .env</h3></div>";
echo "<div class='card-body'>";

$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    echo "<div class='alert alert-success'>✅ Fichier .env trouvé</div>";
    
    $envContent = file_get_contents($envPath);
    echo "<h5>Contenu du fichier:</h5>";
    echo "<pre class='bg-light p-3 rounded'>" . htmlspecialchars($envContent) . "</pre>";
} else {
    echo "<div class='alert alert-warning'>⚠️ Fichier .env non trouvé</div>";
    echo "<p>Créez un fichier .env à la racine du projet avec le contenu suivant:</p>";
    echo "<pre class='bg-light p-3 rounded'># Configuration de l'environnement Cyberchasse
URL_SITE=http://127.0.0.1:8888
DB_HOST=localhost
DB_NAME=cyberchasse
DB_USER=root
DB_PASS=root</pre>";
}

echo "</div></div>";

// Test de chargement des variables
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>�� Test de Chargement</h3></div>";
echo "<div class='card-body'>";

try {
    require_once '../config/env.php';
    
    if (function_exists('env')) {
        echo "<div class='alert alert-success'>✅ Fonction env() disponible</div>";
        
        $variables = [
            'URL_SITE' => env('URL_SITE'),
            'DB_HOST' => env('DB_HOST'),
            'DB_NAME' => env('DB_NAME'),
            'DB_USER' => env('DB_USER'),
            'DB_PASS' => env('DB_PASS')
        ];
        
        foreach ($variables as $key => $value) {
            $status = $value ? 'success' : 'warning';
            $icon = $value ? '✅' : '⚠️';
            echo "<div class='env-var'>";
            echo "<strong>$key:</strong> <span class='text-$status'>$icon $value</span>";
            echo "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>❌ Fonction env() non disponible</div>";
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur lors du chargement: " . $e->getMessage() . "</div>";
}

echo "</div></div>";

// Test de connexion à la base de données
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>🗄️ Test Base de Données</h3></div>";
echo "<div class='card-body'>";

try {
    require_once '../config/connexion.php';
    echo "<div class='alert alert-success'>✅ Connexion à la base de données réussie</div>";
    
    // Test des variables d'environnement dans la connexion
    echo "<h5>Variables utilisées pour la connexion:</h5>";
    echo "<div class='env-var'>";
    echo "<strong>Host:</strong> " . env('DB_HOST', 'non défini') . "<br>";
    echo "<strong>Database:</strong> " . env('DB_NAME', 'non défini') . "<br>";
    echo "<strong>User:</strong> " . env('DB_USER', 'non défini');
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Erreur de connexion: " . $e->getMessage() . "</div>";
}

echo "</div></div>";

// Actions recommandées
echo "<div class='card mb-4'>";
echo "<div class='card-header'><h3>🚀 Actions Recommandées</h3></div>";
echo "<div class='card-body'>";
echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<h5>Si tout fonctionne:</h5>";
echo "<a href='../admin/generate_qr.php' class='btn btn-success w-100 mb-2'>🎯 Tester la Génération des QR Codes</a>";
echo "<a href='../admin/parcours.php' class='btn btn-primary w-100 mb-2'>�� Gérer les Parcours</a>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<h5>En cas de problème:</h5>";
echo "<a href='test_qr_generation.php' class='btn btn-warning w-100 mb-2'>🧪 Tests de Diagnostic</a>";
echo "<a href='init_database.php' class='btn btn-info w-100 mb-2'>🗄️ Initialiser la BDD</a>";
echo "</div>";
echo "</div>";
echo "</div></div>";

echo "</div></div>";
echo "</body></html>";
