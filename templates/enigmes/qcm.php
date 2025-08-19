


<?php
// Récupération des données de l'énigme
$stmt = $pdo->prepare("SELECT donnees FROM enigmes WHERE id = ?");
$stmt->execute([$lieu['enigme_id']]);
$enigme_data = $stmt->fetch(PDO::FETCH_ASSOC);
$donnees = json_decode($enigme_data['donnees'], true);
?>

<div class='enigme-content'>
    <h4>🎯 Question principale</h4>
    <p class='lead'><?php echo htmlspecialchars($donnees['question']); ?></p>
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
                        L'indice sera disponible après <?php echo $lieu['delai_indice'] ?? 6; ?> minutes de réflexion
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
function validateAnswer() {
    const selectedAnswer = document.querySelector('input[name="answer"]:checked');
    
    if (!selectedAnswer) {
        Swal.fire({
            title: '⚠️ Attention',
            text: 'Veuillez sélectionner une réponse avant de valider.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    const answer = selectedAnswer.value;
    const reponseCorrecte = '<?php echo $donnees['reponse_correcte']; ?>';
    
    if (answer === reponseCorrecte) {
        // Mise à jour du parcours
        updateParcoursStatus(true);
        
        Swal.fire({
            title: '🎉 Bravo !',
            text: 'Vous avez résolu l\'énigme !',
            icon: 'success',
            confirmButtonText: 'Continuer l\'aventure'
        }).then((result) => {
            window.location.href = 'lieux/' + LIEU_SLUG + '/';
        });
    } else {
        Swal.fire({
            title: '❌ Réponse incorrecte',
            text: 'Réfléchissez et réessayez...',
            icon: 'error',
            confirmButtonText: 'Réessayer'
        });
    }
}

function updateParcoursStatus(success) {
    fetch('update_parcours_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lieu: LIEU_SLUG,
            team: TEAM_NAME,
            success: success,
            score: 10
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Statut du parcours mis à jour');
        } else {
            console.error('Erreur mise à jour parcours:', data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Variables pour le timer des indices
let indiceConsulte = <?php echo $indice_consulte ? 'true' : 'false'; ?>;
let indiceAvailable = <?php echo $indice_available ? 'true' : 'false'; ?>;

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
        const enigmeStart = <?php echo $enigme_start_time; ?>;
        const remaining = <?php echo $delai_indice_secondes; ?> - (now - enigmeStart);
        
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
            const timeStr = \`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}\`;
            
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
            <div class="alert alert-info">
                <i class="fas fa-lightbulb"></i>
                <strong>💡 Indice :</strong> <?php echo htmlspecialchars($donnees['indice']); ?>
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

// Démarrer le timer au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    if (!indiceAvailable && !indiceConsulte) {
        startIndiceTimer();
    }
});</script>