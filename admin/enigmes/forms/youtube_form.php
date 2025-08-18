<?php
/**
 * Formulaire YouTube pour la création/édition d'énigmes
 */
?>
<div id="form-youtube" class="form-type-container">
    <div class="card border-danger">
        <div class="card-header bg-danger text-white">
            <h6><i class="fab fa-youtube"></i> Configuration YouTube</h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="question_youtube" class="form-label">Question</label>
                <textarea class="form-control" name="question_youtube" rows="3" placeholder="Posez votre question basée sur la vidéo..."></textarea>
            </div>
            
            <div class="mb-3">
                <label for="youtube_url" class="form-label">URL YouTube <span class="text-danger">*</span></label>
                <input type="url" class="form-control" name="youtube_url" id="youtube_url" 
                       placeholder="https://www.youtube.com/watch?v=..." required>
                <small class="text-muted">Collez l'URL complète de la vidéo YouTube</small>
            </div>
            
            <div class="mb-3">
                <label for="reponse_correcte_youtube" class="form-label">Réponse correcte <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="reponse_correcte_youtube" 
                       placeholder="La réponse exacte attendue" required>
            </div>
            
            <div class="mb-3">
                <label for="reponses_acceptees_youtube" class="form-label">Réponses acceptées</label>
                <input type="text" class="form-control" name="reponses_acceptees_youtube" 
                       placeholder="reponse1, reponse2, reponse3">
                <small class="text-muted">Séparez plusieurs réponses par des virgules. La première sera la réponse principale.</small>
            </div>
            
            <div class="mb-3">
                <label for="indice_youtube" class="form-label">Indice</label>
                <textarea class="form-control" name="indice_youtube" rows="2" placeholder="Indice pour aider le joueur..."></textarea>
            </div>
            
            <div class="mb-3">
                <label for="contexte_youtube" class="form-label">Contexte/Description</label>
                <textarea class="form-control" name="contexte_youtube" rows="3" placeholder="Décrivez le contexte de la vidéo..."></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="autoplay_youtube" id="autoplay_youtube" value="1">
                        <label class="form-check-label" for="autoplay_youtube">
                            Lecture automatique
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="loop_youtube" id="loop_youtube" value="1">
                        <label class="form-check-label" for="loop_youtube">
                            Lecture en boucle
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="show_controls_youtube" id="show_controls_youtube" value="1" checked>
                        <label class="form-check-label" for="show_controls_youtube">
                            Afficher les contrôles
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
