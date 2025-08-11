<?php
// Vérification de l'authentification admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Déterminer la page active pour le menu
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Administration - Cyberchasse'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --admin-primary: #667eea;
            --admin-secondary: #764ba2;
            --admin-success: #28a745;
            --admin-warning: #ffc107;
            --admin-danger: #dc3545;
            --admin-info: #17a2b8;
        }
        
        body { 
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%); 
            min-height: 100vh; 
        }
        
        .admin-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .admin-navbar {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .admin-navbar .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .admin-navbar .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
        }
        
        .admin-navbar .navbar-nav .nav-link:hover,
        .admin-navbar .navbar-nav .nav-link.active {
            color: white !important;
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        
        .admin-navbar .navbar-nav .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }
        
        .admin-navbar .navbar-toggler {
            border: 1px solid rgba(255,255,255,0.3);
            color: rgba(255,255,255,0.8);
        }
        
        .admin-navbar .navbar-toggler:focus {
            box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
        }
        
        .admin-navbar .dropdown-menu {
            background: rgba(44, 62, 80, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        
        .admin-navbar .dropdown-item {
            color: rgba(255,255,255,0.9);
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .admin-navbar .dropdown-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .admin-navbar .dropdown-item i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        
        .user-menu .dropdown-toggle::after {
            display: none;
        }
        
        .user-menu .dropdown-toggle {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .user-menu .dropdown-toggle:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--admin-danger);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .main-content {
            padding-top: 2rem;
        }
        
        .breadcrumb {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255,255,255,0.6);
        }
        
        .breadcrumb-item.active {
            color: rgba(255,255,255,0.8);
        }
        
        @media (max-width: 768px) {
            .admin-navbar .navbar-nav .nav-link {
                padding: 0.5rem 0.75rem;
                margin: 0.125rem 0;
            }
            
            .admin-navbar .navbar-brand {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header principal -->
    <header class="admin-header">
        <div class="container-fluid">
            <div class="row align-items-center py-2">
                <!-- Logo et titre -->
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-shield-alt fa-2x text-white"></i>
                        </div>
                        <div>
                            <h1 class="h4 mb-0 text-white">Cyberchasse</h1>
                            <small class="text-white-50">Administration</small>
                        </div>
                    </div>
                </div>
                
                <!-- Statistiques rapides -->
                <div class="col-md-4 d-none d-md-block">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="text-white-50">
                                <i class="fas fa-users"></i>
                                <small>Équipes</small>
                            </div>
                            <div class="text-white fw-bold" id="quick-stats-teams">-</div>
                        </div>
                        <div class="col-4">
                            <div class="text-white-50">
                                <i class="fas fa-route"></i>
                                <small>Parcours</small>
                            </div>
                            <div class="text-white fw-bold" id="quick-stats-parcours">-</div>
                        </div>
                        <div class="col-4">
                            <div class="text-white-50">
                                <i class="fas fa-flag-checkered"></i>
                                <small>Terminés</small>
                            </div>
                            <div class="text-white fw-bold" id="quick-stats-finished">-</div>
                        </div>
                    </div>
                </div>
                
                <!-- Menu utilisateur -->
                <div class="col-md-4 text-end">
                    <div class="dropdown user-menu">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield"></i>
                            Administrateur
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../index.php"><i class="fas fa-home"></i>Accueil public</a></li>
                            <li><a class="dropdown-item" href="../scenario.php"><i class="fas fa-gamepad"></i>Voir le jeu</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt"></i>Déconnexion</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation principale -->
        <nav class="navbar navbar-expand-lg admin-navbar">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav me-auto">
                        <!-- Tableau de bord -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'admin' ? 'active' : ''; ?>" href="admin.php">
                                <i class="fas fa-tachometer-alt"></i>Tableau de bord
                            </a>
                        </li>
                        
                        <!-- Gestion des équipes -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-users"></i>Équipes
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="admin.php#equipes"><i class="fas fa-cog"></i>Gérer les équipes</a></li>
                                <li><a class="dropdown-item" href="generate_qr.php"><i class="fas fa-qrcode"></i>Générer QR codes</a></li>
                                <li><a class="dropdown-item" href="generate_qr_image.php"><i class="fas fa-image"></i>QR codes images</a></li>
                            </ul>
                        </li>
                        
                        <!-- Gestion des parcours -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-route"></i>Parcours
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="parcours.php"><i class="fas fa-cog"></i>Gérer les parcours</a></li>
                                <li><a class="dropdown-item" href="parcours.php#actions-rapides"><i class="fas fa-bolt"></i>Actions rapides</a></li>
                            </ul>
                        </li>
                        

                    </ul>
                    
                    <!-- Actions rapides -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="resetAllGames()" title="Réinitialiser tous les jeux">
                                <i class="fas fa-redo"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="generateAllQRCodes()" title="Générer tous les QR codes">
                                <i class="fas fa-qrcode"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showNotifications()" title="Notifications">
                                <i class="fas fa-bell"></i>
                                <span class="notification-badge">3</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Contenu principal -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Fil d'Ariane -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin.php" class="text-white-50">Administration</a></li>
                    <?php if (isset($breadcrumb_items)): ?>
                        <?php foreach ($breadcrumb_items as $item): ?>
                            <?php if ($item['active']): ?>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo $item['text']; ?></li>
                            <?php else: ?>
                                <li class="breadcrumb-item"><a href="<?php echo $item['url']; ?>" class="text-white-50"><?php echo $item['text']; ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </nav>
