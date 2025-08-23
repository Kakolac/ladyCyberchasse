


<?php
// Inclure les fonctions de timing centralisées
include 'includes/timing-functions.php';

// Récupération des données de l'énigme
$stmt = $pdo->prepare("SELECT donnees FROM enigmes WHERE id = ?");
$stmt->execute([$lieu['enigme_id']]);
$enigme_data = $stmt->fetch(PDO::FETCH_ASSOC);
$donnees = json_decode($enigme_data['donnees'], true);

// NOUVELLE LOGIQUE : Récupérer les timestamps depuis la BDD
$stmt = $pdo->prepare("
    SELECT 
        enigme_start_time,
        indice_start_time,
        statut,
        TIMESTAMPDIFF(SECOND, enigme_start_time, NOW()) as enigme_elapsed_seconds
    FROM parcours 
    WHERE equipe_id = ? AND lieu_id = ?
");
$stmt->execute([$equipe['id'], $lieu['id']]);
$parcours_timing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($parcours_timing && $parcours_timing['enigme_start_time']) {
    // Timestamps existants dans la BDD
    $enigme_start_time = strtotime($parcours_timing['enigme_start_time']);
    $indice_start_time = strtotime($parcours_timing['indice_start_time']);
    $enigme_elapsed_time = $parcours_timing['enigme_elapsed_seconds'];
} else {
    // Fallback si pas de données (ne devrait plus arriver)
    $enigme_start_time = time();
    $indice_start_time = time() + 180;
    $enigme_elapsed_time = 0;
}

// Calculer la disponibilité de l'indice
$delai_indice_secondes = ($lieu['delai_indice'] ?? 6) * 60;
$indice_available = ($enigme_elapsed_time >= $delai_indice_secondes);
$remaining_time = max(0, $delai_indice_secondes - $enigme_elapsed_time);

// Vérifier si l'indice a déjà été consulté par cette équipe
$indice_consulte = false;
if (isset($equipe['id']) && isset($lieu['enigme_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM indices_consultes WHERE equipe_id = ? AND enigme_id = ?");
    $stmt->execute([$equipe['id'], $lieu['enigme_id']]);
    $indice_consulte = ($stmt->fetchColumn() > 0);
}

// Debug dans la console
echo "<!-- Debug PHP: enigme_start_time={$enigme_start_time}, indice_available=" . ($indice_available ? 'true' : 'false') . " -->";

// INCLURE les fonctions centralisées
include 'includes/enigme-functions.php';
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
// Variables PHP passées au JavaScript - MAINTENANT DEPUIS LA BDD
let indiceConsulte = <?php echo $indice_consulte ? 'true' : 'false'; ?>;
let indiceAvailable = <?php echo $indice_available ? 'true' : 'false'; ?>;

// NOUVELLES VARIABLES DEPUIS LA BDD
const enigmeStartTimestamp = <?php echo $enigme_start_time; ?>;
const indiceStartTimestamp = <?php echo $indice_start_time; ?>;
const delaiIndiceSecondes = <?php echo $delai_indice_secondes; ?>;

// Debug dans la console
console.log(' DEBUG TIMER INDICE QCM - Variables BDD:');
console.log('  - enigme_start_time (BDD):', enigmeStartTimestamp);
console.log('  - indice_start_time (BDD):', indiceStartTimestamp);
console.log('  - indice_available:', indiceAvailable);
console.log('  - indice_consulte:', indiceConsulte);
console.log('  - remaining_time:', <?php echo $remaining_time; ?>);
console.log('  - delai_indice_secondes:', delaiIndiceSecondes);
console.log('  - current_time:', Math.floor(Date.now() / 1000));

// Les variables globales sont maintenant définies par enigme-functions.php
// LIEU_ID, EQUIPE_ID, ENIGME_ID sont disponibles

// Fonction de validation simplifiée - utilise la fonction centralisée
function validateAnswer() {
    const reponseCorrecte = '<?php echo $donnees['reponse_correcte']; ?>';
    const score = 10; // Score par défaut
    
    // Appel de la fonction centralisée
    validateQCMAnswer(reponseCorrecte, score);
}

// FONCTION startIndiceTimer - MAINTENANT AVEC LES TIMESTAMPS BDD
function startIndiceTimer() {
    console.log('⏰ startIndiceTimer() QCM appelée avec timestamps BDD');
    console.log('  - indiceAvailable:', indiceAvailable);
    console.log('  - indiceConsulte:', indiceConsulte);
    
    if (indiceAvailable) {
        console.log('  - Indice déjà disponible, sortie');
        return;
    }
    
    const indiceButton = document.getElementById('indice-button');
    if (!indiceButton) {
        console.log('  - Bouton indice non trouvé, sortie');
        return;
    }
    
    console.log('  - Démarrage du timer d\'indice QCM avec BDD');
    console.log('  - Temps restant initial:', <?php echo $remaining_time; ?>);
    
    // Synchroniser immédiatement l'affichage du bouton avec le temps PHP
    const countdownSpan = indiceButton.querySelector('#indice-countdown');
    if (countdownSpan) {
        countdownSpan.textContent = '<?php echo gmdate('i:s', $remaining_time); ?>';
        console.log('  - Compte à rebours initial:', '<?php echo gmdate('i:s', $remaining_time); ?>');
    }
    
    // Mettre à jour le bouton toutes les secondes
    const countdown = setInterval(() => {
        const now = Math.floor(Date.now() / 1000);
        const remaining = delaiIndiceSecondes - (now - enigmeStartTimestamp);
        
        console.log('⏱️ Timer tick QCM BDD:', {
            now: now,
            enigmeStart: enigmeStartTimestamp,
            delaiIndice: delaiIndiceSecondes,
            remaining: remaining,
            elapsed: now - enigmeStartTimestamp
        });
        
        if (remaining <= 0) {
            // L'indice est maintenant disponible
            console.log('🎉 Indice QCM maintenant disponible !');
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
    
    console.log('  - Timer d\'indice QCM BDD démarré avec intervalle de 1 seconde');
}

// FONCTION consulterIndice - CORRIGÉE
function consulterIndice() {
    if (indiceConsulte) {
        return;
    }
    
    // Afficher l'indice AVANT d'envoyer la requête
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
            lieu: '<?php echo $lieu_slug; ?>', // AJOUTER LE SLUG DU LIEU
            equipe_id: <?php echo $equipe['id'] ?? 'null'; ?>,
            enigme_id: <?php echo $lieu['enigme_id'] ?? 'null'; ?>
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Consultation d\'indice enregistrée');
            indiceConsulte = true;
            
            // Mettre à jour l'interface - GARDER L'INDICE VISIBLE
            const indiceButton = document.querySelector('button[onclick="consulterIndice()"]');
            if (indiceButton) {
                indiceButton.innerHTML = '<i class="fas fa-check"></i> Indice consulté';
                indiceButton.className = 'btn btn-secondary btn-sm';
                indiceButton.disabled = true;
                indiceButton.onclick = null;
            }
            
            // S'assurer que l'indice reste visible
            if (indiceContent) {
                indiceContent.style.display = 'block';
            }
        } else {
            console.error('Erreur enregistrement consultation:', data.error);
            // En cas d'erreur, masquer l'indice
            if (indiceContent) {
                indiceContent.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        // En cas d'erreur, masquer l'indice
        if (indiceContent) {
            indiceContent.style.display = 'none';
        }
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