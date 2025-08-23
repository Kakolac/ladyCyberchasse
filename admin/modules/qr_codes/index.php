<?php
session_start();
require_once '../../../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../../../admin/login.php');
    exit();
}

// Configuration pour le header
$page_title = 'Génération QR Codes - Administration Cyberchasse';
$breadcrumb_items = [
    ['text' => 'Administration', 'url' => '../../../admin/admin2.php', 'active' => false],
    ['text' => 'Génération QR Codes', 'url' => 'index.php', 'active' => true]
];

include '../../../admin/includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card admin-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-qrcode"></i> Génération des QR Codes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-cog"></i> Génération des Tokens</h6>
                                </div>
                                <div class="card-body">
                                    <p>Générer les tokens d'accès pour les équipes et lieux.</p>
                                    <a href="generate.php" class="btn btn-primary">
                                        <i class="fas fa-play"></i> Lancer la Génération
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-image"></i> Génération des Images</h6>
                                </div>
                                <div class="card-body">
                                    <p>Générer les images QR codes à partir des tokens.</p>
                                    <a href="generate_image.php" class="btn btn-success">
                                        <i class="fas fa-image"></i> Générer les Images
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../../../admin/includes/footer.php';
?>
