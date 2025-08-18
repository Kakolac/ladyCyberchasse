<?php
session_start();
require_once '../config/connexion.php';

echo "<h1>�� Test du Système d'Énigmes</h1>";

// Simuler la session admin
$_SESSION['admin'] = true;

// Simuler une requête POST
$_POST['action'] = 'create_enigme';
$_POST['type_enigme_id'] = '5';
$_POST['titre'] = 'Test Audio';
$_POST['question_audio'] = 'Question test';
$_POST['reponse_correcte_audio'] = 'Réponse test';

// Simuler un fichier uploadé
$_FILES['audio_file'] = [
    'name' => 'test.mp3',
    'type' => 'audio/mpeg',
    'tmp_name' => '/tmp/test',
    'error' => 0,
    'size' => 1000
];

echo "<h2>�� Données simulées</h2>";
echo "<pre>";
echo "POST: " . print_r($_POST, true) . "\n";
echo "FILES: " . print_r($_FILES, true) . "\n";
echo "</pre>";

// Inclure et tester le gestionnaire audio
require_once '../admin/enigmes/handlers/audio_handler.php';

echo "<h2>🔍 Test du gestionnaire audio</h2>";

// Test de validation
$errors = AudioHandler::validate($_POST);
echo "Erreurs de validation: " . (empty($errors) ? 'Aucune' : implode(', ', $errors)) . "<br>";

// Test de génération JSON
$json = AudioHandler::generateJSON($_POST);
echo "JSON généré: " . $json . "<br>";

echo "<h2>✅ Test terminé</h2>";
?>
