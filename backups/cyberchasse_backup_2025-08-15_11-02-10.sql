-- Sauvegarde de la base de données Cyberchasse
-- Générée le : 2025-08-15 11:02:10
-- Version MySQL : 8.0.40

SET FOREIGN_KEY_CHECKS=0;

-- Structure de la table `enigmes`
DROP TABLE IF EXISTS `enigmes`;
CREATE TABLE `enigmes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type_enigme_id` int NOT NULL,
  `titre` varchar(255) NOT NULL,
  `donnees` json NOT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `type_enigme_id` (`type_enigme_id`),
  CONSTRAINT `enigmes_ibfk_1` FOREIGN KEY (`type_enigme_id`) REFERENCES `types_enigmes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Données de la table `enigmes`
INSERT INTO `enigmes` (`id`, `type_enigme_id`, `titre`, `donnees`, `actif`, `created_at`) VALUES
('1', '1', 'test', '{\"options\": {\"A\": \"Ne jamais partager ses informations personnelles\", \"B\": \"Partager ses mots de passe avec ses amis\", \"C\": \"Cliquer sur tous les liens reçus\", \"D\": \"Désactiver l\'antivirus\"}, \"question\": \"Quelle est la première règle de cybersécurité ?\", \"reponse_correcte\": \"A\"}', '1', '2025-08-14 10:09:39'),
('2', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Utiliser le même mot de passe partout\", \"B\": \"Écrire ses mots de passe sur un post-it\", \"C\": \"Utiliser des mots de passe forts et uniques\", \"D\": \"Partager ses mots de passe en famille\"}, \"question\": \"Quelle est la bonne pratique pour les mots de passe ?\", \"reponse_correcte\": \"C\"}', '1', '2025-08-14 10:09:39'),
('3', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Croire tout ce qu\'on lit sur internet\", \"B\": \"Vérifier la source et croiser les informations\", \"C\": \"Se fier uniquement aux réseaux sociaux\", \"D\": \"Ignorer la date de publication\"}, \"question\": \"Comment identifier une source d\'information fiable ?\", \"reponse_correcte\": \"B\"}', '1', '2025-08-14 10:09:39'),
('4', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Répondre aux messages agressifs\", \"B\": \"Partager les messages avec tout le monde\", \"C\": \"Supprimer son compte immédiatement\", \"D\": \"Signaler et bloquer l\'agresseur\"}, \"question\": \"Que faire en cas de cyberharcèlement ?\", \"reponse_correcte\": \"D\"}', '1', '2025-08-14 10:09:39'),
('5', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Ne pas ouvrir et signaler comme spam\", \"B\": \"Ouvrir pour voir ce que c\'est\", \"C\": \"Répondre pour demander plus d\'infos\", \"D\": \"Partager avec ses collègues\"}, \"question\": \"Quelle est la bonne attitude face aux emails suspects ?\", \"reponse_correcte\": \"A\"}', '1', '2025-08-14 10:09:39'),
('6', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Les partager sur tous les sites\", \"B\": \"Accepter tous les cookies\", \"C\": \"Lire les conditions d\'utilisation\", \"D\": \"Donner accès à toutes les applications\"}, \"question\": \"Comment protéger ses données personnelles ?\", \"reponse_correcte\": \"C\"}', '1', '2025-08-14 10:09:39'),
('7', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Se connecter à tous les réseaux ouverts\", \"B\": \"Utiliser uniquement des réseaux sécurisés connus\", \"C\": \"Partager son mot de passe WiFi\", \"D\": \"Désactiver le pare-feu\"}, \"question\": \"Quelle est la bonne pratique pour les réseaux WiFi ?\", \"reponse_correcte\": \"B\"}', '1', '2025-08-14 10:09:39'),
('8', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Utiliser l\'authentification à deux facteurs\", \"B\": \"Utiliser le même mot de passe partout\", \"C\": \"Partager ses identifiants\", \"D\": \"Ne jamais changer ses mots de passe\"}, \"question\": \"Comment gérer ses comptes en ligne ?\", \"reponse_correcte\": \"A\"}', '1', '2025-08-14 10:09:39'),
('9', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Télécharger depuis n\'importe quel site\", \"B\": \"Ignorer les avertissements antivirus\", \"C\": \"Vérifier la source avant de télécharger\", \"D\": \"Exécuter tous les fichiers reçus\"}, \"question\": \"Quelle est la bonne pratique pour les téléchargements ?\", \"reponse_correcte\": \"C\"}', '1', '2025-08-14 10:09:39'),
('10', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Désactiver l\'antivirus\", \"B\": \"Ne jamais faire de mises à jour\", \"C\": \"Partager son ordinateur avec tout le monde\", \"D\": \"Installer les mises à jour de sécurité\"}, \"question\": \"Comment protéger son ordinateur ?\", \"reponse_correcte\": \"D\"}', '1', '2025-08-14 10:09:39'),
('11', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Ne jamais faire de sauvegarde\", \"B\": \"Faire des sauvegardes régulières\", \"C\": \"Sauvegarder uniquement sur l\'ordinateur\", \"D\": \"Partager ses sauvegardes en ligne\"}, \"question\": \"Quelle est la bonne pratique pour les sauvegardes ?\", \"reponse_correcte\": \"B\"}', '1', '2025-08-14 10:09:39'),
('12', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Vérifier les paramètres de confidentialité\", \"B\": \"Partager toutes ses photos publiquement\", \"C\": \"Accepter toutes les demandes d\'amis\", \"D\": \"Taguer tout le monde sans permission\"}, \"question\": \"Comment gérer ses photos en ligne ?\", \"reponse_correcte\": \"A\"}', '1', '2025-08-14 10:09:39'),
('13', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Utiliser des mots simples à retenir\", \"B\": \"Écrire ses mots de passe partout\", \"C\": \"Utiliser un gestionnaire de mots de passe\", \"D\": \"Partager ses mots de passe\"}, \"question\": \"Quelle est la bonne pratique pour les mots de passe ?\", \"reponse_correcte\": \"C\"}', '1', '2025-08-14 10:09:39'),
('14', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Cliquer sur tous les liens reçus\", \"B\": \"Répondre aux emails demandant des infos\", \"C\": \"Partager ses coordonnées bancaires\", \"D\": \"Vérifier l\'expéditeur avant de répondre\"}, \"question\": \"Comment se protéger du phishing ?\", \"reponse_correcte\": \"D\"}', '1', '2025-08-14 10:09:39'),
('15', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Accepter toutes les demandes d\'amis\", \"B\": \"Vérifier l\'identité des personnes\", \"C\": \"Partager toutes ses informations\", \"D\": \"Publier ses coordonnées personnelles\"}, \"question\": \"Quelle est la bonne pratique pour les réseaux sociaux ?\", \"reponse_correcte\": \"B\"}', '1', '2025-08-14 10:09:39'),
('16', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Utiliser un compte professionnel séparé\", \"B\": \"Utiliser son compte personnel\", \"C\": \"Partager son mot de passe avec ses collègues\", \"D\": \"Répondre à tous les emails reçus\"}, \"question\": \"Comment gérer ses emails professionnels ?\", \"reponse_correcte\": \"A\"}', '1', '2025-08-14 10:09:39'),
('17', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Partager ses mots de passe avec ses amis de confiance\", \"B\": \"Installer les mises à jour de sécurité dès qu\'elles sont disponibles\", \"C\": \"Cliquer sur tous les liens reçus par email\", \"D\": \"Désactiver l\'antivirus pour améliorer les performances\"}, \"question\": \"Quelle est la BONNE pratique de cybersécurité ?\", \"reponse_correcte\": \"B\"}', '1', '2025-08-14 10:09:39'),
('18', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Les partager avec tout le monde\", \"B\": \"Les laisser sur des post-its\", \"C\": \"Les chiffrer et limiter l\'accès\", \"D\": \"Les publier sur internet\"}, \"question\": \"Comment protéger les données sensibles ?\", \"reponse_correcte\": \"C\"}', '1', '2025-08-14 10:09:39'),
('19', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Collecter toutes les données possibles\", \"B\": \"Partager les données avec tout le monde\", \"C\": \"Garder les données indéfiniment\", \"D\": \"Demander le consentement avant collecte\"}, \"question\": \"Quelle est la bonne pratique RGPD ?\", \"reponse_correcte\": \"D\"}', '1', '2025-08-14 10:09:39'),
('20', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Utiliser un chiffrement WPA3 et un mot de passe fort\", \"B\": \"Laisser le réseau ouvert sans mot de passe\", \"C\": \"Partager le mot de passe avec tout le monde\", \"D\": \"Désactiver le pare-feu\"}, \"question\": \"Comment sécuriser un réseau WiFi ?\", \"reponse_correcte\": \"A\"}', '1', '2025-08-14 10:09:39'),
('21', '1', 'lady', '{\"options\": {\"A\": \"toto\", \"B\": \"yoyoyo\", \"C\": \"Cliquer sur tous les liens reçus\", \"D\": \"Désactiver l\'antivirus\"}, \"question\": \"Quelle est la première règle de cybersécurité ?\", \"reponse_correcte\": \"A\"}', '1', '2025-08-14 11:29:46'),
('22', '2', 'tester cos conaissances', '{\"indice\": \"C\'est l\'évolution sécurisée du protocole HTTP standard\", \"question\": \"Quel est le nom du protocole de sécurité utilisé pour sécuriser les connexions web ?\", \"reponse_correcte\": \"HTTPS\", \"reponses_acceptees\": [\"https\", \"Https\", \"http secure\"]}', '1', '2025-08-14 11:45:47'),
('23', '2', 'pain surprise', '{\"indice\": \"C\'est un pain qui utilise une fermentation naturelle grâce à des levures et bactéries présentes dans un mélange de farine et d\'eau.\", \"question\": \"Quel est le nom de ce pain traditionnel fabriqué avec un levain naturel ?\", \"reponse_correcte\": \"Pain au levain\", \"reponses_acceptees\": [\"pain au levain\", \"levain\", \"pain levain\"]}', '1', '2025-08-14 11:51:07'),
('24', '2', 'alan', '{\"indice\": \"C\'est un jeu vidéo de type bac à sable où l\'on peut construire, explorer et survivre dans un monde composé de blocs.\", \"question\": \"Quel est le nom de ce jeu vidéo créé par Markus Persson et publié par Mojang en 2011 ?\", \"reponse_correcte\": \"Minecraft\", \"reponses_acceptees\": [\"minecraft\", \"MineCraft\"]}', '1', '2025-08-14 12:17:13');

-- Structure de la table `equipes`
DROP TABLE IF EXISTS `equipes`;
CREATE TABLE `equipes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `couleur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('active','inactive','terminee') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `temps_total` int DEFAULT '0' COMMENT 'Temps total en secondes',
  `score` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`),
  KEY `idx_nom` (`nom`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données de la table `equipes`
INSERT INTO `equipes` (`id`, `nom`, `couleur`, `mot_de_passe`, `statut`, `temps_total`, `score`, `created_at`, `updated_at`) VALUES
('1', 'Rouge', '#dc3545', '$2y$10$sFxvaJiVdtyjBPtmboisXOrTC5OjecI4Wom3mQDMTyRruxhetp/1S', 'active', '45837', '180', '2025-08-11 15:16:01', '2025-08-15 12:55:31'),
('2', 'Bleu', '#007bff', '$2y$10$pIjjjb5k0JIQdO/slq1S2uBEsKiQcSXCUrEdHADIIEcOPRi5DBr1S', 'active', '0', '0', '2025-08-11 15:16:01', '2025-08-11 21:44:24'),
('3', 'Vert', '#28a745', '$2y$10$80Vd/TPlMaA3g25j5hG.POz7tZBoMKky/n3e4CZguL.LVTkezonq2', 'active', '0', '0', '2025-08-11 15:16:01', '2025-08-11 21:44:24'),
('4', 'Jaune', '#ffc107', '$2y$10$tuqFM4ZhIAKXGRdUGYE36eb3eJYdz8XFTRFyV//0Q3a5lpYleyQLG', 'active', '0', '0', '2025-08-11 15:16:01', '2025-08-11 21:44:24');

-- Structure de la table `indices_consultes`
DROP TABLE IF EXISTS `indices_consultes`;
CREATE TABLE `indices_consultes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe_id` int NOT NULL,
  `lieu_id` int NOT NULL,
  `enigme_id` int NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_consultation` (`equipe_id`,`lieu_id`,`enigme_id`),
  KEY `lieu_id` (`lieu_id`),
  KEY `enigme_id` (`enigme_id`),
  CONSTRAINT `indices_consultes_ibfk_1` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`),
  CONSTRAINT `indices_consultes_ibfk_2` FOREIGN KEY (`lieu_id`) REFERENCES `lieux` (`id`),
  CONSTRAINT `indices_consultes_ibfk_3` FOREIGN KEY (`enigme_id`) REFERENCES `enigmes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Données de la table `indices_consultes`
INSERT INTO `indices_consultes` (`id`, `equipe_id`, `lieu_id`, `enigme_id`, `timestamp`) VALUES
('29', '1', '5', '24', '2025-08-15 08:16:02'),
('30', '1', '22', '23', '2025-08-15 12:49:42');

-- Structure de la table `indices_forces`
DROP TABLE IF EXISTS `indices_forces`;
CREATE TABLE `indices_forces` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe_id` int NOT NULL,
  `lieu_id` int NOT NULL,
  `enigme_id` int NOT NULL,
  `admin_id` varchar(100) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_force` (`equipe_id`,`lieu_id`,`enigme_id`),
  KEY `lieu_id` (`lieu_id`),
  KEY `enigme_id` (`enigme_id`),
  CONSTRAINT `indices_forces_ibfk_1` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `indices_forces_ibfk_2` FOREIGN KEY (`lieu_id`) REFERENCES `lieux` (`id`) ON DELETE CASCADE,
  CONSTRAINT `indices_forces_ibfk_3` FOREIGN KEY (`enigme_id`) REFERENCES `enigmes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Structure de la table `lieux`
DROP TABLE IF EXISTS `lieux`;
CREATE TABLE `lieux` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL friendly',
  `description` text COLLATE utf8mb4_unicode_ci,
  `ordre` int DEFAULT '0' COMMENT 'Ordre dans le parcours',
  `temps_limite` int DEFAULT '300' COMMENT 'Temps limite en secondes',
  `enigme_requise` tinyint(1) DEFAULT '0',
  `statut` enum('actif','inactif') COLLATE utf8mb4_unicode_ci DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reponse_enigme` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'B' COMMENT 'Réponse correcte de l''énigme (A, B, C, D)',
  `enigme_texte` text COLLATE utf8mb4_unicode_ci COMMENT 'Texte de l''énigme',
  `options_enigme` json DEFAULT NULL COMMENT 'Options de réponse de l''énigme',
  `enigme_id` int DEFAULT NULL,
  `delai_indice` int DEFAULT '6' COMMENT 'Délai en minutes avant disponibilité de l''indice',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_ordre` (`ordre`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données de la table `lieux`
INSERT INTO `lieux` (`id`, `nom`, `slug`, `description`, `ordre`, `temps_limite`, `enigme_requise`, `statut`, `created_at`, `updated_at`, `reponse_enigme`, `enigme_texte`, `options_enigme`, `enigme_id`, `delai_indice`) VALUES
('1', 'Accueil', 'accueil', 'Point de départ de la cyberchasse', '1', '120', '0', 'actif', '2025-08-11 15:16:01', '2025-08-15 08:18:16', 'A', 'Quelle est la première règle de cybersécurité ?', '{\"A\": \"Ne jamais partager ses informations personnelles\", \"B\": \"Partager ses mots de passe avec ses amis\", \"C\": \"Cliquer sur tous les liens reçus\", \"D\": \"Désactiver l\'antivirus\"}', '24', '1'),
('2', 'Cantine', 'cantine', 'Zone de restauration', '2', '300', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'C', 'Quelle est la bonne pratique pour les mots de passe ?', '{\"A\": \"Utiliser le même mot de passe partout\", \"B\": \"Écrire ses mots de passe sur un post-it\", \"C\": \"Utiliser des mots de passe forts et uniques\", \"D\": \"Partager ses mots de passe en famille\"}', '2', '6'),
('3', 'CDI', 'cdi', 'Centre de Documentation et d\'Information', '3', '420', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'B', 'Comment identifier une source d\'information fiable ?', '{\"A\": \"Croire tout ce qu\'on lit sur internet\", \"B\": \"Vérifier la source et croiser les informations\", \"C\": \"Se fier uniquement aux réseaux sociaux\", \"D\": \"Ignorer la date de publication\"}', '3', '6'),
('4', 'Cour', 'cour', 'Espace extérieur', '4', '180', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'D', 'Que faire en cas de cyberharcèlement ?', '{\"A\": \"Répondre aux messages agressifs\", \"B\": \"Partager les messages avec tout le monde\", \"C\": \"Supprimer son compte immédiatement\", \"D\": \"Signaler et bloquer l\'agresseur\"}', '4', '6'),
('5', 'Direction', 'direction', 'Bureau de la direction', '5', '360', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 19:37:38', 'A', 'Quelle est la bonne attitude face aux emails suspects ?', '{\"A\": \"Ne pas ouvrir et signaler comme spam\", \"B\": \"Ouvrir pour voir ce que c\'est\", \"C\": \"Répondre pour demander plus d\'infos\", \"D\": \"Partager avec ses collègues\"}', '24', '1'),
('6', 'Gymnase', 'gymnase', 'Salle de sport', '6', '240', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'C', 'Comment protéger ses données personnelles ?', '{\"A\": \"Les partager sur tous les sites\", \"B\": \"Accepter tous les cookies\", \"C\": \"Lire les conditions d\'utilisation\", \"D\": \"Donner accès à toutes les applications\"}', '6', '6'),
('7', 'Infirmerie', 'infirmerie', 'Zone médicale', '7', '300', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'B', 'Quelle est la bonne pratique pour les réseaux WiFi ?', '{\"A\": \"Se connecter à tous les réseaux ouverts\", \"B\": \"Utiliser uniquement des réseaux sécurisés connus\", \"C\": \"Partager son mot de passe WiFi\", \"D\": \"Désactiver le pare-feu\"}', '7', '6'),
('8', 'Internat', 'internat', 'Zone d\'hébergement', '8', '360', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'A', 'Comment gérer ses comptes en ligne ?', '{\"A\": \"Utiliser l\'authentification à deux facteurs\", \"B\": \"Utiliser le même mot de passe partout\", \"C\": \"Partager ses identifiants\", \"D\": \"Ne jamais changer ses mots de passe\"}', '8', '6'),
('9', 'Labo Chimie', 'labo_chimie', 'Laboratoire de chimie', '9', '480', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'C', 'Quelle est la bonne pratique pour les téléchargements ?', '{\"A\": \"Télécharger depuis n\'importe quel site\", \"B\": \"Ignorer les avertissements antivirus\", \"C\": \"Vérifier la source avant de télécharger\", \"D\": \"Exécuter tous les fichiers reçus\"}', '9', '6'),
('10', 'Labo Physique', 'labo_physique', 'Laboratoire de physique', '10', '480', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'D', 'Comment protéger son ordinateur ?', '{\"A\": \"Désactiver l\'antivirus\", \"B\": \"Ne jamais faire de mises à jour\", \"C\": \"Partager son ordinateur avec tout le monde\", \"D\": \"Installer les mises à jour de sécurité\"}', '10', '6'),
('11', 'Labo SVT', 'labo_svt', 'Laboratoire de SVT', '11', '480', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'B', 'Quelle est la bonne pratique pour les sauvegardes ?', '{\"A\": \"Ne jamais faire de sauvegarde\", \"B\": \"Faire des sauvegardes régulières\", \"C\": \"Sauvegarder uniquement sur l\'ordinateur\", \"D\": \"Partager ses sauvegardes en ligne\"}', '11', '6'),
('12', 'Salle Arts', 'salle_arts', 'Salle d\'arts plastiques', '12', '300', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'A', 'Comment gérer ses photos en ligne ?', '{\"A\": \"Vérifier les paramètres de confidentialité\", \"B\": \"Partager toutes ses photos publiquement\", \"C\": \"Accepter toutes les demandes d\'amis\", \"D\": \"Taguer tout le monde sans permission\"}', '12', '6'),
('13', 'Salle Info', 'salle_info', 'Salle informatique', '13', '420', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'C', 'Quelle est la bonne pratique pour les mots de passe ?', '{\"A\": \"Utiliser des mots simples à retenir\", \"B\": \"Écrire ses mots de passe partout\", \"C\": \"Utiliser un gestionnaire de mots de passe\", \"D\": \"Partager ses mots de passe\"}', '13', '6'),
('14', 'Salle Langues', 'salle_langues', 'Salle de langues', '14', '300', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'D', 'Comment se protéger du phishing ?', '{\"A\": \"Cliquer sur tous les liens reçus\", \"B\": \"Répondre aux emails demandant des infos\", \"C\": \"Partager ses coordonnées bancaires\", \"D\": \"Vérifier l\'expéditeur avant de répondre\"}', '14', '6'),
('15', 'Salle Musique', 'salle_musique', 'Salle de musique', '15', '300', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'B', 'Quelle est la bonne pratique pour les réseaux sociaux ?', '{\"A\": \"Accepter toutes les demandes d\'amis\", \"B\": \"Vérifier l\'identité des personnes\", \"C\": \"Partager toutes ses informations\", \"D\": \"Publier ses coordonnées personnelles\"}', '15', '6'),
('16', 'Salle Profs', 'salle_profs', 'Salle des professeurs', '16', '240', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'A', 'Comment gérer ses emails professionnels ?', '{\"A\": \"Utiliser un compte professionnel séparé\", \"B\": \"Utiliser son compte personnel\", \"C\": \"Partager son mot de passe avec ses collègues\", \"D\": \"Répondre à tous les emails reçus\"}', '16', '6'),
('17', 'Salle Réunion', 'salle_reunion', 'Salle de réunion', '17', '360', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'B', 'Quelle est la BONNE pratique de cybersécurité ?', '{\"A\": \"Partager ses mots de passe avec ses amis de confiance\", \"B\": \"Installer les mises à jour de sécurité dès qu\'elles sont disponibles\", \"C\": \"Cliquer sur tous les liens reçus par email\", \"D\": \"Désactiver l\'antivirus pour améliorer les performances\"}', '17', '6'),
('18', 'Secrétariat', 'secretariat', 'Bureau du secrétariat', '18', '300', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'C', 'Comment protéger les données sensibles ?', '{\"A\": \"Les partager avec tout le monde\", \"B\": \"Les laisser sur des post-its\", \"C\": \"Les chiffrer et limiter l\'accès\", \"D\": \"Les publier sur internet\"}', '18', '6'),
('19', 'Vie Scolaire', 'vie_scolaire', 'Bureau de la vie scolaire', '19', '300', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'D', 'Quelle est la bonne pratique RGPD ?', '{\"A\": \"Collecter toutes les données possibles\", \"B\": \"Partager les données avec tout le monde\", \"C\": \"Garder les données indéfiniment\", \"D\": \"Demander le consentement avant collecte\"}', '19', '6'),
('20', 'Atelier Techno', 'atelier_techno', 'Atelier de technologie', '20', '480', '0', 'actif', '2025-08-11 15:16:01', '2025-08-14 10:09:39', 'A', 'Comment sécuriser un réseau WiFi ?', '{\"A\": \"Utiliser un chiffrement WPA3 et un mot de passe fort\", \"B\": \"Laisser le réseau ouvert sans mot de passe\", \"C\": \"Partager le mot de passe avec tout le monde\", \"D\": \"Désactiver le pare-feu\"}', '20', '6'),
('21', 'testM', 'testm', 'ssss', '1', '300', '0', 'actif', '2025-08-15 12:32:37', '2025-08-15 12:32:58', 'B', NULL, NULL, '24', '6'),
('22', 'TestAdrien', 'testadrien', 'dsqdqsd', '21', '720', '1', 'actif', '2025-08-15 12:46:20', '2025-08-15 12:49:10', 'B', NULL, NULL, '23', '1');

-- Structure de la table `logs_activite`
DROP TABLE IF EXISTS `logs_activite`;
CREATE TABLE `logs_activite` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe_id` int DEFAULT NULL,
  `lieu_id` int DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type d''action (connexion, acces_lieu, validation, etc.)',
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_equipe` (`equipe_id`),
  KEY `idx_lieu` (`lieu_id`),
  KEY `idx_action` (`action`),
  KEY `idx_date` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=229 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données de la table `logs_activite`
INSERT INTO `logs_activite` (`id`, `equipe_id`, `lieu_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
('116', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.11.62', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-12 20:48:27'),
('117', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.11.62', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-12 21:55:58'),
('118', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.11.62', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-12 22:01:27'),
('119', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.11.62', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-12 22:02:27'),
('120', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.11.62', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-12 22:03:06'),
('121', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.11.62', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-12 22:16:37'),
('122', NULL, NULL, 'acces_refuse', 'Token invalide: c95cbde4c507c34d0d8ddf09593bf380 pour lieu: direction', '192.168.11.62', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-12 22:56:19'),
('123', NULL, NULL, 'acces_refuse', 'Token invalide: c95cbde4c507c34d0d8ddf09593bf380 pour lieu: direction', '192.168.11.62', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-12 23:20:19'),
('124', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.11.62', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-12 23:21:05'),
('125', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.80', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 10:44:07'),
('126', '1', '5', 'enigme_resolue', '{\"lieu\":\"direction\",\"score_obtenu\":10,\"timestamp\":\"2025-08-13 08:44:21\"}', NULL, NULL, '2025-08-13 10:44:21'),
('127', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.80', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 10:51:02'),
('128', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.80', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 10:53:38'),
('129', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.0.135', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-13 12:05:15'),
('130', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.80', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 12:07:01'),
('131', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.0.80', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 12:24:21'),
('132', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.80', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 12:39:15'),
('133', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.0.150', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-13 12:42:49'),
('134', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 14:58:12'),
('135', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 15:02:09'),
('136', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 15:03:34'),
('137', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 15:18:55'),
('138', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 15:29:08'),
('139', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 15:30:59'),
('140', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 15:32:45'),
('141', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 15:33:52'),
('142', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 15:44:00'),
('143', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 15:49:01'),
('144', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 15:54:30'),
('145', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 15:57:59'),
('146', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 16:00:41'),
('147', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 16:06:24'),
('148', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 16:07:23'),
('149', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 16:30:20'),
('150', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 16:31:45'),
('151', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 16:49:09'),
('152', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 16:49:10'),
('153', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.150', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-13 17:07:00'),
('154', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 17:08:13'),
('155', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 17:08:19'),
('156', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 17:08:25'),
('157', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.150', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-13 17:08:54'),
('158', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.150', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-13 17:10:21'),
('159', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.150', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-13 17:11:13'),
('160', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 17:11:57'),
('161', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 17:12:05'),
('162', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 17:12:10'),
('163', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 17:12:21'),
('164', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 17:12:46'),
('165', '1', NULL, 'tentative_fraude', 'Équipe Rouge tente d\'utiliser le token de l\'équipe Bleu', '192.168.0.108', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-13 17:12:50'),
('166', NULL, NULL, 'tentative_fraude', 'Équipe Rouge tente d\'utiliser le token de l\'équipe Bleu', '192.168.1.41', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-14 09:13:46'),
('167', NULL, NULL, 'tentative_fraude', 'Équipe Rouge tente d\'utiliser le token de l\'équipe Bleu', '192.168.1.41', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-14 09:13:53'),
('168', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.1.41', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-14 09:13:58'),
('169', '1', '1', 'acces_premature', 'Ordre de visite non respecté', '192.168.1.41', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-14 09:15:10'),
('170', '1', '1', 'acces_premature', 'Ordre de visite non respecté', '192.168.1.41', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-14 09:15:16'),
('171', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.41', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-14 09:15:25'),
('172', '1', '5', 'enigme_resolue', '{\"lieu\":\"direction\",\"score_obtenu\":10,\"timestamp\":\"2025-08-14 07:15:39\"}', NULL, NULL, '2025-08-14 09:15:39'),
('173', NULL, NULL, 'acces_refuse', 'Token invalide: 4f7c26951a58e55fd7af76699a5968ec pour lieu: direction', '192.168.1.41', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-14 09:15:59'),
('174', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.1.41', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-14 09:16:07'),
('175', NULL, NULL, 'tentative_fraude', 'Équipe Rouge tente d\'utiliser le token de l\'équipe Bleu', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 09:27:16'),
('176', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 09:27:29'),
('177', '1', '5', 'enigme_resolue', '{\"lieu\":\"direction\",\"score_obtenu\":10,\"timestamp\":\"2025-08-14 07:27:51\"}', NULL, NULL, '2025-08-14 09:27:51'),
('178', '1', '3', 'acces_premature', 'Ordre de visite non respecté', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 09:28:30'),
('179', '1', '1', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 09:28:43'),
('180', NULL, NULL, 'acces_refuse', 'Token invalide: 4f7c26951a58e55fd7af76699a5968ec pour lieu: direction', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 10:00:55'),
('181', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 10:01:28'),
('182', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 10:11:49'),
('183', '1', '5', 'enigme_resolue', '{\"lieu\":\"direction\",\"score_obtenu\":10,\"timestamp\":\"2025-08-14 08:11:58\"}', NULL, NULL, '2025-08-14 10:11:58'),
('184', NULL, NULL, 'acces_refuse', 'Token invalide: 4f7c26951a58e55fd7af76699a5968ec pour lieu: direction', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 11:30:45'),
('185', NULL, NULL, 'acces_refuse', 'Token invalide: 4f7c26951a58e55fd7af76699a5968ec pour lieu: direction', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 11:30:56'),
('186', NULL, NULL, 'acces_refuse', 'Token invalide: 78a4c6aec50a2b6a212e6f7cdeffd0dd pour lieu: direction', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 11:31:37'),
('187', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 11:32:03'),
('188', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 11:32:09'),
('189', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 11:46:37'),
('190', '1', '5', 'enigme_resolue', '{\"lieu\":\"direction\",\"score_obtenu\":10,\"timestamp\":\"2025-08-14 09:46:59\"}', NULL, NULL, '2025-08-14 11:46:59'),
('191', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 11:51:31'),
('192', '1', '5', 'enigme_resolue', '{\"lieu\":\"direction\",\"score_obtenu\":10,\"timestamp\":\"2025-08-14 09:52:43\"}', NULL, NULL, '2025-08-14 11:52:43'),
('193', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 11:53:16'),
('194', '1', '5', 'enigme_resolue', '{\"lieu\":\"direction\",\"score_obtenu\":10,\"timestamp\":\"2025-08-14 09:53:57\"}', NULL, NULL, '2025-08-14 11:53:57'),
('195', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 12:03:46'),
('196', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 12:18:12'),
('197', '1', '5', 'enigme_resolue', '{\"lieu\":\"direction\",\"score_obtenu\":10,\"timestamp\":\"2025-08-14 10:19:12\"}', NULL, NULL, '2025-08-14 12:19:12'),
('198', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 12:20:11'),
('199', '1', '5', 'enigme_resolue', '{\"lieu\":\"direction\",\"score_obtenu\":10,\"timestamp\":\"2025-08-14 10:20:50\"}', NULL, NULL, '2025-08-14 12:20:50'),
('200', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 12:49:45'),
('201', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 12:54:59'),
('202', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 13:11:18'),
('203', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 13:45:38'),
('204', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 13:57:28'),
('205', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 14:06:39'),
('206', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 14:19:40'),
('207', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 14:21:07'),
('208', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 14:27:38'),
('209', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 14:34:45'),
('210', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 14:51:48'),
('211', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 14:56:06'),
('212', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 14:56:27'),
('213', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.70', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-14 15:12:18'),
('214', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.70', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-14 15:12:59'),
('215', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.70', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-14 15:13:26'),
('216', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 15:14:33'),
('217', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.70', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-14 15:30:39'),
('218', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-14 16:45:39'),
('219', '1', '5', 'acces_reussi', 'Accès validé via token', '192.168.1.70', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-14 16:47:04'),
('220', '1', '5', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-14 18:39:00'),
('221', '1', '5', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-14 19:38:29'),
('222', '1', '5', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 07:34:43'),
('223', '1', '1', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 08:18:54'),
('224', '1', '21', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 12:34:32'),
('225', '1', '22', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 12:48:33'),
('226', '1', '5', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 12:51:38'),
('227', '1', '22', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 12:55:13'),
('228', '1', '22', 'enigme_resolue', '{\"lieu\":\"testadrien\",\"score_obtenu\":10,\"timestamp\":\"2025-08-15 10:55:31\"}', NULL, NULL, '2025-08-15 12:55:31');

-- Structure de la table `parcours`
DROP TABLE IF EXISTS `parcours`;
CREATE TABLE `parcours` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe_id` int NOT NULL,
  `lieu_id` int NOT NULL,
  `ordre_visite` int NOT NULL COMMENT 'Ordre de visite pour cette équipe',
  `token_acces` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token unique pour accéder au lieu',
  `qr_code_generer` tinyint(1) DEFAULT '0',
  `statut` enum('en_attente','en_cours','termine','echec') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `temps_debut` timestamp NULL DEFAULT NULL COMMENT 'Début du timer',
  `temps_fin` timestamp NULL DEFAULT NULL COMMENT 'Fin du timer',
  `temps_ecoule` int DEFAULT '0' COMMENT 'Temps écoulé en secondes',
  `score_obtenu` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_acces` (`token_acces`),
  UNIQUE KEY `unique_equipe_lieu` (`equipe_id`,`lieu_id`),
  KEY `lieu_id` (`lieu_id`),
  KEY `idx_token` (`token_acces`),
  KEY `idx_statut` (`statut`),
  KEY `idx_ordre` (`ordre_visite`),
  CONSTRAINT `parcours_ibfk_1` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `parcours_ibfk_2` FOREIGN KEY (`lieu_id`) REFERENCES `lieux` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données de la table `parcours`
INSERT INTO `parcours` (`id`, `equipe_id`, `lieu_id`, `ordre_visite`, `token_acces`, `qr_code_generer`, `statut`, `temps_debut`, `temps_fin`, `temps_ecoule`, `score_obtenu`, `created_at`, `updated_at`) VALUES
('36', '2', '3', '1', '29703ea72cece31c057361349e70979f', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-11 21:55:58', '2025-08-14 13:45:34'),
('37', '2', '6', '2', 'ae792644225d3ce890a6d62380285cb3', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-11 21:56:06', '2025-08-11 21:56:06'),
('38', '2', '11', '3', 'a24d03769eb0cbc3e7ff73c87b59b07c', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-11 21:56:18', '2025-08-11 21:56:18'),
('39', '1', '5', '1', 'c849ec3bd64333169d317e6d40a62afb', '0', 'termine', '2025-08-15 12:51:38', '2025-08-14 12:20:50', '39', '10', '2025-08-11 21:56:28', '2025-08-15 12:54:36'),
('43', '1', '1', '2', '0ab33a9cd918a8807cb1649ef705ad36', '0', 'termine', '2025-08-15 08:18:54', NULL, '0', '0', '2025-08-14 09:14:41', '2025-08-15 12:54:44'),
('46', '1', '22', '3', '695547373ad8793a3a929020293091e9', '0', 'termine', '2025-08-15 12:55:13', '2025-08-15 12:55:31', '18', '10', '2025-08-15 12:47:21', '2025-08-15 12:55:31');

-- Structure de la table `resets_timers`
DROP TABLE IF EXISTS `resets_timers`;
CREATE TABLE `resets_timers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe_id` int DEFAULT NULL,
  `type_reset` enum('equipe','global') NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `equipe_id` (`equipe_id`),
  CONSTRAINT `resets_timers_ibfk_1` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Données de la table `resets_timers`
INSERT INTO `resets_timers` (`id`, `equipe_id`, `type_reset`, `timestamp`) VALUES
('4', '1', 'equipe', '2025-08-14 15:19:03'),
('5', NULL, 'global', '2025-08-14 17:21:26'),
('6', '1', 'equipe', '2025-08-14 17:50:14'),
('7', NULL, 'global', '2025-08-14 17:50:38'),
('8', '1', 'equipe', '2025-08-14 17:58:20'),
('9', '1', 'equipe', '2025-08-14 18:28:15'),
('10', '1', 'equipe', '2025-08-14 18:39:44'),
('11', '1', 'equipe', '2025-08-14 18:48:15'),
('12', '1', 'equipe', '2025-08-14 19:10:27'),
('13', '1', 'equipe', '2025-08-14 19:20:44'),
('14', NULL, 'global', '2025-08-14 19:39:33'),
('15', '1', 'equipe', '2025-08-15 07:43:00'),
('16', '1', 'equipe', '2025-08-15 07:54:22'),
('17', '1', 'equipe', '2025-08-15 07:56:33'),
('18', '1', 'equipe', '2025-08-15 08:00:50'),
('19', '1', 'equipe', '2025-08-15 08:04:34'),
('20', '1', 'equipe', '2025-08-15 08:07:11'),
('21', '1', 'equipe', '2025-08-15 08:11:14'),
('22', '1', 'equipe', '2025-08-15 08:14:50');

-- Structure de la table `sessions_jeu`
DROP TABLE IF EXISTS `sessions_jeu`;
CREATE TABLE `sessions_jeu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe_id` int NOT NULL,
  `lieu_id` int NOT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_validation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('active','terminee','expiree') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `temps_debut` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `temps_fin` timestamp NULL DEFAULT NULL,
  `temps_restant` int DEFAULT '0' COMMENT 'Temps restant en secondes',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `equipe_id` (`equipe_id`),
  KEY `lieu_id` (`lieu_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_token` (`token_validation`),
  KEY `idx_statut` (`statut`),
  CONSTRAINT `sessions_jeu_ibfk_1` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sessions_jeu_ibfk_2` FOREIGN KEY (`lieu_id`) REFERENCES `lieux` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données de la table `sessions_jeu`
INSERT INTO `sessions_jeu` (`id`, `equipe_id`, `lieu_id`, `session_id`, `token_validation`, `statut`, `temps_debut`, `temps_fin`, `temps_restant`, `created_at`, `updated_at`) VALUES
('35', '1', '1', '4e9911274aeab2450271d76d71965dcd', '534765990ebf93c2f026ba9cbc5f5971', 'active', '2025-08-12 20:48:27', NULL, '0', '2025-08-12 20:48:27', '2025-08-12 20:48:27'),
('36', '1', '1', '6a433a54755356d4f6724dac1b1aa466', 'b9a869cb4c1a01cae51c26071986508e', 'active', '2025-08-12 21:55:58', NULL, '0', '2025-08-12 21:55:58', '2025-08-12 21:55:58'),
('37', '1', '1', 'a665012b501fee0bac6937cf3e210766', '2bb43877fabfe15d5deac155aa5b284f', 'active', '2025-08-12 22:01:27', NULL, '0', '2025-08-12 22:01:27', '2025-08-12 22:01:27'),
('38', '1', '1', '9b1b3a2f21b9e94012e9f08597eb6efe', 'c877f864682e3f4ac7e814d7c66e534d', 'active', '2025-08-12 22:02:27', NULL, '0', '2025-08-12 22:02:27', '2025-08-12 22:02:27'),
('39', '1', '1', 'c88a6f96df80213f10c8b0e4b8f5af4f', '4f68fe61bf482f4c0e98e37f5f83fce4', 'active', '2025-08-12 22:03:06', NULL, '0', '2025-08-12 22:03:06', '2025-08-12 22:03:06'),
('40', '1', '1', '43214a84c80fd173befd6348aa90cb74', '6ddc34b3da504a4a6b97872047d9d2fa', 'active', '2025-08-12 22:16:37', NULL, '0', '2025-08-12 22:16:37', '2025-08-12 22:16:37'),
('41', '1', '5', '2008fd05a984b347d04b84c4116ff21a', '3796ae95ddcc8502c520465b0eb37ab7', 'active', '2025-08-12 23:21:05', NULL, '0', '2025-08-12 23:21:05', '2025-08-12 23:21:05'),
('42', '1', '5', '71e4811acdf50f294e26a5b7140c5e57', '36816c53c9f3e08b2aa99267684619f6', 'active', '2025-08-13 10:44:07', NULL, '0', '2025-08-13 10:44:07', '2025-08-13 10:44:07'),
('43', '1', '5', '0f035ebb16a3343de42b88b0da14e0d1', '33e36c2854dad8b3d840753b4517f000', 'active', '2025-08-13 10:51:02', NULL, '0', '2025-08-13 10:51:02', '2025-08-13 10:51:02'),
('44', '1', '5', 'e1e16c850d95b4ec4de212d766c5a14d', 'f6bd45bdbb1b4c7e481eeb243a546685', 'active', '2025-08-13 10:53:38', NULL, '0', '2025-08-13 10:53:38', '2025-08-13 10:53:38'),
('45', '1', '1', 'e5cf2c5c38428f2e47e8193db68e8e8a', '9e207041f72fb89aa575a5b1af45876e', 'active', '2025-08-13 12:05:15', NULL, '0', '2025-08-13 12:05:15', '2025-08-13 12:05:15'),
('46', '1', '5', '5e1d54d32563793fc5d21771d987be7b', '12113ec61bc8cd44a80b63ddc4eb9b6b', 'active', '2025-08-13 12:07:01', NULL, '0', '2025-08-13 12:07:01', '2025-08-13 12:07:01'),
('47', '1', '1', 'fc1a7dac47b2e4557147de67a39e2bdf', 'c5ff48cb672eecb1e1baca39843d5526', 'active', '2025-08-13 12:24:21', NULL, '0', '2025-08-13 12:24:21', '2025-08-13 12:24:21'),
('48', '1', '5', '45c3c0b0538a9b7716c9e16415cad7f2', '32d83df985a9b7f2e57b52db848bb920', 'active', '2025-08-13 12:39:15', NULL, '0', '2025-08-13 12:39:15', '2025-08-13 12:39:15'),
('49', '1', '1', '864199437a4726d94e1565ae24d0f5ce', '9caf33c10986cb78caa685a774d5c393', 'active', '2025-08-13 12:42:49', NULL, '0', '2025-08-13 12:42:49', '2025-08-13 12:42:49'),
('50', '1', '5', '7aeedaf2758c68cbf58f6fd6ac24f490', '7de0011e35cf5c102863d61067bf5d2e', 'active', '2025-08-13 14:58:12', NULL, '0', '2025-08-13 14:58:12', '2025-08-13 14:58:12'),
('51', '1', '5', '21ad513beba6a3741b81bf4a79c53213', 'ed9a326609aed4058ee25159efffd064', 'active', '2025-08-13 15:02:09', NULL, '0', '2025-08-13 15:02:09', '2025-08-13 15:02:09'),
('52', '1', '5', '5eefcae5c6f6faa78272c8e9ba787ea4', 'dbe50f29632efdf000e9ca0a4149507a', 'active', '2025-08-13 15:03:34', NULL, '0', '2025-08-13 15:03:34', '2025-08-13 15:03:34'),
('53', '1', '5', 'd43a93cb6c2d41c584ffd609280e4579', 'f6378b566faa02785b09d0fa39e41941', 'active', '2025-08-13 15:18:55', NULL, '0', '2025-08-13 15:18:55', '2025-08-13 15:18:55'),
('54', '1', '5', '100eaef350800da1552883263e9212f7', '6d5a7601bc7679f0be6035f96bb29ea6', 'active', '2025-08-13 15:29:08', NULL, '0', '2025-08-13 15:29:08', '2025-08-13 15:29:08'),
('55', '1', '5', '44ec0920afc669df4bb63929ed68f99e', '7f90fda2a5c7b06dc7ced6752cbed6e9', 'active', '2025-08-13 15:30:59', NULL, '0', '2025-08-13 15:30:59', '2025-08-13 15:30:59'),
('56', '1', '5', '038f2dad5c7044ff614b2e8d1be2a131', 'cd349bf77278ea76bf1c69122d7a282c', 'active', '2025-08-13 15:32:45', NULL, '0', '2025-08-13 15:32:45', '2025-08-13 15:32:45'),
('57', '1', '5', '93534232713853e32b821b4e082d2b4a', '7c223ddec1492f549efb4e033578c5d1', 'active', '2025-08-13 15:33:52', NULL, '0', '2025-08-13 15:33:52', '2025-08-13 15:33:52'),
('58', '1', '5', '8eaccefe8949ddfab2fc844f17fa975f', '5db56abfc5351600ebfff1ba037fcfc7', 'active', '2025-08-13 15:44:00', NULL, '0', '2025-08-13 15:44:00', '2025-08-13 15:44:00'),
('59', '1', '5', 'd5cc0451d955d0ab9ea5095ddee99814', '68c6120f9a4dd51e1694261855288735', 'active', '2025-08-13 15:49:01', NULL, '0', '2025-08-13 15:49:01', '2025-08-13 15:49:01'),
('60', '1', '5', 'cba0ca8be0cd94af7c3f5728777b5f85', '9da9c9ad5fe594a0281e251ea0fecac4', 'active', '2025-08-13 15:54:30', NULL, '0', '2025-08-13 15:54:30', '2025-08-13 15:54:30'),
('61', '1', '5', '7c93cdbfe378f87165af9a9385d9bad6', '887632351f5690a34b7bb4be68476c5e', 'active', '2025-08-13 15:57:59', NULL, '0', '2025-08-13 15:57:59', '2025-08-13 15:57:59'),
('62', '1', '5', 'f07758d0e8cabe8f840786f21167def6', '989c19017811ba91914a99ec479d7380', 'active', '2025-08-13 16:00:41', NULL, '0', '2025-08-13 16:00:41', '2025-08-13 16:00:41'),
('63', '1', '5', 'aaaa9a25160117678c5ab082cb9a2373', '0cf430154bcff95262c870a54f44c7b2', 'active', '2025-08-13 16:06:24', NULL, '0', '2025-08-13 16:06:24', '2025-08-13 16:06:24'),
('64', '1', '5', '96c0526a89a89987d039617cf10140d4', 'a7a2b03da0547bd4ffee366b56fd66a0', 'active', '2025-08-13 16:07:23', NULL, '0', '2025-08-13 16:07:23', '2025-08-13 16:07:23'),
('65', '1', '5', 'ec55db27aa14f28016a9948e7e834dce', '6d05470cf10e8605bad50fa73f7ca6a6', 'active', '2025-08-13 16:30:20', NULL, '0', '2025-08-13 16:30:20', '2025-08-13 16:30:20'),
('66', '1', '5', 'a1268730ac6907db6cad6e7efa14d39d', '6c84eca84b61fa028bb46a35eadf688c', 'active', '2025-08-13 16:31:45', NULL, '0', '2025-08-13 16:31:45', '2025-08-13 16:31:45'),
('67', '1', '5', '3b505ce05cbd5831fb86dccd55be85b2', '7299ee7677b3ae127bd795a4c45e7384', 'active', '2025-08-13 16:49:09', NULL, '0', '2025-08-13 16:49:09', '2025-08-13 16:49:09'),
('68', '1', '5', 'e697f8e30c79dec6b49657d728ce4e16', 'f3f60d86aee0bd300012cdaec332faf1', 'active', '2025-08-13 16:49:10', NULL, '0', '2025-08-13 16:49:10', '2025-08-13 16:49:10'),
('69', '1', '5', 'e9d17cecf574112d339ca4db86326edc', 'f4e72c32a939b2ad1e1e5925b4938636', 'active', '2025-08-13 17:07:00', NULL, '0', '2025-08-13 17:07:00', '2025-08-13 17:07:00'),
('70', '1', '5', 'd2dcec97d4c5b8f04db9654723a4b575', '82de6606345cdc1f8547f18f8b1305b4', 'active', '2025-08-13 17:08:13', NULL, '0', '2025-08-13 17:08:13', '2025-08-13 17:08:13'),
('71', '1', '5', '6b14d0dfe7f6118565a9a6ec4267616c', '6bed5ba8b5bce096d35b0ea019d1bc8a', 'active', '2025-08-13 17:08:19', NULL, '0', '2025-08-13 17:08:19', '2025-08-13 17:08:19'),
('72', '1', '1', 'c9af39f0ed69e44c08db581062b24a7e', '185f8957f90fe696e2fcd40363bf15b7', 'active', '2025-08-13 17:08:25', NULL, '0', '2025-08-13 17:08:25', '2025-08-13 17:08:25'),
('73', '1', '5', '053ecdcd7428ed87752b5667cf1045f1', 'f60bbecdf37ee2b04cf0451312c138d9', 'active', '2025-08-13 17:08:54', NULL, '0', '2025-08-13 17:08:54', '2025-08-13 17:08:54'),
('74', '1', '5', '7e40541f010e7d2bd963e037b1c0a73c', 'df24a7a6cabe23a0ab3f891b2d6baf93', 'active', '2025-08-13 17:10:21', NULL, '0', '2025-08-13 17:10:21', '2025-08-13 17:10:21'),
('75', '1', '5', '78d0a0b18e2b1aff86356dad8c2335d1', 'cd32eebb2cc99cb71c88e3339a0abf96', 'active', '2025-08-13 17:11:13', NULL, '0', '2025-08-13 17:11:13', '2025-08-13 17:11:13'),
('76', '1', '5', 'da42a1cbf3140102920fd53905c19e1e', '81169680c37dbebd89ccd8d5abe9bce1', 'active', '2025-08-13 17:11:57', NULL, '0', '2025-08-13 17:11:57', '2025-08-13 17:11:57'),
('77', '1', '5', '282e6ab37e4ae3a13dd8449f2b58828f', 'caba4cea078ac177777798c405c23472', 'active', '2025-08-13 17:12:05', NULL, '0', '2025-08-13 17:12:05', '2025-08-13 17:12:05'),
('78', '1', '1', '97bf3224a0b7318006e68c583fea65de', '5b076ca726cf5b1b6b0a5102ade8077f', 'active', '2025-08-13 17:12:10', NULL, '0', '2025-08-13 17:12:10', '2025-08-13 17:12:10'),
('79', '1', '1', '77a34bd36bc7627a3ff58ce923258a72', '9fba0cb632098ee96c8c5272de5c3f75', 'active', '2025-08-13 17:12:21', NULL, '0', '2025-08-13 17:12:21', '2025-08-13 17:12:21'),
('80', '1', '5', 'c39182216936673f706bbadef5906af3', '140b91082fd6247978156c57e0c00337', 'active', '2025-08-13 17:12:46', NULL, '0', '2025-08-13 17:12:46', '2025-08-13 17:12:46'),
('81', '1', '1', '14b62e93bd0e1395554f5223367c0fed', 'af4b33af0f02438f447b5936be0f48b6', 'active', '2025-08-14 09:13:58', NULL, '0', '2025-08-14 09:13:58', '2025-08-14 09:13:58'),
('82', '1', '5', '91d16ce31d1fabf8b2e25d07cb729f1d', '5ecb57732a6df14d07bb1a685ccd6717', 'active', '2025-08-14 09:15:25', NULL, '0', '2025-08-14 09:15:25', '2025-08-14 09:15:25'),
('83', '1', '1', '95e5bd3b55420aed0dbac1fd6fe0f098', '75e188fb6503038b995895c95cafeabc', 'active', '2025-08-14 09:16:07', NULL, '0', '2025-08-14 09:16:07', '2025-08-14 09:16:07'),
('84', '1', '5', '6903b96c60463b7f4df7edbc483ef417', '1e6e4a6d0d3df9a5ce876c11d07dda5d', 'active', '2025-08-14 09:27:29', NULL, '0', '2025-08-14 09:27:29', '2025-08-14 09:27:29'),
('85', '1', '1', '299a12dd453746058b5324bb4520542f', '8d7b8636968fd340a40f01f133b8920c', 'active', '2025-08-14 09:28:43', NULL, '0', '2025-08-14 09:28:43', '2025-08-14 09:28:43'),
('86', '1', '5', '2850cc13d4ebe3928b7ecc658dcae62b', '64f204ca0fe87a564514ed7c7264e67c', 'active', '2025-08-14 10:01:28', NULL, '0', '2025-08-14 10:01:28', '2025-08-14 10:01:28'),
('87', '1', '5', 'e55571a76625d2957282edd39e658cbe', 'a12b728e6b1d12237361850727d388af', 'active', '2025-08-14 10:11:49', NULL, '0', '2025-08-14 10:11:49', '2025-08-14 10:11:49'),
('88', '1', '5', '192857da2a677a5f5011e15f333fa710', '2a9aef15876f5c49b3a5af2b79e410f6', 'active', '2025-08-14 11:32:03', NULL, '0', '2025-08-14 11:32:03', '2025-08-14 11:32:03'),
('89', '1', '5', '60803368988b14cecebf2472c4384e28', '08516861213f9b3cfcf4cf7bceecb578', 'active', '2025-08-14 11:32:09', NULL, '0', '2025-08-14 11:32:09', '2025-08-14 11:32:09'),
('90', '1', '5', '3a4094a5dceed607814dc3d7c356e0da', 'cddaddaa1c0f1221552d951cbd28a318', 'active', '2025-08-14 11:46:37', NULL, '0', '2025-08-14 11:46:37', '2025-08-14 11:46:37'),
('91', '1', '5', '89c3a607c08b37d66b249b1e405f71f1', '5b8c314e9c22c6b927ac056fc39320ad', 'active', '2025-08-14 11:51:31', NULL, '0', '2025-08-14 11:51:31', '2025-08-14 11:51:31'),
('92', '1', '5', 'd0336c209b6981727a7e8320baf2491c', 'fd5d23c64f81a826f10d2b0a8c73cb7f', 'active', '2025-08-14 11:53:16', NULL, '0', '2025-08-14 11:53:16', '2025-08-14 11:53:16'),
('93', '1', '5', '2ee8491d7ec231d4da87a018cadbdf27', '792787aa0c916649437b40d345e7d759', 'active', '2025-08-14 12:03:46', NULL, '0', '2025-08-14 12:03:46', '2025-08-14 12:03:46'),
('94', '1', '5', '6f523bdc50c61c15de3d0d070080838b', 'bcc5704521659c27a4f310da9a616def', 'active', '2025-08-14 12:18:12', NULL, '0', '2025-08-14 12:18:12', '2025-08-14 12:18:12'),
('95', '1', '5', '2dca3f0acafda51c4dd474d22893d097', '2e21a3756eed35eda4e939d10d5c6ef9', 'active', '2025-08-14 12:20:11', NULL, '0', '2025-08-14 12:20:11', '2025-08-14 12:20:11'),
('96', '1', '5', '7473d28ad0b6619cc1b04e3829684e13', 'bfe0794d73117ca39ace3ed3619b3090', 'active', '2025-08-14 12:49:45', NULL, '0', '2025-08-14 12:49:45', '2025-08-14 12:49:45'),
('97', '1', '5', '6d949d98e2dfed265f74ce1726d03022', '86b1fbf27cb3cc026bd37299fd5d71ad', 'active', '2025-08-14 12:54:59', NULL, '0', '2025-08-14 12:54:59', '2025-08-14 12:54:59'),
('98', '1', '5', '40db83ee4f5cbe0510419be233a9f4f4', '51aee2493e5ee1161764ae3ae8b5f7c1', 'active', '2025-08-14 13:11:18', NULL, '0', '2025-08-14 13:11:18', '2025-08-14 13:11:18'),
('99', '1', '5', '493deece4698d445992ea28ddcaecbb1', '94ac3558968a666c521a7a1be11704e6', 'active', '2025-08-14 13:45:38', NULL, '0', '2025-08-14 13:45:38', '2025-08-14 13:45:38'),
('100', '1', '5', '75d8a8dfca57db6d39dcd1b99cb0b71b', 'a34bb0674e0894cb569d1dbd60a0f83b', 'active', '2025-08-14 13:57:28', NULL, '0', '2025-08-14 13:57:28', '2025-08-14 13:57:28'),
('101', '1', '5', 'b2a667d7b42fc547c3c00be56c99e5ea', 'bab28aee0bcde7ef87b8a4857706ffc9', 'active', '2025-08-14 14:06:39', NULL, '0', '2025-08-14 14:06:39', '2025-08-14 14:06:39'),
('102', '1', '5', '4e6d83589db614d1336a94eb33065264', 'e3e10889d1d09fe75debd5c294e58630', 'active', '2025-08-14 14:19:40', NULL, '0', '2025-08-14 14:19:40', '2025-08-14 14:19:40'),
('103', '1', '5', '51537582157bca03295cda766971eb38', '86e10cd588bae30813a3eb28b1e88aa1', 'active', '2025-08-14 14:21:07', NULL, '0', '2025-08-14 14:21:07', '2025-08-14 14:21:07'),
('104', '1', '5', 'a941d174f5eb7020448abd7dd85bfdff', 'bc911e2d4f3b01daac9fdec392c54963', 'active', '2025-08-14 14:27:38', NULL, '0', '2025-08-14 14:27:38', '2025-08-14 14:27:38'),
('105', '1', '5', '464fda032e3222324c43aa85b532133f', '5f5e19df08b4084ba4ccc58f863e1a75', 'active', '2025-08-14 14:34:45', NULL, '0', '2025-08-14 14:34:45', '2025-08-14 14:34:45'),
('106', '1', '5', 'cc1fe57653dfb28385fc187cd8bbaed5', '3e765404cdab7dc5c0e9648a0b6e8500', 'active', '2025-08-14 14:51:48', NULL, '0', '2025-08-14 14:51:48', '2025-08-14 14:51:48'),
('107', '1', '5', 'b3e8cd8d6ff8ca26d548563f75479619', '47a2f37d6aa5daf65d631ae723145566', 'active', '2025-08-14 14:56:06', NULL, '0', '2025-08-14 14:56:06', '2025-08-14 14:56:06'),
('108', '1', '5', '0cf0a04d06b21772e6f2a8231b122928', 'f3ea73df7039bd334c1164d1b0faee96', 'active', '2025-08-14 14:56:27', NULL, '0', '2025-08-14 14:56:27', '2025-08-14 14:56:27'),
('109', '1', '5', '76de2a8fe1b3542dfbd84decfa3822df', '7cea8debccb3b1b86938f4315ca6fac2', 'active', '2025-08-14 15:12:18', NULL, '0', '2025-08-14 15:12:18', '2025-08-14 15:12:18'),
('110', '1', '5', '6f9f97864f24c203468906894ca717e5', '234deeb850054835536b3f7055ef4244', 'active', '2025-08-14 15:12:59', NULL, '0', '2025-08-14 15:12:59', '2025-08-14 15:12:59'),
('111', '1', '5', '1d0a222a7ac578b4c0bbc00ae2768441', '6daddb654435160e6e0de4122f20eb6d', 'active', '2025-08-14 15:13:26', NULL, '0', '2025-08-14 15:13:26', '2025-08-14 15:13:26'),
('112', '1', '5', '6f46d9d98aba5558d8770ca3789f6e46', '1529367cf6d0ad61dba5a7369d604c16', 'active', '2025-08-14 15:14:33', NULL, '0', '2025-08-14 15:14:33', '2025-08-14 15:14:33'),
('113', '1', '5', '9168350f336b74474aded8427b9516d8', '18cac8354ea26fff767dab9eda1c4fe6', 'active', '2025-08-14 15:30:39', NULL, '0', '2025-08-14 15:30:39', '2025-08-14 15:30:39'),
('114', '1', '5', '811ee7b0d359b25b62d4ac86cb23bf9a', '39f1544527d85b7f8c0553462106de03', 'active', '2025-08-14 16:45:39', NULL, '0', '2025-08-14 16:45:39', '2025-08-14 16:45:39'),
('115', '1', '5', '398bc7a92a7c5ba652d38e3136f9334f', 'ffdf5e04420bc9814bc3dd605101b105', 'active', '2025-08-14 16:47:04', NULL, '0', '2025-08-14 16:47:04', '2025-08-14 16:47:04'),
('116', '1', '5', 'ef1c81658631805f1a95ff3438829714', '37499ffd0c3d2fa9b52538153885b178', 'active', '2025-08-14 18:39:00', NULL, '0', '2025-08-14 18:39:00', '2025-08-14 18:39:00'),
('117', '1', '5', '31c836d39759f2496ed667d06de1518f', 'f8443a49709803f56860bc4d6898d956', 'active', '2025-08-14 19:38:29', NULL, '0', '2025-08-14 19:38:29', '2025-08-14 19:38:29'),
('118', '1', '5', '1e231fd631464204a2fb187c80de1920', 'c5f2485c4fe0a51494f6c9c9440e25d3', 'active', '2025-08-15 07:34:43', NULL, '0', '2025-08-15 07:34:43', '2025-08-15 07:34:43'),
('119', '1', '1', 'fa129c019f106cfdb8f674cb1283e8ec', '7f81272629aa7fe27c5534eb8ac6c9c9', 'active', '2025-08-15 08:18:54', NULL, '0', '2025-08-15 08:18:54', '2025-08-15 08:18:54'),
('120', '1', '21', '6f91c6b754a2ebb0a98e269fa44190a5', '3fdf5041a49fc3bbd00ffa7f23752ae1', 'active', '2025-08-15 12:34:32', NULL, '0', '2025-08-15 12:34:32', '2025-08-15 12:34:32'),
('121', '1', '22', 'bbf28a255c5a00dd466c3377349d6b15', '27ccb98b70541aa1b115f61168bb19b9', 'active', '2025-08-15 12:48:33', NULL, '0', '2025-08-15 12:48:33', '2025-08-15 12:48:33'),
('122', '1', '5', '9c70dc35f31a0ae08d6ad59d5f8479d7', 'e44ccf5927911f6f72597a7e98e75591', 'active', '2025-08-15 12:51:38', NULL, '0', '2025-08-15 12:51:38', '2025-08-15 12:51:38'),
('123', '1', '22', '6ce59d5a812023cc5a8a0d6286d0dff3', '37f0c6dee5d667489a813e888245d2c1', 'active', '2025-08-15 12:55:13', NULL, '0', '2025-08-15 12:55:13', '2025-08-15 12:55:13');

-- Structure de la table `types_enigmes`
DROP TABLE IF EXISTS `types_enigmes`;
CREATE TABLE `types_enigmes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `template` varchar(100) NOT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Données de la table `types_enigmes`
INSERT INTO `types_enigmes` (`id`, `nom`, `description`, `template`, `actif`, `created_at`) VALUES
('1', 'QCM', 'Question à choix multiples avec 4 options', 'qcm', '1', '2025-08-14 10:09:39'),
('2', 'Texte Libre', 'Réponse libre à saisir', 'texte_libre', '1', '2025-08-14 10:09:39'),
('3', 'Calcul', 'Énigme mathématique', 'calcul', '1', '2025-08-14 10:09:39'),
('4', 'Image', 'Énigme basée sur une image', 'image', '1', '2025-08-14 10:09:39');

-- Structure de la table `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `teamName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`teamName`),
  KEY `idx_teamName` (`teamName`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données de la table `users`
INSERT INTO `users` (`id`, `teamName`, `password`, `email`, `created_at`, `updated_at`) VALUES
('2', 'rouge', '$2y$10$92H0D4fv/aI30D61rkYYqO0uj5LnGU4usHyY3t97caJD0odrLIQVu', 'rouge@cyberchasse.local', '2025-08-11 11:26:13', '2025-08-11 11:26:13');

SET FOREIGN_KEY_CHECKS=1;
