<?php
/**
 * Gestionnaire pour les énigmes de type Audio
 */
class AudioHandler {
    
    /**
     * Génère le JSON pour une énigme Audio
     */
    public static function generateJSON($post_data) {
        $json_data = [
            'question' => $post_data['question_audio'],
            'reponse_correcte' => $post_data['reponse_correcte_audio'],
            'reponses_acceptees' => self::parseReponsesAcceptees($post_data['reponses_acceptees_audio'] ?? ''),
            'indice' => $post_data['indice_audio'] ?? '',
            'contexte' => $post_data['contexte_audio'] ?? '',
            'autoplay' => isset($post_data['autoplay_audio']) ? true : false,
            'loop' => isset($post_data['loop_audio']) ? true : false,
            'volume_control' => isset($post_data['volume_control_audio']) ? true : false
        ];
        
        // Ajouter l'URL audio si fournie
        if (!empty($post_data['audio_url'])) {
            $json_data['audio_url'] = $post_data['audio_url'];
        }
        
        // Ajouter le fichier audio si uploadé
        if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
            // Debug visible sur la page
            if (!isset($_SESSION['debug_messages'])) {
                $_SESSION['debug_messages'] = [];
            }
            
            $_SESSION['debug_messages'][] = "=== DEBUG UPLOAD AUDIO ===";
            $_SESSION['debug_messages'][] = "Fichier reçu: " . print_r($_FILES['audio_file'], true);
            $_SESSION['debug_messages'][] = "Chemin absolu du script: " . __DIR__;
            $_SESSION['debug_messages'][] = "Chemin d'upload calculé: " . $upload_dir;
            
            // Chemin correct depuis admin/enigmes/handlers/
            $upload_dir = __DIR__ . '/../../../uploads/audio/';
            $_SESSION['debug_messages'][] = "Dossier d'upload: " . $upload_dir;
            $_SESSION['debug_messages'][] = "Dossier existe: " . (is_dir($upload_dir) ? 'OUI' : 'NON');
            
            // Créer le dossier s'il n'existe pas
            if (!is_dir($upload_dir)) {
                $created = mkdir($upload_dir, 0755, true);
                $_SESSION['debug_messages'][] = "Dossier créé: " . ($created ? 'OUI' : 'NON');
            }
            
            $file_extension = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['mp3', 'wav', 'ogg'];
            
            $_SESSION['debug_messages'][] = "Extension: " . $file_extension;
            $_SESSION['debug_messages'][] = "Extension autorisée: " . (in_array($file_extension, $allowed_extensions) ? 'OUI' : 'NON');
            
            if (in_array($file_extension, $allowed_extensions)) {
                $filename = uniqid() . '_' . time() . '.' . $file_extension;
                $filepath = $upload_dir . $filename;
                
                $_SESSION['debug_messages'][] = "Fichier de destination: " . $filepath;
                
                if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $filepath)) {
                    $json_data['audio_file'] = 'uploads/audio/' . $filename;
                    error_log("✅ Fichier audio uploadé avec succès: " . $filepath);
                } else {
                    error_log("❌ Erreur lors de l'upload du fichier audio");
                    // Correction : vérifier que error_get_last() retourne quelque chose
                    $last_error = error_get_last();
                    $error_message = $last_error ? $last_error['message'] : 'Aucune erreur PHP';
                    error_log("Erreur PHP: " . $error_message);
                    
                    // Correction : vérifier que le dossier existe avant d'accéder à ses permissions
                    if (is_dir($upload_dir)) {
                        error_log("Permissions du dossier: " . substr(sprintf('%o', fileperms($upload_dir)), -4));
                    } else {
                        error_log("Dossier n'existe pas: " . $upload_dir);
                    }
                }
            } else {
                $_SESSION['debug_messages'][] = "❌ Extension de fichier non autorisée: " . $file_extension;
            }
        } else {
            $_SESSION['debug_messages'][] = "❌ Aucun fichier audio uploadé ou erreur";
            if (isset($_FILES['audio_file'])) {
                $_SESSION['debug_messages'][] = "Détails du fichier: " . print_r($_FILES['audio_file'], true);
            }
        }
        
        return json_encode($json_data);
    }
    
    /**
     * Valide les données d'une énigme Audio
     */
    public static function validate($post_data) {
        $errors = [];
        
        if (empty($post_data['question_audio'])) {
            $errors[] = "La question est obligatoire";
        }
        
        if (empty($post_data['reponse_correcte_audio'])) {
            $errors[] = "La réponse correcte est obligatoire";
        }
        
        // Vérifier qu'au moins un fichier audio ou URL est fourni
        $has_audio_file = isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK;
        $has_audio_url = !empty($post_data['audio_url']);
        
        if (!$has_audio_file && !$has_audio_url) {
            $errors[] = "Vous devez fournir un fichier audio ou une URL audio";
        }
        
        // Validation du fichier audio si uploadé
        if ($has_audio_file) {
            $file_extension = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['mp3', 'wav', 'ogg'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                $errors[] = "Format de fichier non supporté. Utilisez MP3, WAV ou OGG";
            }
            
            if ($_FILES['audio_file']['size'] > 10 * 1024 * 1024) { // 10MB
                $errors[] = "Le fichier audio est trop volumineux (max 10MB)";
            }
        }
        
        return $errors;
    }
    
    /**
     * Remplit le formulaire d'édition avec les données existantes
     */
    public static function fillEditForm($donnees, $prefix = 'edit_') {
        $form_data = [];
        
        if (isset($donnees['question'])) {
            $form_data[$prefix . 'question_audio'] = $donnees['question'];
        }
        
        if (isset($donnees['reponse_correcte'])) {
            $form_data[$prefix . 'reponse_correcte_audio'] = $donnees['reponse_correcte'];
        }
        
        if (isset($donnees['reponses_acceptees']) && is_array($donnees['reponses_acceptees'])) {
            $form_data[$prefix . 'reponses_acceptees_audio'] = implode(', ', $donnees['reponses_acceptees']);
        }
        
        if (isset($donnees['indice'])) {
            $form_data[$prefix . 'indice_audio'] = $donnees['indice'];
        }
        
        if (isset($donnees['contexte'])) {
            $form_data[$prefix . 'contexte_audio'] = $donnees['contexte'];
        }
        
        if (isset($donnees['audio_url'])) {
            $form_data[$prefix . 'audio_url'] = $donnees['audio_url'];
        }
        
        if (isset($donnees['autoplay'])) {
            $form_data[$prefix . 'autoplay_audio'] = $donnees['autoplay'] ? '1' : '';
        }
        
        if (isset($donnees['loop'])) {
            $form_data[$prefix . 'loop_audio'] = $donnees['loop'] ? '1' : '';
        }
        
        if (isset($donnees['volume_control'])) {
            $form_data[$prefix . 'volume_control_audio'] = $donnees['volume_control'] ? '1' : '';
        }
        
        return $form_data;
    }
    
    /**
     * Parse les réponses acceptées depuis une chaîne séparée par des virgules
     */
    private static function parseReponsesAcceptees($reponses_string) {
        if (empty($reponses_string)) {
            return [];
        }
        
        $reponses = array_map('trim', explode(',', $reponses_string));
        $reponses = array_filter($reponses); // Supprimer les éléments vides
        
        return $reponses;
    }
}
