<?php
session_start();
require_once '../config/connexion.php';
require_once '../config/env.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
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

        <div class="row">
            <?php include 'includes/outils_administration.php'; ?>
        </div>



<?php
// Inclure le footer
include 'includes/footer.php';
?>
