<?php
/**
 * Gestionnaire pour les énigmes de type Texte Libre
 */
class TexteLibreHandler {
    
    /**
     * Génère le JSON pour une énigme Texte Libre
     */
    public static function generateJSON($post_data) {
        // Debug : afficher les données reçues
        error_log("TexteLibreHandler - Données reçues: " . print_r($post_data, true));
        
        return json_encode([
            'titre' => $post_data['titre_texte'] ?? '',
            'indice' => $post_data['indice_texte'] ?? '',
            'contexte' => $post_data['contexte_texte'] ?? '',
            'question' => $post_data['question_texte'] ?? '',
            'reponse_correcte' => $post_data['reponse_correcte_texte'] ?? '',
            'reponses_acceptees' => array_filter(explode(',', $post_data['reponses_acceptees_texte'] ?? ''))
        ]);
    }
    
    /**
     * Valide les données d'une énigme Texte Libre
     */
    public static function validate($post_data) {
        $errors = [];
        
        // Debug : afficher les données reçues
        error_log("TexteLibreHandler - Validation des données: " . print_r($post_data, true));
        
        if (empty($post_data['titre_texte'])) {
            $errors[] = "Le titre est obligatoire";
        }
        
        if (empty($post_data['question_texte'])) {
            $errors[] = "La question est obligatoire";
        }
        
        if (empty($post_data['reponse_correcte_texte'])) {
            $errors[] = "La réponse correcte est obligatoire";
        }
        
        return $errors;
    }
    
    /**
     * Remplit le formulaire d'édition avec les données existantes
     */
    public static function fillEditForm($donnees, $prefix = 'edit_') {
        $form_data = [];
        
        if (isset($donnees['titre'])) {
            $form_data[$prefix . 'titre_texte'] = $donnees['titre'];
        }
        
        if (isset($donnees['indice'])) {
            $form_data[$prefix . 'indice_texte'] = $donnees['indice'];
        }
        
        if (isset($donnees['contexte'])) {
            $form_data[$prefix . 'contexte_texte'] = $donnees['contexte'];
        }
        
        if (isset($donnees['question'])) {
            $form_data[$prefix . 'question_texte'] = $donnees['question'];
        }
        
        if (isset($donnees['reponse_correcte'])) {
            $form_data[$prefix . 'reponse_correcte_texte'] = $donnees['reponse_correcte'];
        }
        
        if (isset($donnees['reponses_acceptees'])) {
            $form_data[$prefix . 'reponses_acceptees_texte'] = implode(', ', $donnees['reponses_acceptees']);
        }
        
        return $form_data;
    }
}
