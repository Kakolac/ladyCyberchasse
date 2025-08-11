<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

require_once '../../config/connexion.php';

// Récupération de l'énigme depuis la base de données
$stmt = $pdo->prepare("SELECT enigme_texte, options_enigme FROM lieux WHERE slug = 'salle_reunion'");
$stmt->execute();
$lieu = $stmt->fetch(PDO::FETCH_ASSOC);

$enigme_texte = $lieu['enigme_texte'] ?? 'Énigme en cours de configuration...';
$options = json_decode($lieu['options_enigme'] ?? '{}', true);

include '../../includes/header.php';
?>

<div class='container mt-4'>
    <div class='row justify-content-center'>
        <div class='col-md-10'>
            <div class='card'>
                <div class='card-header bg-secondary text-white'>
                    <h2>🔍 Énigme - Salle de réunion</h2>
                </div>
                <div class='card-body'>
                    <div class='alert alert-info'>
                        <h5>🎯 Contexte</h5>
                        <p>Résolvez cette énigme de cybersécurité pour progresser dans votre mission et débloquer le prochain lieu !</p>
                    </div>
                    
                    <div class='enigme-content'>
                        <h4>🎯 Question principale</h4>
                        <p class='lead'><?php echo htmlspecialchars($enigme_texte); ?></p>
                        
                        <div class='options mt-4'>
                            <?php if ($options && count($options) > 0): ?>
                                <?php foreach ($options as $key => $option): ?>
                                    <div class='form-check mb-3'>
                                        <input class='form-check-input' type='radio' name='answer' id='option<?php echo $key; ?>' value='<?php echo $key; ?>'>
                                        <label class='form-check-label' for='option<?php echo $key; ?>'>
                                            <strong><?php echo $key; ?>)</strong> <?php echo htmlspecialchars($option); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class='alert alert-warning'>
                                    <i class='fas fa-exclamation-triangle'></i>
                                    Cette énigme n'est pas encore configurée. Contactez l'administrateur.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($options && count($options) > 0): ?>
                            <div class='text-center mt-4'>
                                <button type='button' class='btn btn-secondary btn-lg' onclick='validateAnswer()'>
                                    ✅ Valider ma réponse
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
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
    
    // Envoi de la réponse au serveur pour validation SÉCURISÉE
    fetch('../../validation_enigme.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lieu: 'salle_reunion',
            reponse: answer
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.correct) {
            // Réponse correcte
            saveProgress('salle_reunion', true);
            
            Swal.fire({
                title: '🎉 Bravo !',
                text: 'Vous avez résolu l\'énigme !',
                icon: 'success',
                confirmButtonText: 'Continuer l\'aventure'
            }).then((result) => {
                window.location.href = '../accueil/';
            });
        } else {
            // Réponse incorrecte
            Swal.fire({
                title: '❌ Réponse incorrecte',
                text: data.message || 'Réfléchissez et réessayez...',
                icon: 'error',
                confirmButtonText: 'Réessayer'
            });
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        Swal.fire({
            title: '❌ Erreur',
            text: 'Une erreur est survenue. Veuillez réessayer.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

function saveProgress(lieu, success) {
    fetch('../../save_time.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lieu: lieu,
            success: success,
            team: '<?php echo $_SESSION["team_name"]; ?>'
        })
    });
}
</script>

<?php include '../../includes/footer.php'; ?>