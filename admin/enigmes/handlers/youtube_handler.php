<?php
/**
 * Gestionnaire pour les énigmes de type YouTube
 */
class YouTubeHandler {
    
    /**
     * Génère le JSON pour une énigme YouTube
     */
    public static function generateJSON($post_data) {
        return json_encode([
            'question' => $post_data['question_youtube'] ?? '',
            'youtube_url' => self::extractYouTubeID($post_data['youtube_url'] ?? ''),
            'reponse_correcte' => $post_data['reponse_correcte_youtube'] ?? '',
            'reponses_acceptees' => self::parseReponsesAcceptees($post_data['reponses_acceptees_youtube'] ?? ''),
            'indice' => $post_data['indice_youtube'] ?? '',
            'contexte' => $post_data['contexte_youtube'] ?? '',
            'autoplay' => isset($post_data['autoplay_youtube']),
            'loop' => isset($post_data['loop_youtube']),
            'show_controls' => isset($post_data['show_controls_youtube'])
        ]);
    }
    
    /**
     * Extrait l'ID YouTube de l'URL
     */
    private static function extractYouTubeID($url) {
        $pattern = '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return $url; // Retourner l'URL complète si pas d'ID trouvé
    }
    
    /**
     * Parse les réponses acceptées
     */
    private static function parseReponsesAcceptees($reponses) {
        if (empty($reponses)) {
            return [];
        }
        return array_filter(array_map('trim', explode(',', $reponses)));
    }
    
    /**
     * Valide les données d'une énigme YouTube
     */
    public static function validate($post_data) {
        $errors = [];
        
        if (empty($post_data['question_youtube'])) {
            $errors[] = "La question est obligatoire";
        }
        
        if (empty($post_data['youtube_url'])) {
            $errors[] = "L'URL YouTube est obligatoire";
        } elseif (!self::isValidYouTubeURL($post_data['youtube_url'])) {
            $errors[] = "L'URL YouTube n'est pas valide";
        }
        
        if (empty($post_data['reponse_correcte_youtube'])) {
            $errors[] = "La réponse correcte est obligatoire";
        }
        
        return $errors;
    }
    
    /**
     * Vérifie si l'URL YouTube est valide
     */
    private static function isValidYouTubeURL($url) {
        $patterns = [
            '/^https?:\/\/(www\.)?youtube\.com\/watch\?v=[a-zA-Z0-9_-]{11}/',
            '/^https?:\/\/youtu\.be\/[a-zA-Z0-9_-]{11}/',
            '/^https?:\/\/(www\.)?youtube\.com\/embed\/[a-zA-Z0-9_-]{11}/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Remplit le formulaire d'édition avec les données existantes
     */
    public static function fillEditForm($donnees, $prefix = 'edit_') {
        $form_data = [];
        
        if (isset($donnees['question'])) {
            $form_data[$prefix . 'question_youtube'] = $donnees['question'];
        }
        
        if (isset($donnees['youtube_url'])) {
            $form_data[$prefix . 'youtube_url'] = 'https://www.youtube.com/watch?v=' . $donnees['youtube_url'];
        }
        
        if (isset($donnees['reponse_correcte'])) {
            $form_data[$prefix . 'reponse_correcte_youtube'] = $donnees['reponse_correcte'];
        }
        
        if (isset($donnees['reponses_acceptees'])) {
            $form_data[$prefix . 'reponses_acceptees_youtube'] = implode(', ', $donnees['reponses_acceptees']);
        }
        
        if (isset($donnees['indice'])) {
            $form_data[$prefix . 'indice_youtube'] = $donnees['indice'];
        }
        
        if (isset($donnees['contexte'])) {
            $form_data[$prefix . 'contexte_youtube'] = $donnees['contexte'];
        }
        
        if (isset($donnees['autoplay'])) {
            $form_data[$prefix . 'autoplay_youtube'] = $donnees['autoplay'];
        }
        
        if (isset($donnees['loop'])) {
            $form_data[$prefix . 'loop_youtube'] = $donnees['loop'];
        }
        
        if (isset($donnees['show_controls'])) {
            $form_data[$prefix . 'show_controls_youtube'] = $donnees['show_controls'];
        }
        
        return $form_data;
    }
}
