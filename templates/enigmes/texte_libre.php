<?php
// Vérification que l'énigme existe et est de type texte libre
if (!isset($lieu['enigme_id']) || !isset($lieu['donnees'])) {
    echo '<div class="alert alert-danger">Aucune énigme configurée pour ce lieu</div>';
    return;
}

$donnees = json_decode($lieu['donnees'], true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($donnees['question']) || !isset($donnees['reponse_correcte'])) {
    echo '<div class="alert alert-danger">Données d\'énigme invalides</div>';
    return;
}

// Vérifier si l'indice a été consulté pour cette énigme
$indice_consulte = false;
$indice_available = false;
$enigme_start_time = 0;
$remaining_time = 0;
$delai_indice_secondes = 0;

if (isset($enigme_start_time) && $enigme_start_time > 0) {
    $enigme_elapsed_time = time() - $enigme_start_time;
    $delai_indice_secondes = $lieu['delai_indice'] * 60; // Convertir en secondes
    $indice_available = ($enigme_elapsed_time >= $delai_indice_secondes);
    $remaining_time = max(0, $delai_indice_secondes - $enigme_elapsed_time);
    
    if (isset($equipe['id']) && isset($lieu['enigme_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM indices_consultations WHERE equipe_id = ? AND enigme_id = ?");
        $stmt->execute([$equipe['id'], $lieu['enigme_id']]);
        $indice_consulte = ($stmt->fetch() !== false);
    }
}
?>

<div class='enigme-container'>
    <div class='question mb-4'>
        <h4 class='text-primary'>❓ Question :</h4>
        <p class='lead'><?php echo htmlspecialchars($donnees['question']); ?></p>
    </div>
    
    <?php if (!empty($donnees['indice'])): ?>
        <div class='indice-section mb-4'>
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
    
    <div class='reponse-section'>
        <div class='form-group mb-3'>
            <label for='reponse_libre' class='form-label'>
                <strong>💬 Votre réponse :</strong>
            </label>
            <input type='text' 
                   class='form-control form-control-lg' 
                   id='reponse_libre' 
                   placeholder='Tapez votre réponse ici...'
                   autocomplete='off'>
        </div>
        
        <div class='text-center'>
            <button type='button' class='btn btn-dark btn-lg' onclick='validateAnswer()'>
                ✅ Valider ma réponse
            </button>
        </div>
    </div>
</div>

<?php
// Inclusion des fonctions centralisées
include 'includes/enigme-functions.php';
?>

<script>
// Variables PHP passées au JavaScript
let indiceConsulte = <?php echo $indice_consulte ? 'true' : 'false'; ?>;
let indiceAvailable = <?php echo $indice_available ? 'true' : 'false'; ?>;

// Fonction de validation simplifiée - utilise la fonction centralisée
function validateAnswer() {
    const reponseCorrecte = '<?php echo htmlspecialchars($donnees['reponse_correcte']); ?>';
    const reponsesAcceptees = <?php echo json_encode($donnees['reponses_acceptees'] ?? []); ?>;
    const score = 10; // Score par défaut
    
    // Appel de la fonction centralisée
    validateTextAnswer(reponseCorrecte, reponsesAcceptees, score);
}

// Permettre la validation avec la touche Entrée
document.getElementById('reponse_libre').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        validateAnswer();
    }
});

// FONCTION consulterIndice - COPIER DEPUIS l'original
function consulterIndice() {
    if (indiceConsulte) {
        return;
    }
    
    // Afficher l'indice
    const indiceContent = document.getElementById('indice-content');
    if (indiceContent) {
        indiceContent.style.display = 'block';
    }
    
    // Marquer comme consulté dans la base de données
    fetch('save_indice_consultation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            equipe_id: <?php echo $equipe['id'] ?? 'null'; ?>,
            enigme_id: <?php echo $lieu['enigme_id'] ?? 'null'; ?>
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Consultation d\'indice enregistrée');
            indiceConsulte = true;
            
            // Mettre à jour l'interface
            const indiceButton = document.querySelector('button[onclick="consulterIndice()"]');
            if (indiceButton) {
                indiceButton.innerHTML = '<i class="fas fa-check"></i> Indice consulté';
                indiceButton.className = 'btn btn-secondary btn-sm';
                indiceButton.disabled = true;
                indiceButton.onclick = null;
            }
        } else {
            console.error('Erreur enregistrement consultation:', data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// FONCTION startIndiceTimer - COPIER DEPUIS l'original
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

// Vérification au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si on doit démarrer le timer de l'indice
    if (!indiceAvailable && !indiceConsulte) {
        startIndiceTimer();
    }
});
</script>
