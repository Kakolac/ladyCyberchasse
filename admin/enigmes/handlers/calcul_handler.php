<?php
/**
 * Gestionnaire pour les énigmes de type Calcul
 */
class CalculHandler {
    
    /**
     * Génère le JSON pour une énigme Calcul
     */
    public static function generateJSON($post_data) {
        return json_encode([
            'question' => $post_data['question_calcul'],
            'reponse_correcte' => $post_data['reponse_correcte_calcul'],
            'reponses_acceptees' => array_filter(explode(',', $post_data['reponses_acceptees_calcul'])),
            'indice' => $post_data['indice_calcul'] ?? ''
        ]);
    }
    
    /**
     * Valide les données d'une énigme Calcul
     */
    public static function validate($post_data) {
        $errors = [];
        
        if (empty($post_data['question_calcul'])) {
            $errors[] = "La question est obligatoire";
        }
        
        if (empty($post_data['reponse_correcte_calcul'])) {
            $errors[] = "La réponse correcte est obligatoire";
        }
        
        return $errors;
    }
    
    /**
     * Remplit le formulaire d'édition avec les données existantes
     */
    public static function fillEditForm($donnees, $prefix = 'edit_') {
        $form_data = [];
        
        if (isset($donnees['question'])) {
            $form_data[$prefix . 'question_calcul'] = $donnees['question'];
        }
        
        if (isset($donnees['reponse_correcte'])) {
            $form_data[$prefix . 'reponse_correcte_calcul'] = $donnees['reponse_correcte'];
        }
        
        if (isset($donnees['reponses_acceptees'])) {
            $form_data[$prefix . 'reponses_acceptees_calcul'] = implode(', ', $donnees['reponses_acceptees']);
        }
        
        if (isset($donnees['indice'])) {
            $form_data[$prefix . 'indice_calcul'] = $donnees['indice'];
        }
        
        return $form_data;
    }
}
