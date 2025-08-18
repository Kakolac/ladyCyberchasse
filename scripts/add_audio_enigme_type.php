<?php
require_once '../config/connexion.php';

echo "<h1>Ajout du type d'énigme Audio</h1>";

try {
    // Vérifier si le type audio existe déjà
    $stmt = $pdo->prepare("SELECT id FROM types_enigmes WHERE template = ?");
    $stmt->execute(['audio']);
    
    if ($stmt->rowCount() == 0) {
        // Ajouter le type audio
        $stmt = $pdo->prepare("INSERT INTO types_enigmes (nom, description, template, actif) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            'Audio',
            'Question basée sur un fichier audio',
            'audio',
            1
        ]);
        
        $audio_id = $pdo->lastInsertId();
        echo "✅ Type d'énigme Audio ajouté avec l'ID: " . $audio_id . "<br>";
    } else {
        $audio_type = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ Type d'énigme Audio existe déjà avec l'ID: " . $audio_type['id'] . "<br>";
    }
    
    // Créer le dossier uploads/audio s'il n'existe pas
    $upload_dir = '../uploads/audio/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
        echo "✅ Dossier uploads/audio créé<br>";
    } else {
        echo "✅ Dossier uploads/audio existe déjà<br>";
    }
    
    echo "<br>🎉 Configuration terminée ! Vous pouvez maintenant créer des énigmes audio.";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
?>
