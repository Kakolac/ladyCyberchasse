<?php
/**
 * Formulaire Calcul pour la création/édition d'énigmes
 */
?>
<div id="form-calcul" class="form-type-container">
    <div class="card border-info">
        <div class="card-header bg-info text-white">
            <h6><i class="fas fa-calculator"></i> Configuration Calcul</h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="question_calcul" class="form-label">Question/Énoncé</label>
                <textarea class="form-control" name="question_calcul" rows="3" placeholder="Posez votre problème mathématique..."></textarea>
            </div>
            <div class="mb-3">
                <label for="reponse_correcte_calcul" class="form-label">Réponse correcte</label>
                <input type="text" class="form-control" name="reponse_correcte_calcul" placeholder="La réponse numérique exacte">
            </div>
            <div class="mb-3">
                <label for="reponses_acceptees_calcul" class="form-label">Réponses acceptées (séparées par des virgules)</label>
                <input type="text" class="form-control" name="reponses_acceptees_calcul" 
                       placeholder="42, 42.0, quarante-deux" 
                       value="">
                <small class="text-muted">Séparez plusieurs réponses par des virgules.</small>
            </div>
            <div class="mb-3">
                <label for="indice_calcul" class="form-label">Indice (optionnel)</label>
                <textarea class="form-control" name="indice_calcul" rows="2" placeholder="Un petit indice pour aider..."></textarea>
            </div>
        </div>
    </div>
</div>
