<?php
header('Content-Type: application/json; charset=utf-8');

$result = [
  'ok' => false,
  'message' => '',
  'server' => null,
  'database' => null,
];

try {
  // Réutilise la connexion existante (définit $pdo ou meurt avec un message d'erreur)
  require_once __DIR__ . '/../config/connexion.php';

  $version = null;
  $dbName = null;

  if (isset($pdo) && $pdo instanceof PDO) {
    $stmt = $pdo->query('SELECT VERSION() AS version');
    $versionRow = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    $version = $versionRow['version'] ?? null;

    $stmt = $pdo->query('SELECT DATABASE() AS db');
    $dbRow = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    $dbName = $dbRow['db'] ?? null;

    $result['ok'] = true;
    $result['message'] = 'Connexion MySQL réussie';
    $result['server'] = $version;
    $result['database'] = $dbName;
  } else {
    http_response_code(500);
    $result['message'] = "La variable \$pdo n'est pas initialisée.";
  }
} catch (Throwable $e) {
  http_response_code(500);
  $result['ok'] = false;
  $result['message'] = 'Erreur: ' . $e->getMessage();
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

