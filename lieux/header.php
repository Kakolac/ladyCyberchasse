<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyberchasse - Lieu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../styles/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../accueil/">
                🏫 Cyberchasse
            </a>
            
            <div class="navbar-nav ms-auto">
                <!-- Bouton Caméra QR Code -->
                <button id="qrScannerBtn" class="btn btn-outline-light me-2">
                    📷 Scanner QR
                </button>
                
                <!-- Menu utilisateur -->
                <div class="navbar-nav">
                    <span class="navbar-text me-3">
                         Équipe: <?php echo isset($_SESSION['team_name']) ? $_SESSION['team_name'] : 'Non connecté'; ?>
                    </span>
                    <a class="nav-link" href="../../logout.php">🚪 Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- SUPPRIMÉ : Tout le modal et le code scanner intégré -->
    
    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Inclure le composant QR scanner -->
    <?php include_once '../../includes/qr-scanner.php'; ?>
</body>
</html>