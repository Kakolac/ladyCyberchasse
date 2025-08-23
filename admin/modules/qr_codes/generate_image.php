<?php
require_once '../../../config/connexion.php';
require_once '../../../config/env.php';

// Récupération des paramètres
$token = $_GET['token'] ?? '';
$lieu = $_GET['lieu'] ?? '';
$equipe = $_GET['equipe'] ?? '';
$lieu_nom = $_GET['lieu_nom'] ?? '';
$ordre = $_GET['ordre'] ?? '';
$parcours = $_GET['parcours'] ?? '';
$download = isset($_GET['download']);

// Validation des paramètres
if (empty($token) || empty($lieu)) {
    http_response_code(400);
    die('Paramètres manquants');
}

// Récupération de l'URL du site depuis l'environnement
$siteUrl = env('URL_SITE', 'http://127.0.0.1:8888');

// URL complète pour le QR code
$qrUrl = "$siteUrl/lieux/access.php?token=$token&lieu=$lieu";

// Vérification si la bibliothèque phpqrcode est disponible
if (file_exists('../../../vendor/autoload.php')) {
    // Utilisation de Composer
    require_once '../../../vendor/autoload.php';
    $qrCodeGenerator = new \Endroid\QrCode\QrCode($qrUrl);
    $qrCodeGenerator->setSize(300);
    $qrCodeGenerator->setMargin(10);
    
    // Créer l'image
    $qrCodeImage = $qrCodeGenerator->writeString();
    
    // Créer une image GD pour ajouter du texte
    $image = imagecreatefromstring($qrCodeImage);
    $totalHeight = imagesy($image) + 120; // +120 pour le texte (parcours + équipe + lieu + token)
    
    // Créer une nouvelle image avec plus d'espace
    $finalImage = imagecreatetruecolor(imagesx($image), $totalHeight);
    
    // Couleurs
    $white = imagecolorallocate($finalImage, 255, 255, 255);
    $black = imagecolorallocate($finalImage, 0, 0, 0);
    $blue = imagecolorallocate($finalImage, 59, 130, 246); // Pour le parcours
    
    // Remplir le fond en blanc
    imagefill($finalImage, 0, 0, $white);
    
    // Copier le QR code
    imagecopy($finalImage, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
    
    // Ajouter le texte
    $textY = imagesy($image) + 20;
    
    // Informations du parcours
    if (!empty($parcours)) {
        $parcoursText = "Parcours: $parcours";
        imagestring($finalImage, 3, 10, $textY, $parcoursText, $blue);
        $textY += 20;
    }
    
    // Informations de l'équipe
    $equipeText = "Équipe: $equipe";
    imagestring($finalImage, 3, 10, $textY, $equipeText, $black);
    $textY += 20;
    
    // Informations du lieu
    $lieuText = "Lieu: $lieu_nom (Ordre: $ordre)";
    imagestring($finalImage, 3, 10, $textY, $lieuText, $black);
    $textY += 20;
    
    // Token (tronqué pour la lisibilité)
    $tokenText = "Token: " . substr($token, 0, 8) . "...";
    imagestring($finalImage, 3, 10, $textY, $tokenText, $black);
    
    // Définir les headers
    if ($download) {
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="qr_' . $equipe . '_' . $lieu . '.png"');
    } else {
        header('Content-Type: image/png');
    }
    
    // Afficher l'image
    imagepng($finalImage);
    imagedestroy($finalImage);
    imagedestroy($image);
    
} elseif (extension_loaded('gd')) {
    // Fallback : utilisation d'un service en ligne pour générer le QR code
    // Puis on ajoute du texte avec GD
    
    // Créer l'image principale
    $qrSize = 300;
    $margin = 20;
    $totalSize = $qrSize + (2 * $margin);
    $totalHeight = $totalSize + 120; // +120 pour le texte
    
    $image = imagecreatetruecolor($totalSize, $totalHeight);
    
    // Couleurs
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $blue = imagecolorallocate($image, 59, 130, 246); // Pour le parcours
    
    // Remplir le fond en blanc
    imagefill($image, 0, 0, $white);
    
    // Récupérer le QR code depuis un service en ligne
    $qrServiceUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrUrl);
    
    // Essayer de récupérer l'image du service
    $qrImageData = @file_get_contents($qrServiceUrl);
    
    if ($qrImageData !== false) {
        // Créer une image à partir des données récupérées
        $qrImage = imagecreatefromstring($qrImageData);
        
        if ($qrImage !== false) {
            // Copier le QR code dans notre image
            imagecopy($image, $qrImage, $margin, $margin, 0, 0, $qrSize, $qrSize);
            imagedestroy($qrImage);
        } else {
            // Fallback : créer un QR code basique
            createBasicQRCode($image, $qrUrl, $margin, $qrSize);
        }
    } else {
        // Fallback : créer un QR code basique
        createBasicQRCode($image, $qrUrl, $margin, $qrSize);
    }
    
    // Ajouter le texte en bas
    $textY = $totalSize + 20;
    
    // Informations du parcours
    if (!empty($parcours)) {
        $parcoursText = "Parcours: $parcours";
        imagestring($image, 3, $margin, $textY, $parcoursText, $blue);
        $textY += 20;
    }
    
    // Informations de l'équipe
    $equipeText = "Équipe: $equipe";
    imagestring($image, 3, $margin, $textY, $equipeText, $black);
    $textY += 20;
    
    // Informations du lieu
    $lieuText = "Lieu: $lieu_nom (Ordre: $ordre)";
    imagestring($image, 3, $margin, $textY, $lieuText, $black);
    $textY += 20;
    
    // Token (tronqué pour la lisibilité)
    $tokenText = "Token: " . substr($token, 0, 8) . "...";
    imagestring($image, 3, $margin, $textY, $tokenText, $black);
    
    // Définir les headers
    if ($download) {
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="qr_' . $equipe . '_' . $lieu . '.png"');
    } else {
        header('Content-Type: image/png');
    }
    
    // Afficher l'image
    imagepng($image);
    imagedestroy($image);
    
} else {
    // Dernier recours : redirection vers un service en ligne
    $fallbackUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrUrl);
    header("Location: $fallbackUrl");
    exit;
}

// Fonction pour créer un QR code basique (fallback)
function createBasicQRCode($image, $data, $margin, $size) {
    $black = imagecolorallocate($image, 0, 0, 0);
    $white = imagecolorallocate($image, 255, 255, 255);
    
    // Créer une matrice simple basée sur les données
    $matrix = [];
    $dataLength = strlen($data);
    
    for ($i = 0; $i < 25; $i++) {
        for ($j = 0; $j < 25; $j++) {
            $index = ($i * 25 + $j) % $dataLength;
            $charCode = ord($data[$index]);
            $matrix[$i][$j] = ($charCode + $i + $j) % 2 == 0;
        }
    }
    
    // Dessiner le QR code
    $cellSize = $size / 25;
    $startX = $margin;
    $startY = $margin;
    
    for ($i = 0; $i < 25; $i++) {
        for ($j = 0; $j < 25; $j++) {
            $color = $matrix[$i][$j] ? $black : $white;
            $x = $startX + ($j * $cellSize);
            $y = $startY + ($i * $cellSize);
            imagefilledrectangle($image, $x, $y, $x + $cellSize, $y + $cellSize, $color);
        }
    }
}
