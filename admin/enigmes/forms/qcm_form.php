<?php
/**
 * Formulaire QCM pour la création/édition d'énigmes
 */
?>
<div id="form-qcm" class="form-type-container">
    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            <h6><i class="fas fa-list-check"></i> Configuration QCM</h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="question_qcm" class="form-label">Question</label>
                <textarea class="form-control" name="question_qcm" rows="3" placeholder="Posez votre question ici..."></textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="option_a" class="form-label">Option A</label>
                        <input type="text" class="form-control" name="option_a" placeholder="Première option">
                    </div>
                    <div class="mb-3">
                        <label for="option_b" class="form-label">Option B</label>
                        <input type="text" class="form-control" name="option_b" placeholder="Deuxième option">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="option_c" class="form-label">Option C</label>
                        <input type="text" class="form-control" name="option_c" placeholder="Troisième option">
                    </div>
                    <div class="mb-3">
                        <label for="option_d" class="form-label">Option D</label>
                        <input type="text" class="form-control" name="option_d" placeholder="Quatrième option">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="reponse_correcte_qcm" class="form-label">Réponse correcte</label>
                <select class="form-select" name="reponse_correcte_qcm">
                    <option value="">Sélectionner la bonne réponse</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
        </div>
    </div>
</div>
