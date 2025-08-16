-- Script d'export/import pour la base de données Cyberchasse
-- Compatible avec mysql dump et import
-- Généré le : 2025-08-16 11:37:56
-- Version MySQL : 8.0.40

-- Configuration pour l'import
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Désactiver les vérifications de clés étrangères
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Structure de la table `enigmes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `enigmes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- --------------------------------------------------------
-- Contenu de la table `enigmes`
-- --------------------------------------------------------

LOCK TABLES `enigmes` WRITE;
/*!40000 ALTER TABLE `enigmes` DISABLE KEYS */;

INSERT INTO `enigmes` (`id`, `type_enigme_id`, `titre`, `donnees`, `actif`, `created_at`) VALUES
('19', '1', 'Énigme QCM', '{\"options\": {\"A\": \"Collecter toutes les données possibles\", \"B\": \"Partager les données avec tout le monde\", \"C\": \"Garder les données indéfiniment\", \"D\": \"Demander le consentement avant collecte\"}, \"question\": \"Quelle est la bonne pratique RGPD ?\", \"reponse_correcte\": \"D\"}', '1', '2025-08-14 10:09:39'),
('25', '2', 'Les gardiens alignés', '{\"titre\": \"Les gardiens alignés\", \"indice\": \"La salle à manger est baignée d’une lumière tamisée, filtrée par de lourds rideaux rouges. Sept pots à mesure anciens, rangés avec un soin presque cérémoniel, trônent en hauteur sur une large tablette de pierre. Du plus petit au plus grand, leurs parois usées reflètent une lueur chaude, comme si le feu avait laissé sa marque au fil des ans. Sur une plaque gravée, à demi effacée, on peut lire : « Cherche la matière qui unit la forme et le poids, et elle te guidera. »\", \"contexte\": \"La pièce s’ouvre devant vous, vaste et silencieuse, figée comme une photographie d’un autre siècle. Une longue table occupe presque tout l’espace, recouverte d’une nappe brodée dont les motifs s’estompent sous la poussière. De lourds rideaux rouges filtrent la lumière extérieure, laissant passer des rais dorés qui se posent sur les murs tapissés. Les meubles portent les marques d’un usage ancien : le bois est patiné, les poignées de métal sont tièdes au toucher, et l’air est chargé d’un parfum mêlant cire, poussière et quelque chose d’indéfinissable… un écho de chaleur ancienne. Des objets, soigneusement disposés, semblent veiller sur la pièce comme des témoins silencieux d’un rituel oublié. Pourtant, un détail vous attire : en hauteur, un alignement impeccable rompt la monotonie des lignes.\\n\\nAlors que vous avancez de quelques pas, le silence devient presque oppressant. Il y a dans cet endroit une précision, un ordre presque maniaque, comme si chaque élément avait été figé volontairement. Votre regard est instinctivement attiré vers cet alignement, sans que vous puissiez expliquer pourquoi. Un frisson vous parcourt : vous savez que quelque chose se cache ici, et qu’il vous observe autant que vous l’observez.\", \"question\": \"Quel est le mot-clé qui vous permettra de quitter la salle à manger ?\", \"reponse_correcte\": \"cuivre\", \"reponses_acceptees\": [\"cuivre\", \"le cuivre\"]}', '1', '2025-08-15 19:22:47'),
('26', '2', 'La Cuisine en folie', '{\"titre\": \"Les murmures du chaudron\", \"indice\": \"Sur le rebord du fourneau, gravé à même le métal, un proverbe ancien : « C’est dans le métal qui ne rouille pas que mijotent les secrets les plus sûrs. »\", \"contexte\": \"L’odeur est la première chose qui vous frappe en entrant : un mélange d’épices séchées, de bois brûlé et d’un parfum plus ancien, presque minéral. La pièce est encombrée d’ustensiles suspendus au plafond, oscillant imperceptiblement comme s’ils venaient d’être frôlés par une main invisible. La lumière pénètre par une petite fenêtre haute, découpant des carrés jaunes sur les carreaux du sol. Sur un plan de travail usé, des traces de farine dessinent des formes indistinctes, comme si quelqu’un avait tenté d’écrire un mot… puis s’était ravisé.\\n\\nAlors que vous avancez, votre regard est attiré par un objet sur une étagère basse : un petit pot à mesure en cuivre, identique à ceux que vous avez vus dans la pièce précédente. Ici, il est cabossé et terni, comme s’il avait servi à un usage bien plus rude. Plus vous observez la cuisine, plus le silence vous semble étrange, presque artificiel. Le fourneau, massif et noirci par le temps, paraît éteint depuis des années, mais une chaleur subtile émane encore de ses parois. Dans l’ombre, un vieux chaudron repose sur une plaque de fonte. Vous avez l’impression qu’il vous observe, ou qu’il garde quelque chose que vous ne devriez pas voir.\", \"question\": \"Quel est le mot-clé qui vous permettra de quitter la cuisine ?\", \"reponse_correcte\": \"acier\", \"reponses_acceptees\": [\"acier\", \"l\'acier\"]}', '1', '2025-08-15 20:13:40'),
('27', '2', 'Les fragments du papier blanc', '{\"titre\": \"Les fragments du papier blanc\", \"indice\": \"Le papier n’est pas toujours fait pour écrire. Ici, il manque à l’appel là où il est attendu.\", \"contexte\": \"Vous pénétrez dans un espace étroit, presque étouffant, où l’air semble figé depuis des années. Les murs, recouverts de carreaux ébréchés, renvoient une lumière pâle qui s’échappe d’une unique ampoule suspendue au plafond. Le sol, jonché de poussière et de petits débris, craque légèrement sous vos pas, amplifiant le silence pesant.\\n\\nUne odeur de pierre humide se mêle à celle, plus lointaine, de produits d’entretien oubliés. Le vieux réservoir fixé en hauteur laisse échapper un léger goutte-à-goutte régulier, comme une horloge patientant dans l’ombre. Sur un crochet rouillé, un cylindre de carton vide oscille doucement, comme s’il venait d’être effleuré. Les murs sont nus, mais un détail retient votre attention : sur une étagère poussiéreuse, un morceau de papier plié porte deux lettres tracées au crayon, partiellement effacées…\", \"question\": \"Quel est le mot-clé qui vous permettra de quitter les toilettes ?\", \"reponse_correcte\": \"PQ\", \"reponses_acceptees\": [\"pq\", \"PQ\", \"papier toilette\", \"papier-toilette\"]}', '1', '2025-08-15 20:47:35'),
('28', '2', 'La chance du joueur', '{\"titre\": \"La chance du joueur\", \"indice\": \"Trois symboles identiques s’alignent… et c’est la victoire. Mais encore faut-il dire le mot qui déclenche la récompense.\", \"contexte\": \"La porte grince doucement lorsque vous la poussez. Une odeur de poussière et de bois sec vous accueille, mêlée à une note sucrée, comme celle d’un bonbon oublié. La pièce est petite, presque étouffante sous le poids du temps. Un lit étroit, recouvert d’une couverture râpée, occupe un coin. Sur le sol, une poignée de billes colorées sont éparpillées, comme si une partie avait été interrompue brusquement.\\n\\nSur le bureau, un vieux jeu de société est encore ouvert. Les pions semblent avoir été figés en pleine partie, et une carte pliée repose à côté, laissant dépasser un symbole étrange. Une lumière faible traverse les rideaux jaunis, projetant des ombres mouvantes sur les murs où des posters défraîchis racontent un autre âge.\\n\\nUn détail attire votre regard : une tirelire en forme de machine à sous, légèrement ouverte, laissant apparaître l’éclat métallique d’une pièce à l’intérieur.\", \"question\": \"Quel est le mot-clé qui vous permettra de quitter la vieille chambre du petit garçon ?\", \"reponse_correcte\": \"jackpot\", \"reponses_acceptees\": [\"jackpot\", \"Jackpot\"]}', '1', '2025-08-15 20:52:26'),
('29', '2', 'fffffff', '{\"titre\": \"Les fragments dlmlkmlmmlmu papier blanc\", \"indice\": \"lllklll\", \"contexte\": \"Vous pénétrez dans un espace étroit, presque étouffant, où l’air semble figé depuis des ann…\", \"question\": \"lllll\", \"reponse_correcte\": \"PQ\", \"reponses_acceptees\": [\"pq\", \"PQ\", \"papier toilette\", \"papier-toilette\"]}', '1', '2025-08-16 12:30:08');

/*!40000 ALTER TABLE `enigmes` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Structure de la table `equipes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `equipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

-- --------------------------------------------------------
-- Contenu de la table `equipes`
-- --------------------------------------------------------

LOCK TABLES `equipes` WRITE;
/*!40000 ALTER TABLE `equipes` DISABLE KEYS */;

INSERT INTO `equipes` (`id`, `nom`, `couleur`, `mot_de_passe`, `statut`, `temps_total`, `score`, `created_at`, `updated_at`) VALUES
('1', 'Rouge', '#dc3545', '$2y$10$sFxvaJiVdtyjBPtmboisXOrTC5OjecI4Wom3mQDMTyRruxhetp/1S', 'active', '47039', '280', '2025-08-11 15:16:01', '2025-08-16 09:46:33'),
('2', 'Bleu', '#007bff', '$2y$10$pIjjjb5k0JIQdO/slq1S2uBEsKiQcSXCUrEdHADIIEcOPRi5DBr1S', 'active', '0', '0', '2025-08-11 15:16:01', '2025-08-11 21:44:24'),
('3', 'Vert', '#28a745', '$2y$10$80Vd/TPlMaA3g25j5hG.POz7tZBoMKky/n3e4CZguL.LVTkezonq2', 'active', '0', '0', '2025-08-11 15:16:01', '2025-08-11 21:44:24'),
('4', 'Jaune', '#ffc107', '$2y$10$tuqFM4ZhIAKXGRdUGYE36eb3eJYdz8XFTRFyV//0Q3a5lpYleyQLG', 'active', '0', '0', '2025-08-11 15:16:01', '2025-08-11 21:44:24');

/*!40000 ALTER TABLE `equipes` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Structure de la table `indices_consultes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `indices_consultes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- --------------------------------------------------------
-- Contenu de la table `indices_consultes`
-- --------------------------------------------------------

LOCK TABLES `indices_consultes` WRITE;
/*!40000 ALTER TABLE `indices_consultes` DISABLE KEYS */;

INSERT INTO `indices_consultes` (`id`, `equipe_id`, `lieu_id`, `enigme_id`, `timestamp`) VALUES
('36', '1', '31', '25', '2025-08-16 12:11:14'),
('37', '1', '34', '26', '2025-08-16 12:18:44');

/*!40000 ALTER TABLE `indices_consultes` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Structure de la table `indices_forces`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `indices_forces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

-- --------------------------------------------------------
-- Structure de la table `lieux`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `lieux`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- --------------------------------------------------------
-- Contenu de la table `lieux`
-- --------------------------------------------------------

LOCK TABLES `lieux` WRITE;
/*!40000 ALTER TABLE `lieux` DISABLE KEYS */;

INSERT INTO `lieux` (`id`, `nom`, `slug`, `description`, `ordre`, `temps_limite`, `enigme_requise`, `statut`, `created_at`, `updated_at`, `reponse_enigme`, `enigme_texte`, `options_enigme`, `enigme_id`, `delai_indice`) VALUES
('31', 'SalleAManger', 'salleamanger', 'La pièce s’ouvre devant vous, vaste et silencieuse, figée comme une photographie d’un autre siècle.
Une longue table occupe presque tout l’espace, recouverte d’une nappe brodée dont les motifs s’estompent sous la poussière. De lourds rideaux rouges filtrent la lumière extérieure, laissant passer des rais dorés qui se posent sur les murs tapissés.
Les meubles portent les marques d’un usage ancien : le bois est patiné, les poignées de métal sont tièdes au toucher, et l’air est chargé d’un parfum mêlant cire, poussière et quelque chose d’indéfinissable… un écho de chaleur ancienne.
Des objets, soigneusement disposés, semblent veiller sur la pièce comme des témoins silencieux d’un rituel oublié. Pourtant, un détail vous attire : là, en hauteur, un alignement impeccable rompt la monotonie des lignes.
Peut-être que la clé de votre avancée ne réside pas dans ce que vous voyez au premier regard, mais dans ce que vous comprenez de ce qui est resté inchangé au fil du temps.', '1', '300', '0', 'actif', '2025-08-15 19:11:31', '2025-08-16 12:10:49', 'B', NULL, NULL, '25', '1'),
('34', 'Cuisine', 'cuisine', 'La pièce vous accueille dans une pénombre parfumée d’épices oubliées et de bois ancien. L’espace est plus étroit que la salle précédente, mais chaque centimètre semble chargé d’histoires. Des ustensiles patinés par le temps pendent au-dessus de votre tête, oscillant doucement comme s’ils avaient gardé en mémoire un geste ancien.

La lumière, filtrée par une petite fenêtre haute, découpe des carrés pâles sur les carreaux du sol, révélant la poussière en suspension. Un large fourneau, noirci par des années de cuisson, trône au fond de la pièce. Sur ses parois émane encore une chaleur discrète, comme si la dernière préparation avait été faite il y a quelques instants… ou il y a des décennies.

Sur les plans de travail en bois usé, des traces diffuses de farine dessinent des motifs presque effacés, peut-être des lettres ou des symboles que seul un œil attentif saurait reconstituer. Entre les odeurs, les ombres et ce silence presque calculé, vous avez la sensation que cette cuisine ne dort pas vraiment… elle attend.', '1', '300', '0', 'actif', '2025-08-15 20:44:20', '2025-08-16 12:17:19', 'B', NULL, NULL, '26', '1'),
('35', 'Toilettes', 'toilettes', 'Vous pénétrez dans un espace étroit, presque étouffant, où l’air semble figé depuis des années. Les murs, recouverts de carreaux ébréchés, renvoient une lumière pâle qui s’échappe d’une unique ampoule suspendue au plafond. Le sol, jonché de poussière et de petits débris, craque légèrement sous vos pas, amplifiant le silence pesant.

Une odeur de pierre humide se mêle à celle, plus lointaine, de produits d’entretien oubliés. Le vieux réservoir fixé en hauteur laisse échapper un léger goutte-à-goutte régulier, comme une horloge patientant dans l’ombre.

Sur la cuvette en porcelaine ternie, un couvercle fendu laisse entrevoir un reflet métallique, aussitôt englouti dans la pénombre. Les murs sont nus, mais un détail retient votre attention : une minuscule fissure verticale, si fine qu’elle pourrait passer pour une ombre. Vous sentez qu’il y a ici quelque chose qui n’appartient pas à une simple pièce d’eau… quelque chose qui attend d’être découvert.', '1', '300', '0', 'actif', '2025-08-15 20:46:16', '2025-08-16 12:17:26', 'B', NULL, NULL, '27', '1'),
('36', 'Vieille chambre du petit garçon', 'vieille_chambre_du_petit_garon', 'La porte grince doucement lorsque vous la poussez. Une odeur de poussière et de bois sec vous accueille, mêlée à une note sucrée, comme celle d’un bonbon oublié. La pièce est petite, presque étouffante sous le poids du temps. Un lit étroit, recouvert d’une couverture râpée, occupe un coin. Sur le sol, une poignée de billes colorées sont éparpillées, comme si une partie avait été interrompue brusquement.

Sur le bureau, un vieux jeu de société est encore ouvert. Les pions semblent avoir été figés en pleine partie, et une carte pliée repose à côté, laissant dépasser un symbole étrange. Une lumière faible traverse les rideaux jaunis, projetant des ombres mouvantes sur les murs où des posters défraîchis racontent un autre âge.

Un détail attire votre regard : une tirelire en forme de machine à sous, légèrement ouverte, laissant apparaître l’éclat métallique d’une pièce à l’intérieur.', '1', '300', '0', 'actif', '2025-08-15 20:51:56', '2025-08-15 20:57:01', 'B', NULL, NULL, '28', '1'),
('37', 'cave', 'cave', 'fgfdgfdgdfgdfg
gf
gfd
gfd
gfd
g
fdg
fdg
fdg', '1', '300', '0', 'actif', '2025-08-16 12:26:53', '2025-08-16 12:30:18', 'B', NULL, NULL, '29', '1');

/*!40000 ALTER TABLE `lieux` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Structure de la table `logs_activite`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `logs_activite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=273 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- --------------------------------------------------------
-- Contenu de la table `logs_activite`
-- --------------------------------------------------------

LOCK TABLES `logs_activite` WRITE;
/*!40000 ALTER TABLE `logs_activite` DISABLE KEYS */;

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
('228', '1', '22', 'enigme_resolue', '{\"lieu\":\"testadrien\",\"score_obtenu\":10,\"timestamp\":\"2025-08-15 10:55:31\"}', NULL, NULL, '2025-08-15 12:55:31'),
('229', NULL, NULL, 'acces_refuse', 'Token invalide: c849ec3bd64333169d317e6d40a62afb pour lieu: direction', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 14:32:20'),
('230', '1', '5', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 14:33:12'),
('231', '1', '5', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 18:59:40'),
('232', '1', '5', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 19:00:23'),
('233', '1', NULL, 'tentative_fraude', 'Équipe Rouge tente d\'utiliser le token de l\'équipe Bleu', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 19:16:23'),
('234', '1', '31', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 19:16:50'),
('235', '1', '32', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 20:15:00'),
('236', '1', '32', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 20:17:36'),
('237', '1', '32', 'acces_premature', 'Ordre de visite non respecté', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 20:24:49'),
('238', '1', '31', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 20:25:05'),
('239', '1', '31', 'enigme_resolue', '{\"lieu\":\"salleamanger\",\"score_obtenu\":10,\"timestamp\":\"2025-08-15 18:25:12\"}', NULL, NULL, '2025-08-15 20:25:12'),
('240', NULL, NULL, 'acces_refuse', 'Token invalide: c01da916728def3c9b3fe503be0eb5c4 pour lieu: cuisine', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 20:25:44'),
('241', NULL, NULL, 'acces_refuse', 'Token invalide: c01da916728def3c9b3fe503be0eb5c4 pour lieu: cuisine', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 20:27:06'),
('242', '1', '32', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 20:29:12'),
('243', '1', '33', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 20:38:21'),
('244', '1', '36', 'acces_premature', 'Ordre de visite non respecté', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 20:54:23'),
('245', '1', '36', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-15 20:54:55'),
('246', '1', '36', 'enigme_resolue', '{\"lieu\":\"vieille_chambre_du_petit_garon\",\"score_obtenu\":10,\"timestamp\":\"2025-08-15 18:55:29\"}', NULL, NULL, '2025-08-15 20:55:29'),
('247', '1', '31', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-16 09:10:11'),
('248', '1', '31', 'enigme_resolue', '{\"lieu\":\"salleamanger\",\"score_obtenu\":10,\"timestamp\":\"2025-08-16 07:10:32\"}', NULL, NULL, '2025-08-16 09:10:32'),
('249', '1', '34', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-16 09:10:44'),
('250', '1', '34', 'enigme_resolue', '{\"lieu\":\"cuisine\",\"score_obtenu\":10,\"timestamp\":\"2025-08-16 07:11:07\"}', NULL, NULL, '2025-08-16 09:11:07'),
('251', '1', '36', 'acces_premature', 'Ordre de visite non respecté', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-16 09:11:19'),
('252', '1', NULL, 'tentative_fraude', 'Équipe Rouge tente d\'utiliser le token de l\'équipe Bleu', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-16 09:11:36'),
('253', '1', NULL, 'tentative_fraude', 'Équipe Rouge tente d\'utiliser le token de l\'équipe Bleu', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-16 09:11:39'),
('254', '1', '35', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-16 09:11:49'),
('255', '1', '35', 'enigme_resolue', '{\"lieu\":\"toilettes\",\"score_obtenu\":10,\"timestamp\":\"2025-08-16 07:12:15\"}', NULL, NULL, '2025-08-16 09:12:15'),
('256', '1', '36', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-16 09:12:33'),
('257', '1', '36', 'enigme_resolue', '{\"lieu\":\"vieille_chambre_du_petit_garon\",\"score_obtenu\":10,\"timestamp\":\"2025-08-16 07:12:48\"}', NULL, NULL, '2025-08-16 09:12:48'),
('258', '1', '36', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-16 09:26:36'),
('259', '1', '36', 'enigme_resolue', '{\"lieu\":\"vieille_chambre_du_petit_garon\",\"score_obtenu\":10,\"timestamp\":\"2025-08-16 07:27:06\"}', NULL, NULL, '2025-08-16 09:27:06'),
('260', '1', '36', 'enigme_resolue', '{\"lieu\":\"vieille_chambre_du_petit_garon\",\"score_obtenu\":10,\"timestamp\":\"2025-08-16 07:32:33\"}', NULL, NULL, '2025-08-16 09:32:33'),
('261', '1', '36', 'enigme_resolue', '{\"lieu\":\"vieille_chambre_du_petit_garon\",\"score_obtenu\":10,\"timestamp\":\"2025-08-16 07:38:05\"}', NULL, NULL, '2025-08-16 09:38:05'),
('262', '1', '36', 'enigme_resolue', '{\"lieu\":\"vieille_chambre_du_petit_garon\",\"score_obtenu\":10,\"timestamp\":\"2025-08-16 07:46:33\"}', NULL, NULL, '2025-08-16 09:46:33'),
('263', '1', '31', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-16 11:07:58'),
('264', '1', '31', 'acces_reussi', 'Accès validé via token', '192.168.1.41', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-16 12:06:16'),
('265', '1', '34', 'acces_reussi', 'Accès validé via token', '192.168.1.41', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-16 12:15:35'),
('266', '1', '35', 'acces_reussi', 'Accès validé via token', '192.168.1.41', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-16 12:20:57'),
('267', NULL, NULL, 'acces_refuse', 'Token invalide: 7a4c8cdf3d5628552da2f3811fe4e8b0 pour lieu: salleamanger', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-16 12:21:41'),
('268', NULL, NULL, 'acces_refuse', 'Token invalide: 7a4c8cdf3d5628552da2f3811fe4e8b0 pour lieu: salleamanger', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-16 12:22:21'),
('269', '2', '31', 'acces_reussi', 'Accès validé via token', '192.168.1.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-16 12:23:11'),
('270', '1', '36', 'acces_reussi', 'Accès validé via token', '192.168.1.41', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1', '2025-08-16 12:23:57'),
('271', '1', '37', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-16 12:33:52'),
('272', '1', '37', 'acces_reussi', 'Accès validé via token', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-16 13:34:48');

/*!40000 ALTER TABLE `logs_activite` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Structure de la table `parcours`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `parcours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parcours` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe_id` int NOT NULL,
  `lieu_id` int NOT NULL,
  `ordre_visite` int NOT NULL COMMENT 'Ordre de visite pour cette équipe',
  `token_acces` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token unique pour accéder au lieu',
  `qr_code_generer` tinyint(1) DEFAULT '0',
  `statut` enum('en_attente','en_cours','termine','echec','parcours_termine') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- --------------------------------------------------------
-- Contenu de la table `parcours`
-- --------------------------------------------------------

LOCK TABLES `parcours` WRITE;
/*!40000 ALTER TABLE `parcours` DISABLE KEYS */;

INSERT INTO `parcours` (`id`, `equipe_id`, `lieu_id`, `ordre_visite`, `token_acces`, `qr_code_generer`, `statut`, `temps_debut`, `temps_fin`, `temps_ecoule`, `score_obtenu`, `created_at`, `updated_at`) VALUES
('47', '2', '31', '1', 'e75561bb4bf8dd01f79124a74a24c232', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-15 19:12:19', '2025-08-16 12:28:13'),
('48', '4', '31', '1', '730b6cab0a9c6cd752d699f76f32f10e', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-15 19:12:19', '2025-08-15 19:12:19'),
('49', '1', '31', '1', '7a4c8cdf3d5628552da2f3811fe4e8b0', '0', 'termine', NULL, NULL, '0', '0', '2025-08-15 19:12:19', '2025-08-16 12:32:43'),
('50', '3', '31', '1', '23b5cfa6ea5d7214368cc4f64bf2f0bc', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-15 19:12:19', '2025-08-15 19:12:19'),
('59', '2', '34', '2', 'f54a6e43b5e77d32e7ca13b9649b3e5c', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-15 20:52:38', '2025-08-15 20:52:38'),
('60', '2', '35', '3', 'ee297d55bd733b6f1b29d061bb80df3c', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-15 20:52:38', '2025-08-15 20:52:38'),
('61', '2', '36', '4', '9a42247e6433ecea82fc78268ff2b689', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-15 20:52:38', '2025-08-15 20:52:38'),
('62', '4', '34', '2', 'c603cc106d0f881e1a934b3fa1102272', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-15 20:52:38', '2025-08-15 20:52:38'),
('63', '4', '35', '3', '92dbfea7203e6ac8bf0c259bd7683918', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-15 20:52:38', '2025-08-15 20:52:38'),
('64', '4', '36', '4', 'cc045a7583457a2b2ab51302adb7a3de', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-15 20:52:38', '2025-08-15 20:52:38'),
('65', '1', '34', '2', '118efbe7982d72cee56aeeb1a808fd97', '0', 'termine', NULL, NULL, '0', '0', '2025-08-15 20:52:38', '2025-08-16 12:32:49'),
('66', '1', '35', '3', '8d1b4eb0cf6bed4280ed4babd8b8619d', '0', 'termine', NULL, NULL, '0', '0', '2025-08-15 20:52:38', '2025-08-16 12:32:54'),
('67', '1', '36', '4', 'f444358b3d6ff0360b1a177d710bbed0', '0', 'termine', NULL, NULL, '0', '0', '2025-08-15 20:52:38', '2025-08-16 12:33:00'),
('68', '3', '34', '2', 'ede8e6b1c602e0c4b0b660bf8d719015', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-15 20:52:38', '2025-08-15 20:52:38'),
('69', '3', '35', '3', '0b3d0a28570efda2c02fef48554fb2aa', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-15 20:52:38', '2025-08-15 20:52:38'),
('70', '3', '36', '4', 'b814736484adc30c7c96afef3057ff1a', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-15 20:52:38', '2025-08-15 20:52:38'),
('75', '2', '37', '5', 'cbbf7a14859a468e0cd2ab96d863b986', '0', 'en_attente', NULL, NULL, '0', '0', '2025-08-16 12:27:26', '2025-08-16 12:27:26'),
('76', '1', '37', '5', '730716a7bdce3c79ed6b005917ef8961', '0', 'en_cours', '2025-08-16 12:33:52', NULL, '0', '0', '2025-08-16 12:31:46', '2025-08-16 12:33:52');

/*!40000 ALTER TABLE `parcours` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Structure de la table `resets_timers`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `resets_timers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resets_timers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe_id` int DEFAULT NULL,
  `type_reset` enum('equipe','global') NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `equipe_id` (`equipe_id`),
  CONSTRAINT `resets_timers_ibfk_1` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- --------------------------------------------------------
-- Contenu de la table `resets_timers`
-- --------------------------------------------------------

LOCK TABLES `resets_timers` WRITE;
/*!40000 ALTER TABLE `resets_timers` DISABLE KEYS */;

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
('22', '1', 'equipe', '2025-08-15 08:14:50'),
('23', '1', 'equipe', '2025-08-15 19:04:24'),
('24', '1', 'equipe', '2025-08-15 19:05:43'),
('25', '1', 'equipe', '2025-08-15 19:07:04');

/*!40000 ALTER TABLE `resets_timers` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Structure de la table `sessions_jeu`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `sessions_jeu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- --------------------------------------------------------
-- Contenu de la table `sessions_jeu`
-- --------------------------------------------------------

LOCK TABLES `sessions_jeu` WRITE;
/*!40000 ALTER TABLE `sessions_jeu` DISABLE KEYS */;

INSERT INTO `sessions_jeu` (`id`, `equipe_id`, `lieu_id`, `session_id`, `token_validation`, `statut`, `temps_debut`, `temps_fin`, `temps_restant`, `created_at`, `updated_at`) VALUES
('127', '1', '31', 'dc5942713950b442bab38ddba86f6016', '2767def15758fb7b119e1a80dda6111f', 'active', '2025-08-15 19:16:50', NULL, '0', '2025-08-15 19:16:50', '2025-08-15 19:16:50'),
('130', '1', '31', '816dac4f8d9dc2a454f5797420fb1b10', 'a1e0d4e8ec95eb2021361dd778c68325', 'active', '2025-08-15 20:25:05', NULL, '0', '2025-08-15 20:25:05', '2025-08-15 20:25:05'),
('133', '1', '36', '54f6c4ee7a887481060d2afadb52b9a7', '83b1e104154849a0f1d5292d2453ebe8', 'active', '2025-08-15 20:54:55', NULL, '0', '2025-08-15 20:54:55', '2025-08-15 20:54:55'),
('134', '1', '31', 'a0229a341c5f1a5e169de2a0e2a601e3', 'a9b4addaf0b5ee5067f77b26e8a5b213', 'active', '2025-08-16 09:10:11', NULL, '0', '2025-08-16 09:10:11', '2025-08-16 09:10:11'),
('135', '1', '34', 'd7331fa6dff05d25fde9e8fa0d7103e1', '7fa092a18ea0ebf3907e6957f16a9c0f', 'active', '2025-08-16 09:10:44', NULL, '0', '2025-08-16 09:10:44', '2025-08-16 09:10:44'),
('136', '1', '35', '2945fbb507da743db6e77a741889980a', '9573d1b18af37a3783d5fbc495e0c20e', 'active', '2025-08-16 09:11:49', NULL, '0', '2025-08-16 09:11:49', '2025-08-16 09:11:49'),
('137', '1', '36', '5b01a8c81eb569d1b6bf51cbc5e1aa2a', '6bbd5862fb26a655abeb075cb9c62a53', 'active', '2025-08-16 09:12:33', NULL, '0', '2025-08-16 09:12:33', '2025-08-16 09:12:33'),
('138', '1', '36', 'c942b51ca506ad0ca13e91e433f2b629', 'dec2dc35f67b25853a0129c2f46bd703', 'active', '2025-08-16 09:26:36', NULL, '0', '2025-08-16 09:26:36', '2025-08-16 09:26:36'),
('139', '1', '31', '8799f5ed7e5eb5a18974e0f4e1edba0b', '109476700b7310389b9f515963721385', 'active', '2025-08-16 11:07:58', NULL, '0', '2025-08-16 11:07:58', '2025-08-16 11:07:58'),
('140', '1', '31', '8b76b62de47732a0fb402e2d5de92a04', 'f976c8d168be0138547e7c8c89ea7e8a', 'active', '2025-08-16 12:06:15', NULL, '0', '2025-08-16 12:06:15', '2025-08-16 12:06:15'),
('141', '1', '34', 'f60be907569c55bc5954833125bbc479', '9d4b971c2f2bce8a3e49841384de48fe', 'active', '2025-08-16 12:15:35', NULL, '0', '2025-08-16 12:15:35', '2025-08-16 12:15:35'),
('142', '1', '35', '6015d4b78756510deb9e76bbdd0cdd94', 'aa3c660ecfd81aabdefd4bd8c18a4c44', 'active', '2025-08-16 12:20:57', NULL, '0', '2025-08-16 12:20:57', '2025-08-16 12:20:57'),
('143', '2', '31', '45f95c2131c1837922f86c0f19ea800e', 'e63b4f73af96d463d339cb61492fa9bc', 'active', '2025-08-16 12:23:11', NULL, '0', '2025-08-16 12:23:11', '2025-08-16 12:23:11'),
('144', '1', '36', '7a09c20bc4a14feb7b5ade9a69eb0f5e', '893a32e37452c653a5a4e985b0af641f', 'active', '2025-08-16 12:23:57', NULL, '0', '2025-08-16 12:23:57', '2025-08-16 12:23:57'),
('145', '1', '37', '7054b5fdeca910dfe3fe28049dd1db9c', '191599275cc7ba42b504ec33cde1dd61', 'active', '2025-08-16 12:33:52', NULL, '0', '2025-08-16 12:33:52', '2025-08-16 12:33:52'),
('146', '1', '37', '704d28cb7e6c84b42e43c0c8c14a0306', '25dc4fc55309cf60ddfa502adf783307', 'active', '2025-08-16 13:34:48', NULL, '0', '2025-08-16 13:34:48', '2025-08-16 13:34:48');

/*!40000 ALTER TABLE `sessions_jeu` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Structure de la table `types_enigmes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `types_enigmes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `types_enigmes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `template` varchar(100) NOT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- --------------------------------------------------------
-- Contenu de la table `types_enigmes`
-- --------------------------------------------------------

LOCK TABLES `types_enigmes` WRITE;
/*!40000 ALTER TABLE `types_enigmes` DISABLE KEYS */;

INSERT INTO `types_enigmes` (`id`, `nom`, `description`, `template`, `actif`, `created_at`) VALUES
('1', 'QCM', 'Question à choix multiples avec 4 options', 'qcm', '1', '2025-08-14 10:09:39'),
('2', 'Texte Libre', 'Réponse libre à saisir', 'texte_libre', '1', '2025-08-14 10:09:39'),
('3', 'Calcul', 'Énigme mathématique', 'calcul', '1', '2025-08-14 10:09:39'),
('4', 'Image', 'Énigme basée sur une image', 'image', '1', '2025-08-14 10:09:39');

/*!40000 ALTER TABLE `types_enigmes` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Structure de la table `users`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

-- --------------------------------------------------------
-- Contenu de la table `users`
-- --------------------------------------------------------

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `teamName`, `password`, `email`, `created_at`, `updated_at`) VALUES
('2', 'rouge', '$2y$10$92H0D4fv/aI30D61rkYYqO0uj5LnGU4usHyY3t97caJD0odrLIQVu', 'rouge@cyberchasse.local', '2025-08-11 11:26:13', '2025-08-11 11:26:13');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

-- Réactiver les vérifications de clés étrangères
SET FOREIGN_KEY_CHECKS = 1;

-- Validation de la transaction
COMMIT;

-- Script terminé avec succès
