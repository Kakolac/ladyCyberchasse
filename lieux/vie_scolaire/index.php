<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

require_once '../../config/connexion.php';

// Récupération des informations de l'équipe et du lieu
$team_name = $_SESSION['team_name'];
$lieu_slug = 'vie_scolaire';

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
                <div class="card-header bg-primary text-white">
                    <h2><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($lieu['nom']); ?></h2>
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
                            <a href="../accueil/" class="btn btn-primary btn-lg">
                                <i class="fas fa-home"></i> Retour à l'accueil
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
                            <p class="lead">Quelle est la bonne pratique RGPD ?</p>
                            
                            <div class="options mt-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="answer" id="option1" value="A">
                                    <label class="form-check-label" for="option1">
                                        <strong>A)</strong> Collecter toutes les données possibles
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="answer" id="option2" value="B">
                                    <label class="form-check-label" for="option2">
                                        <strong>B)</strong> Partager les données avec tout le monde
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="answer" id="option3" value="C">
                                    <label class="form-check-label" for="option3">
                                        <strong>C)</strong> Garder les données indéfiniment
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="answer" id="option4" value="D">
                                    <label class="form-check-label" for="option4">
                                        <strong>D)</strong> Demander le consentement avant collecte
                                    </label>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-primary btn-lg" onclick="validateAnswer()">
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
// Test de la fonction au chargement de la page
console.log('Page chargée, fonction validateAnswer disponible');

function validateAnswer() {
    console.log('Fonction validateAnswer appelée');
    
    const selectedAnswer = document.querySelector('input[name="answer"]:checked');
    console.log('Réponse sélectionnée:', selectedAnswer ? selectedAnswer.value : 'Aucune');
    
    if (!selectedAnswer) {
        console.log('Aucune réponse sélectionnée, affichage de l\'alerte');
        
        // Test avec alert() simple d'abord
        alert('⚠️ Veuillez sélectionner une réponse avant de valider.');
        
        // Puis test avec SweetAlert2
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '⚠️ Attention',
                text: 'Veuillez sélectionner une réponse avant de valider.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        } else {
            console.error('SweetAlert2 n\'est pas chargé');
        }
        return;
    }
    
    const answer = selectedAnswer.value;
    console.log('Validation de la réponse:', answer);
    
    // Test de la validation avec une réponse codée en dur d'abord
    if (answer === 'D') { // Réponse correcte
        console.log('Réponse correcte détectée');
        
        // Mise à jour du parcours
        updateParcoursStatus(true);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '🎉 Bravo !',
                text: 'Vous avez résolu l\'énigme !',
                icon: 'success',
                confirmButtonText: 'Continuer l\'aventure'
            }).then((result) => {
                window.location.reload();
            });
        } else {
            alert('🎉 Bravo ! Vous avez résolu l\'énigme !');
            window.location.reload();
        }
    } else {
        console.log('Réponse incorrecte détectée');
        
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
    
    // Envoi de la réponse au serveur pour validation SÉCURISÉE (décommenter plus tard)
    /*
    fetch('../../validation_enigme.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lieu: 'vie_scolaire',
            reponse: answer
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.correct) {
            updateParcoursStatus(true);
            
            Swal.fire({
                title: '🎉 Bravo !',
                text: 'Vous avez résolu l\'énigme !',
                icon: 'success',
                confirmButtonText: 'Continuer l\'aventure'
            }).then((result) => {
                window.location.reload();
            });
        } else {
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
    */
}

function updateParcoursStatus(success) {
    console.log('Mise à jour du statut du parcours:', success);
    
    // Mise à jour du statut du parcours en base de données
    fetch('../../update_parcours_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lieu: 'vie_scolaire',
            team: '<?php echo $_SESSION["team_name"]; ?>',
            success: success,
            score: 10
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Réponse du serveur:', data);
        if (data.success) {
            console.log('Statut du parcours mis à jour avec succès');
        } else {
            console.error('Erreur mise à jour parcours:', data.error);
        }
    })
    .catch(error => {
        console.error('Erreur lors de la mise à jour:', error);
    });
}

// Démarrer le timer seulement si l'énigme n'est pas résolue
<?php if (!$enigme_resolue): ?>
    console.log('Démarrage du timer');
    startTimer(720, 'timer');
<?php endif; ?>
</script>

<?php include './footer.php'; ?>