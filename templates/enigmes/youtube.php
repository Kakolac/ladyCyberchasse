<?php
// Récupération des données de l'énigme
$stmt = $pdo->prepare("SELECT donnees FROM enigmes WHERE id = ?");
$stmt->execute([$lieu['enigme_id']]);
$enigme_data = $stmt->fetch(PDO::FETCH_ASSOC);
$donnees = json_decode($enigme_data['donnees'], true);
?>

<div class='enigme-content'>
    <h4>🎬 Question vidéo YouTube</h4>
    <p class='lead'><?php echo htmlspecialchars($donnees['question']); ?></p>
    
    <?php if (!empty($donnees['contexte'])): ?>
        <div class="contexte-section mb-4">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>�� Contexte :</strong> <?php echo htmlspecialchars($donnees['contexte']); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Lecteur YouTube -->
    <div class="youtube-player mb-4">
        <h5><i class="fab fa-youtube"></i> Regardez la vidéo</h5>
        
        <?php if (isset($donnees['youtube_url']) && !empty($donnees['youtube_url'])): ?>
            <div class="video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%;">
                <iframe 
                    src="https://www.youtube.com/embed/<?php echo htmlspecialchars($donnees['youtube_url']); ?>?<?php echo http_build_query([
                        'autoplay' => ($donnees['autoplay'] ?? false) ? 1 : 0,
                        'loop' => ($donnees['loop'] ?? false) ? 1 : 0,
                        'controls' => ($donnees['show_controls'] ?? true) ? 1 : 0,
                        'rel' => 0,
                        'modestbranding' => 1
                    ]); ?>"
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
            
            <!-- Contrôles vidéo -->
            <div class="video-controls mt-2 text-center">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="replayVideo()">
                    <i class="fas fa-redo"></i> Revoir la vidéo
                </button>
                <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleFullscreen()">
                    <i class="fas fa-expand"></i> Plein écran
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Aucune vidéo YouTube disponible.
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
    <div class='reponse-youtube mt-4'>
        <label for="reponse_youtube" class="form-label">
            <strong>✍️ Votre réponse :</strong>
        </label>
        <input type="text" class="form-control form-control-lg" 
               id="reponse_youtube" 
               placeholder="Tapez votre réponse ici..."
               maxlength="100">
        <small class="text-muted">Réponse sensible à la casse</small>
    </div>
    
    <div class='text-center mt-4'>
        <button type='button' class='btn btn-dark btn-lg' onclick='validateYouTubeAnswer()'>
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

// Contrôles vidéo
function replayVideo() {
    const iframe = document.querySelector('.video-container iframe');
    if (iframe) {
        const currentSrc = iframe.src;
        iframe.src = currentSrc;
    }
}

function toggleFullscreen() {
    const iframe = document.querySelector('.video-container iframe');
    if (iframe) {
        if (iframe.requestFullscreen) {
            iframe.requestFullscreen();
        } else if (iframe.webkitRequestFullscreen) {
            iframe.webkitRequestFullscreen();
        } else if (iframe.msRequestFullscreen) {
            iframe.msRequestFullscreen();
        }
    }
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
function validateYouTubeAnswer() {
    const reponse = document.getElementById('reponse_youtube').value.trim();
    
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
            text: 'Vous avez résolu l\'énigme vidéo !',
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
            text: 'Regardez bien la vidéo et réessayez...',
            confirmButtonText: 'Réessayer'
        });
        
        // Vider le champ de réponse pour faciliter la nouvelle tentative
        document.getElementById('reponse_youtube').value = '';
        document.getElementById('reponse_youtube').focus();
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
document.getElementById('reponse_youtube').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        validateYouTubeAnswer();
    }
});

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Focus sur le champ de réponse
    document.getElementById('reponse_youtube').focus();
});
</script>
