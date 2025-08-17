/**
 * Gestion des formulaires dynamiques pour les énigmes
 */
class EnigmeFormManager {
    
    constructor() {
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.hideAllForms();
        
        // Debug : vérifier que la classe est bien initialisée
        console.log('EnigmeFormManager initialisé');
    }
    
    bindEvents() {
        // Gestion du changement de type pour la création
        const createTypeSelect = document.getElementById('type_enigme_id');
        if (createTypeSelect) {
            createTypeSelect.addEventListener('change', () => {
                console.log('Type d\'énigme changé:', createTypeSelect.value);
                this.showFormType('create');
            });
        } else {
            console.error('Select type_enigme_id non trouvé');
        }
        
        // Gestion du changement de type pour l'édition
        const editTypeSelect = document.getElementById('editTypeEnigmeId');
        if (editTypeSelect) {
            editTypeSelect.addEventListener('change', () => {
                console.log('Type d\'énigme édition changé:', editTypeSelect.value);
                this.showFormType('edit');
            });
        } else {
            console.error('Select editTypeEnigmeId non trouvé');
        }
        
        // Validation du formulaire avant soumission
        const createForm = document.querySelector('#createEnigmeModal form');
        if (createForm) {
            createForm.addEventListener('submit', (e) => {
                if (!this.validateFormBeforeSubmit()) {
                    e.preventDefault();
                    console.log('Validation échouée, soumission bloquée');
                }
            });
        }
    }
    
    /**
     * Valide le formulaire avant soumission
     */
    validateFormBeforeSubmit() {
        const typeSelect = document.getElementById('type_enigme_id');
        const selectedType = typeSelect.value;
        const titre = document.getElementById('titre').value.trim();
        
        if (!selectedType) {
            alert('Veuillez sélectionner un type d\'énigme');
            return false;
        }
        
        if (!titre) {
            alert('Veuillez saisir un titre pour l\'énigme');
            return false;
        }
        
        // Validation spécifique selon le type
        switch (selectedType) {
            case '1': // QCM
                return this.validateQCMForm();
            case '2': // Texte Libre
                return this.validateTexteLibreForm();
            case '3': // Calcul
                return this.validateCalculForm();
            case '4': // Image
                return this.validateImageForm();
            default:
                return true;
        }
    }
    
    /**
     * Valide le formulaire QCM
     */
    validateQCMForm() {
        const question = document.querySelector('[name="question_qcm"]').value.trim();
        const optionA = document.querySelector('[name="option_a"]').value.trim();
        const optionB = document.querySelector('[name="option_b"]').value.trim();
        const optionC = document.querySelector('[name="option_c"]').value.trim();
        const optionD = document.querySelector('[name="option_d"]').value.trim();
        const reponse = document.querySelector('[name="reponse_correcte_qcm"]').value;
        
        if (!question) {
            alert('Veuillez saisir une question pour le QCM');
            return false;
        }
        
        if (!optionA || !optionB || !optionC || !optionD) {
            alert('Veuillez remplir toutes les options du QCM');
            return false;
        }
        
        if (!reponse) {
            alert('Veuillez sélectionner la réponse correcte');
            return false;
        }
        
        return true;
    }
    
    /**
     * Valide le formulaire Texte Libre
     */
    validateTexteLibreForm() {
        const titre = document.querySelector('[name="titre_texte"]').value.trim();
        const question = document.querySelector('[name="question_texte"]').value.trim();
        const reponse = document.querySelector('[name="reponse_correcte_texte"]').value.trim();
        
        if (!titre) {
            alert('Veuillez saisir un titre pour l\'énigme texte libre');
            return false;
        }
        
        if (!question) {
            alert('Veuillez saisir une question pour l\'énigme texte libre');
            return false;
        }
        
        if (!reponse) {
            alert('Veuillez saisir la réponse correcte');
            return false;
        }
        
        return true;
    }
    
    /**
     * Valide le formulaire Calcul
     */
    validateCalculForm() {
        const question = document.querySelector('[name="question_calcul"]').value.trim();
        const reponse = document.querySelector('[name="reponse_correcte_calcul"]').value.trim();
        
        if (!question) {
            alert('Veuillez saisir une question pour l\'énigme calcul');
            return false;
        }
        
        if (!reponse) {
            alert('Veuillez saisir la réponse correcte');
            return false;
        }
        
        return true;
    }
    
    /**
     * Valide le formulaire Image
     */
    validateImageForm() {
        const question = document.querySelector('[name="question_image"]').value.trim();
        const reponse = document.querySelector('[name="reponse_correcte_image"]').value.trim();
        
        if (!question) {
            alert('Veuillez saisir une question pour l\'énigme image');
            return false;
        }
        
        if (!reponse) {
            alert('Veuillez saisir la réponse correcte');
            return false;
        }
        
        return true;
    }
    
    /**
     * Affiche le formulaire correspondant au type sélectionné
     */
    showFormType(mode = 'create') {
        const prefix = mode === 'create' ? 'form-' : 'edit-form-';
        const typeSelect = document.getElementById(mode === 'create' ? 'type_enigme_id' : 'editTypeEnigmeId');
        const selectedType = typeSelect.value;
        
        // Masquer tous les formulaires
        this.hideAllForms(mode);
        
        // Désactiver la validation HTML sur tous les champs cachés
        this.disableValidationOnHiddenForms(mode);
        
        // Afficher le formulaire correspondant au type sélectionné
        if (selectedType) {
            const formId = this.getFormIdByType(selectedType, mode);
            if (formId) {
                const formElement = document.getElementById(formId);
                if (formElement) {
                    formElement.classList.add('active');
                    // Réactiver la validation sur le formulaire visible
                    this.enableValidationOnVisibleForm(formId);
                }
            }
        }
    }
    
    /**
     * Masque tous les formulaires
     */
    hideAllForms(mode = 'create') {
        const prefix = mode === 'create' ? 'form-' : 'edit-form-';
        const forms = document.querySelectorAll(`[id^="${prefix}"]`);
        forms.forEach(form => {
            form.classList.remove('active');
        });
    }
    
    /**
     * Désactive la validation HTML sur les formulaires cachés
     */
    disableValidationOnHiddenForms(mode = 'create') {
        const prefix = mode === 'create' ? 'form-' : 'edit-form-';
        const forms = document.querySelectorAll(`[id^="${prefix}"]`);
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.removeAttribute('required');
                input.removeAttribute('min');
                input.removeAttribute('max');
                input.removeAttribute('pattern');
            });
        });
    }
    
    /**
     * Réactive la validation HTML sur le formulaire visible
     */
    enableValidationOnVisibleForm(formId) {
        const form = document.getElementById(formId);
        if (form) {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                // Remettre les attributs de validation selon le type de champ
                if (input.name && input.name.includes('question') || 
                    input.name && input.name.includes('reponse_correcte') ||
                    input.name && input.name.includes('titre')) {
                    input.setAttribute('required', 'required');
                }
            });
        }
    }
    
    /**
     * Obtenir l'ID du formulaire selon le type
     */
    getFormIdByType(typeId, mode = 'create') {
        const prefix = mode === 'create' ? 'form-' : 'edit-form-';
        const formMap = {
            '1': `${prefix}qcm`,
            '2': `${prefix}texte-libre`,
            '3': `${prefix}calcul`,
            '4': `${prefix}image`
        };
        return formMap[typeId];
    }
    
    /**
     * Remplit le formulaire d'édition avec les données existantes
     */
    fillEditFormData(donneesJson, typeId) {
        try {
            const donnees = JSON.parse(donneesJson);
            const formData = this.getFormDataByType(donnees, typeId);
            
            // Remplir les champs du formulaire
            Object.keys(formData).forEach(fieldId => {
                const element = document.getElementById(fieldId);
                if (element) {
                    element.value = formData[fieldId];
                }
            });
            
        } catch (e) {
            console.error('Erreur lors du parsing des données:', e);
        }
    }
    
    /**
     * Obtient les données du formulaire selon le type
     */
    getFormDataByType(donnees, typeId) {
        switch (typeId) {
            case '1': // QCM
                return {
                    'edit_question_qcm': donnees.question || '',
                    'edit_option_a': donnees.options?.A || '',
                    'edit_option_b': donnees.options?.B || '',
                    'edit_option_c': donnees.options?.C || '',
                    'edit_option_d': donnees.options?.D || '',
                    'edit_reponse_correcte_qcm': donnees.reponse_correcte || ''
                };
                
            case '2': // Texte Libre
                return {
                    'edit_titre_texte': donnees.titre || '',
                    'edit_indice_texte': donnees.indice || '',
                    'edit_contexte_texte': donnees.contexte || '',
                    'edit_question_texte': donnees.question || '',
                    'edit_reponse_correcte_texte': donnees.reponse_correcte || '',
                    'edit_reponses_acceptees_texte': donnees.reponses_acceptees ? donnees.reponses_acceptees.join(', ') : ''
                };
                
            case '3': // Calcul
                return {
                    'edit_question_calcul': donnees.question || '',
                    'edit_reponse_correcte_calcul': donnees.reponse_correcte || '',
                    'edit_reponses_acceptees_calcul': donnees.reponses_acceptees ? donnees.reponses_acceptees.join(', ') : '',
                    'edit_indice_calcul': donnees.indice || ''
                };
                
            case '4': // Image
                return {
                    'edit_question_image': donnees.question || '',
                    'edit_reponse_correcte_image': donnees.reponse_correcte || '',
                    'edit_reponses_acceptees_image': donnees.reponses_acceptees ? donnees.reponses_acceptees.join(', ') : '',
                    'edit_indice_image': donnees.indice || '',
                    'edit_url_image': donnees.url_image || ''
                };
                
            default:
                return {};
        }
    }
}

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    window.enigmeFormManager = new EnigmeFormManager();
});
