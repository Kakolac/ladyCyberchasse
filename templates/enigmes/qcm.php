


<?php
// Récupération des données de l'énigme
$stmt = $pdo->prepare("SELECT donnees FROM enigmes WHERE id = ?");
$stmt->execute([$lieu['enigme_id']]);
$enigme_data = $stmt->fetch(PDO::FETCH_ASSOC);
$donnees = json_decode($enigme_data['donnees'], true);

// AJOUTER LA LOGIQUE DE TIMING DEPUIS texte_libre.php
// Vérifier si l'indice a déjà été consulté par cette équipe
$indice_consulte = false;
if (isset($_SESSION['team_name'])) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM indices_consultes ic
        JOIN equipes e ON ic.equipe_id = e.id
        JOIN lieux l ON ic.lieu_id = l.id
        WHERE e.nom = ? AND l.slug = ? AND ic.enigme_id = ?
    ");
    $stmt->execute([$_SESSION['team_name'], $lieu_slug, $lieu['enigme_id']]);
    $indice_consulte = $stmt->fetchColumn() > 0;
}

// Variables pour le timing - NE PAS RECRÉER
$enigme_session_key = "enigme_start_{$lieu['id']}_{$equipe['id']}";
$indice_session_key = "indice_start_{$lieu['id']}_{$equipe['id']}";

// RÉCUPÉRER les valeurs de la session, ne pas les recréer
$enigme_start_time = $_SESSION[$enigme_session_key];
$indice_start_time = $_SESSION[$indice_session_key];

// Calculer les temps écoulés avec le délai dynamique
$enigme_elapsed_time = time() - $enigme_start_time;
$delai_indice_secondes = $lieu['delai_indice'] * 60; // Délai dynamique en secondes
$indice_available = ($enigme_elapsed_time >= $delai_indice_secondes);
$remaining_time = max(0, $delai_indice_secondes - $enigme_elapsed_time);
?>

<div class='enigme-content'>
    <h4>🎯 Question principale</h4>
    <p class='lead'><?php echo htmlspecialchars($donnees['question']); ?></p>
    
    <!-- Indice - UTILISER LA MÊME LOGIQUE QUE texte_libre.php -->
    <?php if (!empty($donnees['indice'])): ?>
        <div class="indice-section mt-3">
            <?php if ($indice_consulte): ?>
                <!-- Indice déjà consulté -->
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb"></i>
                    <strong>💡 Indice consulté :</strong> <?php echo htmlspecialchars($donnees['indice']); ?>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" disabled>
                    <i class="fas fa-check"></i> Indice consulté
                </button>
            <?php elseif ($indice_available): ?>
                <!-- Indice disponible -->
                <button type="button" class="btn btn-info btn-sm" onclick="consulterIndice()">
                    <i class="fas fa-lightbulb"></i> Consulter l'indice
                </button>
                <div id="indice-content" class="mt-2" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i>
                        <strong>💡 Indice :</strong> <?php echo htmlspecialchars($donnees['indice']); ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Indice pas encore disponible -->
                <button type="button" class="btn btn-secondary btn-sm" disabled id="indice-button">
                    <i class="fas fa-clock"></i> ⏳ Indice disponible dans <span id="indice-countdown"><?php echo gmdate('i:s', $remaining_time); ?></span>
                </button>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        L'indice sera disponible après <?php echo $lieu['delai_indice']; ?> minutes de réflexion
                    </small>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div class='options mt-4'>
        <?php foreach ($donnees['options'] as $key => $option): ?>
            <div class='form-check mb-3'>
                <input class='form-check-input' type='radio' name='answer' id='option<?php echo $key; ?>' value='<?php echo $key; ?>'>
                <label class='form-check-label' for='option<?php echo $key; ?>'>
                    <strong><?php echo $key; ?>)</strong> 
                    <?php echo htmlspecialchars($option); ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class='text-center mt-4'>
        <button type='button' class='btn btn-dark btn-lg' onclick='validateAnswer()'>
            ✅ Valider ma réponse
        </button>
    </div>
</div>

<script>
// Variables PHP passées au JavaScript - UTILISER LES MÊMES QUE texte_libre.php
let indiceConsulte = <?php echo $indice_consulte ? 'true' : 'false'; ?>;
let indiceAvailable = <?php echo $indice_available ? 'true' : 'false'; ?>;

// Les variables globales sont maintenant définies par enigme-functions.php
// LIEU_ID, EQUIPE_ID, ENIGME_ID sont disponibles

// Fonction de validation simplifiée - utilise la fonction centralisée
function validateAnswer() {
    const reponseCorrecte = '<?php echo $donnees['reponse_correcte']; ?>';
    const score = 10; // Score par défaut
    
    // Appel de la fonction centralisée
    validateQCMAnswer(reponseCorrecte, score);
}

// FONCTION startIndiceTimer - COPIER DEPUIS texte_libre.php
function startIndiceTimer() {
    if (indiceAvailable) {
        return;
    }
    
    const indiceButton = document.getElementById('indice-button');
    if (!indiceButton) {
        return;
    }
    
    // Synchroniser immédiatement l'affichage du bouton avec le temps PHP
    const countdownSpan = indiceButton.querySelector('#indice-countdown');
    if (countdownSpan) {
        countdownSpan.textContent = '<?php echo gmdate('i:s', $remaining_time); ?>';
    }
    
    // Mettre à jour le bouton toutes les secondes
    const countdown = setInterval(() => {
        const now = Math.floor(Date.now() / 1000);
        const enigmeStart = <?php echo $enigme_start_time; ?>;
        const remaining = <?php echo $delai_indice_secondes; ?> - (now - enigmeStart);
        
        if (remaining <= 0) {
            // L'indice est maintenant disponible
            clearInterval(countdown);
            indiceAvailable = true;
            
            // Debug avec SweetAlert2
            Swal.fire({
                icon: 'success',
                title: '💡 Indice disponible !',
                text: 'Vous pouvez maintenant consulter l\'indice',
                timer: 3000,
                showConfirmButton: false
            });
            
            // Mettre à jour l'interface
            indiceButton.innerHTML = '<i class="fas fa-lightbulb"></i> Consulter l\'indice';
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
            const timeStr = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            const countdownSpan = indiceButton.querySelector('#indice-countdown');
            if (countdownSpan) {
                countdownSpan.textContent = timeStr;
            }
        }
    }, 1000);
}

// FONCTION consulterIndice - COPIER DEPUIS texte_libre.php
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
        indiceContent.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-lightbulb"></i>
                <strong>💡 Indice :</strong> <?php echo htmlspecialchars($donnees['indice']); ?>
            </div>
        `;
        
        // Insérer l'indice après le bouton
        const indiceButton = indiceSection.querySelector('button');
        if (indiceButton) {
            indiceButton.parentNode.insertBefore(indiceContent, indiceButton.nextSibling);
        }
    }
    
    // Mettre à jour le bouton
    const indiceButton = document.querySelector('.indice-section button.btn-info');
    if (indiceButton) {
        indiceButton.innerHTML = '<i class="fas fa-check"></i> Indice consulté';
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

// VÉRIFICATION AU CHARGEMENT DE LA PAGE - COPIER DEPUIS texte_libre.php
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si on doit démarrer le timer de l'indice
    if (!indiceAvailable && !indiceConsulte) {
        startIndiceTimer();
    }
});
</script>