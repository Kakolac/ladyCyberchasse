<?php
// Vérification que l'énigme existe et est de type texte libre
if (!isset($current['enigme_id']) || !isset($current['donnees'])) {
    echo '<div class="alert alert-danger">Aucune énigme configurée pour ce lieu</div>';
    return;
}

$donnees = json_decode($current['donnees'], true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($donnees['question']) || !isset($donnees['reponse_correcte'])) {
    echo '<div class="alert alert-danger">Données d\'énigme invalides</div>';
    return;
}

// NOUVELLE LOGIQUE : Récupérer les timestamps depuis cyber_token
$stmt = $pdo->prepare("
    SELECT 
        temps_debut,
        statut,
        TIMESTAMPDIFF(SECOND, temps_debut, NOW()) as enigme_elapsed_seconds
    FROM cyber_token 
    WHERE equipe_id = ? AND lieu_id = ? AND parcours_id = ?
");
$stmt->execute([$equipe_id_for_template, $lieu_id_for_template, $_SESSION['parcours_id']]);
$token_timing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($token_timing && $token_timing['temps_debut']) {
    // Timestamps existants dans la BDD
    $enigme_start_time = strtotime($token_timing['temps_debut']);
    $indice_start_time = $enigme_start_time + (($current['delai_indice'] ?? 6) * 60);
    $enigme_elapsed_time = $token_timing['enigme_elapsed_seconds'];
} else {
    // Fallback si pas de données
    $enigme_start_time = time();
    $indice_start_time = time() + (($current['delai_indice'] ?? 6) * 60);
    $enigme_elapsed_time = 0;
}

// Calculer la disponibilité de l'indice
$delai_indice_secondes = ($current['delai_indice'] ?? 6) * 60;
$indice_available = ($enigme_elapsed_time >= $delai_indice_secondes);
$remaining_time = max(0, $delai_indice_secondes - $enigme_elapsed_time);

// Vérifier si l'indice a été consulté pour cette énigme
$indice_consulte = false;
if (isset($equipe_id_for_template) && isset($enigme_id_for_template)) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM indices_consultes WHERE equipe_id = ? AND enigme_id = ?");
    $stmt->execute([$equipe_id_for_template, $enigme_id_for_template]);
    $indice_consulte = ($stmt->fetchColumn() > 0);
}

// Debug dans la console
echo "<!-- Debug PHP: enigme_start_time={$enigme_start_time}, indice_available=" . ($indice_available ? 'true' : 'false') . " -->";
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
                    <i class="fas fa-clock"></i> ⏳ Indice disponible dans <span id="indice-countdown">
                        <?php 
                        // CORRECTION AFFICHAGE : Formater correctement le temps
                        if ($remaining_time > 0) {
                            $minutes = floor($remaining_time / 60);
                            $seconds = $remaining_time % 60;
                            echo sprintf('%02d:%02d', $minutes, $seconds);
                        } else {
                            echo '00:00';
                        }
                        ?>
                    </span>
                </button>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        L'indice sera disponible après <?php echo $current['delai_indice']; ?> minutes de réflexion
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
// Variables PHP passées au JavaScript - MAINTENANT DEPUIS LA BDD
let indiceConsulte = <?php echo $indice_consulte ? 'true' : 'false'; ?>;
let indiceAvailable = <?php echo $indice_available ? 'true' : 'false'; ?>;

// NOUVELLES VARIABLES DEPUIS LA BDD
const enigmeStartTimestamp = <?php echo $enigme_start_time; ?>;
const indiceStartTimestamp = <?php echo $indice_start_time; ?>;
const delaiIndiceSecondes = <?php echo $delai_indice_secondes; ?>;

// NOUVEAU : Utiliser le temps restant calculé côté serveur
const remainingTimeServer = <?php echo $remaining_time; ?>;

// Variables pour l'initialisation de l'énigme
const enigmeConfig = {
    lieu_slug: '<?php echo $lieu_slug_for_template; ?>',
    team_name: '<?php echo $_SESSION['team_name']; ?>',
    lieu_id: <?php echo $lieu_id_for_template ?? 'null'; ?>,
    equipe_id: <?php echo $equipe_id_for_template ?? 'null'; ?>,
    enigme_id: <?php echo $enigme_id_for_template ?? 'null'; ?>
};

// Debug dans la console
console.log('🔍 DEBUG TIMER INDICE - Variables BDD:');
console.log('  - enigme_start_time (BDD):', enigmeStartTimestamp);
console.log('  - indice_start_time (BDD):', indiceStartTimestamp);
console.log('  - indice_available:', indiceAvailable);
console.log('  - indice_consulte:', indiceConsulte);
console.log('  - remaining_time (serveur):', remainingTimeServer);
console.log('  - delai_indice_secondes:', delaiIndiceSecondes);
console.log('  - current_time:', Math.floor(Date.now() / 1000));
console.log('  - enigmeConfig:', enigmeConfig);

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
            lieu: '<?php echo $lieu_slug_for_template; ?>',
            equipe_id: <?php echo $equipe_id_for_template; ?>,
            enigme_id: <?php echo $enigme_id_for_template; ?>
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
            
            // S'assurer que l'indice reste visible
            if (indiceContent) {
                indiceContent.style.display = 'block';
            }
        } else {
            console.error('Erreur enregistrement consultation:', data.error);
            if (indiceContent) {
                indiceContent.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        if (indiceContent) {
            indiceContent.style.display = 'none';
        }
    });
}

// FONCTION startIndiceTimer - CORRIGÉE POUR SYNCHRONISATION
function startIndiceTimer() {
    console.log('⏰ startIndiceTimer() appelée avec timestamps BDD');
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
    
    console.log('  - Démarrage du timer d\'indice avec BDD');
    console.log('  - Temps restant initial (serveur):', remainingTimeServer);
    
    // NOUVEAU : Utiliser le temps restant du serveur et le décompter
    let remaining = remainingTimeServer;
    
    // Synchroniser immédiatement l'affichage du bouton avec le temps PHP
    const countdownSpan = indiceButton.querySelector('#indice-countdown');
    if (countdownSpan) {
        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        const timeStr = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        countdownSpan.textContent = timeStr;
        console.log('  - Compte à rebours initial:', timeStr);
    }
    
    // Mettre à jour le bouton toutes les secondes
    const countdown = setInterval(() => {
        remaining--; // Décompter simplement
        
        console.log('⏱️ Timer tick BDD:', {
            remaining: remaining,
            minutes: Math.floor(remaining / 60),
            seconds: remaining % 60
        });
        
        if (remaining <= 0) {
            // L'indice est maintenant disponible
            console.log('🎉 Indice maintenant disponible !');
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
    
    console.log('  - Timer d\'indice BDD démarré avec intervalle de 1 seconde');
}

// Vérification au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    console.log('🖱️ Page chargée - Vérification des timers');
    console.log('  - indiceAvailable:', indiceAvailable);
    console.log('  - indiceConsulte:', indiceConsulte);
    
    // Vérifier si on doit démarrer le timer de l'indice
    if (!indiceAvailable && !indiceConsulte) {
        console.log('  - Démarrage du timer d\'indice requis');
        startIndiceTimer();
    } else {
        console.log('  - Timer d\'indice non requis');
        if (indiceAvailable) {
            console.log('    - Raison: Indice déjà disponible');
        }
        if (indiceConsulte) {
            console.log('    - Raison: Indice déjà consulté');
        }
    }
});
</script>
