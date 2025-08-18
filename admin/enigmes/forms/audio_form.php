<?php
/**
 * Formulaire Audio pour la création/édition d'énigmes
 */
?>
<div id="form-audio" class="form-type-container">
    <div class="card border-warning">
        <div class="card-header bg-warning text-dark">
            <h6><i class="fas fa-music"></i> Configuration Audio</h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="question_audio" class="form-label">Question</label>
                <textarea class="form-control" name="question_audio" rows="3" placeholder="Posez votre question basée sur l'audio..." required></textarea>
            </div>
            
            <div class="mb-3">
                <label for="audio_file" class="form-label">Fichier Audio</label>
                <input type="file" class="form-control" name="audio_file" id="audio_file" accept=".mp3,.wav,.ogg">
                <small class="text-muted">Formats acceptés : MP3, WAV, OGG. Taille max : 10MB</small>
            </div>
            
            <div class="mb-3">
                <label for="audio_url" class="form-label">URL Audio (alternative)</label>
                <input type="url" class="form-control" name="audio_url" placeholder="https://exemple.com/audio.mp3">
                <small class="text-muted">Laissez vide si vous uploadez un fichier</small>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="reponse_correcte_audio" class="form-label">Réponse correcte</label>
                        <input type="text" class="form-control" name="reponse_correcte_audio" placeholder="Réponse exacte attendue" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="reponses_acceptees_audio" class="form-label">Réponses acceptées</label>
                        <input type="text" class="form-control" name="reponses_acceptees_audio" placeholder="Réponses séparées par des virgules">
                        <small class="text-muted">Ex: réponse1, réponse2, réponse3</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="indice_audio" class="form-label">Indice (optionnel)</label>
                <textarea class="form-control" name="indice_audio" rows="2" placeholder="Un indice pour aider les joueurs..."></textarea>
            </div>
            
            <div class="mb-3">
                <label for="contexte_audio" class="form-label">Contexte/Description</label>
                <textarea class="form-control" name="contexte_audio" rows="3" placeholder="Description du contexte de l'énigme..."></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="autoplay_audio" id="autoplay_audio" value="1">
                        <label class="form-check-label" for="autoplay_audio">
                            Lecture automatique
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="loop_audio" id="loop_audio" value="1">
                        <label class="form-check-label" for="loop_audio">
                            Lecture en boucle
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="volume_control_audio" id="volume_control_audio" value="1" checked>
                        <label class="form-check-label" for="volume_control_audio">
                            Contrôle du volume
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
