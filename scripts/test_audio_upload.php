<?php
session_start();
require_once '../config/connexion.php';

echo "<h1>🎵 Test Upload Audio</h1>";
echo "<p>Ce script teste uniquement l'upload de fichiers audio</p>";

// Vérifier si un fichier a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['audio_file'])) {
    echo "<h2>📁 Fichier reçu</h2>";
    echo "<pre>";
    print_r($_FILES['audio_file']);
    echo "</pre>";
    
    // Vérifier les erreurs
    if ($_FILES['audio_file']['error'] !== UPLOAD_ERR_OK) {
        echo "<h3>❌ Erreur d'upload</h3>";
        echo "Code d'erreur: " . $_FILES['audio_file']['error'] . "<br>";
        
        switch ($_FILES['audio_file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                echo "Fichier trop volumineux (php.ini)<br>";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                echo "Fichier trop volumineux (formulaire)<br>";
                break;
            case UPLOAD_ERR_PARTIAL:
                echo "Upload partiel<br>";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "Aucun fichier uploadé<br>";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                echo "Dossier temporaire manquant<br>";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                echo "Erreur d'écriture<br>";
                break;
            case UPLOAD_ERR_EXTENSION:
                echo "Extension non autorisée<br>";
                break;
        }
    } else {
        echo "<h3>✅ Fichier reçu sans erreur</h3>";
        
        // Informations du fichier
        echo "Nom: " . $_FILES['audio_file']['name'] . "<br>";
        echo "Type: " . $_FILES['audio_file']['type'] . "<br>";
        echo "Taille: " . $_FILES['audio_file']['size'] . " bytes<br>";
        echo "Fichier temporaire: " . $_FILES['audio_file']['tmp_name'] . "<br>";
        
        // Vérifier l'extension
        $file_extension = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['mp3', 'wav', 'ogg'];
        
        echo "<h3>🔍 Validation de l'extension</h3>";
        echo "Extension détectée: " . $file_extension . "<br>";
        echo "Extension autorisée: " . (in_array($file_extension, $allowed_extensions) ? 'OUI' : 'NON') . "<br>";
        
        if (in_array($file_extension, $allowed_extensions)) {
            echo "<h3>�� Tentative d'upload</h3>";
            
            // Chemin d'upload
            $upload_dir = '../uploads/audio/';
            echo "Dossier d'upload: " . $upload_dir . "<br>";
            echo "Dossier existe: " . (is_dir($upload_dir) ? 'OUI' : 'NON') . "<br>";
            
            // Créer le dossier s'il n'existe pas
            if (!is_dir($upload_dir)) {
                $created = mkdir($upload_dir, 0755, true);
                echo "Dossier créé: " . ($created ? 'OUI' : 'NON') . "<br>";
            }
            
            // Vérifier les permissions
            if (is_dir($upload_dir)) {
                echo "Permissions du dossier: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "<br>";
                echo "Dossier accessible en écriture: " . (is_writable($upload_dir) ? 'OUI' : 'NON') . "<br>";
            }
            
            // Tentative de déplacement
            $filename = uniqid() . '_' . time() . '.' . $file_extension;
            $filepath = $upload_dir . $filename;
            
            echo "Fichier de destination: " . $filepath . "<br>";
            
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $filepath)) {
                echo "<h3>�� Upload réussi !</h3>";
                echo "Fichier sauvegardé: " . $filepath . "<br>";
                echo "Chemin relatif: uploads/audio/" . $filename . "<br>";
                
                // Vérifier que le fichier existe
                echo "Fichier existe après upload: " . (file_exists($filepath) ? 'OUI' : 'NON') . "<br>";
                echo "Taille du fichier sauvegardé: " . filesize($filepath) . " bytes<br>";
            } else {
                echo "<h3>❌ Échec de l'upload</h3>";
                echo "Erreur PHP: " . error_get_last()['message'] ?? 'Aucune erreur PHP' . "<br>";
                
                // Vérifier les permissions
                if (is_dir($upload_dir)) {
                    echo "Permissions du dossier: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "<br>";
                    echo "Dossier accessible en écriture: " . (is_writable($upload_dir) ? 'OUI' : 'NON') . "<br>";
                }
            }
        } else {
            echo "<h3>❌ Extension non autorisée</h3>";
            echo "Extensions autorisées: " . implode(', ', $allowed_extensions) . "<br>";
        }
    }
} else {
    // Formulaire d'upload
    echo "<h2>�� Formulaire de test</h2>";
    echo "<form method='POST' enctype='multipart/form-data'>";
    echo "<div class='mb-3'>";
    echo "<label for='audio_file' class='form-label'>Sélectionnez un fichier audio</label>";
    echo "<input type='file' class='form-control' name='audio_file' accept='.mp3,.wav,.ogg' required>";
    echo "<small class='text-muted'>Formats acceptés : MP3, WAV, OGG. Taille max : 10MB</small>";
    echo "</div>";
    echo "<button type='submit' class='btn btn-primary'>Tester l'upload</button>";
    echo "</form>";
}

echo "<hr>";
echo "<h2>📋 Informations système</h2>";
echo "PHP version: " . phpversion() . "<br>";
echo "Upload max filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "Post max size: " . ini_get('post_max_size') . "<br>";
echo "Max file uploads: " . ini_get('max_file_uploads') . "<br>";
echo "Temporary directory: " . ini_get('upload_tmp_dir') ?: sys_get_temp_dir() . "<br>";
echo "Session save path: " . session_save_path() . "<br>";
?>
