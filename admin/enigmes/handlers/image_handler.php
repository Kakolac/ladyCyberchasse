<?php
/**
 * Gestionnaire pour les énigmes de type Image
 */
class ImageHandler {
    
    /**
     * Génère le JSON pour une énigme Image
     */
    public static function generateJSON($post_data) {
        return json_encode([
            'question' => $post_data['question_image'],
            'reponse_correcte' => $post_data['reponse_correcte_image'],
            'reponses_acceptees' => array_filter(explode(',', $post_data['reponses_acceptees_image'])),
            'indice' => $post_data['indice_image'] ?? '',
            'url_image' => $post_data['url_image'] ?? ''
        ]);
    }
    
    /**
     * Valide les données d'une énigme Image
     */
    public static function validate($post_data) {
        $errors = [];
        
        if (empty($post_data['question_image'])) {
            $errors[] = "La question est obligatoire";
        }
        
        if (empty($post_data['reponse_correcte_image'])) {
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
            $form_data[$prefix . 'question_image'] = $donnees['question'];
        }
        
        if (isset($donnees['reponse_correcte'])) {
            $form_data[$prefix . 'reponse_correcte_image'] = $donnees['reponse_correcte'];
        }
        
        if (isset($donnees['reponses_acceptees'])) {
            $form_data[$prefix . 'reponses_acceptees_image'] = implode(', ', $donnees['reponses_acceptees']);
        }
        
        if (isset($donnees['indice'])) {
            $form_data[$prefix . 'indice_image'] = $donnees['indice'];
        }
        
        if (isset($donnees['url_image'])) {
            $form_data[$prefix . 'url_image'] = $donnees['url_image'];
        }
        
        return $form_data;
    }
}
