<?php
session_start();
require_once '../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Traitement de la création du lieu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $ordre = (int)$_POST['ordre'];
    $temps_limite = (int)$_POST['temps_limite'];
    $enigme_requise = isset($_POST['enigme_requise']) ? 1 : 0;
    $statut = $_POST['statut'];
    $delai_indice = (int)$_POST['delai_indice'];
    
    // Génération automatique du slug à partir du nom
    $slug = strtolower(trim($nom));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug); // Supprimer les caractères spéciaux
    $slug = preg_replace('/[\s-]+/', '_', $slug); // Remplacer espaces et tirets par des underscores
    $slug = trim($slug, '_'); // Supprimer les underscores en début/fin
    
    if (!empty($nom) && $ordre > 0 && $temps_limite > 0) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO lieux (nom, slug, description, ordre, temps_limite, enigme_requise, statut, delai_indice) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$nom, $slug, $description, $ordre, $temps_limite, $enigme_requise, $statut, $delai_indice])) {
                $success_message = "Lieu créé avec succès !";
            } else {
                $error_message = "Erreur lors de la création du lieu";
            }
        } catch (Exception $e) {
            $error_message = "Erreur : " . $e->getMessage();
        }
    } else {
        $error_message = "Tous les champs obligatoires doivent être remplis";
    }
}

// Récupération du prochain ordre disponible
try {
    $stmt = $pdo->query("SELECT COALESCE(MAX(ordre), 0) + 1 as prochain_ordre FROM lieux");
    $prochain_ordre = $stmt->fetch(PDO::FETCH_ASSOC)['prochain_ordre'];
} catch (Exception $e) {
    $prochain_ordre = 1;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Lieu - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .modal-content { border: none; border-radius: 15px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; }
        .btn-close { filter: invert(1); }
        .form-control, .form-select { border-radius: 10px; border: 2px solid #e9ecef; }
        .form-control:focus, .form-select:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
        .btn { border-radius: 10px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
        <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus"></i> Créer un Nouveau Lieu
                        </h5>
                        <a href="lieux.php" class="btn-close" aria-label="Close"></a>
                    </div>
                    
                    <form method="POST">
                        <div class="modal-body">
                            <!-- Messages de succès/erreur -->
                            <?php if (isset($success_message)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $success_message; ?>
                                    <a href="lieux.php" class="btn btn-success btn-sm ms-2">Retour aux lieux</a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $error_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom du lieu *</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required 
                                           placeholder="Ex: CDI, Cour, Laboratoire...">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ordre" class="form-label">Ordre de passage *</label>
                                    <input type="number" class="form-control" id="ordre" name="ordre" 
                                           value="<?php echo $prochain_ordre; ?>" min="1" required>
                                    <small class="text-muted">Ordre dans lequel les équipes doivent visiter ce lieu</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                          placeholder="Description optionnelle du lieu..."></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="temps_limite" class="form-label">Temps limite (secondes) *</label>
                                    <input type="number" class="form-control" id="temps_limite" name="temps_limite" 
                                           value="300" min="60" max="3600" required>
                                    <small class="text-muted">Temps maximum pour résoudre l'énigme (60s à 3600s)</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="delai_indice" class="form-label">Délai d'indice (minutes)</label>
                                    <input type="number" class="form-control" id="delai_indice" name="delai_indice" 
                                           value="6" min="1" max="60">
                                    <small class="text-muted">Temps d'attente avant que l'indice soit disponible</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="statut" class="form-label">Statut *</label>
                                    <select class="form-select" id="statut" name="statut" required>
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="enigme_requise" name="enigme_requise">
                                        <label class="form-check-label" for="enigme_requise">
                                            Énigme obligatoire
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Si coché, ce lieu doit être visité pour terminer le parcours
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Conseil :</strong> Vous pourrez affecter une énigme à ce lieu après sa création 
                                depuis la page de gestion des lieux.
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <a href="lieux.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Créer le lieu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
