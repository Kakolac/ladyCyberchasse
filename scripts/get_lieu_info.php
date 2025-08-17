<?php
// Script pour récupérer les informations d'un lieu depuis la base de données
// Appelé par le scanner QR pour obtenir les informations dynamiques des lieux

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/connexion.php';

try {
    // Récupérer le slug du lieu depuis la requête
    $lieu_slug = $_GET['lieu'] ?? '';
    
    if (empty($lieu_slug)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Paramètre "lieu" manquant',
            'lieu_info' => [
                'nom' => 'Lieu inconnu',
                'description' => 'Paramètre manquant',
                'icon' => '❓'
            ]
        ]);
        exit;
    }
    
    // Debug : afficher le slug reçu
    error_log("Recherche du lieu avec le slug: " . $lieu_slug);
    
    // Rechercher le lieu dans la base de données
    $stmt = $pdo->prepare("
        SELECT id, nom, description, slug, statut, ordre, temps_limite, delai_indice
        FROM lieux 
        WHERE slug = ? AND statut = 'actif'
        ORDER BY ordre
    ");
    $stmt->execute([$lieu_slug]);
    $lieu = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug : afficher le résultat de la requête
    error_log("Résultat de la requête: " . ($lieu ? "trouvé" : "non trouvé"));
    if ($lieu) {
        error_log("Lieu trouvé: " . $lieu['nom'] . " (slug: " . $lieu['slug'] . ")");
    }
    
    if ($lieu) {
        // Lieu trouvé - retourner les informations
        $lieu_info = [
            'nom' => $lieu['nom'],
            'description' => $lieu['description'] ?: 'Lieu de la cyberchasse',
            'icon' => getLieuIcon($lieu['nom'], $lieu['slug']),
            'ordre' => $lieu['ordre'],
            'temps_limite' => $lieu['temps_limite'],
            'delai_indice' => $lieu['delai_indice'],
            'statut' => $lieu['statut']
        ];
        
        echo json_encode([
            'success' => true,
            'lieu_info' => $lieu_info
        ]);
        
    } else {
        // Lieu non trouvé - retourner des informations par défaut
        $lieu_info = [
            'nom' => 'Lieu inconnu',
            'description' => 'Ce lieu n\'existe pas ou n\'est pas actif (slug: ' . $lieu_slug . ')',
            'icon' => '❓',
            'ordre' => 0,
            'temps_limite' => 300,
            'delai_indice' => 6,
            'statut' => 'inactif'
        ];
        
        echo json_encode([
            'success' => false,
            'error' => 'Lieu non trouvé',
            'lieu_info' => $lieu_info
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur: ' . $e->getMessage(),
        'lieu_info' => [
            'nom' => 'Erreur serveur',
            'description' => 'Impossible de récupérer les informations du lieu',
            'icon' => '⚠️'
        ]
    ]);
}

/**
 * Fonction pour déterminer l'icône du lieu selon son nom ou slug
 */
function getLieuIcon($nom, $slug) {
    // Mapping intelligent basé sur le nom ou slug du lieu
    $nom_lower = strtolower($nom);
    $slug_lower = strtolower($slug);
    
    // Vérifier d'abord le slug (plus précis)
    if (strpos($slug_lower, 'cantine') !== false || strpos($slug_lower, 'restaurant') !== false) {
        return '🍽️';
    }
    if (strpos($slug_lower, 'cour') !== false || strpos($slug_lower, 'exterieur') !== false) {
        return '🌳';
    }
    if (strpos($slug_lower, 'cdi') !== false || strpos($slug_lower, 'bibliotheque') !== false) {
        return '📚';
    }
    if (strpos($slug_lower, 'direction') !== false || strpos($slug_lower, 'bureau') !== false) {
        return '👔';
    }
    if (strpos($slug_lower, 'gymnase') !== false || strpos($slug_lower, 'sport') !== false) {
        return '⚽';
    }
    if (strpos($slug_lower, 'infirmerie') !== false || strpos($slug_lower, 'medical') !== false) {
        return '🏥';
    }
    if (strpos($slug_lower, 'internat') !== false || strpos($slug_lower, 'chambre') !== false) {
        return '🏠';
    }
    if (strpos($slug_lower, 'labo') !== false || strpos($slug_lower, 'laboratoire') !== false) {
        if (strpos($slug_lower, 'chimie') !== false) {
            return '🧪';
        }
        if (strpos($slug_lower, 'physique') !== false) {
            return '⚡';
        }
        if (strpos($slug_lower, 'svt') !== false) {
            return '🔬';
        }
        return '🧪';
    }
    if (strpos($slug_lower, 'arts') !== false || strpos($slug_lower, 'art') !== false) {
        return '🎨';
    }
    if (strpos($slug_lower, 'info') !== false || strpos($slug_lower, 'informatique') !== false) {
        return '💻';
    }
    if (strpos($slug_lower, 'langues') !== false || strpos($slug_lower, 'langue') !== false) {
        return '🌍';
    }
    if (strpos($slug_lower, 'musique') !== false || strpos($slug_lower, 'musical') !== false) {
        return '🎵';
    }
    if (strpos($slug_lower, 'prof') !== false || strpos($slug_lower, 'enseignant') !== false) {
        return '👨‍🏫';
    }
    if (strpos($slug_lower, 'reunion') !== false || strpos($slug_lower, 'conference') !== false) {
        return '🤝';
    }
    if (strpos($slug_lower, 'secretariat') !== false || strpos($slug_lower, 'admin') !== false) {
        return '📋';
    }
    if (strpos($slug_lower, 'vie_scolaire') !== false || strpos($slug_lower, 'scolaire') !== false) {
        return '👥';
    }
    if (strpos($slug_lower, 'techno') !== false || strpos($slug_lower, 'technologie') !== false) {
        return '⚙️';
    }
    if (strpos($slug_lower, 'accueil') !== false || strpos($slug_lower, 'entree') !== false) {
        return '🏠';
    }
    
    // Vérifier le nom si le slug n'a pas donné de résultat
    if (strpos($nom_lower, 'cantine') !== false || strpos($nom_lower, 'restaurant') !== false) {
        return '🍽️';
    }
    if (strpos($nom_lower, 'cour') !== false || strpos($nom_lower, 'exterieur') !== false) {
        return '🌳';
    }
    if (strpos($nom_lower, 'cdi') !== false || strpos($nom_lower, 'bibliotheque') !== false) {
        return '📚';
    }
    if (strpos($nom_lower, 'direction') !== false || strpos($nom_lower, 'bureau') !== false) {
        return '👔';
    }
    if (strpos($nom_lower, 'gymnase') !== false || strpos($nom_lower, 'sport') !== false) {
        return '⚽';
    }
    if (strpos($nom_lower, 'infirmerie') !== false || strpos($nom_lower, 'medical') !== false) {
        return '🏥';
    }
    if (strpos($nom_lower, 'internat') !== false || strpos($nom_lower, 'chambre') !== false) {
        return '🏠';
    }
    if (strpos($nom_lower, 'labo') !== false || strpos($nom_lower, 'laboratoire') !== false) {
        if (strpos($nom_lower, 'chimie') !== false) {
            return '🧪';
        }
        if (strpos($nom_lower, 'physique') !== false) {
            return '⚡';
        }
        if (strpos($nom_lower, 'svt') !== false) {
            return '🔬';
        }
        return '🧪';
    }
    if (strpos($nom_lower, 'arts') !== false || strpos($nom_lower, 'art') !== false) {
        return '🎨';
    }
    if (strpos($nom_lower, 'info') !== false || strpos($nom_lower, 'informatique') !== false) {
        return '💻';
    }
    if (strpos($nom_lower, 'langues') !== false || strpos($nom_lower, 'langue') !== false) {
        return '🌍';
    }
    if (strpos($nom_lower, 'musique') !== false || strpos($nom_lower, 'musical') !== false) {
        return '🎵';
    }
    if (strpos($nom_lower, 'prof') !== false || strpos($nom_lower, 'enseignant') !== false) {
        return '👨‍🏫';
    }
    if (strpos($nom_lower, 'reunion') !== false || strpos($nom_lower, 'conference') !== false) {
        return '🤝';
    }
    if (strpos($nom_lower, 'secretariat') !== false || strpos($nom_lower, 'admin') !== false) {
        return '📋';
    }
    if (strpos($nom_lower, 'vie_scolaire') !== false || strpos($nom_lower, 'scolaire') !== false) {
        return '👥';
    }
    if (strpos($nom_lower, 'techno') !== false || strpos($nom_lower, 'technologie') !== false) {
        return '⚙️';
    }
    if (strpos($nom_lower, 'accueil') !== false || strpos($nom_lower, 'entree') !== false) {
        return '🏠';
    }
    
    // Icône par défaut si aucun pattern n'est reconnu
    return '📍';
}
?>
