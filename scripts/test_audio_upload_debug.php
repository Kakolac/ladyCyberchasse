<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$upload_dir = __DIR__ . '/../uploads/audio/';
$test_file = __DIR__ . '/test_audio.mp3'; // Nous créerons ce fichier de test

// Fonction pour afficher un message de test
function test_message($message, $success = true) {
    echo ($success ? "✅" : "❌") . " $message<br>";
}

// 1. Vérification de la configuration PHP
echo "<h2>1. Configuration PHP</h2>";
test_message("upload_max_filesize: " . ini_get('upload_max_filesize'));
test_message("post_max_size: " . ini_get('post_max_size'));
test_message("max_execution_time: " . ini_get('max_execution_time'));
test_message("memory_limit: " . ini_get('memory_limit'));

// 2. Vérification des chemins
echo "<h2>2. Vérification des chemins</h2>";
test_message("Chemin du script: " . __DIR__);
test_message("Chemin d'upload absolu: " . $upload_dir);
test_message("Le dossier d'upload existe", is_dir($upload_dir));

// 3. Vérification des permissions
echo "<h2>3. Vérification des permissions</h2>";
if (!is_dir($upload_dir)) {
    // Tentative de création du dossier
    $created = mkdir($upload_dir, 0755, true);
    test_message("Création du dossier d'upload", $created);
}

if (is_dir($upload_dir)) {
    $perms = substr(sprintf('%o', fileperms($upload_dir)), -4);
    test_message("Permissions du dossier: " . $perms);
    test_message("Dossier accessible en lecture", is_readable($upload_dir));
    test_message("Dossier accessible en écriture", is_writable($upload_dir));
    
    // Obtenir l'utilisateur et le groupe
    $owner = posix_getpwuid(fileowner($upload_dir));
    $group = posix_getgrgid(filegroup($upload_dir));
    echo "Propriétaire: " . $owner['name'] . "<br>";
    echo "Groupe: " . $group['name'] . "<br>";
}

// 4. Test d'écriture de fichier
echo "<h2>4. Test d'écriture de fichier</h2>";
$test_content = "Test audio file";
$test_file_path = $upload_dir . "test_" . time() . ".txt";

if (file_put_contents($test_file_path, $test_content) !== false) {
    test_message("Test d'écriture réussi");
    unlink($test_file_path); // Nettoyage
    test_message("Suppression du fichier de test réussie");
} else {
    test_message("Échec du test d'écriture", false);
}

// 5. Simulation d'upload
echo "<h2>5. Simulation d'upload</h2>";
if (!file_exists($test_file)) {
    // Créer un petit fichier MP3 de test
    $mp3_content = str_repeat("TEST", 1000);
    file_put_contents($test_file, $mp3_content);
}

$dest_file = $upload_dir . "test_upload_" . time() . ".mp3";
if (copy($test_file, $dest_file)) {
    test_message("Test de copie réussi");
    unlink($dest_file); // Nettoyage
    test_message("Suppression du fichier uploadé réussie");
} else {
    test_message("Échec du test de copie", false);
}

// 6. Vérification de la structure du formulaire
echo "<h2>6. Structure du formulaire</h2>";
?>
<form action="test_audio_upload_debug.php" method="post" enctype="multipart/form-data">
    <input type="file" name="audio_file" accept=".mp3,.wav,.ogg">
    <input type="submit" value="Tester l'upload">
</form>
<?php

// 7. Traitement de l'upload réel si un fichier est envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['audio_file'])) {
    echo "<h2>7. Résultats de l'upload</h2>";
    echo "<pre>";
    print_r($_FILES['audio_file']);
    echo "</pre>";
    
    if ($_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        $upload_file = $upload_dir . basename($_FILES['audio_file']['name']);
        if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $upload_file)) {
            test_message("Upload réussi: " . $upload_file);
        } else {
            test_message("Échec de l'upload", false);
            echo "Erreur PHP: " . error_get_last()['message'] . "<br>";
        }
    } else {
        test_message("Erreur lors de l'upload: " . $_FILES['audio_file']['error'], false);
    }
}
