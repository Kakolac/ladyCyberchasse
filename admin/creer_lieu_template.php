<?php
session_start();
require_once '../config/connexion.php';

// Vérification des droits d'administration
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

$success_message = '';
$error_message = '';

// Traitement de la création du lieu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_lieu = trim($_POST['nom_lieu']);
    $slug_lieu = trim($_POST['slug_lieu']);
    $description = trim($_POST['description']);
    $ordre = (int)$_POST['ordre'];
    $temps_limite = (int)$_POST['temps_limite'];
    $enigme_requise = isset($_POST['enigme_requise']) ? 1 : 0;
    $statut = $_POST['statut'];
    
    if (!empty($nom_lieu) && !empty($slug_lieu) && $ordre > 0 && $temps_limite > 0) {
        try {
            // 1. Créer le lieu en base de données
            $stmt = $pdo->prepare("
                INSERT INTO lieux (nom, slug, description, ordre, temps_limite, enigme_requise, statut) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$nom_lieu, $slug_lieu, $description, $ordre, $temps_limite, $enigme_requise, $statut])) {
                $lieu_id = $pdo->lastInsertId();
                
                // 2. Créer le répertoire du lieu
                $source_dir = '../templates/TemplateLieu';
                $target_dir = "../lieux/$slug_lieu";
                
                if (!is_dir($target_dir)) {
                    if (mkdir($target_dir, 0755, true)) {
                        // 3. Copier tous les fichiers du template
                        $files = ['index.php', 'enigme.php', 'header.php', 'footer.php', 'style.css'];
                        
                        foreach ($files as $file) {
                            $source_file = "$source_dir/$file";
                            $target_file = "$target_dir/$file";
                            
                            if (file_exists($source_file)) {
                                $content = file_get_contents($source_file);
                                
                                // 4. Remplacer les variables dans les fichiers PHP
                                if (in_array($file, ['index.php', 'enigme.php'])) {
                                    // Remplacer 'direction' par le nouveau slug
                                    $content = str_replace("'direction'", "'$slug_lieu'", $content);
                                    
                                    // Remplacer aussi les liens dans index.php
                                    if ($file === 'index.php') {
                                        $content = str_replace("../../enigme_launcher.php?lieu=direction", "../../enigme_launcher.php?lieu=$slug_lieu", $content);
                                    }
                                    
                                    // Remplacer dans enigme.php si il y a des références
                                    if ($file === 'enigme.php') {
                                        $content = str_replace("'direction'", "'$slug_lieu'", $content);
                                    }
                                }
                                
                                // 5. Écrire le fichier modifié
                                if (file_put_contents($target_file, $content)) {
                                    // Fichier copié et modifié avec succès
                                } else {
                                    throw new Exception("Erreur lors de l'écriture du fichier $file");
                                }
                            }
                        }
                        
                        $success_message = "✅ Lieu '$nom_lieu' créé avec succès !";
                        $success_message .= "<br>📁 Répertoire créé : lieux/$slug_lieu/";
                        $success_message .= "<br>🔧 Variables automatiquement renommées de 'direction' vers '$slug_lieu'";
                        $success_message .= "<br>🗄️ Lieu ajouté en base de données (ID: $lieu_id)";
                        
                    } else {
                        throw new Exception("Impossible de créer le répertoire $target_dir");
                    }
                } else {
                    throw new Exception("Le répertoire $target_dir existe déjà");
                }
                
            } else {
                $error_message = "Erreur lors de la création du lieu en base de données";
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
    $prochain_ordre = $stmt->fetchColumn();
} catch (Exception $e) {
    $prochain_ordre = 1;
}

// Récupération des lieux existants pour vérification
try {
    $stmt = $pdo->query("SELECT nom, slug, ordre FROM lieux ORDER BY ordre");
    $lieux_existants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $lieux_existants = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Lieu depuis le Template</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border: none; box-shadow: 0 8px 32px rgba(0,0,0,0.1); }
        .alert { border: none; border-radius: 10px; }
        .form-control:focus { box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); border-color: #667eea; }
        .btn-primary { background: linear-gradient(45deg, #667eea, #764ba2); border: none; }
        .btn-primary:hover { background: linear-gradient(45deg, #5a6fd8, #6a4190); }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                
                <!-- En-tête -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h2><i class="fas fa-map-marker-alt"></i> Créer un Lieu depuis le Template</h2>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">Ce script copie automatiquement le template <code>TemplateLieu</code> et renomme les variables nécessaires.</p>
                    </div>
                </div>

                <!-- Messages -->
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <!-- Formulaire de création -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h4><i class="fas fa-plus-circle"></i> Nouveau Lieu</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nom_lieu" class="form-label">Nom du lieu *</label>
                                        <input type="text" class="form-control" id="nom_lieu" name="nom_lieu" required 
                                               placeholder="Ex: Salle Informatique">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="slug_lieu" class="form-label">Slug (identifiant URL) *</label>
                                        <input type="text" class="form-control" id="slug_lieu" name="slug_lieu" required 
                                               placeholder="Ex: salle_info" pattern="[a-z0-9_]+" title="Lettres minuscules, chiffres et underscores uniquement">
                                        <div class="form-text">Sera utilisé pour l'URL et les variables PHP</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2" 
                                          placeholder="Description du lieu"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="ordre" class="form-label">Ordre dans le parcours *</label>
                                        <input type="number" class="form-control" id="ordre" name="ordre" required 
                                               min="1" value="<?php echo $prochain_ordre; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="temps_limite" class="form-label">Temps limite (secondes) *</label>
                                        <input type="number" class="form-control" id="temps_limite" name="temps_limite" required 
                                               min="60" value="720" step="60">
                                        <div class="form-text">720s = 12 minutes</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="statut" class="form-label">Statut</label>
                                        <select class="form-select" id="statut" name="statut">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="enigme_requise" name="enigme_requise" checked>
                                    <label class="form-check-label" for="enigme_requise">
                                        <strong>Énigme requise</strong> - Le lieu nécessite la résolution d'une énigme
                                    </label>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-magic"></i> Créer le Lieu Complet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lieux existants -->
                <?php if (!empty($lieux_existants)): ?>
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4><i class="fas fa-list"></i> Lieux Existants</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Ordre</th>
                                        <th>Nom</th>
                                        <th>Slug</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lieux_existants as $lieu): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary"><?php echo $lieu['ordre']; ?></span></td>
                                        <td><?php echo htmlspecialchars($lieu['nom']); ?></td>
                                        <td><code><?php echo $lieu['slug']; ?></code></td>
                                        <td>
                                            <a href="../lieux/<?php echo $lieu['slug']; ?>/" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt"></i> Voir
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-génération du slug à partir du nom
        document.getElementById('nom_lieu').addEventListener('input', function() {
            let slug = this.value.toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '_')
                .trim('_');
            document.getElementById('slug_lieu').value = slug;
        });
    </script>
</body>
</html>
