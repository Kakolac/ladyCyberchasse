/**
 * Fonctions centralisées pour la validation des énigmes
 * Ce fichier élimine la duplication de code entre tous les templates d'énigmes
 */

// Variables globales (seront définies par chaque template)
let LIEU_SLUG = null;
let TEAM_NAME = null;
let LIEU_ID = null;
let EQUIPE_ID = null;
let ENIGME_ID = null;

/**
 * Met à jour le statut du parcours après résolution d'énigme
 * @param {boolean} success - true si l'énigme est résolue, false sinon
 * @param {number} score - Score obtenu (défaut: 10)
 */
function updateParcoursStatus(success, score = 10) {
    if (!LIEU_SLUG || !TEAM_NAME) {
        console.error('Variables LIEU_SLUG ou TEAM_NAME non définies');
        return;
    }

    fetch('update_parcours_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lieu: LIEU_SLUG,
            team: TEAM_NAME,
            success: success,
            score: score
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Statut du parcours mis à jour:', data.message);
            
            // Si c'est la fin du parcours, afficher un message spécial
            if (data.parcours_termine) {
                Swal.fire({
                    icon: 'success',
                    title: '🎉 PARCOURS TERMINÉ !',
                    text: data.message,
                    confirmButtonText: 'Voir le classement',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'accueil/';
                    }
                });
            }
        } else {
            console.error('Erreur mise à jour parcours:', data.error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Problème lors de la mise à jour du parcours',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Erreur réseau:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erreur réseau',
            text: 'Impossible de contacter le serveur',
            confirmButtonText: 'OK'
        });
    });
}

/**
 * Valide une réponse d'énigme QCM
 * @param {string} reponseCorrecte - La réponse correcte attendue
 * @param {string} score - Score par défaut (défaut: 10)
 */
function validateQCMAnswer(reponseCorrecte, score = 10) {
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
    
    if (answer === reponseCorrecte) {
        // Mise à jour du parcours
        updateParcoursStatus(true, score);
        
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

/**
 * Valide une réponse d'énigme à texte libre
 * @param {string} reponseCorrecte - La réponse exacte attendue
 * @param {Array} reponsesAcceptees - Tableau des réponses acceptées alternatives
 * @param {string} score - Score par défaut (défaut: 10)
 */
function validateTextAnswer(reponseCorrecte, reponsesAcceptees = [], score = 10) {
    const reponse = document.getElementById('reponse_libre').value.trim();
    
    if (!reponse) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ Attention',
            text: 'Veuillez saisir une réponse avant de valider.'
        });
        return;
    }
    
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
        updateParcoursStatus(true, score);
        
        Swal.fire({
            icon: 'success',
            title: '🎉 Bravo !',
            text: 'Vous avez résolu l\'énigme !',
            confirmButtonText: 'Continuer',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'lieux/' + LIEU_SLUG + '/';
            }
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: '❌ Réponse incorrecte',
            text: 'Réfléchissez et réessayez...',
            confirmButtonText: 'Réessayer'
        });
        
        // Vider le champ de réponse pour faciliter la nouvelle tentative
        document.getElementById('reponse_libre').value = '';
        document.getElementById('reponse_libre').focus();
    }
}

/**
 * Valide une réponse d'énigme audio
 * @param {string} reponseCorrecte - La réponse correcte attendue
 * @param {string} score - Score par défaut (défaut: 10)
 */
function validateAudioAnswer(reponseCorrecte, score = 10) {
    const reponse = document.getElementById('reponse_audio').value.trim();
    
    if (!reponse) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ Attention',
            text: 'Veuillez saisir une réponse avant de valider.'
        });
        return;
    }
    
    if (reponse.toLowerCase() === reponseCorrecte.toLowerCase()) {
        // Mise à jour du parcours
        updateParcoursStatus(true, score);
        
        Swal.fire({
            icon: 'success',
            title: '🎉 Bravo !',
            text: 'Vous avez résolu l\'énigme audio !',
            confirmButtonText: 'Continuer l\'aventure'
        }).then((result) => {
            window.location.href = 'lieux/' + LIEU_SLUG + '/';
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: '❌ Réponse incorrecte',
            text: 'Écoutez attentivement et réessayez...',
            confirmButtonText: 'Réessayer'
        });
        
        // Vider le champ de réponse
        document.getElementById('reponse_audio').value = '';
        document.getElementById('reponse_audio').focus();
    }
}

/**
 * Valide une réponse d'énigme YouTube
 * @param {string} reponseCorrecte - La réponse correcte attendue
 * @param {string} score - Score par défaut (défaut: 10)
 */
function validateYouTubeAnswer(reponseCorrecte, score = 10) {
    const reponse = document.getElementById('reponse_youtube').value.trim();
    
    if (!reponse) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ Attention',
            text: 'Veuillez saisir une réponse avant de valider.'
        });
        return;
    }
    
    if (reponse.toLowerCase() === reponseCorrecte.toLowerCase()) {
        // Mise à jour du parcours
        updateParcoursStatus(true, score);
        
        Swal.fire({
            icon: 'success',
            title: '🎉 Bravo !',
            text: 'Vous avez résolu l\'énigme vidéo !',
            confirmButtonText: 'Continuer l\'aventure'
        }).then((result) => {
            window.location.href = 'lieux/' + LIEU_SLUG + '/';
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: '❌ Réponse incorrecte',
            text: 'Regardez attentivement la vidéo et réessayez...',
            confirmButtonText: 'Réessayer'
        });
        
        // Vider le champ de réponse
        document.getElementById('reponse_youtube').value = '';
        document.getElementById('reponse_youtube').focus();
    }
}

/**
 * Initialise les variables globales pour une énigme
 * @param {Object} config - Configuration de l'énigme
 */
function initEnigme(config) {
    LIEU_SLUG = config.lieu_slug;
    TEAM_NAME = config.team_name;
    LIEU_ID = config.lieu_id;
    EQUIPE_ID = config.equipe_id;
    ENIGME_ID = config.enigme_id;
    
    console.log('Énigme initialisée:', config);
}

/**
 * Gère l'échec d'une énigme
 * @param {string} message - Message d'échec personnalisé
 */
function handleEnigmeFailure(message = 'Énigme échouée') {
    updateParcoursStatus(false, 0);
    
    Swal.fire({
        icon: 'error',
        title: '❌ Échec',
        text: message,
        confirmButtonText: 'Réessayer'
    });
}

// Export des fonctions pour utilisation dans d'autres modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        updateParcoursStatus,
        validateQCMAnswer,
        validateTextAnswer,
        validateAudioAnswer,
        validateYouTubeAnswer,
        initEnigme,
        handleEnigmeFailure
    };
}
