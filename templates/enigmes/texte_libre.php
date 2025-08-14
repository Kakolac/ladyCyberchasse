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
            <?php else: ?>
                <!-- Bouton pour consulter l'indice -->
                <button type="button" class="btn btn-info btn-sm" onclick="consulterIndice()">
                    <i class="fas fa-lightbulb"></i> 💡 Consulter l'indice
                </button>
                <div id="indice-content" class="mt-2" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i>
                        <strong>💡 Indice :</strong> <?php echo htmlspecialchars($donnees['indice']); ?>
                    </div>
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

function consulterIndice() {
    if (indiceConsulte) return;
    
    // Afficher l'indice
    document.getElementById('indice-content').style.display = 'block';
    
    // Changer le bouton
    const bouton = event.target;
    bouton.innerHTML = '<i class="fas fa-check"></i> ✅ Indice consulté';
    bouton.className = 'btn btn-secondary btn-sm';
    bouton.disabled = true;
    
    // Marquer comme consulté
    indiceConsulte = true;
    
    // Enregistrer la consultation en base
    saveIndiceConsultation();
}

function saveIndiceConsultation() {
    fetch('save_indice_consultation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lieu: LIEU_SLUG,
            enigme_id: <?php echo $lieu['enigme_id']; ?>
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Consultation d\'indice enregistrée');
        } else {
            console.error('Erreur enregistrement indice:', data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

function validateTextAnswer() {
    const reponse = document.getElementById('reponse_libre').value.trim();
    
    if (!reponse) {
        Swal.fire({
            title: '⚠️ Attention',
            text: 'Veuillez saisir une réponse avant de valider.',
            icon: 'warning',
            confirmButtonText: 'OK'
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
</script>
