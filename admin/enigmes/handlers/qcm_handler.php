<?php
/**
 * Gestionnaire pour les énigmes de type QCM
 */
class QCMHandler {
    
    /**
     * Génère le JSON pour une énigme QCM
     */
    public static function generateJSON($post_data) {
        return json_encode([
            'question' => $post_data['question_qcm'],
            'options' => [
                'A' => $post_data['option_a'],
                'B' => $post_data['option_b'],
                'C' => $post_data['option_c'],
                'D' => $post_data['option_d']
            ],
            'reponse_correcte' => $post_data['reponse_correcte_qcm']
        ]);
    }
    
    /**
     * Valide les données d'une énigme QCM
     */
    public static function validate($post_data) {
        $errors = [];
        
        if (empty($post_data['question_qcm'])) {
            $errors[] = "La question est obligatoire";
        }
        
        if (empty($post_data['option_a']) || empty($post_data['option_b']) || 
            empty($post_data['option_c']) || empty($post_data['option_d'])) {
            $errors[] = "Toutes les options sont obligatoires";
        }
        
        if (empty($post_data['reponse_correcte_qcm'])) {
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
            $form_data[$prefix . 'question_qcm'] = $donnees['question'];
        }
        
        if (isset($donnees['options'])) {
            $form_data[$prefix . 'option_a'] = $donnees['options']['A'] ?? '';
            $form_data[$prefix . 'option_b'] = $donnees['options']['B'] ?? '';
            $form_data[$prefix . 'option_c'] = $donnees['options']['C'] ?? '';
            $form_data[$prefix . 'option_d'] = $donnees['options']['D'] ?? '';
        }
        
        if (isset($donnees['reponse_correcte'])) {
            $form_data[$prefix . 'reponse_correcte_qcm'] = $donnees['reponse_correcte'];
        }
        
        return $form_data;
    }
}
