<?php
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
?>

<div class='enigme-content'>
    <h4>🎬 Question vidéo YouTube</h4>
    <p class='lead'><?php echo htmlspecialchars($donnees['question']); ?></p>
    
    <?php if (!empty($donnees['contexte'])): ?>
        <div class="contexte-section mb-4">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong> Contexte :</strong> <?php echo htmlspecialchars($donnees['contexte']); ?>
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
// Variables PHP passées au JavaScript - MAINTENANT DEPUIS LA BDD
let indiceConsulte = <?php echo $indice_consulte ? 'true' : 'false'; ?>;
let indiceAvailable = <?php echo $indice_available ? 'true' : 'false'; ?>;

// NOUVELLES VARIABLES DEPUIS LA BDD
const enigmeStartTimestamp = <?php echo $enigme_start_time; ?>;
const indiceStartTimestamp = <?php echo $indice_start_time; ?>;
const delaiIndiceSecondes = <?php echo $delai_indice_secondes; ?>;

// Debug dans la console
console.log(' DEBUG TIMER INDICE YOUTUBE - Variables BDD:');
console.log('  - enigme_start_time (BDD):', enigmeStartTimestamp);
console.log('  - indice_start_time (BDD):', indiceStartTimestamp);
console.log('  - indice_available:', indiceAvailable);
console.log('  - indice_consulte:', indiceConsulte);
console.log('  - remaining_time:', <?php echo $remaining_time; ?>);
console.log('  - delai_indice_secondes:', delaiIndiceSecondes);
console.log('  - current_time:', Math.floor(Date.now() / 1000));

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

// FONCTION startIndiceTimer - MAINTENANT AVEC LES TIMESTAMPS BDD
function startIndiceTimer() {
    console.log('⏰ startIndiceTimer() YOUTUBE appelée avec timestamps BDD');
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
    
    console.log('  - Démarrage du timer d\'indice YOUTUBE avec BDD');
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
        
        console.log('⏱️ Timer tick YOUTUBE BDD:', {
            now: now,
            enigmeStart: enigmeStartTimestamp,
            delaiIndice: delaiIndiceSecondes,
            remaining: remaining,
            elapsed: now - enigmeStartTimestamp
        });
        
        if (remaining <= 0) {
            // L'indice est maintenant disponible
            console.log('🎉 Indice YOUTUBE maintenant disponible !');
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
    
    console.log('  - Timer d\'indice YOUTUBE BDD démarré avec intervalle de 1 seconde');
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

// VÉRIFICATION AU CHARGEMENT DE LA PAGE - COPIER DEPUIS texte_libre.php
document.addEventListener('DOMContentLoaded', function() {
    // Focus sur le champ de réponse
    document.getElementById('reponse_youtube').focus();
    
    // Vérifier si on doit démarrer le timer de l'indice
    if (!indiceAvailable && !indiceConsulte) {
        startIndiceTimer();
    }
});
</script>
