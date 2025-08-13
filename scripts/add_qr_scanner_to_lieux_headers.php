<?php
// Script simplifié pour ajouter le composant QR
$lieuxDir = '../lieux/';

echo "<h1>📱 Ajout du Scanner QR - Version Simplifiée</h1>";

// Parcourir tous les lieux
$lieux = scandir($lieuxDir);
foreach ($lieux as $lieu) {
    if ($lieu === '.' || $lieu === '..' || !is_dir($lieuxDir . $lieu)) continue;
    
    $headerFile = $lieuxDir . $lieu . '/header.php';
    if (!file_exists($headerFile)) continue;
    
    echo "<p><strong>📍 {$lieu}</strong>: ";
    
    // Lire le contenu
    $content = file_get_contents($headerFile);
    
    // Vérifier si déjà présent
    if (strpos($content, 'qrScannerBtn') !== false) {
        echo "✅ Déjà présent</p>";
        continue;
    }
    
    // Ajouter le bouton QR dans user-info
    if (strpos($content, '<div class="user-info">') !== false) {
        $qrButton = '<button id="qrScannerBtn" class="qr-scanner-btn">📷 Scanner QR</button>';
        
        // Ajouter le bouton après le nom de l'équipe
        $content = preg_replace(
            '/(<div class="user-info">.*?<span class="team-name">.*?<\/span>)/s',
            '$1' . "\n                    " . $qrButton,
            $content
        );
        
        // Ajouter le CSS
        $css = '<style>.qr-scanner-btn{background:rgba(0,123,255,0.9);color:white;border:2px solid rgba(0,123,255,0.9);padding:8px 20px;border-radius:25px;font-weight:500;cursor:pointer;transition:all 0.3s ease;display:inline-block;text-decoration:none;box-shadow:0 2px 8px rgba(0,123,255,0.3);}.qr-scanner-btn:hover{background:rgba(0,123,255,1);transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,123,255,0.4);}</style>';
        
        if (strpos($content, '</head>') !== false) {
            $content = str_replace('</head>', $css . "\n</head>", $content);
        }
        
        // Sauvegarder
        if (file_put_contents($headerFile, $content)) {
            echo "✅ Ajouté avec succès</p>";
        } else {
            echo "❌ Erreur sauvegarde</p>";
        }
    } else {
        echo "⚠️ Section user-info non trouvée</p>";
    }
}

echo "<h2>🎉 Terminé !</h2>";
echo "<p>Le composant QR scanner a été ajouté à tous les lieux.</p>";
echo "<p><a href='../lieux/direction/'>Tester sur la direction</a></p>";
?>
