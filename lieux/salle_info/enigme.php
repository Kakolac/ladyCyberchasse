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
                <div class="card-header bg-info text-white">
                    <h2>🔒 Énigme Cybersécurité - Force des Mots de Passe</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>💻 Contexte</h5>
                        <p>Testez la force de différents mots de passe et apprenez à créer des mots de passe sécurisés pour protéger vos comptes.</p>
                    </div>
                    
                    <div class="enigme-content">
                        <h4>🎯 Question principale</h4>
                        <p class="lead">Quel est le mot de passe le <strong>PLUS SÉCURISÉ</strong> parmi ces options ?</p>
                        
                        <div class="options mt-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option1" value="A">
                                <label class="form-check-label" for="option1">
                                    <strong>A)</strong> "password123"
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option2" value="B">
                                <label class="form-check-label" for="option2">
                                    <strong>B)</strong> "azerty"
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option3" value="C">
                                <label class="form-check-label" for="option3">
                                    <strong>C)</strong> "K9#mP$2vL@8nQ"
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option4" value="D">
                                <label class="form-check-label" for="option4">
                                    <strong>D)</strong> "123456789"
                                </label>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-info btn-lg" onclick="validateAnswer()">
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
    
    // La réponse correcte est C (mot de passe complexe)
    if (answer === 'C') {
        saveProgress('salle_info', true);
        
        Swal.fire({
            title: '🎉 Bravo !',
            text: 'Vous avez identifié le mot de passe le plus sécurisé ! La complexité est la clé.',
            icon: 'success',
            confirmButtonText: 'Continuer l\'aventure'
        }).then((result) => {
            window.location.href = '../labo_physique/';
        });
    } else {
        Swal.fire({
            title: '❌ Réponse incorrecte',
            text: 'Réfléchissez à ce qui rend un mot de passe sécurisé...',
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

