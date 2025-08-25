<?php
session_start();
require_once '../config/connexion.php';
require_once '../config/env.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

// Récupération des statistiques depuis les vraies tables Cyberchasse
try {
    // Statistiques des équipes
    $stmt = $pdo->query("SELECT COUNT(*) as total_equipes FROM cyber_equipes");
    $totalEquipes = $stmt->fetch(PDO::FETCH_ASSOC)['total_equipes'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as equipes_connectees FROM cyber_equipes WHERE statut = 'active'");
    $equipesConnectees = $stmt->fetch(PDO::FETCH_ASSOC)['equipes_connectees'];
    
    // Statistiques des parcours
    $stmt = $pdo->query("SELECT COUNT(*) as total_parcours FROM cyber_equipes_parcours");
    $totalParcours = $stmt->fetch(PDO::FETCH_ASSOC)['total_parcours'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as parcours_termines FROM cyber_equipes_parcours WHERE statut = 'termine'");
    $parcoursTermines = $stmt->fetch(PDO::FETCH_ASSOC)['parcours_termines'];
    
    // Statistiques des lieux
    $stmt = $pdo->query("SELECT COUNT(*) as total_lieux FROM cyber_lieux WHERE statut = 'actif'");
    $totalLieux = $stmt->fetch(PDO::FETCH_ASSOC)['total_lieux'];
    
    // Statistiques des équipes disqualifiées
    $stmt = $pdo->query("SELECT COUNT(*) as equipes_disqualifiees FROM cyber_equipes WHERE statut = 'disqualifiee'");
    $equipesDisqualifiees = $stmt->fetch(PDO::FETCH_ASSOC)['equipes_disqualifiees'];
    
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des données: " . $e->getMessage();
}

// Configuration pour le header
$page_title = 'Administration Cyberchasse - Outils Principaux';
$breadcrumb_items = [
    ['text' => 'Administration', 'url' => 'admin2.php', 'active' => true]
];

// Inclure le header
include 'includes/header.php';
?>

        <!-- Styles CSS spécifiques à cette page -->
        <style>
            .admin-card { 
                border: none; 
                border-radius: 15px; 
                box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
            }
            .stat-card { 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                color: white; 
            }
            .stat-card.success { 
                background: linear-gradient(135deg, #28a745, #20c997); 
            }
            .stat-card.warning { 
                background: linear-gradient(135deg, #ffc107, #fd7e14); 
            }
            .stat-card.info { 
                background: linear-gradient(135deg, #17a2b8, #6f42c1); 
            }
            .stat-card.danger { 
                background: linear-gradient(135deg, #dc3545, #c82333); 
            }
            .stat-card.secondary { 
                background: linear-gradient(135deg, #6c757d, #495057); 
            }
            .tool-card { 
                transition: transform 0.3s ease; 
                border: none;
                border-radius: 15px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
            .tool-card:hover { 
                transform: translateY(-5px); 
                box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            }
            .card-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-radius: 15px 15px 0 0 !important;
            }
            .btn-wizard {
                background: linear-gradient(135deg, #ffc107, #fd7e14);
                border: none;
                border-radius: 20px;
                padding: 8px 20px;
            }
            .btn-wizard:hover {
                background: linear-gradient(135deg, #fd7e14, #e55a00);
                transform: scale(1.05);
            }
        </style>

        <!-- Statistiques Cyberchasse -->
        <div class="row mb-4" id="statistiques">
            <div class="col-md-2">
                <div class="card admin-card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h3><?php echo $totalEquipes ?? 0; ?></h3>
                        <p class="mb-0">Total Équipes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card admin-card stat-card success">
                    <div class="card-body text-center">
                        <i class="fas fa-user-check fa-3x mb-3"></i>
                        <h3><?php echo $equipesConnectees ?? 0; ?></h3>
                        <p class="mb-0">Équipes Actives</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card admin-card stat-card warning">
                    <div class="card-body text-center">
                        <i class="fas fa-route fa-3x mb-3"></i>
                        <h3><?php echo $totalParcours ?? 0; ?></h3>
                        <p class="mb-0">Total Parcours</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card admin-card stat-card info">
                    <div class="card-body text-center">
                        <i class="fas fa-flag-checkered fa-3x mb-3"></i>
                        <h3><?php echo $parcoursTermines ?? 0; ?></h3>
                        <p class="mb-0">Parcours Terminés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card admin-card stat-card secondary">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                        <h3><?php echo $totalLieux ?? 0; ?></h3>
                        <p class="mb-0">Total Lieux</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card admin-card stat-card danger">
                    <div class="card-body text-center">
                        <i class="fas fa-user-times fa-3x mb-3"></i>
                        <h3><?php echo $equipesDisqualifiees ?? 0; ?></h3>
                        <p class="mb-0">Équipes Disqualifiées</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <?php include 'includes/outils_administration.php'; ?>
        </div>

<?php
// Inclure le footer
include 'includes/footer.php';
?>
