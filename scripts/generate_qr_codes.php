<?php
// Script pour générer des QR codes pour chaque lieu
require_once '../vendor/autoload.php'; // Si vous avez Composer
// Ou utilisez une bibliothèque QR native

// Configuration des lieux
$lieux = [
    'accueil' => 'https://localhost/lieux/accueil/',
    'cdi' => 'https://localhost/lieux/cdi/',
    'salle_info' => 'https://localhost/lieux/salle_info/',
    'vie_scolaire' => 'https://localhost/lieux/vie_scolaire/',
    'labo_physique' => 'https://localhost/lieux/labo_physique/',
    'labo_chimie' => 'https://localhost/lieux/labo_chimie/',
    'labo_svt' => 'https://localhost/lieux/labo_svt/',
    'salle_arts' => 'https://localhost/lieux/salle_arts/',
    'salle_musique' => 'https://localhost/lieux/salle_musique/',
    'gymnase' => 'https://localhost/lieux/gymnase/',
    'cantine' => 'https://localhost/lieux/cantine/',
    'direction' => 'https://localhost/lieux/direction/',
    'secretariat' => 'https://localhost/lieux/secretariat/',
    'salle_reunion' => 'https://localhost/lieux/salle_reunion/',
    'salle_profs' => 'https://localhost/lieux/salle_profs/',
    'atelier_techno' => 'https://localhost/lieux/atelier_techno/',
    'salle_langues' => 'https://localhost/lieux/salle_langues/',
    'internat' => 'https://localhost/lieux/internat/',
    'infirmerie' => 'https://localhost/lieux/infirmerie/',
    'cour' => 'https://localhost/lieux/cour/'
];

// Créer le dossier pour les QR codes
$qrDir = '../images/qr_codes/';
if (!is_dir($qrDir)) {
    mkdir($qrDir, 0755, true);
}

echo "<h1>Génération des QR Codes</h1>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto;'>";

foreach ($lieux as $nom => $url) {
    echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 15px; border-radius: 8px;'>";
    echo "<h3>🏫 $nom</h3>";
    echo "<p><strong>URL:</strong> $url</p>";
    
    // Générer le QR code (méthode simple avec Google Charts API)
    $qrUrl = "https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=" . urlencode($url);
    
    echo "<img src='$qrUrl' alt='QR Code pour $nom' style='border: 1px solid #ddd; border-radius: 4px;'>";
    echo "<p><a href='$qrUrl' target='_blank' class='btn btn-primary'>Télécharger QR Code</a></p>";
    echo "</div>";
}

echo "</div>";
echo "<style>
.btn {
    display: inline-block;
    padding: 8px 16px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    margin: 5px;
}
.btn:hover {
    background: #0056b3;
}
</style>";
?>
