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
        
        // Validation du formulaire avant soumission ET traitement AJAX
        const createForm = document.querySelector('#createEnigmeModal form');
        if (createForm) {
            createForm.addEventListener('submit', (e) => {
                e.preventDefault(); // Empêcher la soumission normale
                console.log('🔄 Formulaire soumis - Traitement AJAX');
                
                if (this.validateFormBeforeSubmit()) {
                    this.submitFormAjax(createForm);
                } else {
                    console.log('❌ Validation échouée, soumission bloquée');
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
        
        console.log('Validation du formulaire :');
        console.log('- Type sélectionné:', selectedType);
        console.log('- Titre:', titre);
        
        if (!selectedType) {
            this.showErrorMessage('Veuillez sélectionner un type d\'énigme');
            typeSelect.focus();
            return false;
        }
        
        if (!titre) {
            this.showErrorMessage('Veuillez saisir un titre pour l\'énigme');
            document.getElementById('titre').focus();
            return false;
        }
        
        // Ne valider que le formulaire actif selon le type sélectionné
        let isValid = true;
        let validationMessage = '';
        
        switch (selectedType) {
            case '1': // QCM
                isValid = this.validateQCMForm();
                validationMessage = 'QCM';
                break;
            case '2': // Texte Libre
                isValid = this.validateTexteLibreForm();
                validationMessage = 'Texte Libre';
                break;
            case '3': // Calcul
                isValid = this.validateCalculForm();
                validationMessage = 'Calcul';
                break;
            case '4': // Image
                isValid = this.validateImageForm();
                validationMessage = 'Image';
                break;
            case '5': // Audio
                isValid = this.validateAudioForm();
                validationMessage = 'Audio';
                break;
            default:
                isValid = true;
                validationMessage = 'Type inconnu';
        }
        
        console.log(`Validation ${validationMessage}: ${isValid ? 'OK' : 'ÉCHEC'}`);
        return isValid;
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
     * Valide le formulaire Audio
     */
    validateAudioForm() {
        const question = document.querySelector('[name="question_audio"]').value.trim();
        const reponseCorrecte = document.querySelector('[name="reponse_correcte_audio"]').value.trim();
        const audioFile = document.querySelector('[name="audio_file"]').files[0];
        const audioUrl = document.querySelector('[name="audio_url"]').value.trim();
        
        console.log('Validation audio - Question:', question);
        console.log('Validation audio - Réponse:', reponseCorrecte);
        console.log('Validation audio - Fichier:', audioFile);
        console.log('Validation audio - URL:', audioUrl);
        
        if (!question) {
            alert('Veuillez saisir une question pour l\'énigme audio');
            return false;
        }
        
        if (!reponseCorrecte) {
            alert('Veuillez saisir la réponse correcte');
            return false;
        }
        
        // Vérifier qu'au moins un audio est fourni
        if (!audioFile && !audioUrl) {
            alert('Veuillez fournir un fichier audio OU une URL audio');
            return false;
        }
        
        // Validation du fichier audio si uploadé
        if (audioFile) {
            const allowedTypes = ['audio/mpeg', 'audio/wav', 'audio/ogg'];
            if (!allowedTypes.includes(audioFile.type)) {
                alert('Format de fichier non supporté. Utilisez MP3, WAV ou OGG');
                return false;
            }
            
            if (audioFile.size > 10 * 1024 * 1024) { // 10MB
                alert('Le fichier audio est trop volumineux (max 10MB)');
                return false;
            }
        }
        
        console.log('✅ Validation audio réussie');
        return true;
    }
    
    /**
     * Soumet le formulaire en AJAX
     */
    submitFormAjax(form) {
        const formData = new FormData(form);
        
        // Debug des données envoyées
        console.log('Données du formulaire :');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        // Correction de l'URL - utiliser une URL fixe ou l'URL relative correcte
        const submitUrl = '/admin/enigmes.php';  // URL fixe
        
        console.log('Soumission vers:', submitUrl); // Debug de l'URL
        
        fetch(submitUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Status de la réponse:', response.status);
            console.log('Headers:', response.headers);
            return response.text();
        })
        .then(data => {
            console.log('Réponse complète:', data);
            
            // Vérifier si la réponse contient un message de succès
            if (data.includes('success_message') || data.includes('Énigme créée avec succès')) {
                this.showSuccessMessage('Énigme créée avec succès !');
                // Recharger la page après un court délai
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                // Si la réponse contient une erreur, l'afficher
                const errorMatch = data.match(/error_message">(.*?)</);
                if (errorMatch) {
                    this.showErrorMessage(errorMatch[1]);
                } else {
                    this.showErrorMessage('Erreur lors de la création de l\'énigme');
                    console.error('Réponse inattendue:', data);
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors de la soumission:', error);
            this.showErrorMessage(`Erreur lors de la soumission: ${error.message}`);
        });
    }
    
    /**
     * Ajoute un message de debug au panel
     */
    addDebugMessage(message) {
        const timestamp = new Date().toLocaleTimeString();
        const debugMessage = `${timestamp} - ${message}`;
        
        // Ajouter à la console
        console.log(debugMessage);
        
        // Ajouter au debug panel s'il existe
        const debugPanel = document.querySelector('.alert-info');
        if (debugPanel) {
            const debugContent = debugPanel.querySelector('div[style*="font-family: monospace"]');
            if (debugContent) {
                const messageDiv = document.createElement('div');
                messageDiv.textContent = debugMessage;
                debugContent.appendChild(messageDiv);
                
                // Garder seulement les 20 derniers messages
                const messages = debugContent.querySelectorAll('div');
                if (messages.length > 20) {
                    for (let i = 0; i < messages.length - 20; i++) {
                        messages[i].remove();
                    }
                }
                
                // Faire défiler vers le bas
                debugContent.scrollTop = debugContent.scrollHeight;
            }
        }
    }
    
    /**
     * Affiche le bon formulaire selon le type sélectionné
     */
    showFormType(mode = 'create') {
        const typeSelect = document.getElementById(mode === 'create' ? 'type_enigme_id' : 'editTypeEnigmeId');
        const selectedType = typeSelect.value;
        
        console.log('Type sélectionné:', selectedType); // Debug
        
        // Masquer tous les formulaires
        this.hideAllForms(mode);
        
        // Afficher le bon formulaire
        switch (selectedType) {
            case '1': // QCM
                this.showForm('qcm', mode);
                break;
            case '2': // Texte Libre
                this.showForm('texte-libre', mode);
                break;
            case '3': // Calcul
                this.showForm('calcul', mode);
                break;
            case '4': // Image
                this.showForm('image', mode);
                break;
            case '5': // Audio
                console.log('Affichage du formulaire audio'); // Debug
                this.showForm('audio', mode);
                break;
            default:
                console.log('Type non reconnu:', selectedType); // Debug
        }
    }
    
    /**
     * Masque tous les formulaires
     */
    hideAllForms(mode = 'create') {
        const prefix = mode === 'create' ? 'form-' : 'edit-form-';
        const forms = document.querySelectorAll(`[id^="${prefix}"]`);
        forms.forEach(form => {
            form.style.display = 'none';
            // Désactiver les champs required des formulaires cachés
            form.querySelectorAll('[required]').forEach(field => {
                field.removeAttribute('required');
                // Stocker l'information que le champ était required
                field.dataset.wasRequired = 'true';
            });
        });
    }
    
    /**
     * Affiche un formulaire spécifique
     */
    showForm(formType, mode = 'create') {
        const prefix = mode === 'create' ? 'form-' : 'edit-form-';
        const formId = `${prefix}${formType}`;
        const form = document.getElementById(formId);
        
        if (form) {
            form.style.display = 'block';
            // Réactiver les champs qui étaient required
            form.querySelectorAll('[data-was-required="true"]').forEach(field => {
                field.setAttribute('required', '');
            });
            
            console.log(`✅ Affichage du formulaire: ${formId}`);
            console.log('Style display:', form.style.display);
            console.log('Form visible:', form.offsetHeight > 0);
        } else {
            console.error(`❌ Formulaire non trouvé: ${formId}`);
            
            // Lister tous les formulaires disponibles
            const allForms = document.querySelectorAll('[id^="form-"]');
            console.log('Formulaires disponibles:', Array.from(allForms).map(f => f.id));
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
            '4': `${prefix}image`,
            '5': `${prefix}audio`
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
                
            case '5': // Audio
                return {
                    'edit_question_audio': donnees.question || '',
                    'edit_reponse_correcte_audio': donnees.reponse_correcte || '',
                    'edit_reponses_acceptees_audio': donnees.reponses_acceptees ? donnees.reponses_acceptees.join(', ') : '',
                    'edit_indice_audio': donnees.indice || '',
                    'edit_contexte_audio': donnees.contexte || '',
                    'edit_audio_url': donnees.audio_url || '',
                    'edit_autoplay_audio': donnees.autoplay ? '1' : '',
                    'edit_loop_audio': donnees.loop ? '1' : '',
                    'edit_volume_control_audio': donnees.volume_control ? '1' : ''
                };
                
            default:
                return {};
        }
    }

    /**
     * Affiche un message de succès
     */
    showSuccessMessage(message) {
        // Créer une notification visible
        const notification = document.createElement('div');
        notification.className = 'alert alert-success position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <strong>✅ Succès !</strong> ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Supprimer automatiquement après 5 secondes
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
    
    /**
     * Affiche un message d'erreur
     */
    showErrorMessage(message) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-danger position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <strong>❌ Erreur !</strong> ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
    
    /**
     * Affiche un message d'information
     */
    showInfoMessage(message) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-info position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <strong>ℹ️ Information</strong> ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    /**
     * Test direct de la requête POST
     */
    testDirectRequest() {
        this.addDebugMessage('🧪 Test de requête POST directe...');
        
        // Correction de l'URL - utiliser l'URL absolue
        const targetUrl = window.location.origin + '/admin/enigmes.php';
        this.addDebugMessage(`🎯 URL cible: ${targetUrl}`);
        
        fetch(targetUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=create_enigme&type_enigme_id=5&titre=test'
        }).then(r => {
            this.addDebugMessage('Réponse directe reçue: ' + r.status + ' ' + r.statusText);
            return r.text();
        }).then(data => {
            this.addDebugMessage('Contenu direct reçu: ' + data.substring(0, 200));
        }).catch(err => {
            this.addDebugMessage('Erreur directe: ' + err.message);
        });
    }

    /**
     * Test avec XMLHttpRequest
     */
    testXHR() {
        this.addDebugMessage('🧪 Test avec XMLHttpRequest...');
        
        // Correction de l'URL - utiliser l'URL absolue
        const targetUrl = window.location.origin + '/admin/enigmes.php';
        this.addDebugMessage(`🎯 URL cible: ${targetUrl}`);
        
        const xhr = new XMLHttpRequest();
        xhr.open('POST', targetUrl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                this.addDebugMessage('📥 XHR Status: ' + xhr.status);
                this.addDebugMessage('📥 XHR Response: ' + xhr.responseText.substring(0, 200));
            }
        };
        
        xhr.send('action=create_enigme&type_enigme_id=5&titre=test');
    }
}

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    window.enigmeFormManager = new EnigmeFormManager();
});
