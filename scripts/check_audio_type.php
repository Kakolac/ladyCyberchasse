<?php
require_once '../config/connexion.php';

echo "<h1>🔍 Vérification du type Audio</h1>";

try {
    // Vérifier si le type audio existe
    $stmt = $pdo->query("SELECT * FROM types_enigmes WHERE template = 'audio'");
    $audio_type = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($audio_type) {
        echo "✅ Type Audio trouvé :<br>";
        echo "ID: " . $audio_type['id'] . "<br>";
        echo "Nom: " . $audio_type['nom'] . "<br>";
        echo "Template: " . $audio_type['template'] . "<br>";
        echo "Actif: " . ($audio_type['actif'] ? 'Oui' : 'Non') . "<br>";
    } else {
        echo "❌ Type Audio non trouvé<br>";
        echo "Types disponibles :<br>";
        
        $stmt = $pdo->query("SELECT * FROM types_enigmes ORDER BY id");
        while ($type = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- ID " . $type['id'] . ": " . $type['nom'] . " (" . $type['template'] . ")<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
?>
