


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
</script>