<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

require_once '../../config/connexion.php';

// Récupération des informations de l'équipe et du lieu
$team_name = $_SESSION['team_name'];
$lieu_slug = 'salle_info';

// Récupération de l'équipe
$stmt = $pdo->prepare("SELECT id FROM equipes WHERE nom = ?");
$stmt->execute([$team_name]);
$equipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipe) {
    header('Location: ../../login.php');
    exit();
}

// Récupération du lieu
$stmt = $pdo->prepare("SELECT id, nom, ordre FROM lieux WHERE slug = ?");
$stmt->execute([$lieu_slug]);
$lieu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lieu) {
    header('Location: ../../accueil/');
    exit();
}

// Récupération du parcours de l'équipe pour ce lieu
$stmt = $pdo->prepare("SELECT * FROM parcours WHERE equipe_id = ? AND lieu_id = ?");
$stmt->execute([$equipe['id'], $lieu['id']]);
$parcours = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérification si l'énigme est déjà résolue
$enigme_resolue = ($parcours && $parcours['statut'] === 'termine');

include './header.php';
?>

<!-- Inclusion de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h2>🔍 Énigme - <?php echo htmlspecialchars($lieu['nom']); ?> - Cybersécurité</h2>
                </div>
                <div class="card-body">
                    
                    <?php if ($enigme_resolue): ?>
                        <!-- Énigme déjà résolue -->
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h4>🎉 Bravo !</h4>
                            <p>Vous avez déjà résolu l'énigme de ce lieu.</p>
                            <p class="mb-0"><strong>Score obtenu :</strong> <?php echo $parcours['score_obtenu'] ?? 0; ?> points</p>
                        </div>
                        
                        <div class="text-center">
                            <a href="index.php" class="btn btn-info btn-lg">
                                <i class="fas fa-arrow-left"></i> Retour au lieu
                            </a>
                        </div>
                        
                    <?php else: ?>
                        <!-- Énigme à résoudre -->
                        <div class="alert alert-info">
                            <h5>🎯 Contexte</h5>
                            <p>Résolvez cette énigme de cybersécurité pour progresser dans votre mission et débloquer le prochain lieu !</p>
                        </div>
                        
                        <div class="enigme-content">
                            <h4>🎯 Question principale</h4>
                            <p class="lead">Quelle est la bonne pratique pour les mots de passe ?</p>
                            
                            <div class="options mt-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="answer" id="option1" value="A">
                                    <label class="form-check-label" for="option1">
                                        <strong>A)</strong> Utiliser des mots simples à retenir
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="answer" id="option2" value="B">
                                    <label class="form-check-label" for="option2">
                                        <strong>B)</strong> Écrire ses mots de passe partout
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="answer" id="option3" value="C">
                                    <label class="form-check-label" for="option3">
                                        <strong>C)</strong> Utiliser un gestionnaire de mots de passe
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="answer" id="option4" value="D">
                                    <label class="form-check-label" for="option4">
                                        <strong>D)</strong> Partager ses mots de passe
                                    </label>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-info btn-lg" onclick="validateAnswer()">
                                    ✅ Valider ma réponse
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateAnswer() {
    const selectedAnswer = document.querySelector('input[name="answer"]:checked');
    
    if (!selectedAnswer) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '⚠️ Attention',
                text: 'Veuillez sélectionner une réponse avant de valider.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        } else {
            alert('⚠️ Veuillez sélectionner une réponse avant de valider.');
        }
        return;
    }
    
    const answer = selectedAnswer.value;
    
    // Test de la validation avec une réponse codée en dur d'abord
    if (answer === 'C') { // Réponse correcte
        // Mise à jour du parcours
        updateParcoursStatus(true);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '🎉 Bravo !',
                text: 'Vous avez résolu l\'énigme !',
                icon: 'success',
                confirmButtonText: 'Continuer l\'aventure'
            }).then((result) => {
                window.location.href = 'index.php';
            });
        } else {
            alert('🎉 Bravo ! Vous avez résolu l\'énigme !');
            window.location.href = 'index.php';
        }
    } else {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '❌ Réponse incorrecte',
                text: 'Réfléchissez et réessayez...',
                icon: 'error',
                confirmButtonText: 'Réessayer'
            });
        } else {
            alert('❌ Réponse incorrecte. Réfléchissez et réessayez...');
        }
    }
}

function updateParcoursStatus(success) {
    fetch('../../update_parcours_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lieu: 'salle_info',
            team: '<?php echo $_SESSION["team_name"]; ?>',
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

<?php include './footer.php'; ?>
```

