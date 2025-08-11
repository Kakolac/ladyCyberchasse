<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h2>🔍 Énigme Physique - Décodage Cryptographique</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>⚡ Contexte</h5>
                        <p>Utilisez vos connaissances en physique pour décoder ce message crypté. Les principes scientifiques peuvent révéler des secrets cachés !</p>
                    </div>
                    
                    <div class="enigme-content">
                        <h4>🎯 Question principale</h4>
                        <p class="lead">Décodez ce message en utilisant le principe de <strong>résonance</strong> :</p>
                        
                        <div class="crypto-message mt-4 p-4 bg-light border rounded">
                            <h5>🔍 Indice :</h5>
                            <p class="display-6 text-monospace">F = 1 / (2π√LC)</p>
                            <p class="text-muted">Cette formule représente la fréquence de résonance d'un circuit LC.</p>
                        </div>
                        
                        <div class="mt-4">
                            <h5>🔍 Indice :</h5>
                            <p>En physique, la résonance permet de transmettre des informations. Quelle est la fréquence de résonance si L = 1 H et C = 1 F ?</p>
                        </div>
                        
                        <div class="options mt-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option1" value="A">
                                <label class="form-check-label" for="option1">
                                    <strong>A)</strong> 0.159 Hz
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option2" value="B">
                                <label class="form-check-label" for="option2">
                                    <strong>B)</strong> 1 Hz
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option3" value="C">
                                <label class="form-check-label" for="option3">
                                    <strong>C)</strong> 6.28 Hz
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option4" value="D">
                                <label class="form-check-label" for="option4">
                                    <strong>D)</strong> 15.9 Hz
                                </label>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-danger btn-lg" onclick="validateAnswer()">
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
    
    // La réponse correcte est A (0.159 Hz)
    if (answer === 'A') {
        saveProgress('labo_physique', true);
        
        Swal.fire({
            title: '🎉 Bravo !',
            text: 'Vous avez décodé le message ! La fréquence de résonance est bien 0.159 Hz.',
            icon: 'success',
            confirmButtonText: 'Mission accomplie !'
        }).then((result) => {
            window.location.href = '../accueil/?mission=complete';
        });
    } else {
        Swal.fire({
            title: '❌ Réponse incorrecte',
            text: 'Vérifiez vos calculs avec la formule F = 1/(2π√LC)...',
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
```

