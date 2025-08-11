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
    <title>Cyberchasse - Escape Game</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <header class="bg-header">
        <div class="header-content">
            <h1>Bienvenue à la Cyberchasse</h1>
            <?php if (isset($_SESSION['team_name'])): ?>
                <div class="user-info">
                    <span class="team-name">Équipe: <?php echo htmlspecialchars($_SESSION['team_name']); ?></span>
                    <a href="logout.php" class="logout-btn">Déconnexion</a>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <div class="container">
