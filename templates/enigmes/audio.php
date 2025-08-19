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
    <h4> Question audio</h4>
    <p class='lead'><?php echo htmlspecialchars($donnees['question']); ?></p>
    
    <?php if (!empty($donnees['contexte'])): ?>
        <div class="contexte-section mb-4">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong> Contexte :</strong> <?php echo htmlspecialchars($donnees['contexte']); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Lecteur audio -->
    <div class="audio-player mb-4">
        <h5><i class="fas fa-music"></i> Écoutez l'audio</h5>
        
        <?php if (isset($donnees['audio_file']) && !empty($donnees['audio_file'])): ?>
            <audio id="audio-player" controls 
                   <?php echo ($donnees['autoplay'] ?? false) ? 'autoplay' : ''; ?>
                   <?php echo ($donnees['loop'] ?? false) ? 'loop' : ''; ?>
                   style="width: 100%; max-width: 400px;">
                <source src="<?php echo htmlspecialchars($donnees['audio_file']); ?>" type="audio/mpeg">
                Votre navigateur ne supporte pas l'élément audio.
            </audio>
            
            <!-- Contrôles audio personnalisés -->
            <div class="audio-controls mt-2">
                <?php if ($donnees['volume_control'] ?? false): ?>
                    <div class="volume-control">
                        <label for="volume-slider">Volume :</label>
                        <input type="range" id="volume-slider" min="0" max="1" step="0.1" value="0.7" 
                               onchange="document.getElementById('audio-player').volume = this.value">
                    </div>
                <?php endif; ?>
                
                <div class="playback-controls">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="togglePlayPause()">
                        <i class="fas fa-play"></i> <span id="play-text">Lecture</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="restartAudio()">
                        <i class="fas fa-redo"></i> Recommencer
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Aucun fichier audio disponible.
            </div>
        <?php endif; ?>
    </div>
    
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
    
    <!-- Champ de réponse -->
    <div class='reponse-audio mt-4'>
        <label for="reponse_audio" class="form-label">
            <strong>✍️ Votre réponse :</strong>
        </label>
        <input type="text" class="form-control form-control-lg" 
               id="reponse_audio" 
               placeholder="Tapez votre réponse ici..."
               maxlength="100">
        <small class="text-muted">Réponse sensible à la casse</small>
    </div>
    
    <div class='text-center mt-4'>
        <button type='button' class='btn btn-dark btn-lg' onclick='validateAudioAnswer()'>
            ✅ Valider ma réponse
        </button>
        </div>
</div>

<script>
// Variables PHP passées au JavaScript - UTILISER LES MÊMES QUE texte_libre.php
let indiceConsulte = <?php echo $indice_consulte ? 'true' : 'false'; ?>;
let indiceAvailable = <?php echo $indice_available ? 'true' : 'false'; ?>;

const LIEU_ID = <?php echo $lieu['id'] ?? 'null'; ?>;
const EQUIPE_ID = <?php echo $equipe['id'] ?? 'null'; ?>;
const ENIGME_ID = <?php echo $lieu['enigme_id'] ?? 'null'; ?>;

// Contrôles audio
function togglePlayPause() {
    const audio = document.getElementById('audio-player');
    const button = document.querySelector('.playback-controls button');
    const playText = document.getElementById('play-text');
    
    if (audio.paused) {
        audio.play();
        button.innerHTML = '<i class="fas fa-pause"></i> <span id="play-text">Pause</span>';
    } else {
        audio.pause();
        button.innerHTML = '<i class="fas fa-play"></i> <span id="play-text">Lecture</span>';
    }
}

function restartAudio() {
    const audio = document.getElementById('audio-player');
    audio.currentTime = 0;
    audio.play();
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

// Validation de la réponse
function validateAudioAnswer() {
    const reponse = document.getElementById('reponse_audio').value.trim();
    
    if (!reponse) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ Attention',
            text: 'Veuillez saisir une réponse avant de valider.'
        });
        return;
    }
    
    const reponseCorrecte = '<?php echo htmlspecialchars($donnees['reponse_correcte']); ?>';
    const reponsesAcceptees = <?php echo json_encode($donnees['reponses_acceptees'] ?? []); ?>;
    
    // Vérifier la réponse exacte et les réponses acceptées
    let reponseValide = false;
    
    // Réponse exacte (sensible à la casse)
    if (reponse === reponseCorrecte) {
        reponseValide = true;
    }
    
    // Réponses acceptées (insensibles à la casse)
    if (!reponseValide && reponsesAcceptees.length > 0) {
        reponseValide = reponsesAcceptees.some(rep => 
            reponse.toLowerCase() === rep.toLowerCase()
        );
    }
    
    if (reponseValide) {
        // Mise à jour du parcours
        updateParcoursStatus(true);
        
        Swal.fire({
            icon: 'success',
            title: '🎉 Bravo !',
            text: 'Vous avez résolu l\'énigme audio !',
            confirmButtonText: 'Continuer',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'lieux/' + LIEU_SLUG + '/';
            }
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: '❌ Réponse incorrecte',
            text: 'Écoutez bien l\'audio et réessayez...',
            confirmButtonText: 'Réessayer'
        });
        
        // Vider le champ de réponse pour faciliter la nouvelle tentative
        document.getElementById('reponse_audio').value = '';
        document.getElementById('reponse_audio').focus();
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

// Permettre la validation avec la touche Entrée
document.getElementById('reponse_audio').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        validateAudioAnswer();
    }
});

// VÉRIFICATION AU CHARGEMENT DE LA PAGE - COPIER DEPUIS texte_libre.php
document.addEventListener('DOMContentLoaded', function() {
    // Focus sur le champ de réponse
    document.getElementById('reponse_audio').focus();
    
    // Vérifier si on doit démarrer le timer de l'indice
    if (!indiceAvailable && !indiceConsulte) {
        startIndiceTimer();
    }
});
</script>
