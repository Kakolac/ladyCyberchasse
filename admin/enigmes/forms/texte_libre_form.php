<?php
/**
 * Formulaire Texte Libre pour la création/édition d'énigmes
 */
?>
<div id="form-texte-libre" class="form-type-container">
    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <h6><i class="fas fa-font"></i> Configuration Texte Libre</h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="titre_texte" class="form-label">Titre de l'énigme</label>
                <input type="text" class="form-control" name="titre_texte" placeholder="Titre descriptif de l'énigme">
            </div>
            <div class="mb-3">
                <label for="indice_texte" class="form-label">Indice</label>
                <textarea class="form-control" name="indice_texte" rows="2" placeholder="Indice pour aider le joueur..."></textarea>
            </div>
            <div class="mb-3">
                <label for="contexte_texte" class="form-label">Contexte/Description</label>
                <textarea class="form-control" name="contexte_texte" rows="4" placeholder="Décrivez l'ambiance, le lieu, la situation..."></textarea>
            </div>
            <div class="mb-3">
                <label for="question_texte" class="form-label">Question</label>
                <textarea class="form-control" name="question_texte" rows="3" placeholder="La question à laquelle le joueur doit répondre"></textarea>
            </div>
            <div class="mb-3">
                <label for="reponse_correcte_texte" class="form-label">Réponse correcte</label>
                <input type="text" class="form-control" name="reponse_correcte_texte" placeholder="La réponse exacte attendue">
            </div>
            <div class="mb-3">
                <label for="reponses_acceptees_texte" class="form-label">Réponses acceptées (séparées par des virgules)</label>
                <input type="text" class="form-control" name="reponses_acceptees_texte" 
                       placeholder="cuivre, le cuivre, CUIVRE" 
                       value="">
                <small class="text-muted">Séparez plusieurs réponses par des virgules. La première sera la réponse principale.</small>
            </div>
        </div>
    </div>
</div>
