<?php
// Chargement des variables d'environnement depuis le fichier .env
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Supprimer les guillemets si présents
            if (preg_match('/^"(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                $value = $matches[1];
            }
            
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
    return true;
}

// Charger le fichier .env
$envPath = __DIR__ . '/../.env';
if (!loadEnv($envPath)) {
    // Valeurs par défaut si le fichier .env n'existe pas
    $_ENV['URL_SITE'] = 'http://127.0.0.1:8888';
    $_ENV['DB_HOST'] = 'localhost';
    $_ENV['DB_NAME'] = 'cyberchasse';
    $_ENV['DB_USER'] = 'root';
    $_ENV['DB_PASS'] = 'root';
}

// Fonction helper pour récupérer une variable d'environnement
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}
