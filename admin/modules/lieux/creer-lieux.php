<?php
// Ce script s'exécute automatiquement en arrière-plan
// Il ne doit pas afficher d'interface, juste générer les fichiers

// Récupérer les variables du contexte parent
$lieu_id = $lieu_id ?? null;
$nom = $nom ?? '';
$slug = $slug ?? '';
$type_lieu = $type_lieu ?? 'standard';
$description = $description ?? '';

if ($lieu_id && $nom && $slug) {
    try {
        // 1. Sélection du template selon le type
        $source_dir = '../../../templates/TemplateLieu'; // Par défaut
        if ($type_lieu === 'fin') {
            $source_dir = '../../../templates/TemplateLieuFin';
        } elseif ($type_lieu === 'demarrage') {
            $source_dir = '../../../templates/TemplateLieuDemarrage';
        }
        
        $target_dir = "../../../lieux/$slug";
        
        if (!is_dir($target_dir)) {
            if (mkdir($target_dir, 0755, true)) {
                // 2. Copier tous les fichiers du template
                $files = ['index.php', 'header.php', 'footer.php', 'style.css'];
                
                // Ajouter enigme.php seulement pour les lieux standard
                if ($type_lieu === 'standard') {
                    $files[] = 'enigme.php';
                }
                
                foreach ($files as $file) {
                    $source_file = "$source_dir/$file";
                    $target_file = "$target_dir/$file";
                    
                    if (file_exists($source_file)) {
                        $content = file_get_contents($source_file);
                        
                        // 3. Remplacer les variables dans les fichiers PHP
                        if (in_array($file, ['index.php', 'enigme.php'])) {
                            $content = str_replace("'direction'", "'$slug'", $content);
                            
                            if ($file === 'index.php') {
                                $content = str_replace(
                                    "../../enigme_launcher.php?lieu=direction",
                                    "../../enigme_launcher.php?lieu=$slug",
                                    $content
                                );
                            }
                        }
                        
                        // 4. Écrire le fichier modifié
                        if (!file_put_contents($target_file, $content)) {
                            // Erreur silencieuse - ne pas interrompre le processus
                            error_log("Erreur lors de l'écriture du fichier $file pour le lieu $slug");
                        }
                    }
                }
                
                // 5. Mettre à jour le message de succès avec plus de détails
                $success_message .= "<br>📁 Dossier créé : lieux/$slug/";
                $success_message .= "<br>🔧 Type : " . ucfirst($type_lieu);
                $success_message .= "<br>🌐 <a href='../../../lieux/$slug/' target='_blank'>Voir le lieu</a>";
                
            } else {
                error_log("Impossible de créer le répertoire $target_dir pour le lieu $slug");
            }
        } else {
            error_log("Le répertoire $target_dir existe déjà pour le lieu $slug");
        }
        
    } catch (Exception $e) {
        // Erreur silencieuse - ne pas interrompre le processus principal
        error_log("Erreur lors de la génération des fichiers pour le lieu $slug : " . $e->getMessage());
    }
}
?>
