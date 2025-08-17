<?php
/**
 * Formulaire Image pour la création/édition d'énigmes
 */
?>
<div id="form-image" class="form-type-container">
    <div class="card border-warning">
        <div class="card-header bg-warning text-dark">
            <h6><i class="fas fa-image"></i> Configuration Image</h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="question_image" class="form-label">Question</label>
                <textarea class="form-control" name="question_image" rows="3" placeholder="Que doit observer le joueur dans l'image ?"></textarea>
            </div>
            <div class="mb-3">
                <label for="reponse_correcte_image" class="form-label">Réponse correcte</label>
                <input type="text" class="form-control" name="reponse_correcte_image" placeholder="La réponse exacte attendue">
            </div>
            <div class="mb-3">
                <label for="reponses_acceptees_image" class="form-label">Réponses acceptées (séparées par des virgules)</label>
                <input type="text" class="form-control" name="reponses_acceptees_image" 
                       placeholder="chat, le chat, CHAT" 
                       value="">
                <small class="text-muted">Séparez plusieurs réponses par des virgules.</small>
            </div>
            <div class="mb-3">
                <label for="indice_image" class="form-label">Indice (optionnel)</label>
                <textarea class="form-control" name="indice_image" rows="2" placeholder="Un indice pour guider l'observation..."></textarea>
            </div>
            <div class="mb-3">
                <label for="url_image" class="form-label">URL de l'image (optionnel)</label>
                <input type="url" class="form-control" name="url_image" placeholder="https://exemple.com/image.jpg">
                <small class="text-muted">Laissez vide si l'image sera ajoutée manuellement.</small>
            </div>
        </div>
    </div>
</div>
