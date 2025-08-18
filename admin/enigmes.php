<?php
session_start();
require_once '../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Inclusion des gestionnaires
require_once 'enigmes/handlers/qcm_handler.php';
require_once 'enigmes/handlers/texte_libre_handler.php';
require_once 'enigmes/handlers/calcul_handler.php';
require_once 'enigmes/handlers/image_handler.php';
require_once 'enigmes/handlers/audio_handler.php';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    switch ($_POST['action']) {
        case 'create_enigme':
            $type_enigme_id = $_POST['type_enigme_id'];
            $titre = trim($_POST['titre']);
            
            // Validation selon le type
            $errors = validateEnigmeData($_POST, $type_enigme_id);
            
            if (empty($errors)) {
                // Génération automatique du JSON selon le type
                $donnees_json = generateEnigmeJSON($_POST, $type_enigme_id);
                
                $stmt = $pdo->prepare("INSERT INTO enigmes (type_enigme_id, titre, donnees) VALUES (?, ?, ?)");
                if ($stmt->execute([$type_enigme_id, $titre, $donnees_json])) {
                    $success_message = "Énigme créée avec succès !";
                } else {
                    $error_message = "Erreur lors de la création de l'énigme";
                }
            } else {
                $error_message = implode(', ', $errors);
            }
            break;
            
        case 'update_enigme':
            $enigme_id = $_POST['enigme_id'];
            $type_enigme_id = $_POST['type_enigme_id'];
            $titre = trim($_POST['titre']);
            $actif = isset($_POST['actif']) ? 1 : 0;
            
            // Validation selon le type
            $errors = validateEnigmeData($_POST, $type_enigme_id);
            
            if (empty($errors)) {
                // Génération automatique du JSON selon le type
                $donnees_json = generateEnigmeJSON($_POST, $type_enigme_id);
                
                $stmt = $pdo->prepare("UPDATE enigmes SET type_enigme_id = ?, titre = ?, donnees = ?, actif = ? WHERE id = ?");
                if ($stmt->execute([$type_enigme_id, $titre, $donnees_json, $actif, $enigme_id])) {
                    $success_message = "Énigme mise à jour avec succès !";
                } else {
                    $error_message = "Erreur lors de la mise à jour de l'énigme";
                }
            } else {
                $error_message = implode(', ', $errors);
            }
            break;
            
        case 'delete_enigme':
            $enigme_id = $_POST['enigme_id'];
            
            // Vérifier qu'aucun lieu n'utilise cette énigme
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM lieux WHERE enigme_id = ?");
            $stmt->execute([$enigme_id]);
            $count = $stmt->fetchColumn();
            
            if ($count == 0) {
                $stmt = $pdo->prepare("DELETE FROM enigmes WHERE id = ?");
                if ($stmt->execute([$enigme_id])) {
                    $success_message = "Énigme supprimée avec succès !";
                } else {
                    $error_message = "Erreur lors de la suppression de l'énigme";
                }
            } else {
                $error_message = "Impossible de supprimer cette énigme : elle est utilisée par " . $count . " lieu(x)";
            }
            break;
    }
}

// Fonction de validation selon le type d'énigme
function validateEnigmeData($post_data, $type_enigme_id) {
    
    switch ($type_enigme_id) {
        case '1': // QCM
            return QCMHandler::validate($post_data);
        case '2': // Texte Libre
            return TexteLibreHandler::validate($post_data);
        case '3': // Calcul
            return CalculHandler::validate($post_data);
        case '4': // Image
            return ImageHandler::validate($post_data);
        case '5': // Audio
            return AudioHandler::validate($post_data);
        default:
            return ["Type d'énigme non reconnu"];
    }
}

// Fonction pour générer automatiquement le JSON selon le type d'énigme
function generateEnigmeJSON($post_data, $type_enigme_id) {
    
    switch ($type_enigme_id) {
        case '1': // QCM
            return QCMHandler::generateJSON($post_data);
        case '2': // Texte Libre
            return TexteLibreHandler::generateJSON($post_data);
        case '3': // Calcul
            return CalculHandler::generateJSON($post_data);
        case '4': // Image
            return ImageHandler::generateJSON($post_data);
        case '5': // Audio
            return AudioHandler::generateJSON($post_data);
        default:
            return json_encode([]);
    }
}

// Récupération des énigmes avec leurs types
try {
    $stmt = $pdo->query("
        SELECT e.*, te.nom as type_nom, te.template, te.actif as type_actif
        FROM enigmes e 
        LEFT JOIN types_enigmes te ON e.type_enigme_id = te.id 
        ORDER BY e.created_at DESC
    ");
    $enigmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des énigmes: " . $e->getMessage();
}

// Récupération des types d'énigmes pour les formulaires
try {
    $stmt = $pdo->query("SELECT id, nom, template FROM types_enigmes WHERE actif = 1 ORDER BY nom");
    $types_enigmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $types_enigmes = [];
}

// Configuration pour le header
$page_title = 'Gestion des Énigmes - Administration Cyberchasse';
$current_page = 'enigmes';
$breadcrumb_items = [
    ['text' => 'Tableau de bord', 'url' => 'admin.php', 'active' => false],
    ['text' => 'Énigmes', 'url' => 'enigmes.php', 'active' => true]
];

include 'includes/header.php';
?>

        <!-- Messages de succès/erreur -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Contenu principal -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h1 class="mb-4">Gestion des Énigmes</h1>
                    
                    <!-- Boutons d'action -->
                    <div class="mb-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEnigmeModal">
                            <i class="fas fa-plus"></i> Nouvelle Énigme
                        </button>
                    </div>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card admin-card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-puzzle-piece fa-3x mb-3"></i>
                        <h3><?php echo count($enigmes); ?></h3>
                        <p class="mb-0">Total Énigmes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h3><?php echo count(array_filter($enigmes, function($e) { return $e['actif']; })); ?></h3>
                        <p class="mb-0">Énigmes Actives</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-list fa-3x mb-3"></i>
                        <h3><?php echo count(array_unique(array_column($enigmes, 'type_nom'))); ?></h3>
                        <p class="mb-0">Types Utilisés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-warning text-dark">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                        <h3><?php 
                            $stmt = $pdo->query("SELECT COUNT(*) FROM lieux WHERE enigme_id IS NOT NULL");
                            echo $stmt->fetchColumn();
                        ?></h3>
                        <p class="mb-0">Lieux avec Énigmes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des énigmes -->
        <div class="card admin-card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-list"></i> Énigmes Disponibles</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <h6>❌ Erreur</h6>
                        <p><?php echo $error; ?></p>
                    </div>
                <?php elseif (empty($enigmes)): ?>
                    <div class="alert alert-info">
                        <h6>ℹ️ Aucune énigme trouvée</h6>
                        <p>Créez votre première énigme pour commencer.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($enigmes as $enigme): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card enigme-card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-puzzle-piece text-primary"></i>
                                            <?php echo htmlspecialchars($enigme['titre']); ?>
                                        </h5>
                                        <div>
                                            <span class="badge bg-<?php echo $enigme['actif'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $enigme['actif'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                            <span class="badge bg-info ms-1">
                                                <?php echo htmlspecialchars($enigme['type_nom']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Type :</small>
                                                <strong><?php echo htmlspecialchars($enigme['type_nom']); ?></strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Template :</small>
                                                <strong><?php echo htmlspecialchars($enigme['template']); ?></strong>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">Données JSON :</small>
                                            <div class="json-editor bg-light p-2 rounded mt-1" style="max-height: 100px; overflow-y: auto;">
                                                <pre><?php echo htmlspecialchars(substr($enigme['donnees'], 0, 200)); ?><?php echo strlen($enigme['donnees']) > 200 ? '...' : ''; ?></pre>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editEnigmeModal"
                                                    data-enigme-id="<?php echo $enigme['id']; ?>"
                                                    data-type-enigme-id="<?php echo $enigme['type_enigme_id']; ?>"
                                                    data-titre="<?php echo htmlspecialchars($enigme['titre']); ?>"
                                                    data-donnees="<?php echo htmlspecialchars($enigme['donnees']); ?>"
                                                    data-actif="<?php echo $enigme['actif']; ?>">
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>
                                            
                                            <?php if ($enigme['actif']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_enigme">
                                                    <input type="hidden" name="enigme_id" value="<?php echo $enigme['id']; ?>">
                                                    <input type="hidden" name="type_enigme_id" value="<?php echo $enigme['type_enigme_id']; ?>">
                                                    <input type="hidden" name="titre" value="<?php echo htmlspecialchars($enigme['titre']); ?>">
                                                    <input type="hidden" name="actif" value="0">
                                                    <button type="submit" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-pause"></i> Désactiver
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_enigme">
                                                    <input type="hidden" name="enigme_id" value="<?php echo $enigme['id']; ?>">
                                                    <input type="hidden" name="type_enigme_id" value="<?php echo $enigme['type_enigme_id']; ?>">
                                                    <input type="hidden" name="titre" value="<?php echo htmlspecialchars($enigme['titre']); ?>">
                                                    <input type="hidden" name="actif" value="1">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-play"></i> Activer
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette énigme ?')">
                                                <input type="hidden" name="action" value="delete_enigme">
                                                <input type="hidden" name="enigme_id" value="<?php echo $enigme['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal de création d'énigme -->
    <div class="modal fade" id="createEnigmeModal" tabindex="-1" aria-labelledby="createEnigmeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEnigmeModalLabel">
                        <i class="fas fa-plus"></i> Nouvelle Énigme
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_enigme">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="type_enigme_id" class="form-label">Type d'énigme</label>
                                <select class="form-select" name="type_enigme_id" id="type_enigme_id" required>
                                    <option value="">Sélectionner un type</option>
                                    <?php foreach ($types_enigmes as $type): ?>
                                        <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['nom']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="titre" class="form-label">Titre de l'énigme</label>
                                <input type="text" class="form-control" name="titre" id="titre" required 
                                       placeholder="Ex: Question sur la cybersécurité">
                            </div>
                        </div>
                        
                        <!-- Inclusion des formulaires spécifiques -->
                        <?php include 'enigmes/forms/qcm_form.php'; ?>
                        <?php include 'enigmes/forms/texte_libre_form.php'; ?>
                        <?php include 'enigmes/forms/calcul_form.php'; ?>
                        <?php include 'enigmes/forms/image_form.php'; ?>
                        <?php include 'enigmes/forms/audio_form.php'; ?>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Créer l'énigme
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'édition d'énigme -->
    <div class="modal fade" id="editEnigmeModal" tabindex="-1" aria-labelledby="editEnigmeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEnigmeModalLabel">
                        <i class="fas fa-edit"></i> Modifier l'Énigme
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_enigme">
                        <input type="hidden" name="enigme_id" id="editEnigmeId">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editTypeEnigmeId" class="form-label">Type d'énigme</label>
                                <select class="form-select" name="type_enigme_id" id="editTypeEnigmeId" required>
                                    <?php foreach ($types_enigmes as $type): ?>
                                        <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['nom']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="editTitre" class="form-label">Titre de l'énigme</label>
                                <input type="text" class="form-control" name="titre" id="editTitre" required>
                            </div>
                        </div>
                        
                        <!-- Formulaires d'édition spécifiques par type -->
                        <!-- QCM Édition -->
                        <div id="edit-form-qcm" class="form-type-container">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6><i class="fas fa-list-check"></i> Configuration QCM</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="edit_question_qcm" class="form-label">Question</label>
                                        <textarea class="form-control" name="question_qcm" id="edit_question_qcm" rows="3"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="edit_option_a" class="form-label">Option A</label>
                                                <input type="text" class="form-control" name="option_a" id="edit_option_a">
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_option_b" class="form-label">Option B</label>
                                                <input type="text" class="form-control" name="option_b" id="edit_option_b">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="edit_option_c" class="form-label">Option C</label>
                                                <input type="text" class="form-control" name="option_c" id="edit_option_c">
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_option_d" class="form-label">Option D</label>
                                                <input type="text" class="form-control" name="option_d" id="edit_option_d">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_reponse_correcte_qcm" class="form-label">Réponse correcte</label>
                                        <select class="form-select" name="reponse_correcte_qcm" id="edit_reponse_correcte_qcm" required>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Texte Libre Édition -->
                        <div id="edit-form-texte-libre" class="form-type-container">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6><i class="fas fa-font"></i> Configuration Texte Libre</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="edit_titre_texte" class="form-label">Titre de l'énigme</label>
                                        <input type="text" class="form-control" name="titre_texte" id="edit_titre_texte">
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_indice_texte" class="form-label">Indice</label>
                                        <textarea class="form-control" name="indice_texte" id="edit_indice_texte" rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_contexte_texte" class="form-label">Contexte/Description</label>
                                        <textarea class="form-control" name="contexte_texte" id="edit_contexte_texte" rows="4"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_question_texte" class="form-label">Question</label>
                                        <textarea class="form-control" name="question_texte" id="edit_question_texte" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_reponse_correcte_texte" class="form-label">Réponse correcte</label>
                                        <input type="text" class="form-control" name="reponse_correcte_texte" id="edit_reponse_correcte_texte">
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_reponses_acceptees_texte" class="form-label">Réponses acceptées (séparées par des virgules)</label>
                                        <input type="text" class="form-control" name="reponses_acceptees_texte" id="edit_reponses_acceptees_texte">
                                        <small class="text-muted">Séparez plusieurs réponses par des virgules.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Calcul Édition -->
                        <div id="edit-form-calcul" class="form-type-container">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6><i class="fas fa-calculator"></i> Configuration Calcul</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="edit_question_calcul" class="form-label">Question/Énoncé</label>
                                        <textarea class="form-control" name="question_calcul" id="edit_question_calcul" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_reponse_correcte_calcul" class="form-label">Réponse correcte</label>
                                        <input type="text" class="form-control" name="reponse_correcte_calcul" id="edit_reponse_correcte_calcul">
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_reponses_acceptees_calcul" class="form-label">Réponses acceptées (séparées par des virgules)</label>
                                        <input type="text" class="form-control" name="reponses_acceptees_calcul" id="edit_reponses_acceptees_calcul">
                                        <small class="text-muted">Séparez plusieurs réponses par des virgules.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_indice_calcul" class="form-label">Indice (optionnel)</label>
                                        <textarea class="form-control" name="indice_calcul" id="edit_indice_calcul" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Image Édition -->
                        <div id="edit-form-image" class="form-type-container">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6><i class="fas fa-image"></i> Configuration Image</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="edit_question_image" class="form-label">Question</label>
                                        <textarea class="form-control" name="question_image" id="edit_question_image" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_reponse_correcte_image" class="form-label">Réponse correcte</label>
                                        <input type="text" class="form-control" name="reponse_correcte_image" id="edit_reponse_correcte_image">
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_reponses_acceptees_image" class="form-label">Réponses acceptées (séparées par des virgules)</label>
                                        <input type="text" class="form-control" name="reponses_acceptees_image" id="edit_reponses_acceptees_image">
                                        <small class="text-muted">Séparez plusieurs réponses par des virgules.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_indice_image" class="form-label">Indice (optionnel)</label>
                                        <textarea class="form-control" name="indice_image" id="edit_indice_image" rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_url_image" class="form-label">URL de l'image (optionnel)</label>
                                        <input type="url" class="form-control" name="url_image" id="edit_url_image">
                                        <small class="text-muted">Laissez vide si l'image sera ajoutée manuellement.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Audio Édition -->
                        <div id="edit-form-audio" class="form-type-container">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6><i class="fas fa-music"></i> Configuration Audio</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="edit_question_audio" class="form-label">Question</label>
                                        <textarea class="form-control" name="question_audio" id="edit_question_audio" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_audio_url" class="form-label">URL Audio</label>
                                        <input type="url" class="form-control" name="audio_url" id="edit_audio_url">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="edit_reponse_correcte_audio" class="form-label">Réponse correcte</label>
                                                <input type="text" class="form-control" name="reponse_correcte_audio" id="edit_reponse_correcte_audio">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="edit_reponses_acceptees_audio" class="form-label">Réponses acceptées</label>
                                                <input type="text" class="form-control" name="reponses_acceptees_audio" id="edit_reponses_acceptees_audio">
                                                <small class="text-muted">Séparez plusieurs réponses par des virgules.</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_indice_audio" class="form-label">Indice</label>
                                        <textarea class="form-control" name="indice_audio" id="edit_indice_audio" rows="2"></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_contexte_audio" class="form-label">Contexte/Description</label>
                                        <textarea class="form-control" name="contexte_audio" id="edit_contexte_audio" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="autoplay_audio" id="edit_autoplay_audio" value="1">
                                                <label class="form-check-label" for="edit_autoplay_audio">
                                                    Lecture automatique
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="loop_audio" id="edit_loop_audio" value="1">
                                                <label class="form-check-label" for="edit_loop_audio">
                                                    Lecture en boucle
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="volume_control_audio" id="edit_volume_control_audio" value="1">
                                                <label class="form-check-label" for="edit_volume_control_audio">
                                                    Contrôle du volume
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="actif" id="editActif" value="1">
                                <label class="form-check-label" for="editActif">
                                    Énigme active
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts spécifiques -->
    <script src="enigmes/js/enigme_forms.js"></script>
    <script>
        // Gestion du modal d'édition
        document.addEventListener('DOMContentLoaded', function() {
            const editEnigmeModal = document.getElementById('editEnigmeModal');
            if (editEnigmeModal) {
                editEnigmeModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const enigmeId = button.getAttribute('data-enigme-id');
                    const typeEnigmeId = button.getAttribute('data-type-enigme-id');
                    const titre = button.getAttribute('data-titre');
                    const donnees = button.getAttribute('data-donnees');
                    const actif = button.getAttribute('data-actif');
                    
                    // Mettre à jour le modal
                    document.getElementById('editEnigmeId').value = enigmeId;
                    document.getElementById('editTypeEnigmeId').value = typeEnigmeId;
                    document.getElementById('editTitre').value = titre;
                    document.getElementById('editActif').checked = actif === '1';
                    
                    // Afficher le bon formulaire et remplir les données
                    if (window.enigmeFormManager) {
                        window.enigmeFormManager.showFormType('edit');
                        window.enigmeFormManager.fillEditFormData(donnees, typeEnigmeId);
                    }
                });
            }
        });
    </script>

<?php include 'includes/footer.php'; ?>