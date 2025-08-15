<?php
// Récupération des données de l'énigme
$stmt = $pdo->prepare("SELECT donnees FROM enigmes WHERE id = ?");
$stmt->execute([$lieu['enigme_id']]);
$enigme_data = $stmt->fetch(PDO::FETCH_ASSOC);
$donnees = json_decode($enigme_data['donnees'], true);

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

// Debug pour vérifier la persistance
$debug_info = [
    'enigme_start' => date('H:i:s', $enigme_start_time),
    'indice_start' => date('H:i:s', $indice_start_time),
    'enigme_elapsed' => gmdate('i:s', $enigme_elapsed_time),
    'indice_elapsed' => gmdate('i:s', $indice_elapsed_time),
    'remaining' => gmdate('i:s', $remaining_time),
    'indice_available' => $indice_available,
    'session_keys' => [$enigme_session_key, $indice_session_key],
    'session_exists' => [
        'enigme' => isset($_SESSION[$enigme_session_key]),
        'indice' => isset($_SESSION[$indice_session_key])
    ]
];
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
                        L'indice sera disponible après <?php echo $lieu['delai_indice']; ?> minutes de réflexion
                    </small>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div class='reponse-libre mt-4'>
        <label for="reponse_libre" class="form-label">
            <strong>✍️ Votre réponse :</strong>
        </label>
        <input type="text" class="form-control form-control-lg" 
               id="reponse_libre" 
               placeholder="Tapez votre réponse ici..."
               maxlength="100">
        <small class="text-muted">Réponse sensible à la casse</small>
    </div>
    
    <div class='text-center mt-4'>
        <button type='button' class='btn btn-dark btn-lg' onclick='validateTextAnswer()'>
            ✅ Valider ma réponse
        </button>
    </div>
</div>

<script>
let indiceConsulte = <?php echo $indice_consulte ? 'true' : 'false'; ?>;
let indiceAvailable = <?php echo $indice_available ? 'true' : 'false'; ?>;

// Variables PHP passées au JavaScript - SUPPRIMER la redéclaration de DELAI_INDICE_SECONDES
const LIEU_ID = <?php echo $lieu['id'] ?? 'null'; ?>;
const EQUIPE_ID = <?php echo $equipe['id'] ?? 'null'; ?>;
const ENIGME_ID = <?php echo $lieu['enigme_id'] ?? 'null'; ?>;
// SUPPRIMER cette ligne : const DELAI_INDICE_SECONDES = <?php echo $lieu['delai_indice'] * 60; ?>;

// Fonction pour démarrer le timer de l'indice
function startIndiceTimer() {
    // Debug mobile avec alert
    alert('Timer démarré ! Temps restant: <?php echo gmdate('i:s', $remaining_time); ?>');
    
    if (indiceAvailable) {
        alert('Indice déjà disponible, pas de timer nécessaire');
        return;
    }
    
    const indiceButton = document.getElementById('indice-button');
    if (!indiceButton) {
        alert('Éléments du timer non trouvés');
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
        // Utiliser la variable globale DELAI_INDICE_SECONDES depuis enigme_launcher.php
        const remaining = DELAI_INDICE_SECONDES - (now - enigmeStart);
        
        if (remaining <= 0) {
            // L'indice est maintenant disponible
            clearInterval(countdown);
            indiceAvailable = true;
            
            // Debug
            alert('💡 Indice maintenant disponible !');
            
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

// Fonction pour consulter l'indice
function consulterIndice() {
    if (indiceConsulte) {
        return;
    }
    
    // Afficher l'indice
    const indiceContent = document.getElementById('indice-content');
    if (indiceContent) {
        indiceContent.style.display = 'block';
    }
    
    // Mettre à jour le bouton
    const indiceButton = document.querySelector('.indice-section button');
    if (indiceButton) {
        indiceButton.innerHTML = '<i class="fas fa-check"></i> Indice consulté';
        indiceButton.className = 'btn btn-secondary btn-sm';
        indiceButton.disabled = true;
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
        if (data.success) {
            alert('✅ Consultation d\'indice enregistrée');
        } else {
            alert('❌ Erreur enregistrement indice: ' + data.error);
        }
    })
    .catch(error => {
        alert('❌ Erreur: ' + error);
    });
}

function validateTextAnswer() {
    const reponse = document.getElementById('reponse_libre').value.trim();
    
    if (!reponse) {
        alert('⚠️ Veuillez saisir une réponse avant de valider.');
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
        
        alert('🎉 Bravo ! Vous avez résolu l\'énigme !');
        window.location.href = 'lieux/' + LIEU_SLUG + '/';
    } else {
        alert('❌ Réponse incorrecte. Réfléchissez et réessayez...');
        
        // Vider le champ de réponse pour faciliter la nouvelle tentative
        document.getElementById('reponse_libre').value = '';
        document.getElementById('reponse_libre').focus();
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
document.getElementById('reponse_libre').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        validateTextAnswer();
    }
});

// Vérification au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    alert('Page chargée ! Vérification du timer...');
    
    // Vérifier si on doit démarrer le timer de l'indice
    if (!indiceAvailable && !indiceConsulte) {
        alert('Démarrage automatique du timer...');
        startIndiceTimer();
    } else {
        alert('Timer non nécessaire ou déjà consulté');
    }
});
</script>
