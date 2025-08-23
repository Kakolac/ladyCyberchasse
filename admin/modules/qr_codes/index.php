<?php
session_start();
require_once '../../../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../../../admin/login.php');
    exit();
}

// Configuration pour le header
$page_title = 'Gestion des QR Codes - Administration Cyberchasse';
$breadcrumb_items = [
    ['text' => 'Administration', 'url' => '../../../admin/admin2.php', 'active' => false],
    ['text' => 'QR Codes', 'url' => 'index.php', 'active' => true]
];

include '../../../admin/includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card admin-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-qrcode"></i> Gestion des QR Codes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-eye"></i> Visualisation des Tokens et QR Codes</h6>
                                </div>
                                <div class="card-body">
                                    <p>Visualiser, gérer et imprimer tous les tokens générés pour les parcours avec leurs QR codes.</p>
                                    <a href="generate.php" class="btn btn-primary btn-lg">
                                        <i class="fas fa-list"></i> Voir les Tokens et QR Codes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informations</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Workflow des QR Codes :</strong></p>
                                    <ol>
                                        <li><strong>Génération des tokens</strong> : Utilisez le gestionnaire de tokens dans la gestion des parcours</li>
                                        <li><strong>Visualisation complète</strong> : Consultez tous les tokens avec leurs QR codes générés automatiquement</li>
                                        <li><strong>Impression et téléchargement</strong> : Imprimez ou téléchargez les QR codes selon vos besoins</li>
                                        <li><strong>Utilisation</strong> : Les équipes scannent les QR codes pour accéder aux lieux</li>
                                    </ol>
                                    
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Important :</strong> Les tokens doivent être générés dans la gestion des parcours avant de pouvoir créer des QR codes.
                                        <a href="../parcours/" class="btn btn-warning btn-sm ms-2">
                                            <i class="fas fa-arrow-right"></i> Aller aux Parcours
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
</div>

<?php
include '../../../admin/includes/footer.php';
?>
