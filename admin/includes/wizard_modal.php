<!-- Modal Wizard -->
<div class="modal fade" id="wizardModal" tabindex="-1" aria-labelledby="wizardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="wizardModalLabel">
                    <i class="fas fa-magic"></i> Assistant de Création
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Étapes du wizard -->
                <div class="wizard-steps">
                    <!-- Étape 1: Créer une énigme -->
                    <div class="wizard-step active" id="step1">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-question-circle"></i> Étape 1: Créer une Énigme
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type d'énigme</label>
                                <select class="form-select" id="enigmeType" required>
                                    <option value="">Choisir un type...</option>
                                    <option value="qcm">Question à choix multiples</option>
                                    <option value="texte_libre">Question à réponse libre</option>
                                    <option value="calcul">Calcul mathématique</option>
                                    <option value="image">Énigme avec image</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Difficulté</label>
                                <select class="form-select" id="enigmeDifficulte" required>
                                    <option value="facile">Facile</option>
                                    <option value="moyen">Moyen</option>
                                    <option value="difficile">Difficile</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Question/Énoncé</label>
                                <textarea class="form-control" id="enigmeQuestion" rows="3" placeholder="Posez votre question ou énigme..." required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Réponse correcte</label>
                                <input type="text" class="form-control" id="enigmeReponse" placeholder="La bonne réponse..." required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Indice (optionnel)</label>
                                <textarea class="form-control" id="enigmeIndice" rows="2" placeholder="Un indice pour aider les équipes..."></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Points</label>
                                <input type="number" class="form-control" id="enigmePoints" min="1" max="100" value="10" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Temps limite (minutes)</label>
                                <input type="number" class="form-control" id="enigmeTemps" min="1" max="60" value="15" required>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 2: Créer un lieu -->
                    <div class="wizard-step" id="step2" style="display: none;">
                        <h6 class="text-success mb-3">
                            <i class="fas fa-map-marker-alt"></i> Étape 2: Créer un Lieu
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom du lieu</label>
                                <input type="text" class="form-control" id="lieuNom" placeholder="Ex: Salle informatique, Cour du lycée..." required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type de lieu</label>
                                <select class="form-select" id="lieuType" required>
                                    <option value="">Choisir un type...</option>
                                    <option value="interieur">Intérieur</option>
                                    <option value="exterieur">Extérieur</option>
                                    <option value="salle">Salle de classe</option>
                                    <option value="couloir">Couloir</option>
                                    <option value="cour">Cour</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="lieuDescription" rows="3" placeholder="Décrivez le lieu et son ambiance..." required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Étage</label>
                                <input type="number" class="form-control" id="lieuEtage" min="-2" max="10" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ordre dans le parcours</label>
                                <input type="number" class="form-control" id="lieuOrdre" min="1" value="1" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Instructions d'accès</label>
                                <textarea class="form-control" id="lieuInstructions" rows="2" placeholder="Comment les équipes doivent accéder à ce lieu..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 3: Créer un parcours -->
                    <div class="wizard-step" id="step3" style="display: none;">
                        <h6 class="text-info mb-3">
                            <i class="fas fa-route"></i> Étape 3: Créer un Parcours
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom du parcours</label>
                                <input type="text" class="form-control" id="parcoursNom" placeholder="Ex: Parcours Découverte, Challenge Expert..." required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Niveau de difficulté</label>
                                <select class="form-select" id="parcoursDifficulte" required>
                                    <option value="debutant">Débutant</option>
                                    <option value="intermediaire">Intermédiaire</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Description du parcours</label>
                                <textarea class="form-control" id="parcoursDescription" rows="3" placeholder="Décrivez le thème et l'objectif de ce parcours..." required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Temps limite total (minutes)</label>
                                <input type="number" class="form-control" id="parcoursTemps" min="30" max="300" value="120" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre maximum d'équipes</label>
                                <input type="number" class="form-control" id="parcoursMaxEquipes" min="1" max="50" value="10" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Règles spéciales</label>
                                <textarea class="form-control" id="parcoursRegles" rows="2" placeholder="Règles particulières pour ce parcours..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 4: Finalisation -->
                    <div class="wizard-step" id="step4" style="display: none;">
                        <h6 class="text-warning mb-3">
                            <i class="fas fa-check-circle"></i> Étape 4: Finalisation
                        </h6>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Récapitulatif de votre création :</strong>
                            <div id="wizardSummary" class="mt-2">
                                <!-- Le récapitulatif sera généré ici -->
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="confirmCreation" required>
                            <label class="form-check-label" for="confirmCreation">
                                Je confirme la création de ces éléments
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="prevStep" style="display: none;">
                    <i class="fas fa-arrow-left"></i> Précédent
                </button>
                <button type="button" class="btn btn-primary" id="nextStep">
                    Suivant <i class="fas fa-arrow-right"></i>
                </button>
                <button type="button" class="btn btn-success" id="finishWizard" style="display: none;">
                    <i class="fas fa-check"></i> Créer
                </button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.wizard-step {
    transition: all 0.3s ease;
}

.wizard-step.active {
    display: block !important;
}

.wizard-step:not(.active) {
    display: none !important;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.alert {
    border-radius: 10px;
}
</style>

<script>
let currentStep = 1;
const totalSteps = 4;

// Initialisation du wizard
document.addEventListener('DOMContentLoaded', function() {
    const nextBtn = document.getElementById('nextStep');
    const prevBtn = document.getElementById('prevStep');
    const finishBtn = document.getElementById('finishWizard');
    
    if (nextBtn) nextBtn.addEventListener('click', nextStep);
    if (prevBtn) prevBtn.addEventListener('click', prevStep);
    if (finishBtn) finishBtn.addEventListener('click', finishWizard);
    
    // Validation des champs requis
    const requiredFields = document.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('input', validateStep);
    });
});

function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            document.getElementById(`step${currentStep}`).classList.remove('active');
            currentStep++;
            document.getElementById(`step${currentStep}`).classList.add('active');
            updateNavigation();
            
            if (currentStep === 4) {
                updateSummary();
            }
        }
    } else {
        alert('Veuillez remplir tous les champs obligatoires avant de continuer.');
    }
}

function prevStep() {
    if (currentStep > 1) {
        document.getElementById(`step${currentStep}`).classList.remove('active');
        currentStep--;
        document.getElementById(`step${currentStep}`).classList.add('active');
        updateNavigation();
    }
}

function validateCurrentStep() {
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    
    for (let field of requiredFields) {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            return false;
        } else {
            field.classList.remove('is-invalid');
        }
    }
    return true;
}

function validateStep() {
    this.classList.remove('is-invalid');
}

function updateNavigation() {
    const nextBtn = document.getElementById('nextStep');
    const prevBtn = document.getElementById('prevStep');
    const finishBtn = document.getElementById('finishWizard');
    
    if (currentStep === 1) {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'inline-block';
        finishBtn.style.display = 'none';
    } else if (currentStep === totalSteps) {
        prevBtn.style.display = 'inline-block';
        nextBtn.style.display = 'none';
        finishBtn.style.display = 'inline-block';
    } else {
        prevBtn.style.display = 'inline-block';
        nextBtn.style.display = 'inline-block';
        finishBtn.style.display = 'none';
    }
}

function updateSummary() {
    const summary = document.getElementById('wizardSummary');
    
    // Récupérer les valeurs de l'énigme
    const enigmeType = document.getElementById('enigmeType').value;
    const enigmeQuestion = document.getElementById('enigmeQuestion').value;
    const enigmePoints = document.getElementById('enigmePoints').value;
    
    // Récupérer les valeurs du lieu
    const lieuNom = document.getElementById('lieuNom').value;
    const lieuType = document.getElementById('lieuType').value;
    const lieuOrdre = document.getElementById('lieuOrdre').value;
    
    // Récupérer les valeurs du parcours
    const parcoursNom = document.getElementById('parcoursNom').value;
    const parcoursTemps = document.getElementById('parcoursTemps').value;
    const parcoursMaxEquipes = document.getElementById('parcoursMaxEquipes').value;
    
    summary.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <h6 class="text-primary">Énigme</h6>
                <ul class="list-unstyled small">
                    <li><strong>Type:</strong> ${enigmeType}</li>
                    <li><strong>Question:</strong> ${enigmeQuestion.substring(0, 50)}...</li>
                    <li><strong>Points:</strong> ${enigmePoints}</li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-success">Lieu</h6>
                <ul class="list-unstyled small">
                    <li><strong>Nom:</strong> ${lieuNom}</li>
                    <li><strong>Type:</strong> ${lieuType}</li>
                    <li><strong>Ordre:</strong> ${lieuOrdre}</li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-info">Parcours</h6>
                <ul class="list-unstyled small">
                    <li><strong>Nom:</strong> ${parcoursNom}</li>
                    <li><strong>Temps:</strong> ${parcoursTemps} min</li>
                    <li><strong>Max équipes:</strong> ${parcoursMaxEquipes}</li>
                </ul>
            </div>
        </div>
    `;
}

function finishWizard() {
    const confirmed = document.getElementById('confirmCreation').checked;
    if (!confirmed) {
        alert('Veuillez confirmer la création avant de continuer.');
        return;
    }
    
    // Ici vous pouvez ajouter le code pour sauvegarder en base de données
    // Pour l'instant, on affiche juste un message de succès
    
    Swal.fire({
        title: 'Création réussie !',
        text: 'Votre énigme, lieu et parcours ont été créés avec succès.',
        icon: 'success',
        confirmButtonText: 'Parfait !'
    }).then(() => {
        // Fermer la modale
        const modal = bootstrap.Modal.getInstance(document.getElementById('wizardModal'));
        modal.hide();
        
        // Réinitialiser le wizard
        resetWizard();
    });
}

function resetWizard() {
    currentStep = 1;
    document.querySelectorAll('.wizard-step').forEach(step => {
        step.classList.remove('active');
    });
    document.getElementById('step1').classList.add('active');
    updateNavigation();
    
    // Réinitialiser tous les champs
    document.querySelectorAll('input, textarea, select').forEach(field => {
        field.value = '';
        field.classList.remove('is-invalid');
    });
    
    // Remettre les valeurs par défaut
    document.getElementById('enigmeDifficulte').value = 'facile';
    document.getElementById('enigmePoints').value = '10';
    document.getElementById('enigmeTemps').value = '15';
    document.getElementById('lieuEtage').value = '0';
    document.getElementById('lieuOrdre').value = '1';
    document.getElementById('parcoursTemps').value = '120';
    document.getElementById('parcoursMaxEquipes').value = '10';
    document.getElementById('parcoursDifficulte').value = 'debutant';
    document.getElementById('confirmCreation').checked = false;
}
</script>
