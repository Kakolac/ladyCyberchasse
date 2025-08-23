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
            case 'generate_all_tokens':
                // Générer tous les tokens pour toutes les équipes du parcours
                try {
                    $pdo->beginTransaction();
                    
                    // Récupérer toutes les équipes assignées au parcours
                    $stmt = $pdo->prepare("
                        SELECT ep.equipe_id, ep.parcours_id
                        FROM cyber_equipes_parcours ep
                        WHERE ep.parcours_id = ? AND ep.statut = 'en_cours'
                    ");
                    $stmt->execute([$parcours_id]);
                    $equipes_parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Récupérer tous les lieux du parcours
                    $stmt = $pdo->prepare("
                        SELECT pl.lieu_id, pl.ordre
                        FROM cyber_parcours_lieux pl
                        WHERE pl.parcours_id = ?
                        ORDER BY pl.ordre
                    ");
                    $stmt->execute([$parcours_id]);
                    $lieux_parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $tokens_generes = 0;
                    
                    foreach ($equipes_parcours as $equipe_parcours) {
                        foreach ($lieux_parcours as $lieu_parcours) {
                            // Vérifier si le token existe déjà
                            $stmt = $pdo->prepare("
                                SELECT COUNT(*) FROM cyber_token 
                                WHERE equipe_id = ? AND parcours_id = ? AND lieu_id = ?
                            ");
                            $stmt->execute([$equipe_parcours['equipe_id'], $parcours_id, $lieu_parcours['lieu_id']]);
                            
                            if ($stmt->fetchColumn() == 0) {
                                // Générer un nouveau token
                                $token = bin2hex(random_bytes(16)); // 32 caractères hexadécimaux
                                
                                $stmt = $pdo->prepare("
                                    INSERT INTO cyber_token (equipe_id, parcours_id, lieu_id, token_acces, ordre_visite, statut) 
                                    VALUES (?, ?, ?, ?, ?, 'en_attente')
                                ");
                                $stmt->execute([
                                    $equipe_parcours['equipe_id'], 
                                    $parcours_id, 
                                    $lieu_parcours['lieu_id'], 
                                    $token, 
                                    $lieu_parcours['ordre']
                                ]);
                                
                                $tokens_generes++;
                            }
                        }
                    }
                    
                    $pdo->commit();
                    $success_message = "✅ {$tokens_generes} tokens générés avec succès pour ce parcours !";
                    
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error_message = "Erreur lors de la génération des tokens : " . $e->getMessage();
                }
                break;
                
            case 'regenerate_equipe_tokens':
                // Régénérer tous les tokens d'une équipe spécifique
                $equipe_id = (int)$_POST['equipe_id'];
                
                if ($equipe_id > 0) {
                    try {
                        $pdo->beginTransaction();
                        
                        // Supprimer les anciens tokens de cette équipe pour ce parcours
                        $stmt = $pdo->prepare("
                            DELETE FROM cyber_token 
                            WHERE equipe_id = ? AND parcours_id = ?
                        ");
                        $stmt->execute([$equipe_id, $parcours_id]);
                        
                        // Récupérer tous les lieux du parcours
                        $stmt = $pdo->prepare("
                            SELECT pl.lieu_id, pl.ordre
                            FROM cyber_parcours_lieux pl
                            WHERE pl.parcours_id = ?
                            ORDER BY pl.ordre
                        ");
                        $stmt->execute([$parcours_id]);
                        $lieux_parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        // Générer de nouveaux tokens
                        foreach ($lieux_parcours as $lieu_parcours) {
                            $token = bin2hex(random_bytes(16));
                            
                            $stmt = $pdo->prepare("
                                INSERT INTO cyber_token (equipe_id, parcours_id, lieu_id, token_acces, ordre_visite, statut) 
                                VALUES (?, ?, ?, ?, ?, 'en_attente')
                            ");
                            $stmt->execute([
                                $equipe_id, 
                                $parcours_id, 
                                $lieu_parcours['lieu_id'], 
                                $token, 
                                $lieu_parcours['ordre']
                            ]);
                        }
                        
                        $pdo->commit();
                        $success_message = "✅ Tokens régénérés avec succès pour cette équipe !";
                        
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $error_message = "Erreur lors de la régénération des tokens : " . $e->getMessage();
                    }
                }
                break;
                
            case 'delete_equipe_tokens':
                // Supprimer tous les tokens d'une équipe pour ce parcours
                $equipe_id = (int)$_POST['equipe_id'];
                
                if ($equipe_id > 0) {
                    try {
                        $pdo->beginTransaction();
                        
                        // 1. Supprimer tous les tokens de cette équipe pour ce parcours
                        $stmt = $pdo->prepare("DELETE FROM cyber_token WHERE equipe_id = ? AND parcours_id = ?");
                        $stmt->execute([$equipe_id, $parcours_id]);
                        $tokens_supprimes = $stmt->rowCount();
                        
                        // 2. Optionnel : Retirer aussi l'équipe du parcours
                        // Décommentez les lignes suivantes si vous voulez cette fonctionnalité
                        /*
                        $stmt = $pdo->prepare("DELETE FROM cyber_equipes_parcours WHERE equipe_id = ? AND parcours_id = ?");
                        $stmt->execute([$equipe_id, $parcours_id]);
                        */
                        
                        $pdo->commit();
                        $success_message = "✅ {$tokens_supprimes} token(s) supprimé(s) avec succès pour cette équipe !";
                        
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Récupération des tokens existants pour ce parcours
try {
    $stmt = $pdo->prepare("
        SELECT ct.*, e.nom as equipe_nom, e.couleur, l.nom as lieu_nom, l.slug as lieu_slug,
               pl.ordre as ordre_parcours, pl.temps_limite_parcours
        FROM cyber_token ct
        JOIN cyber_equipes e ON ct.equipe_id = e.id
        JOIN cyber_lieux l ON ct.lieu_id = l.id
        JOIN cyber_parcours_lieux pl ON ct.parcours_id = pl.parcours_id AND ct.lieu_id = pl.lieu_id
        WHERE ct.parcours_id = ?
        ORDER BY ct.equipe_id, ct.ordre_visite
    ");
    $stmt->execute([$parcours_id]);
    $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = "Erreur lors de la récupération des tokens : " . $e->getMessage();
    $tokens = [];
}

// Récupération des équipes assignées au parcours
try {
    $stmt = $pdo->prepare("
        SELECT ep.*, e.nom, e.couleur
        FROM cyber_equipes_parcours ep
        JOIN cyber_equipes e ON ep.equipe_id = e.id
        WHERE ep.parcours_id = ?
        ORDER BY e.nom
    ");
    $stmt->execute([$parcours_id]);
    $equipes_parcours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $equipes_parcours = [];
}

// Configuration pour le header
$page_title = 'Gestion des Tokens - Administration Cyberchasse';
$breadcrumb_items = [
    ['text' => 'Administration', 'url' => '../../../admin/admin2.php', 'active' => false],
    ['text' => 'Gestion des Parcours', 'url' => 'index.php', 'active' => false],
    ['text' => 'Gestion du Parcours - ' . htmlspecialchars($parcours['nom']), 'url' => 'manage_parcours.php?id=' . $parcours_id, 'active' => false],
    ['text' => 'Gestion des Tokens', 'url' => 'token_manager.php?id=' . $parcours_id, 'active' => true]
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
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-key"></i> Gestion des Tokens - Parcours : <?php echo htmlspecialchars($parcours['nom']); ?>
                        </h5>
                        <div>
                            <a href="manage_parcours.php?id=<?php echo $parcours_id; ?>" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-arrow-left"></i> Retour à la gestion
                            </a>
                            <a href="index.php" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-list"></i> Liste des parcours
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

    <!-- Actions de génération des tokens -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card admin-card">
                <div class="card-header bg-success text-white">
                    <h6><i class="fas fa-magic"></i> Génération Automatique</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Génère automatiquement tous les tokens d'accès pour toutes les équipes assignées à ce parcours.
                    </p>
                    
                    <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir générer tous les tokens ? Cela peut prendre quelques secondes.');">
                        <input type="hidden" name="action" value="generate_all_tokens">
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-magic"></i> Générer Tous les Tokens
                        </button>
                    </form>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Cette action créera des tokens uniques pour chaque équipe et chaque lieu du parcours.
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card admin-card">
                <div class="card-header bg-warning text-dark">
                    <h6><i class="fas fa-chart-bar"></i> Statistiques des Tokens</h6>
                </div>
                <div class="card-body">
                    <?php
                    $total_tokens = count($tokens);
                    $tokens_en_attente = count(array_filter($tokens, fn($t) => $t['statut'] === 'en_attente'));
                    $tokens_en_cours = count(array_filter($tokens, fn($t) => $t['statut'] === 'en_cours'));
                    $tokens_termines = count(array_filter($tokens, fn($t) => $t['statut'] === 'termine'));
                    ?>
                    
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="border rounded p-2">
                                <h4 class="text-primary mb-0"><?php echo $total_tokens; ?></h4>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded p-2">
                                <h4 class="text-warning mb-0"><?php echo $tokens_en_attente; ?></h4>
                                <small class="text-muted">En attente</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded p-2">
                                <h4 class="text-info mb-0"><?php echo $tokens_en_cours; ?></h4>
                                <small class="text-muted">En cours</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded p-2">
                                <h4 class="text-success mb-0"><?php echo $tokens_termines; ?></h4>
                                <small class="text-muted">Terminés</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestion des tokens par équipe -->
    <div class="row">
        <div class="col-12">
            <div class="card admin-card">
                <div class="card-header bg-primary text-white">
                    <h6><i class="fas fa-list"></i> Tokens par Équipe</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($equipes_parcours)): ?>
                        <div class="alert alert-info">
                            <h6>ℹ️ Aucune équipe assignée</h6>
                            <p>Vous devez d'abord assigner des équipes au parcours pour pouvoir générer des tokens.</p>
                            <a href="manage_parcours.php?id=<?php echo $parcours_id; ?>" class="btn btn-primary">
                                <i class="fas fa-users"></i> Gérer les équipes
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($equipes_parcours as $equipe): ?>
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2" style="width: 20px; height: 20px; background-color: <?php echo $equipe['couleur']; ?>; border-radius: 50%;"></div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($equipe['nom']); ?></h6>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-warning btn-sm" 
                                                    onclick="regenerateEquipeTokens(<?php echo $equipe['equipe_id']; ?>, '<?php echo htmlspecialchars($equipe['nom']); ?>')" 
                                                    title="Régénérer tous les tokens">
                                                <i class="fas fa-sync-alt"></i> Régénérer
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="deleteEquipeTokens(<?php echo $equipe['equipe_id']; ?>, '<?php echo htmlspecialchars($equipe['nom']); ?>')" 
                                                    title="Supprimer tous les tokens">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $tokens_equipe = array_filter($tokens, fn($t) => $t['equipe_id'] === $equipe['equipe_id']);
                                    ?>
                                    
                                    <?php if (empty($tokens_equipe)): ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Aucun token généré pour cette équipe.
                                            <button type="button" class="btn btn-success btn-sm ms-2" 
                                                    onclick="generateEquipeTokens(<?php echo $equipe['equipe_id']; ?>)">
                                                <i class="fas fa-plus"></i> Générer
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th style="width: 80px;">Ordre</th>
                                                        <th>Lieu</th>
                                                        <th style="width: 200px;">Token</th>
                                                        <th style="width: 100px;">Statut</th>
                                                        <th style="width: 120px;">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($tokens_equipe as $token): ?>
                                                        <tr>
                                                            <td>
                                                                <span class="badge bg-secondary"><?php echo $token['ordre_visite']; ?></span>
                                                            </td>
                                                            <td>
                                                                <strong><?php echo htmlspecialchars($token['lieu_nom']); ?></strong>
                                                                <br><small class="text-muted"><?php echo htmlspecialchars($token['lieu_slug']); ?></small>
                                                            </td>
                                                            <td>
                                                                <code class="text-primary"><?php echo substr($token['token_acces'], 0, 8) . '...'; ?></code>
                                                                <button type="button" class="btn btn-outline-primary btn-sm ms-1" 
                                                                        onclick="copyToken('<?php echo $token['token_acces']; ?>')" 
                                                                        title="Copier le token">
                                                                    <i class="fas fa-copy"></i>
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-<?php 
                                                                    echo $token['statut'] === 'termine' ? 'success' : 
                                                                        ($token['statut'] === 'en_cours' ? 'info' : 'warning'); 
                                                                ?>">
                                                                    <?php echo ucfirst($token['statut']); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <button type="button" class="btn btn-info btn-sm" 
                                                                            onclick="showQRCode('<?php echo $token['token_acces']; ?>', '<?php echo htmlspecialchars($token['lieu_nom']); ?>')" 
                                                                            title="Voir le QR code">
                                                                        <i class="fas fa-qrcode"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-success btn-sm" 
                                                                            onclick="downloadQRCode('<?php echo $token['token_acces']; ?>')" 
                                                                            title="Télécharger le QR code">
                                                                        <i class="fas fa-download"></i>
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
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour la régénération des tokens -->
<div class="modal fade" id="regenerateTokensModal" tabindex="-1" aria-labelledby="regenerateTokensModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="regenerateTokensModalLabel">
                    <i class="fas fa-sync-alt"></i> Régénérer les Tokens
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="regenerate_equipe_tokens">
                <input type="hidden" name="equipe_id" id="regenerateEquipeId">
                
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Attention !</strong> Cette action va :
                        <ul class="mb-0 mt-2">
                            <li>Supprimer tous les tokens existants pour cette équipe</li>
                            <li>Générer de nouveaux tokens uniques</li>
                            <li>Rendre les anciens QR codes inutilisables</li>
                        </ul>
                    </div>
                    
                    <p>Êtes-vous sûr de vouloir régénérer tous les tokens pour l'équipe <strong id="regenerateEquipeNom"></strong> ?</p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-sync-alt"></i> Régénérer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour la suppression des tokens -->
<div class="modal fade" id="deleteTokensModal" tabindex="-1" aria-labelledby="deleteTokensModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTokensModalLabel">
                    <i class="fas fa-trash"></i> Supprimer les Tokens
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="delete_equipe_tokens">
                <input type="hidden" name="equipe_id" id="deleteEquipeId">
                
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Attention !</strong> Cette action va :
                        <ul class="mb-0 mt-2">
                            <li>Supprimer définitivement tous les tokens de cette équipe</li>
                            <li>Rendre impossible l'accès aux lieux</li>
                            <li>Nécessiter une nouvelle génération de tokens</li>
                        </ul>
                    </div>
                    
                    <p>Êtes-vous sûr de vouloir supprimer tous les tokens pour l'équipe <strong id="deleteEquipeNom"></strong> ?</p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fonction pour copier un token dans le presse-papiers
function copyToken(token) {
    navigator.clipboard.writeText(token).then(function() {
        // Afficher une notification de succès
        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-header bg-success text-white">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong class="me-auto">Token copié !</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    Le token a été copié dans le presse-papiers.
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        
        // Supprimer la notification après 3 secondes
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3000);
    });
}

// Fonction pour afficher le QR code
function showQRCode(token, lieuNom) {
    // Ici vous pouvez implémenter l'affichage du QR code
    alert(`QR Code pour ${lieuNom}\nToken: ${token}\n\nFonctionnalité à implémenter.`);
}

// Fonction pour télécharger le QR code
function downloadQRCode(token) {
    // Ici vous pouvez implémenter le téléchargement du QR code
    alert(`Téléchargement du QR code pour le token: ${token}\n\nFonctionnalité à implémenter.`);
}

// Fonction pour régénérer les tokens d'une équipe
function regenerateEquipeTokens(equipeId, equipeNom) {
    document.getElementById('regenerateEquipeId').value = equipeId;
    document.getElementById('regenerateEquipeNom').textContent = equipeNom;
    
    const modal = new bootstrap.Modal(document.getElementById('regenerateTokensModal'));
    modal.show();
}

// Fonction pour supprimer les tokens d'une équipe
function deleteEquipeTokens(equipeId, equipeNom) {
    document.getElementById('deleteEquipeId').value = equipeId;
    document.getElementById('deleteEquipeNom').textContent = equipeNom;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteTokensModal'));
    modal.show();
}

// Fonction pour générer les tokens d'une équipe (à implémenter)
function generateEquipeTokens(equipeId) {
    // Ici vous pouvez implémenter la génération des tokens pour une équipe spécifique
    alert(`Génération des tokens pour l'équipe ID: ${equipeId}\n\nFonctionnalité à implémenter.`);
}
</script>

<?php include '../../../admin/includes/footer.php'; ?>
