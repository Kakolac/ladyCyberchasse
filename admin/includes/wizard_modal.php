<!-- Modal Wizard -->
<div class="modal fade" id="wizardModal" tabindex="-1" aria-labelledby="wizardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="wizardModalLabel">
                    <i class="fas fa-magic"></i> Assistant de Configuration
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Étapes du wizard -->
                <div class="wizard-steps">
                    <!-- Étape 1: Configuration initiale -->
                    <div class="wizard-step active" id="step1">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-play-circle"></i> Étape 1: Configuration Initiale
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom de l'événement</label>
                                <input type="text" class="form-control" id="eventName" placeholder="Cyberchasse 2024">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de l'événement</label>
                                <input type="date" class="form-control" id="eventDate">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="eventDescription" rows="3" placeholder="Description de votre cyberchasse..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 2: Création des équipes -->
                    <div class="wizard-step" id="step2" style="display: none;">
                        <h6 class="text-success mb-3">
                            <i class="fas fa-users"></i> Étape 2: Création des Équipes
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre d'équipes</label>
                                <input type="number" class="form-control" id="teamCount" min="1" max="20" value="4">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Membres par équipe</label>
                                <input type="number" class="form-control" id="membersPerTeam" min="1" max="6" value="4">
                            </div>
                            <div class="col-12">
                                <div id="teamsPreview" class="border rounded p-3 bg-light">
                                    <small class="text-muted">Aperçu des équipes sera généré ici...</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 3: Configuration du parcours -->
                    <div class="wizard-step" id="step3" style="display: none;">
                        <h6 class="text-info mb-3">
                            <i class="fas fa-route"></i> Étape 3: Configuration du Parcours
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Temps limite (minutes)</label>
                                <input type="number" class="form-control" id="timeLimit" min="30" max="300" value="120">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre de lieux</label>
                                <input type="number" class="form-control" id="locationCount" min="3" max="15" value="8">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Type de parcours</label>
                                <select class="form-select" id="parcoursType">
                                    <option value="linear">Linéaire (ordre fixe)</option>
                                    <option value="free">Libre (ordre libre)</option>
                                    <option value="mixed">Mixte</option>
                                </select>
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
                            <strong>Récapitulatif de votre configuration :</strong>
                            <div id="wizardSummary" class="mt-2">
                                <!-- Le récapitulatif sera généré ici -->
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="confirmConfig">
                            <label class="form-check-label" for="confirmConfig">
                                Je confirme cette configuration et je veux l'appliquer
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
                    <i class="fas fa-check"></i> Terminer
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

#teamsPreview {
    min-height: 60px;
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
    
    // Écouteurs pour les champs dynamiques
    const teamCount = document.getElementById('teamCount');
    if (teamCount) teamCount.addEventListener('change', updateTeamsPreview);
    
    const timeLimit = document.getElementById('timeLimit');
    if (timeLimit) timeLimit.addEventListener('change', updateSummary);
});

function nextStep() {
    if (currentStep < totalSteps) {
        document.getElementById(`step${currentStep}`).classList.remove('active');
        currentStep++;
        document.getElementById(`step${currentStep}`).classList.add('active');
        updateNavigation();
        
        if (currentStep === 2) {
            updateTeamsPreview();
        } else if (currentStep === 4) {
            updateSummary();
        }
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

function updateTeamsPreview() {
    const teamCount = document.getElementById('teamCount').value;
    const membersPerTeam = document.getElementById('membersPerTeam').value;
    const preview = document.getElementById('teamsPreview');
    
    let html = '<div class="row">';
    for (let i = 1; i <= teamCount; i++) {
        html += `
            <div class="col-md-6 mb-2">
                <div class="border rounded p-2 bg-white">
                    <strong>Équipe ${i}</strong> (${membersPerTeam} membres)
                </div>
            </div>
        `;
    }
    html += '</div>';
    
    preview.innerHTML = html;
}

function updateSummary() {
    const summary = document.getElementById('wizardSummary');
    const eventName = document.getElementById('eventName').value || 'Non défini';
    const eventDate = document.getElementById('eventDate').value || 'Non défini';
    const teamCount = document.getElementById('teamCount').value || '0';
    const timeLimit = document.getElementById('timeLimit').value || '0';
    const locationCount = document.getElementById('locationCount').value || '0';
    const parcoursType = document.getElementById('parcoursType').value;
    
    const parcoursTypeText = {
        'linear': 'Linéaire (ordre fixe)',
        'free': 'Libre (ordre libre)',
        'mixed': 'Mixte'
    };
    
    summary.innerHTML = `
        <ul class="list-unstyled mb-0">
            <li><strong>Événement:</strong> ${eventName}</li>
            <li><strong>Date:</strong> ${eventDate}</li>
            <li><strong>Équipes:</strong> ${teamCount}</li>
            <li><strong>Temps limite:</strong> ${timeLimit} minutes</li>
            <li><strong>Lieux:</strong> ${locationCount}</li>
            <li><strong>Type de parcours:</strong> ${parcoursTypeText[parcoursType]}</li>
        </ul>
    `;
}

function finishWizard() {
    const confirmed = document.getElementById('confirmConfig').checked;
    if (!confirmed) {
        alert('Veuillez confirmer la configuration avant de continuer.');
        return;
    }
    
    // Ici vous pouvez ajouter le code pour sauvegarder la configuration
    alert('Configuration sauvegardée avec succès !');
    
    // Fermer la modale
    const modal = bootstrap.Modal.getInstance(document.getElementById('wizardModal'));
    modal.hide();
    
    // Réinitialiser le wizard
    resetWizard();
}

function resetWizard() {
    currentStep = 1;
    document.querySelectorAll('.wizard-step').forEach(step => {
        step.classList.remove('active');
    });
    document.getElementById('step1').classList.add('active');
    updateNavigation();
    
    // Réinitialiser les champs
    document.getElementById('eventName').value = '';
    document.getElementById('eventDate').value = '';
    document.getElementById('teamCount').value = '4';
    document.getElementById('membersPerTeam').value = '4';
    document.getElementById('timeLimit').value = '120';
    document.getElementById('locationCount').value = '8';
    document.getElementById('parcoursType').value = 'linear';
    document.getElementById('confirmConfig').checked = false;
}
</script>
