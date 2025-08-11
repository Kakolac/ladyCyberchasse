<?php
/**
 * Script de migration pour ajouter les champs d'énigmes à la base de données
 * Lancez depuis : http://localhost:8888/scripts/migrate_enigmes_database.php
 */

require_once '../config/connexion.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Migration Base de Données - Énigmes</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; color: white; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); background: rgba(255,255,255,0.1); }
        .success { color: #4ade80; }
        .error { color: #f87171; }
        .info { color: #60a5fa; }
        .warning { color: #fbbf24; }
    </style>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-lg-10'>
                <div class='card'>
                    <div class='card-header text-center'>
                        <h1>🔧 Migration Base de Données - Énigmes</h1>
                        <p class='mb-0'>Ajout des champs pour la gestion des énigmes</p>
                    </div>
                    <div class='card-body'>";

try {
    echo "<h3>📋 Étape 1 : Ajout des nouveaux champs à la table 'lieux'</h3>";
    
    // Fonction pour vérifier si une colonne existe
    function columnExists($pdo, $table, $column) {
        $stmt = $pdo->query("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        return $stmt->rowCount() > 0;
    }
    
    // Ajouter le champ reponse_enigme
    if (!columnExists($pdo, 'lieux', 'reponse_enigme')) {
        $sql = "ALTER TABLE lieux ADD COLUMN reponse_enigme VARCHAR(10) DEFAULT 'B' COMMENT 'Réponse correcte de l''énigme (A, B, C, D)'";
        $pdo->exec($sql);
        echo "<p class='success'>✅ Champ 'reponse_enigme' ajouté à la table 'lieux'</p>";
    } else {
        echo "<p class='info'>ℹ️ Champ 'reponse_enigme' existe déjà</p>";
    }
    
    // Ajouter le champ enigme_texte
    if (!columnExists($pdo, 'lieux', 'enigme_texte')) {
        $sql = "ALTER TABLE lieux ADD COLUMN enigme_texte TEXT COMMENT 'Texte de l''énigme'";
        $pdo->exec($sql);
        echo "<p class='success'>✅ Champ 'enigme_texte' ajouté à la table 'lieux'</p>";
    } else {
        echo "<p class='info'>ℹ️ Champ 'enigme_texte' existe déjà</p>";
    }
    
    // Ajouter le champ options_enigme
    if (!columnExists($pdo, 'lieux', 'options_enigme')) {
        $sql = "ALTER TABLE lieux ADD COLUMN options_enigme JSON COMMENT 'Options de réponse de l''énigme'";
        $pdo->exec($sql);
        echo "<p class='success'>✅ Champ 'options_enigme' ajouté à la table 'lieux'</p>";
    } else {
        echo "<p class='info'>ℹ️ Champ 'options_enigme' existe déjà</p>";
    }
    
    echo "<hr>";
    echo "<h3>📝 Étape 2 : Mise à jour des énigmes existantes</h3>";
    
    // Définir les énigmes par défaut
    $enigmes = [
        'accueil' => [
            'reponse' => 'A',
            'texte' => 'Quelle est la première règle de cybersécurité ?',
            'options' => [
                'A' => 'Ne jamais partager ses informations personnelles',
                'B' => 'Partager ses mots de passe avec ses amis',
                'C' => 'Cliquer sur tous les liens reçus',
                'D' => 'Désactiver l\'antivirus'
            ]
        ],
        'cantine' => [
            'reponse' => 'C',
            'texte' => 'Quelle est la bonne pratique pour les mots de passe ?',
            'options' => [
                'A' => 'Utiliser le même mot de passe partout',
                'B' => 'Écrire ses mots de passe sur un post-it',
                'C' => 'Utiliser des mots de passe forts et uniques',
                'D' => 'Partager ses mots de passe en famille'
            ]
        ],
        'cdi' => [
            'reponse' => 'B',
            'texte' => 'Comment identifier une source d\'information fiable ?',
            'options' => [
                'A' => 'Croire tout ce qu\'on lit sur internet',
                'B' => 'Vérifier la source et croiser les informations',
                'C' => 'Se fier uniquement aux réseaux sociaux',
                'D' => 'Ignorer la date de publication'
            ]
        ],
        'cour' => [
            'reponse' => 'D',
            'texte' => 'Que faire en cas de cyberharcèlement ?',
            'options' => [
                'A' => 'Répondre aux messages agressifs',
                'B' => 'Partager les messages avec tout le monde',
                'C' => 'Supprimer son compte immédiatement',
                'D' => 'Signaler et bloquer l\'agresseur'
            ]
        ],
        'direction' => [
            'reponse' => 'A',
            'texte' => 'Quelle est la bonne attitude face aux emails suspects ?',
            'options' => [
                'A' => 'Ne pas ouvrir et signaler comme spam',
                'B' => 'Ouvrir pour voir ce que c\'est',
                'C' => 'Répondre pour demander plus d\'infos',
                'D' => 'Partager avec ses collègues'
            ]
        ],
        'gymnase' => [
            'reponse' => 'C',
            'texte' => 'Comment protéger ses données personnelles ?',
            'options' => [
                'A' => 'Les partager sur tous les sites',
                'B' => 'Accepter tous les cookies',
                'C' => 'Lire les conditions d\'utilisation',
                'D' => 'Donner accès à toutes les applications'
            ]
        ],
        'infirmerie' => [
            'reponse' => 'B',
            'texte' => 'Quelle est la bonne pratique pour les réseaux WiFi ?',
            'options' => [
                'A' => 'Se connecter à tous les réseaux ouverts',
                'B' => 'Utiliser uniquement des réseaux sécurisés connus',
                'C' => 'Partager son mot de passe WiFi',
                'D' => 'Désactiver le pare-feu'
            ]
        ],
        'internat' => [
            'reponse' => 'A',
            'texte' => 'Comment gérer ses comptes en ligne ?',
            'options' => [
                'A' => 'Utiliser l\'authentification à deux facteurs',
                'B' => 'Utiliser le même mot de passe partout',
                'C' => 'Partager ses identifiants',
                'D' => 'Ne jamais changer ses mots de passe'
            ]
        ],
        'labo_chimie' => [
            'reponse' => 'C',
            'texte' => 'Quelle est la bonne pratique pour les téléchargements ?',
            'options' => [
                'A' => 'Télécharger depuis n\'importe quel site',
                'B' => 'Ignorer les avertissements antivirus',
                'C' => 'Vérifier la source avant de télécharger',
                'D' => 'Exécuter tous les fichiers reçus'
            ]
        ],
        'labo_physique' => [
            'reponse' => 'D',
            'texte' => 'Comment protéger son ordinateur ?',
            'options' => [
                'A' => 'Désactiver l\'antivirus',
                'B' => 'Ne jamais faire de mises à jour',
                'C' => 'Partager son ordinateur avec tout le monde',
                'D' => 'Installer les mises à jour de sécurité'
            ]
        ],
        'labo_svt' => [
            'reponse' => 'B',
            'texte' => 'Quelle est la bonne pratique pour les sauvegardes ?',
            'options' => [
                'A' => 'Ne jamais faire de sauvegarde',
                'B' => 'Faire des sauvegardes régulières',
                'C' => 'Sauvegarder uniquement sur l\'ordinateur',
                'D' => 'Partager ses sauvegardes en ligne'
            ]
        ],
        'salle_arts' => [
            'reponse' => 'A',
            'texte' => 'Comment gérer ses photos en ligne ?',
            'options' => [
                'A' => 'Vérifier les paramètres de confidentialité',
                'B' => 'Partager toutes ses photos publiquement',
                'C' => 'Accepter toutes les demandes d\'amis',
                'D' => 'Taguer tout le monde sans permission'
            ]
        ],
        'salle_info' => [
            'reponse' => 'C',
            'texte' => 'Quelle est la bonne pratique pour les mots de passe ?',
            'options' => [
                'A' => 'Utiliser des mots simples à retenir',
                'B' => 'Écrire ses mots de passe partout',
                'C' => 'Utiliser un gestionnaire de mots de passe',
                'D' => 'Partager ses mots de passe'
            ]
        ],
        'salle_langues' => [
            'reponse' => 'D',
            'texte' => 'Comment se protéger du phishing ?',
            'options' => [
                'A' => 'Cliquer sur tous les liens reçus',
                'B' => 'Répondre aux emails demandant des infos',
                'C' => 'Partager ses coordonnées bancaires',
                'D' => 'Vérifier l\'expéditeur avant de répondre'
            ]
        ],
        'salle_musique' => [
            'reponse' => 'B',
            'texte' => 'Quelle est la bonne pratique pour les réseaux sociaux ?',
            'options' => [
                'A' => 'Accepter toutes les demandes d\'amis',
                'B' => 'Vérifier l\'identité des personnes',
                'C' => 'Partager toutes ses informations',
                'D' => 'Publier ses coordonnées personnelles'
            ]
        ],
        'salle_profs' => [
            'reponse' => 'A',
            'texte' => 'Comment gérer ses emails professionnels ?',
            'options' => [
                'A' => 'Utiliser un compte professionnel séparé',
                'B' => 'Utiliser son compte personnel',
                'C' => 'Partager son mot de passe avec ses collègues',
                'D' => 'Répondre à tous les emails reçus'
            ]
        ],
        'salle_reunion' => [
            'reponse' => 'B',
            'texte' => 'Quelle est la BONNE pratique de cybersécurité ?',
            'options' => [
                'A' => 'Partager ses mots de passe avec ses amis de confiance',
                'B' => 'Installer les mises à jour de sécurité dès qu\'elles sont disponibles',
                'C' => 'Cliquer sur tous les liens reçus par email',
                'D' => 'Désactiver l\'antivirus pour améliorer les performances'
            ]
        ],
        'secretariat' => [
            'reponse' => 'C',
            'texte' => 'Comment protéger les données sensibles ?',
            'options' => [
                'A' => 'Les partager avec tout le monde',
                'B' => 'Les laisser sur des post-its',
                'C' => 'Les chiffrer et limiter l\'accès',
                'D' => 'Les publier sur internet'
            ]
        ],
        'vie_scolaire' => [
            'reponse' => 'D',
            'texte' => 'Quelle est la bonne pratique RGPD ?',
            'options' => [
                'A' => 'Collecter toutes les données possibles',
                'B' => 'Partager les données avec tout le monde',
                'C' => 'Garder les données indéfiniment',
                'D' => 'Demander le consentement avant collecte'
            ]
        ],
        'atelier_techno' => [
            'reponse' => 'A',
            'texte' => 'Comment sécuriser un réseau WiFi ?',
            'options' => [
                'A' => 'Utiliser un chiffrement WPA3 et un mot de passe fort',
                'B' => 'Laisser le réseau ouvert sans mot de passe',
                'C' => 'Partager le mot de passe avec tout le monde',
                'D' => 'Désactiver le pare-feu'
            ]
        ]
    ];
    
    $updatedCount = 0;
    foreach ($enigmes as $slug => $enigme) {
        $stmt = $pdo->prepare("UPDATE lieux SET reponse_enigme = ?, enigme_texte = ?, options_enigme = ? WHERE slug = ?");
        if ($stmt->execute([$enigme['reponse'], $enigme['texte'], json_encode($enigme['options']), $slug])) {
            echo "<p class='success'>✅ Lieu '{$slug}' mis à jour avec l'énigme</p>";
            $updatedCount++;
        } else {
            echo "<p class='error'>❌ Erreur lors de la mise à jour de '{$slug}'</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>🔍 Étape 3 : Vérification de la structure</h3>";
    
    // Vérifier la nouvelle structure
    $stmt = $pdo->query("DESCRIBE lieux");
    $columns = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
    }
    
    $requiredColumns = ['reponse_enigme', 'enigme_texte', 'options_enigme'];
    foreach ($requiredColumns as $col) {
        if (in_array($col, $columns)) {
            echo "<p class='success'>✅ Champ '{$col}' présent dans la table 'lieux'</p>";
        } else {
            echo "<p class='error'>❌ Champ '{$col}' manquant dans la table 'lieux'</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>📊 Résumé de la migration</h3>";
    echo "<div class='alert alert-success'>";
    echo "<h4> Migration terminée avec succès !</h4>";
    echo "<p><strong>{$updatedCount}</strong> lieux ont été mis à jour avec leurs énigmes.</p>";
    echo "<p>La base de données est maintenant prête pour la gestion des énigmes.</p>";
    echo "</div>";
    
    echo "<div class='text-center mt-4'>";
    echo "<a href='../admin/lieux.php' class='btn btn-primary btn-lg'>";
    echo "🏗️ Accéder à l'administration des lieux";
    echo "</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h4>❌ Erreur lors de la migration</h4>";
    echo "<p><strong>Erreur :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>";
?>
