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
    <title>Cyberchasse - Accueil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles/style.css">
</head>
<body>
    <header class="bg-header">
        <div class="header-content">
            <h1>Bienvenue à la Cyberchasse</h1>
            <?php if (isset($_SESSION['team_name'])): ?>
                <div class="user-info">
                    <span class="team-name">Équipe: <?php echo htmlspecialchars($_SESSION['team_name']); ?></span>
                    <!-- Bouton QR Scanner -->
                    <button id="qrScannerBtn" class="qr-scanner-btn">
                        📷 Scanner QR
                    </button>
                    <a href="../../logout.php" class="logout-btn">Déconnexion</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <!-- Contenu de la page accueil -->
        <div class="welcome-content">
            <h2>🎯 Bienvenue dans la Cyberchasse</h2>
            <p>Scannez les QR codes pour découvrir les lieux et résoudre les énigmes !</p>
        </div>
    </div>

    <!-- Inclure le composant QR scanner -->
    <?php include_once '../../includes/qr-scanner.php'; ?>

    <style>
        .qr-scanner-btn {
            background-color: rgba(0, 123, 255, 0.9) !important;
            color: white !important;
            border: 2px solid rgba(0, 123, 255, 0.9) !important;
            padding: 8px 20px !important;
            border-radius: 25px !important;
            font-weight: 500 !important;
            font-size: 1rem !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            display: inline-block !important;
            text-decoration: none !important;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3) !important;
            margin-right: 15px !important;
        }
        
        .qr-scanner-btn:hover {
            background-color: rgba(0, 123, 255, 1) !important;
            color: white !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4) !important;
            text-decoration: none !important;
        }
        
        .qr-scanner-btn:active {
            transform: translateY(0) !important;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3) !important;
        }
    </style>
</body>
</html>
