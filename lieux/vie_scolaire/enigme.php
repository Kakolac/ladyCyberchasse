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
                <div class="card-header bg-warning text-dark">
                    <h2>🔐 Énigme RGPD - Protection des Données</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>📋 Contexte</h5>
                        <p>La vie scolaire gère des données personnelles sensibles. Votre équipe doit identifier les bonnes pratiques RGPD pour protéger la vie privée des élèves.</p>
                    </div>
                    
                    <div class="enigme-content">
                        <h4>🎯 Question principale</h4>
                        <p class="lead">Quelle est la <strong>BONNE</strong> pratique RGPD pour la gestion des données d'élèves ?</p>
                        
                        <div class="options mt-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option1" value="A">
                                <label class="form-check-label" for="option1">
                                    <strong>A)</strong> Partager les notes d'un élève avec ses parents sans son consentement explicite.
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option2" value="B">
                                <label class="form-check-label" for="option2">
                                    <strong>B)</strong> Conserver les données d'élèves indéfiniment "au cas où".
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option3" value="C">
                                <label class="form-check-label" for="option3">
                                    <strong>C)</strong> Demander le consentement explicite avant de collecter des données personnelles.
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option4" value="D">
                                <label class="form-check-label" for="option4">
                                    <strong>D)</strong> Publier les résultats de classe sur un site web public.
                                </label>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-warning btn-lg" onclick="validateAnswer()">
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
    
    // La réponse correcte est C (consentement explicite)
    if (answer === 'C') {
        saveProgress('vie_scolaire', true);
        
        Swal.fire({
            title: '🎉 Bravo !',
            text: 'Vous avez identifié la bonne pratique RGPD ! Le consentement explicite est essentiel.',
            icon: 'success',
            confirmButtonText: 'Continuer l\'aventure'
        }).then((result) => {
            window.location.href = '../salle_info/';
        });
    } else {
        Swal.fire({
            title: '❌ Réponse incorrecte',
            text: 'Réfléchissez aux principes de protection des données personnelles...',
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

