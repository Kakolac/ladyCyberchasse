<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

include '../../includes/header.php';
?>

<div class='container mt-4'>
    <div class='row justify-content-center'>
        <div class='col-md-10'>
            <div class='card'>
                <div class='card-header bg-success text-white'>
                    <h2>🔍 Énigme - Salle de langues</h2>
                </div>
                <div class='card-body'>
                    <div class='alert alert-info'>
                        <h5>🎯 Contexte</h5>
                        <p>Résolvez cette énigme de cybersécurité pour progresser dans votre mission et débloquer le prochain lieu !</p>
                    </div>
                    
                    <div class='enigme-content'>
                        <h4>🎯 Question principale</h4>
                        <p class='lead'>Quelle est la <strong>BONNE</strong> pratique de cybersécurité ?</p>
                        
                        <div class='options mt-4'>
                            <div class='form-check mb-3'>
                                <input class='form-check-input' type='radio' name='answer' id='option1' value='A'>
                                <label class='form-check-label' for='option1'>
                                    <strong>A)</strong> Partager ses mots de passe avec ses amis de confiance.
                                </label>
                            </div>
                            
                            <div class='form-check mb-3'>
                                <input class='form-check-input' type='radio' name='answer' id='option2' value='B'>
                                <label class='form-check-label' for='option2'>
                                    <strong>B)</strong> Installer les mises à jour de sécurité dès qu'elles sont disponibles.
                                </label>
                            </div>
                            
                            <div class='form-check mb-3'>
                                <input class='form-check-input' type='radio' name='answer' id='option3' value='C'>
                                <label class='form-check-label' for='option3'>
                                    <strong>C)</strong> Cliquer sur tous les liens reçus par email.
                                </label>
                            </div>
                            
                            <div class='form-check mb-3'>
                                <input class='form-check-input' type='radio' name='answer' id='option4' value='D'>
                                <label class='form-check-label' for='option4'>
                                    <strong>D)</strong> Désactiver l'antivirus pour améliorer les performances.
                                </label>
                            </div>
                        </div>
                        
                        <div class='text-center mt-4'>
                            <button type='button' class='btn btn-success btn-lg' onclick='validateAnswer()'>
                                ✅ Valider ma réponse
                            </button>
                        </div>
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
        alert('⚠️ Veuillez sélectionner une réponse avant de valider.');
        return;
    }
    
    const answer = selectedAnswer.value;
    
    // La réponse correcte est B (installer les mises à jour)
    if (answer === 'B') {
        saveProgress('salle_langues', true);
        
        Swal.fire({
            title: '🎉 Bravo !',
            text: 'Vous avez résolu l\'énigme ! Les mises à jour de sécurité sont essentielles.',
            icon: 'success',
            confirmButtonText: 'Continuer l\'aventure'
        }).then((result) => {
            window.location.href = '../accueil/';
        });
    } else {
        Swal.fire({
            title: '❌ Réponse incorrecte',
            text: 'Réfléchissez aux bonnes pratiques de cybersécurité...',
            icon: 'error',
            confirmButtonText: 'Réessayer'
        });
    }
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