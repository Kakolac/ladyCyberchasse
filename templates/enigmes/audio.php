<?php
// Récupération des données de l'énigme
$stmt = $pdo->prepare("SELECT donnees FROM enigmes WHERE id = ?");
$stmt->execute([$lieu['enigme_id']]);
$enigme_data = $stmt->fetch(PDO::FETCH_ASSOC);
$donnees = json_decode($enigme_data['donnees'], true);
?>

<div class='enigme-content'>
    <h4>�� Question audio</h4>
    <p class='lead'><?php echo htmlspecialchars($donnees['question']); ?></p>
    
    <?php if (!empty($donnees['contexte'])): ?>
        <div class="contexte-section mb-4">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>�� Contexte :</strong> <?php echo htmlspecialchars($donnees['contexte']); ?>
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
    
    <!-- Indice -->
    <?php if (!empty($donnees['indice'])): ?>
        <div class="indice-section mt-3">
            <button type="button" class="btn btn-info btn-sm" onclick="consulterIndice()">
                <i class="fas fa-lightbulb"></i> Consulter l'indice
            </button>
            <div id="indice-content" class="mt-2" style="display: none;">
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb"></i>
                    <strong>💡 Indice :</strong> <?php echo htmlspecialchars($donnees['indice']); ?>
                </div>
            </div>
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
// Variables PHP passées au JavaScript
const LIEU_ID = <?php echo $lieu['id'] ?? 'null'; ?>;
const EQUIPE_ID = <?php echo $equipe['id'] ?? 'null'; ?>;
const ENIGME_ID = <?php echo $lieu['enigme_id'] ?? 'null'; ?>;

let indiceConsulte = false;

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

// Fonction pour consulter l'indice
function consulterIndice() {
    if (indiceConsulte) {
        return;
    }
    
    // Afficher l'indice
    const indiceContent = document.getElementById('indice-content');
    indiceContent.style.display = 'block';
    
    // Mettre à jour le bouton
    const indiceButton = document.querySelector('.indice-section button.btn-info');
    if (indiceButton) {
        indiceButton.innerHTML = '<i class="fas fa-check"></i> Indice consulté';
        indiceButton.className = 'btn btn-secondary btn-sm';
        indiceButton.disabled = true;
    }
    
    indiceConsulte = true;
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

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Focus sur le champ de réponse
    document.getElementById('reponse_audio').focus();
});
</script>
