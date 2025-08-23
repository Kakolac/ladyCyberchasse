<?php
session_start();
require_once '../../../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../../../admin/login.php');
    exit();
}

// Configuration pour le header
$page_title = 'Gestion des Équipes';
$breadcrumb_items = [
    ['text' => 'Administration', 'url' => '../../../admin/admin2.php', 'active' => false],
    ['text' => 'Gestion des Équipes', 'url' => 'index.php', 'active' => true]
];

include '../../../admin/includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card admin-card">
                <div class="card-header">
                    <h4><i class="fas fa-users"></i> Gestion des Équipes</h4>
                </div>
                <div class="card-body">
                    <!-- Contenu de gestion des équipes -->
                    <p>Module de gestion des équipes en cours de développement...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../admin/includes/footer.php'; ?>
