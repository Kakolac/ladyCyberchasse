<?php
session_start();
require_once '../../../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../../../admin/login.php');
    exit();
}

// Récupération de l'ID du parcours
$parcours_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($parcours_id <= 0) {
    header('Location: index.php');
    exit();
}

$success_message = '';
$error_message = '';

// Récupération des informations du parcours
try {
    $stmt = $pdo->prepare("SELECT * FROM cyber_parcours WHERE id = ?");
    $stmt->execute([$parcours_id]);
    $parcours = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$parcours) {
        header('Location: index.php');
        exit();
    }
} catch (Exception $e) {
    $error_message = "Erreur lors de la récupération du parcours : " . $e->getMessage();
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_lieu':
                // Ajouter un lieu au parcours
                $lieu_id = (int)$_POST['lieu_id'];
                $ordre = (int)$_POST['ordre'];
                $temps_limite = (int)$_POST['temps_limite_parcours'];
                
                if ($lieu_id > 0 && $ordre > 0) {
                    try {
                        // Vérifier si le lieu n'est pas déjà dans ce parcours
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cyber_parcours_lieux WHERE parcours_id = ? AND lieu_id = ?");
                        $stmt->execute([$parcours_id, $lieu_id]);
                        
                        if ($stmt->fetchColumn() > 0) {
                            $error_message = "Ce lieu est déjà présent dans ce parcours";
                        } else {
                            $stmt = $pdo->prepare("
                                INSERT INTO cyber_parcours_lieux (parcours_id, lieu_id, ordre, temps_limite_parcours) 
                                VALUES (?, ?, ?, ?)
                            ");
                            
                            if ($stmt->execute([$parcours_id, $lieu_id, $ordre, $temps_limite])) {
                                $success_message = "Lieu ajouté au parcours avec succès !";
                            } else {
                                $error_message = "Erreur lors de l'ajout du lieu";
                            }
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "Données invalides pour l'ajout du lieu";
                }
                break;
                
            case 'update_ordre':
                // Mettre à jour l'ordre des lieux
                $lieux_data = $_POST['lieux'] ?? [];
                
                if (!empty($lieux_data)) {
                    try {
                        $pdo->beginTransaction();
                        
                        foreach ($lieux_data as $lieu_data) {
                            $lieu_id = (int)$lieu_data['lieu_id'];
                            $ordre = (int)$lieu_data['ordre'];
                            $temps_limite = (int)$lieu_data['temps_limite'];
                            
                            $stmt = $pdo->prepare("
                                UPDATE cyber_parcours_lieux 
                                SET ordre = ?, temps_limite_parcours = ? 
                                WHERE parcours_id = ? AND lieu_id = ?
                            ");
                            $stmt->execute([$ordre, $temps_limite, $parcours_id, $lieu_id]);
                        }
                        
                        $pdo->commit();
                        $success_message = "Ordre des lieux mis à jour avec succès !";
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $error_message = "Erreur lors de la mise à jour : " . $e->getMessage();
                    }
                }
                break;
                
            case 'remove_lieu':
                // Retirer un lieu du parcours
                $lieu_id = (int)$_POST['lieu_id'];
                
                if ($lieu_id > 0) {
                    try {
                        $stmt = $pdo->prepare("DELETE FROM cyber_parcours_lieux WHERE parcours_id = ? AND lieu_id = ?");
                        if ($stmt->execute([$parcours_id, $lieu_id])) {
                            $success_message = "Lieu retiré du parcours avec succès !";
                        } else {
                            $error_message = "Erreur lors du retrait du lieu";
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                }
                break;

            // NOUVELLES ACTIONS POUR LA GESTION DES ÉQUIPES
            case 'assign_equipe':
                // Assigner une équipe au parcours
                $equipe_id = (int)$_POST['equipe_id'];
                
                if ($equipe_id > 0) {
                    try {
                        // Vérifier si l'équipe n'a pas déjà un parcours actif
                        $stmt = $pdo->prepare("
                            SELECT COUNT(*) FROM cyber_equipes_parcours 
                            WHERE equipe_id = ? AND parcours_id = ? AND statut IN ('en_cours', 'termine')
                        ");
                        $stmt->execute([$equipe_id, $parcours_id]);
                        
                        if ($stmt->fetchColumn() > 0) {
                            $error_message = "Cette équipe a déjà un parcours actif";
                        } else {
                            $stmt = $pdo->prepare("
                                INSERT INTO cyber_equipes_parcours (equipe_id, parcours_id, statut, date_debut) 
                                VALUES (?, ?, 'en_cours', NOW())
                            ");
                            
                            if ($stmt->execute([$equipe_id, $parcours_id])) {
                                $success_message = "Équipe assignée au parcours avec succès !";
                            } else {
                                $error_message = "Erreur lors de l'assignation de l'équipe";
                            }
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "ID d'équipe invalide";
                }
                break;
                
            case 'change_status_equipe':
                // Changer le statut d'une équipe
                $equipe_parcours_id = (int)$_POST['equipe_parcours_id'];
                $nouveau_statut = $_POST['nouveau_statut'];
                
                if ($equipe_parcours_id > 0 && in_array($nouveau_statut, ['en_cours', 'termine', 'abandonne'])) {
                    try {
                        $date_fin = ($nouveau_statut === 'termine' || $nouveau_statut === 'abandonne') ? 'NOW()' : 'NULL';
                        
                        $stmt = $pdo->prepare("
                            UPDATE cyber_equipes_parcours 
                            SET statut = ?, date_fin = $date_fin 
                            WHERE id = ? AND parcours_id = ?
                        ");
                        
                        if ($stmt->execute([$nouveau_statut, $equipe_parcours_id, $parcours_id])) {
                            $success_message = "Statut de l'équipe mis à jour avec succès !";
                        } else {
                            $error_message = "Erreur lors de la mise à jour du statut";
                        }
                    } catch (Exception $e) {
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "Données invalides pour le changement de statut";
                }
                break;
                
            case 'remove_equipe':
                // Retirer une équipe du parcours
                $equipe_parcours_id = (int)$_POST['equipe_parcours_id'];
                
                if ($equipe_parcours_id > 0) {
                    try {
                        $pdo->beginTransaction();
                        
                        // 1. Récupérer l'ID de l'équipe avant de la supprimer
                        $stmt = $pdo->prepare("SELECT equipe_id FROM cyber_equipes_parcours WHERE id = ? AND parcours_id = ?");
                        $stmt->execute([$equipe_parcours_id, $parcours_id]);
                        $equipe_id = $stmt->fetchColumn();
                        
                        if ($equipe_id) {
                            // 2. Supprimer tous les tokens de cette équipe pour ce parcours
                            $stmt = $pdo->prepare("DELETE FROM cyber_token WHERE equipe_id = ? AND parcours_id = ?");
                            $stmt->execute([$equipe_id, $parcours_id]);
                            $tokens_supprimes = $stmt->rowCount();
                            
                            // 3. Supprimer l'assignation de l'équipe au parcours
                            $stmt = $pdo->prepare("DELETE FROM cyber_equipes_parcours WHERE id = ? AND parcours_id = ?");
                            if ($stmt->execute([$equipe_parcours_id, $parcours_id])) {
                                $pdo->commit();
                                $success_message = "✅ Équipe retirée du parcours avec succès !";
                                if ($tokens_supprimes > 0) {
                                    $success_message .= " {$tokens_supprimes} token(s) supprimé(s) automatiquement.";
                                }
                            } else {
                                throw new Exception("Erreur lors du retrait de l'équipe");
                            }
                        } else {
                            throw new Exception("Équipe non trouvée dans ce parcours");
                        }
                        
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                } else {
                    $error_message = "ID d'équipe invalide";
                }
                break;
        }
    }
}

// Récupération des lieux du parcours (triés par ordre)
try {
    $stmt = $pdo->prepare("
        SELECT pl.*, l.nom, l.slug, l.type_lieu, l.temps_limite as temps_limite_lieu,
               e.titre as enigme_titre
        FROM cyber_parcours_lieux pl
        JOIN cyber_lieux l ON pl.lieu_id = l.id
        LEFT JOIN enigmes e ON l.enigme_id = e.id
        WHERE pl.parcours_id = ?
        ORDER BY pl.ordre
    ");
    $stmt->execute([$parcours_id]);
    $lieux_parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = "Erreur lors de la récupération des lieux : " . $e->getMessage();
    $lieux_parcours = [];
}

// Récupération des lieux disponibles (non présents dans ce parcours)
try {
    $stmt = $pdo->prepare("
        SELECT l.* 
        FROM cyber_lieux l
        WHERE l.statut = 'actif'
        AND l.id NOT IN (
            SELECT lieu_id FROM cyber_parcours_lieux WHERE parcours_id = ?
        )
        ORDER BY l.nom
    ");
    $stmt->execute([$parcours_id]);
    $lieux_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $lieux_disponibles = [];
}

// Configuration pour le header
$page_title = 'Gestion du Parcours - Administration Cyberchasse';
$breadcrumb_items = [
    ['text' => 'Administration', 'url' => '../../../admin/admin2.php', 'active' => false],
    ['text' => 'Gestion des Parcours', 'url' => 'index.php', 'active' => false],
    ['text' => 'Gestion du Parcours - ' . htmlspecialchars($parcours['nom']), 'url' => 'manage_parcours.php?id=' . $parcours_id, 'active' => true]
];

include '../../../admin/includes/header.php';
?>

<div class="container-fluid">
    <!-- Messages de succès/erreur -->
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- En-tête du parcours -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card admin-card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-route"></i> Gestion du Parcours : <?php echo htmlspecialchars($parcours['nom']); ?>
                        </h5>
                        <div>
                            <a href="token_manager.php?id=<?php echo $parcours_id; ?>" class="btn btn-info btn-sm me-2">
                                <i class="fas fa-key"></i> Gérer les Tokens
                            </a>
                            <a href="index.php" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left"></i> Retour aux parcours
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Description :</strong> 
                        <?php echo htmlspecialchars($parcours['description'] ?: 'Aucune description'); ?>
                    </p>
                    <p class="mb-0">
                        <strong>Statut :</strong> 
                        <span class="badge bg-<?php echo $parcours['statut'] === 'actif' ? 'success' : 'secondary'; ?>">
                            <?php echo ucfirst($parcours['statut']); ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Ajouter un lieu au parcours -->
        <div class="col-md-4">
            <div class="card admin-card">
                <div class="card-header" data-bs-toggle="collapse" data-bs-target="#addLieuCollapse" aria-expanded="false" aria-controls="addLieuCollapse" style="cursor: pointer;">
                    <h6 class="mb-0">
                        <i class="fas fa-plus"></i> Ajouter un Lieu
                        <i class="fas fa-chevron-down float-end" id="addLieuIcon"></i>
                    </h6>
                </div>
                <div class="collapse" id="addLieuCollapse">
                    <div class="card-body">
                        <?php if (empty($lieux_disponibles)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Tous les lieux sont déjà dans ce parcours.
                            </div>
                        <?php else: ?>
                            <form method="POST">
                                <input type="hidden" name="action" value="add_lieu">
                                
                                <div class="mb-3">
                                    <label for="lieu_id" class="form-label">Lieu à ajouter</label>
                                    <select class="form-select" name="lieu_id" required>
                                        <option value="">Choisir un lieu...</option>
                                        <?php foreach ($lieux_disponibles as $lieu): ?>
                                            <option value="<?php echo $lieu['id']; ?>">
                                                <?php echo htmlspecialchars($lieu['nom']); ?> 
                                                <span class="text-muted">(<?php echo ucfirst($lieu['type_lieu']); ?>)</span>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="ordre" class="form-label">Ordre</label>
                                            <input type="number" class="form-control" name="ordre" min="1" 
                                                   value="<?php echo count($lieux_parcours) + 1; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="temps_limite_parcours" class="form-label">Temps limite (min)</label>
                                            <input type="number" class="form-control" name="temps_limite_parcours" 
                                                   min="0" value="5" required>
                                            <small class="text-muted">0 = illimité</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Ajouter au parcours
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Liste des lieux du parcours -->
        <div class="col-md-8">
            <div class="card admin-card">
                <div class="card-header" data-bs-toggle="collapse" data-bs-target="#lieuxParcoursCollapse" aria-expanded="false" aria-controls="lieuxParcoursCollapse" style="cursor: pointer;">
                    <h6 class="mb-0">
                        <i class="fas fa-list"></i> Lieux du Parcours (<?php echo count($lieux_parcours); ?>)
                        <i class="fas fa-chevron-down float-end" id="lieuxParcoursIcon"></i>
                    </h6>
                </div>
                <div class="collapse" id="lieuxParcoursCollapse">
                    <div class="card-body">
                        <?php if (empty($lieux_parcours)): ?>
                            <div class="alert alert-info">
                                <h6>ℹ️ Aucun lieu dans ce parcours</h6>
                                <p>Ajoutez des lieux en utilisant le formulaire à gauche.</p>
                            </div>
                        <?php else: ?>
                            <form method="POST" id="ordreForm">
                                <input type="hidden" name="action" value="update_ordre">
                                
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width: 80px;">Ordre</th>
                                                <th>Lieu</th>
                                                <th>Type</th>
                                                <th style="width: 120px;">Temps Limite</th>
                                                <th style="width: 100px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lieuxTableBody">
                                            <?php foreach ($lieux_parcours as $lieu): ?>
                                                <tr data-lieu-id="<?php echo $lieu['lieu_id']; ?>">
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm" 
                                                               name="lieux[<?php echo $lieu['lieu_id']; ?>][ordre]" 
                                                               value="<?php echo $lieu['ordre']; ?>" min="1" 
                                                               style="width: 60px;">
                                                        <input type="hidden" name="lieux[<?php echo $lieu['lieu_id']; ?>][lieu_id]" 
                                                               value="<?php echo $lieu['lieu_id']; ?>">
                                                    </td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($lieu['nom']); ?></strong>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($lieu['slug']); ?></small>
                                                        <?php if ($lieu['enigme_titre']): ?>
                                                            <br><span class="badge bg-success">🧩 <?php echo htmlspecialchars($lieu['enigme_titre']); ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $lieu['type_lieu'] === 'fin' ? 'success' : ($lieu['type_lieu'] === 'demarrage' ? 'warning' : 'info'); ?>">
                                                            <?php echo ucfirst($lieu['type_lieu']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm" 
                                                               name="lieux[<?php echo $lieu['lieu_id']; ?>][temps_limite]" 
                                                               value="<?php echo $lieu['temps_limite_parcours']; ?>" min="0" 
                                                               style="width: 80px;">
                                                        <small class="text-muted">min</small>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm" 
                                                                onclick="removeLieu(<?php echo $lieu['lieu_id']; ?>, '<?php echo htmlspecialchars($lieu['nom']); ?>')" 
                                                                title="Retirer du parcours">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Mettre à jour l'ordre et les temps
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NOUVELLE SECTION : Gestion des Équipes -->
    <div class="row mt-4">
        <!-- Assigner une équipe au parcours -->
        <div class="col-md-4">
            <div class="card admin-card">
                <div class="card-header bg-success text-white" data-bs-toggle="collapse" data-bs-target="#assignEquipeCollapse" aria-expanded="false" aria-controls="assignEquipeCollapse" style="cursor: pointer;">
                    <h6 class="mb-0">
                        <i class="fas fa-users"></i> Assigner des Équipes
                        <i class="fas fa-chevron-down float-end" id="assignEquipeIcon"></i>
                    </h6>
                </div>
                <div class="collapse" id="assignEquipeCollapse">
                    <div class="card-body">
                        <?php
                        // Récupération des équipes disponibles (non assignées à ce parcours)
                        try {
                            $stmt = $pdo->prepare("
                                SELECT e.* 
                                FROM cyber_equipes e
                                WHERE e.statut = 'active'
                                AND e.id NOT IN (
                                    SELECT equipe_id FROM cyber_equipes_parcours WHERE parcours_id = ?
                                )
                                ORDER BY e.nom
                            ");
                            $stmt->execute([$parcours_id]);
                            $equipes_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (Exception $e) {
                            $equipes_disponibles = [];
                        }
                        ?>
                        
                        <?php if (empty($equipes_disponibles)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Toutes les équipes sont déjà assignées à ce parcours.
                            </div>
                        <?php else: ?>
                            <form method="POST">
                                <input type="hidden" name="action" value="assign_equipe">
                                
                                <div class="mb-3">
                                    <label for="equipe_id" class="form-label">Équipe à assigner</label>
                                    <select class="form-select" name="equipe_id" required>
                                        <option value="">Choisir une équipe...</option>
                                        <?php foreach ($equipes_disponibles as $equipe): ?>
                                            <option value="<?php echo $equipe['id']; ?>">
                                                <span style="color: <?php echo $equipe['couleur']; ?>;">●</span>
                                                <?php echo htmlspecialchars($equipe['nom']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-link"></i> Assigner au parcours
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Liste des équipes assignées -->
        <div class="col-md-8">
            <div class="card admin-card">
                <div class="card-header bg-success text-white" data-bs-toggle="collapse" data-bs-target="#equipesParcoursCollapse" aria-expanded="false" aria-controls="equipesParcoursCollapse" style="cursor: pointer;">
                    <h6 class="mb-0">
                        <i class="fas fa-list"></i> Équipes du Parcours
                        <i class="fas fa-chevron-down float-end" id="equipesParcoursIcon"></i>
                    </h6>
                </div>
                <div class="collapse" id="equipesParcoursCollapse">
                    <div class="card-body">
                        <?php
                        // Récupération des équipes assignées à ce parcours avec détails
                        try {
                            $stmt = $pdo->prepare("
                                SELECT ep.*, e.nom, e.couleur, e.email_contact,
                                       (SELECT COUNT(*) FROM cyber_parcours_lieux WHERE parcours_id = ep.parcours_id) as nb_lieux_total,
                                       (SELECT COUNT(*) FROM cyber_token ct 
                                        WHERE ct.equipe_id = ep.equipe_id 
                                        AND ct.parcours_id = ep.parcours_id 
                                        AND ct.statut = 'termine') as nb_lieux_termines,
                                       (SELECT COUNT(*) FROM cyber_token ct 
                                        WHERE ct.equipe_id = ep.equipe_id 
                                        AND ct.parcours_id = ep.parcours_id 
                                        AND ct.statut = 'en_attente') as nb_lieux_en_attente
                                FROM cyber_equipes_parcours ep
                                JOIN cyber_equipes e ON ep.equipe_id = e.id
                                WHERE ep.parcours_id = ?
                                ORDER BY ep.date_debut
                            ");
                            $stmt->execute([$parcours_id]);
                            $equipes_parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (Exception $e) {
                            $equipes_parcours = [];
                        }
                        ?>
                        
                        <?php if (empty($equipes_parcours)): ?>
                            <div class="alert alert-info">
                                <h6>ℹ️ Aucune équipe assignée</h6>
                                <p>Assignez des équipes en utilisant le formulaire à gauche.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Équipe</th>
                                            <th>Statut</th>
                                            <th>Progression</th>
                                            <th>Date début</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($equipes_parcours as $equipe): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2" style="width: 20px; height: 20px; background-color: <?php echo $equipe['couleur']; ?>; border-radius: 50%;"></div>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($equipe['nom']); ?></strong>
                                                            <?php if ($equipe['email_contact']): ?>
                                                                <br><small class="text-muted"><?php echo htmlspecialchars($equipe['email_contact']); ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $equipe['statut'] === 'termine' ? 'success' : 
                                                            ($equipe['statut'] === 'abandonne' ? 'danger' : 'primary'); 
                                                    ?>">
                                                        <?php echo ucfirst($equipe['statut']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <?php 
                                                        $pourcentage = $equipe['nb_lieux_total'] > 0 ? 
                                                            round(($equipe['nb_lieux_termines'] / $equipe['nb_lieux_total']) * 100) : 0;
                                                        ?>
                                                        <div class="progress-bar bg-success" role="progressbar" 
                                                             style="width: <?php echo $pourcentage; ?>%">
                                                            <?php echo $equipe['nb_lieux_termines']; ?>/<?php echo $equipe['nb_lieux_total']; ?>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
    <?php echo $equipe['nb_lieux_termines']; ?>/<?php echo $equipe['nb_lieux_total']; ?> terminés
    <?php if (isset($equipe['nb_lieux_en_attente']) && $equipe['nb_lieux_en_attente'] > 0): ?>
        (<?php echo $equipe['nb_lieux_en_attente']; ?> en attente)
    <?php endif; ?>
</small>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php echo date('d/m/Y H:i', strtotime($equipe['date_debut'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-warning btn-sm" 
                                                                onclick="changeStatusEquipe(<?php echo $equipe['id']; ?>, '<?php echo $equipe['statut']; ?>', '<?php echo htmlspecialchars($equipe['nom']); ?>')" 
                                                                title="Changer le statut">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" 
                                                                onclick="removeEquipe(<?php echo $equipe['id']; ?>, '<?php echo htmlspecialchars($equipe['nom']); ?>')" 
                                                                title="Retirer du parcours">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de changement de statut d'équipe -->
<div class="modal fade" id="changeStatusEquipeModal" tabindex="-1" aria-labelledby="changeStatusEquipeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStatusEquipeModalLabel">
                    <i class="fas fa-edit"></i> Changer le Statut de l'Équipe
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="change_status_equipe">
                <input type="hidden" name="equipe_parcours_id" id="changeStatusEquipeId">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nouveau_statut_equipe" class="form-label">Nouveau statut</label>
                        <select class="form-select" name="nouveau_statut" id="nouveau_statut_equipe" required>
                            <option value="en_cours">En cours</option>
                            <option value="termine">Terminé</option>
                            <option value="abandonne">Abandonné</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>En cours :</strong> L'équipe suit activement le parcours<br>
                        <strong>Terminé :</strong> L'équipe a terminé le parcours avec succès<br>
                        <strong>Abandonné :</strong> L'équipe a abandonné le parcours
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fonction pour retirer un lieu du parcours
function removeLieu(lieuId, lieuNom) {
    if (confirm(`Êtes-vous sûr de vouloir retirer le lieu "${lieuNom}" de ce parcours ?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="remove_lieu">
            <input type="hidden" name="lieu_id" value="${lieuId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Tri automatique des lignes par ordre
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('lieuxTableBody');
    if (tbody) {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const orderA = parseInt(a.querySelector('input[name*="[ordre]"]').value);
            const orderB = parseInt(b.querySelector('input[name*="[ordre]"]').value);
            return orderA - orderB;
        });
        
        rows.forEach(row => tbody.appendChild(row));
    }
});

// Fonction de changement de statut d'équipe
function changeStatusEquipe(equipeParcoursId, statutActuel, equipeNom) {
    document.getElementById('changeStatusEquipeId').value = equipeParcoursId;
    document.getElementById('nouveau_statut_equipe').value = statutActuel;
    
    // Mettre à jour le titre du modal
    document.getElementById('changeStatusEquipeModalLabel').innerHTML = `<i class="fas fa-edit"></i> Changer le Statut de "${equipeNom}"`;
    
    const modal = new bootstrap.Modal(document.getElementById('changeStatusEquipeModal'));
    modal.show();
}

// Fonction pour retirer une équipe du parcours
function removeEquipe(equipeParcoursId, equipeNom) {
    if (confirm(`Êtes-vous sûr de vouloir retirer l'équipe "${equipeNom}" de ce parcours ?\n\nCette action est réversible.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="remove_equipe">
            <input type="hidden" name="equipe_parcours_id" value="${equipeParcoursId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Fonction pour gérer les icônes des sections collapsibles
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des icônes pour "Ajouter un Lieu"
    const addLieuCollapse = document.getElementById('addLieuCollapse');
    const addLieuIcon = document.getElementById('addLieuIcon');
    if (addLieuCollapse) {
        addLieuCollapse.addEventListener('show.bs.collapse', function() {
            addLieuIcon.className = 'fas fa-chevron-up float-end';
        });
        addLieuCollapse.addEventListener('hide.bs.collapse', function() {
            addLieuIcon.className = 'fas fa-chevron-down float-end';
        });
    }
    
    // Gestion des icônes pour "Lieux du Parcours"
    const lieuxParcoursCollapse = document.getElementById('lieuxParcoursCollapse');
    const lieuxParcoursIcon = document.getElementById('lieuxParcoursIcon');
    if (lieuxParcoursCollapse) {
        lieuxParcoursCollapse.addEventListener('show.bs.collapse', function() {
            lieuxParcoursIcon.className = 'fas fa-chevron-up float-end';
        });
        lieuxParcoursCollapse.addEventListener('hide.bs.collapse', function() {
            lieuxParcoursIcon.className = 'fas fa-chevron-down float-end';
        });
    }
    
    // Gestion des icônes pour "Assigner des Équipes"
    const assignEquipeCollapse = document.getElementById('assignEquipeCollapse');
    const assignEquipeIcon = document.getElementById('assignEquipeIcon');
    if (assignEquipeCollapse) {
        assignEquipeCollapse.addEventListener('show.bs.collapse', function() {
            assignEquipeIcon.className = 'fas fa-chevron-up float-end';
        });
        assignEquipeCollapse.addEventListener('hide.bs.collapse', function() {
            assignEquipeIcon.className = 'fas fa-chevron-down float-end';
        });
    }
    
    // Gestion des icônes pour "Équipes du Parcours"
    const equipesParcoursCollapse = document.getElementById('equipesParcoursCollapse');
    const equipesParcoursIcon = document.getElementById('equipesParcoursIcon');
    if (equipesParcoursCollapse) {
        equipesParcoursCollapse.addEventListener('show.bs.collapse', function() {
            equipesParcoursIcon.className = 'fas fa-chevron-up float-end';
        });
        equipesParcoursCollapse.addEventListener('hide.bs.collapse', function() {
            equipesParcoursIcon.className = 'fas fa-chevron-down float-end';
        });
    }
});

</script>

<?php include '../../../admin/includes/footer.php'; ?>