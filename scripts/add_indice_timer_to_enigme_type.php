<?php
/**
 * Script pour ajouter le système de timer des indices à un type d'énigme
 * 
 * Ce script ajoute automatiquement :
 * - La logique PHP pour gérer les timestamps
 * - L'interface HTML pour afficher les indices
 * - Le JavaScript pour gérer le timer
 * 
 * URL d'exécution : https://localhost/scripts/add_indice_timer_to_enigme_type.php
 */

// Configuration
$config = [
    'template_paths' => [
        'templates/enigmes/',
        'templates/TemplateLieu/'
    ],
    'backup_suffix' => '_backup_' . date('Y-m-d_H-i-s')
];

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Ajout Timer Indices - Type Énigme</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .card-header { border-radius: 15px 15px 0 0 !important; background: linear-gradient(45deg, #343a40, #495057) !important; }
        .btn-primary { background: linear-gradient(45deg, #007bff, #0056b3); border: none; }
        .btn-success { background: linear-gradient(45deg, #28a745, #1e7e34); border: none; }
        .btn-warning { background: linear-gradient(45deg, #ffc107, #e0a800); border: none; }
        .form-control { border-radius: 10px; border: 2px solid #e9ecef; }
        .form-control:focus { border-color: #007bff; box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25); }
        .alert { border-radius: 10px; border: none; }
        .progress { height: 25px; border-radius: 12px; }
        .progress-bar { border-radius: 12px; }
    </style>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-lg-8'>
                <div class='card'>
                    <div class='card-header text-white text-center'>
                        <h2><i class='fas fa-clock'></i> Ajout Timer Indices - Type Énigme</h2>
                        <p class='mb-0'>Configurez le système de timer des indices pour votre nouveau type d'énigme</p>
                    </div>
                    <div class='card-body'>";

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enigme_type = $_POST['enigme_type'] ?? '';
    $action = $_POST['action'] ?? '';
    
    if ($action === 'analyze') {
        // Analyser le type d'énigme existant
        analyzeEnigmeType($enigme_type, $config);
    } elseif ($action === 'add_timer') {
        // Ajouter le timer des indices
        addIndiceTimer($enigme_type, $config);
    }
} else {
    // Afficher le formulaire
    displayForm($config);
}

echo "
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</body>
</html>";

/**
 * Afficher le formulaire de configuration
 */
function displayForm($config) {
    // Récupérer la liste des types d'énigmes existants
    $existing_types = [];
    
    // Debug - afficher le répertoire de travail
    echo "<div class='alert alert-warning mb-4'>
        <h6><i class='fas fa-exclamation-triangle'></i> Debug - Informations système :</h6>
        <ul class='mb-0'>
            <li><strong>Répertoire de travail :</strong> " . getcwd() . "</li>
            <li><strong>Script en cours :</strong> " . __FILE__ . "</li>
            <li><strong>Dossier parent :</strong> " . dirname(__DIR__) . "</li>
        </ul>
    </div>";
    
    foreach ($config['template_paths'] as $template_path) {
        // Essayer plusieurs chemins possibles
        $possible_paths = [
            $template_path,
            dirname(__DIR__) . '/' . $template_path,
            getcwd() . '/' . $template_path,
            '../' . $template_path
        ];
        
        $found_path = null;
        foreach ($possible_paths as $path) {
            if (is_dir($path)) {
                $found_path = $path;
                break;
            }
        }
        
        if ($found_path) {
            echo "<div class='alert alert-success mb-2'>
                <i class='fas fa-check'></i> Dossier trouvé : <code>{$found_path}</code>
            </div>";
            
            $files = scandir($found_path);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $type_name = pathinfo($file, PATHINFO_FILENAME);
                    $folder_name = basename($template_path);
                    $existing_types[] = [
                        'name' => $type_name,
                        'folder' => $folder_name,
                        'path' => $found_path,
                        'full_path' => $found_path . '/' . $file
                    ];
                }
            }
        } else {
            echo "<div class='alert alert-danger mb-2'>
                <i class='fas fa-times'></i> Dossier non trouvé : <code>{$template_path}</code>
                <br>Chemins testés : " . implode(', ', $possible_paths) . "
            </div>";
        }
    }
    
    // Debug - afficher ce qui est trouvé
    echo "<div class='alert alert-info mb-4'>
        <h6><i class='fas fa-info-circle'></i> Types d'énigmes trouvés :</h6>
        <ul class='mb-0'>";
    
    if (empty($existing_types)) {
        echo "<li><i class='fas fa-exclamation-triangle text-warning'></i> Aucun type d'énigme trouvé</li>";
    } else {
        foreach ($existing_types as $type) {
            echo "<li><i class='fas fa-check text-success'></i> {$type['name']} dans {$type['folder']} (chemin: {$type['full_path']})</li>";
        }
    }
    
    echo "</ul>
    </div>";
    
    echo "<form method='POST' action=''>
        <div class='mb-4'>
            <label for='enigme_type' class='form-label'>
                <i class='fas fa-puzzle-piece'></i> <strong>Sélectionner le type d'énigme :</strong>
            </label>
            <select name='enigme_type' id='enigme_type' class='form-select form-select-lg' required>
                <option value=''>-- Choisir un type d'énigme --</option>";
    
    foreach ($existing_types as $type) {
        $display_name = $type['name'] . ' (' . $type['folder'] . ')';
        // CORRECTION : Utiliser le chemin complet au lieu de reconstruire
        $value = $type['full_path'];
        echo "<option value='{$value}'>{$display_name}</option>";
    }
    
    echo "</select>
            <div class='form-text'>
                <i class='fas fa-info-circle'></i> Sélectionnez le type d'énigme auquel vous voulez ajouter le timer des indices
            </div>
        </div>
        
        <div class='d-grid gap-2'>
            <button type='submit' name='action' value='analyze' class='btn btn-primary btn-lg'>
                <i class='fas fa-search'></i> Analyser le Type d'Énigme
            </button>
        </div>
    </form>
    
    <div class='mt-4'>
        <div class='alert alert-info'>
            <h5><i class='fas fa-lightbulb'></i> Comment ça marche ?</h5>
            <ol class='mb-0'>
                <li><strong>Analyse :</strong> Le script examine votre type d'énigme existant</li>
                <li><strong>Modification :</strong> Il ajoute automatiquement le système de timer des indices</li>
                <li><strong>Test :</strong> Vous pouvez tester immédiatement le nouveau système</li>
            </ol>
        </div>
    </div>";
}

/**
 * Analyser le type d'énigme existant
 */
function analyzeEnigmeType($template_file, $config) {
    // Le template_file contient maintenant le chemin complet
    if (!file_exists($template_file)) {
        echo "<div class='alert alert-danger'>
            <i class='fas fa-exclamation-triangle'></i> <strong>Erreur :</strong> Le fichier template '{$template_file}' n'existe pas !
        </div>";
        return;
    }
    
    echo "<div class='alert alert-success mb-3'>
        <i class='fas fa-check'></i> Fichier trouvé : <code>{$template_file}</code>
    </div>";
    
    $content = file_get_contents($template_file);
    
    // Analyser le contenu
    $analysis = [
        'has_indice_system' => strpos($content, 'startIndiceTimer') !== false,
        'has_indice_button' => strpos($content, 'indice-button') !== false,
        'has_timing_variables' => strpos($content, 'indice_available') !== false,
        'has_indice_section' => strpos($content, 'indice-section') !== false,
        'file_size' => strlen($content),
        'lines_count' => substr_count($content, "\n") + 1
    ];
    
    // Extraire le nom du type d'énigme du chemin
    $type_name = basename($template_file, '.php');
    
    echo "<div class='alert alert-info'>
        <h5><i class='fas fa-search'></i> Analyse du Type d'Énigme : <strong>{$type_name}</strong></h5>
        <div class='row'>
            <div class='col-md-6'>
                <ul class='list-unstyled'>
                    <li><i class='fas fa-" . ($analysis['has_indice_system'] ? 'check text-success' : 'times text-danger') . "'></i> Système d'indices : " . ($analysis['has_indice_system'] ? 'Présent' : 'Absent') . "</li>
                    <li><i class='fas fa-" . ($analysis['has_indice_button'] ? 'check text-success' : 'times text-danger') . "'></i> Bouton d'indice : " . ($analysis['has_indice_button'] ? 'Présent' : 'Absent') . "</li>
                    <li><i class='fas fa-" . ($analysis['has_indice_section'] ? 'check text-success' : 'times text-danger') . "'></i> Section indice : " . ($analysis['has_indice_section'] ? 'Présente' : 'Absente') . "</li>
                </ul>
            </div>
            <div class='col-md-6'>
                <ul class='list-unstyled'>
                    <li><i class='fas fa-file-code'></i> Taille du fichier : " . number_format($analysis['file_size']) . " caractères</li>
                    <li><i class='fas fa-list'></i> Nombre de lignes : {$analysis['lines_count']}</li>
                    <li><i class='fas fa-" . ($analysis['has_timing_variables'] ? 'check text-success' : 'times text-danger') . "'></i> Variables de timing : " . ($analysis['has_timing_variables'] ? 'Présentes' : 'Absentes') . "</li>
                </ul>
            </div>
        </div>
    </div>";
    
    if ($analysis['has_indice_system']) {
        echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle'></i> <strong>Parfait !</strong> Ce type d'énigme a déjà le système de timer des indices.
        </div>";
    } else {
        echo "<div class='alert alert-warning'>
            <i class='fas fa-exclamation-triangle'></i> <strong>Action requise :</strong> Ce type d'énigme n'a pas le système de timer des indices.
        </div>
        
        <form method='POST' action=''>
            <input type='hidden' name='enigme_type' value='{$type_name}'>
            <input type='hidden' name='template_file' value='{$template_file}'>
            <div class='d-grid'>
                <button type='submit' name='action' value='add_timer' class='btn btn-success btn-lg'>
                    <i class='fas fa-plus'></i> Ajouter le Timer des Indices
                </button>
            </div>
        </form>";
    }
    
    echo "<div class='mt-3'>
        <a href='' class='btn btn-secondary'>
            <i class='fas fa-arrow-left'></i> Retour au Formulaire
        </a>
    </div>";
}

/**
 * Ajouter le timer des indices au type d'énigme
 */
function addIndiceTimer($type_path, $config) {
    // Récupérer le chemin du fichier depuis le formulaire
    $template_file = $_POST['template_file'] ?? '';
    
    if (empty($template_file) || !file_exists($template_file)) {
        echo "<div class='alert alert-danger'>
            <i class='fas fa-exclamation-triangle'></i> <strong>Erreur :</strong> Fichier template introuvable !
        </div>";
        return;
    }
    
    $backup_file = $template_file . $config['backup_suffix'];
    
    // Créer une sauvegarde
    if (!copy($template_file, $backup_file)) {
        echo "<div class='alert alert-danger'>
            <i class='fas fa-exclamation-triangle'></i> <strong>Erreur :</strong> Impossible de créer la sauvegarde !
        </div>";
        return;
    }
    
    $content = file_get_contents($template_file);
    
    // Préparer les modifications
    $modifications = [];
    
    // 1. Ajouter les variables PHP pour le timing (après la récupération des données)
    $timing_vars = "
// Variables pour le timing des indices
\$enigme_session_key = \"enigme_start_{$type_path}_\" . (\$lieu['id'] ?? 'default');
\$indice_session_key = \"indice_start_{$type_path}_\" . (\$lieu['id'] ?? 'default');

// Récupérer les timestamps de la session
\$enigme_start_time = \$_SESSION[\$enigme_session_key] ?? time();
\$indice_start_time = \$_SESSION[\$indice_session_key] ?? (\$enigme_start_time + 180);

// Sauvegarder les timestamps si c'est la première fois
if (!isset(\$_SESSION[\$enigme_session_key])) {
    \$_SESSION[\$enigme_session_key] = \$enigme_start_time;
}
if (!isset(\$_SESSION[\$indice_session_key])) {
    \$_SESSION[\$indice_session_key] = \$indice_start_time;
}

// Calculer la disponibilité de l'indice
\$enigme_elapsed_time = time() - \$enigme_start_time;
\$delai_indice_secondes = (\$lieu['delai_indice'] ?? 6) * 60;
\$indice_available = (\$enigme_elapsed_time >= \$delai_indice_secondes);
\$remaining_time = max(0, \$delai_indice_secondes - \$enigme_elapsed_time);

// Vérifier si l'indice a déjà été consulté
\$indice_consulte = false;
if (isset(\$_SESSION['team_name'])) {
    \$stmt = \$pdo->prepare(\"
        SELECT COUNT(*) FROM indices_consultes ic
        JOIN equipes e ON ic.equipe_id = e.id
        JOIN lieux l ON ic.lieu_id = l.id
        WHERE e.nom = ? AND l.slug = ? AND ic.enigme_id = ?
    \");
    \$stmt->execute([\$_SESSION['team_name'], \$lieu_slug, \$lieu['enigme_id']]);
    \$indice_consulte = \$stmt->fetchColumn() > 0;
}";
    
    // Chercher où insérer les variables de timing
    if (preg_match('/(// Récupération des données de l\'énigme.*?)(\$donnees = json_decode.*?;)/s', $content, $matches)) {
        $modifications[] = [
            'search' => $matches[0],
            'replace' => $matches[1] . $matches[2] . $timing_vars
        ];
    }
    
    // 2. Ajouter la section HTML pour l'indice (après la question principale)
    $indice_html = "
    <?php if (!empty(\$donnees['indice'])): ?>
        <div class=\"indice-section mt-3\">
            <?php if (\$indice_consulte): ?>
                <!-- Indice déjà consulté -->
                <div class=\"alert alert-info\">
                    <i class=\"fas fa-lightbulb\"></i>
                    <strong>💡 Indice consulté :</strong> <?php echo htmlspecialchars(\$donnees['indice']); ?>
                </div>
                <button type=\"button\" class=\"btn btn-secondary btn-sm\" disabled>
                    <i class=\"fas fa-check\"></i> Indice consulté
                </button>
            <?php elseif (\$indice_available): ?>
                <!-- Indice disponible -->
                <button type=\"button\" class=\"btn btn-info btn-sm\" onclick=\"consulterIndice()\">
                    <i class=\"fas fa-lightbulb\"></i> Consulter l'indice
                </button>
                <div id=\"indice-content\" class=\"mt-2\" style=\"display: none;\">
                    <div class=\"alert alert-info\">
                        <i class=\"fas fa-lightbulb\"></i>
                        <strong>💡 Indice :</strong> <?php echo htmlspecialchars(\$donnees['indice']); ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Indice pas encore disponible -->
                <button type=\"button\" class=\"btn btn-secondary btn-sm\" disabled id=\"indice-button\">
                    <i class=\"fas fa-clock\"></i> ⏳ Indice disponible dans <span id=\"indice-countdown\"><?php echo gmdate('i:s', \$remaining_time); ?></span>
                </button>
                <div class=\"mt-2\">
                    <small class=\"text-muted\">
                        <i class=\"fas fa-info-circle\"></i> 
                        L'indice sera disponible après <?php echo \$lieu['delai_indice'] ?? 6; ?> minutes de réflexion
                    </small>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>";
    
    // Chercher où insérer la section indice
    if (preg_match('/(<h4>.*?Question principale.*?<\/h4>.*?<p.*?<\/p>)/s', $content, $matches)) {
        $modifications[] = [
            'search' => $matches[1],
            'replace' => $matches[1] . $indice_html
        ];
    }
    
    // 3. Ajouter le JavaScript pour le timer (avant la fermeture de la balise script)
    $indice_js = "
// Variables pour le timer des indices
let indiceConsulte = <?php echo \$indice_consulte ? 'true' : 'false'; ?>;
let indiceAvailable = <?php echo \$indice_available ? 'true' : 'false'; ?>;

// Fonction pour démarrer le timer de l'indice
function startIndiceTimer() {
    if (indiceAvailable) {
        return;
    }
    
    const indiceButton = document.getElementById('indice-button');
    if (!indiceButton) {
        return;
    }
    
    // Mettre à jour le bouton toutes les secondes
    const countdown = setInterval(() => {
        const now = Math.floor(Date.now() / 1000);
        const enigmeStart = <?php echo \$enigme_start_time; ?>;
        const remaining = <?php echo \$delai_indice_secondes; ?> - (now - enigmeStart);
        
        if (remaining <= 0) {
            // L'indice est maintenant disponible
            clearInterval(countdown);
            indiceAvailable = true;
            
            // Notification
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '💡 Indice disponible !',
                    text: 'Vous pouvez maintenant consulter l\'indice',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
            
            // Mettre à jour l'interface
            indiceButton.innerHTML = '<i class=\"fas fa-lightbulb\"></i> Consulter l\'indice';
            indiceButton.className = 'btn btn-info btn-sm';
            indiceButton.disabled = false;
            indiceButton.onclick = consulterIndice;
            
            // Supprimer le message d'attente
            const infoDiv = indiceButton.nextElementSibling;
            if (infoDiv) {
                infoDiv.remove();
            }
        } else {
            // Mettre à jour le compte à rebours
            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            const timeStr = \`\${minutes.toString().padStart(2, '0')}:\${seconds.toString().padStart(2, '0')}\`;
            
            const countdownSpan = indiceButton.querySelector('#indice-countdown');
            if (countdownSpan) {
                countdownSpan.textContent = timeStr;
            }
        }
    }, 1000);
}

// Fonction pour consulter l'indice
function consulterIndice() {
    if (indiceConsulte) {
        return;
    }
    
    // Créer et afficher l'indice dynamiquement
    const indiceSection = document.querySelector('.indice-section');
    if (indiceSection) {
        // Supprimer l'ancien contenu de l'indice s'il existe
        const oldIndiceContent = document.getElementById('indice-content');
        if (oldIndiceContent) {
            oldIndiceContent.remove();
        }
        
        // Créer le nouveau contenu de l'indice
        const indiceContent = document.createElement('div');
        indiceContent.id = 'indice-content';
        indiceContent.className = 'mt-2';
        indiceContent.innerHTML = \`
            <div class=\"alert alert-info\">
                <i class=\"fas fa-lightbulb\"></i>
                <strong>💡 Indice :</strong> <?php echo htmlspecialchars(\$donnees['indice']); ?>
            </div>
        \`;
        
        // Insérer l'indice après le bouton
        const indiceButton = indiceSection.querySelector('button');
        if (indiceButton) {
            indiceButton.parentNode.insertBefore(indiceContent, indiceButton.nextSibling);
        }
    }
    
    // Mettre à jour le bouton
    const indiceButton = document.querySelector('.indice-section button.btn-info');
    if (indiceButton) {
        indiceButton.innerHTML = '<i class=\"fas fa-check\"></i> Indice consulté';
        indiceButton.className = 'btn btn-secondary btn-sm';
        indiceButton.disabled = true;
        indiceButton.onclick = null;
    }
    
    // Marquer comme consulté
    indiceConsulte = true;
    
    // Enregistrer la consultation côté serveur
    fetch('save_indice_consultation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lieu: LIEU_SLUG,
            enigme_id: ENIGME_ID
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Erreur enregistrement indice:', data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Démarrer le timer au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    if (!indiceAvailable && !indiceConsulte) {
        startIndiceTimer();
    }
});";
    
    // Chercher où insérer le JavaScript
    if (preg_match('/(<\/script>)/', $content, $matches)) {
        $modifications[] = [
            'search' => $matches[1],
            'replace' => $indice_js . $matches[1]
        ];
    }
    
    // Appliquer les modifications
    $modified_content = $content;
    $modifications_applied = 0;
    
    foreach ($modifications as $mod) {
        if (strpos($modified_content, $mod['search']) !== false) {
            $modified_content = str_replace($mod['search'], $mod['replace'], $modified_content);
            $modifications_applied++;
        }
    }
    
    // Sauvegarder le fichier modifié
    if (file_put_contents($template_file, $modified_content)) {
        echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle'></i> <strong>Succès !</strong> Le timer des indices a été ajouté au type d'énigme '{$type_path}'.
        </div>
        
        <div class='alert alert-info'>
            <h6><i class='fas fa-info-circle'></i> Modifications appliquées :</h6>
            <ul class='mb-0'>
                <li>✅ Variables de timing PHP ajoutées</li>
                <li>✅ Interface HTML pour l'indice ajoutée</li>
                <li>✅ JavaScript du timer ajouté</li>
                <li>✅ Sauvegarde créée : <code>{$backup_file}</code></li>
            </ul>
        </div>
        
        <div class='alert alert-warning'>
            <h6><i class='fas fa-exclamation-triangle'></i> Important :</h6>
            <p class='mb-0'>Votre fichier original a été sauvegardé. Vous pouvez maintenant tester le nouveau système de timer des indices !</p>
        </div>";
        
        $modifications_applied++;
    } else {
        echo "<div class='alert alert-danger'>
            <i class='fas fa-exclamation-triangle'></i> <strong>Erreur :</strong> Impossible de sauvegarder les modifications !
        </div>";
        
        // Restaurer la sauvegarde
        if (file_exists($backup_file)) {
            copy($backup_file, $template_file);
            echo "<div class='alert alert-info'>
                <i class='fas fa-undo'></i> Le fichier original a été restauré depuis la sauvegarde.
            </div>";
        }
    }
    
    echo "<div class='mt-3'>
        <a href='' class='btn btn-secondary'>
            <i class='fas fa-arrow-left'></i> Retour au Formulaire
        </a>
    </div>";
}
?>
