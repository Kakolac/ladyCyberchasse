# ÉTAPE 1 : Structure de Base de Données

## 📋 Sommaire
1. [Objectif](#objectif)
2. [Tables créées](#tables-créées)
3. [Structure détaillée](#structure-détaillée)
4. [Données de test](#données-de-test)
5. [Test de validation](#test-de-validation)
6. [Prochaines étapes](#prochaines-étapes)

## 🎯 Objectif
Créer la structure de base de données complète pour gérer les lieux, équipes et parcours de la cyberchasse.

## 🗄️ Tables créées

### 1. Table `equipes`
- **Rôle** : Stockage des informations des équipes participantes
- **Champs clés** : nom, couleur, mot_de_passe, statut, temps_total, score
- **Index** : nom, statut

### 2. Table `lieux`
- **Rôle** : Définition des lieux visitables dans la cyberchasse
- **Champs clés** : nom, slug (URL friendly), ordre, temps_limite, enigme_requise
- **Index** : slug, ordre, statut

### 3. Table `parcours`
- **Rôle** : Relation entre équipes et lieux avec ordre de visite
- **Champs clés** : equipe_id, lieu_id, ordre_visite, token_acces, statut, temps_debut/fin
- **Index** : token_acces, statut, ordre_visite
- **Contraintes** : Clés étrangères vers equipes et lieux

### 4. Table `sessions_jeu`
- **Rôle** : Gestion des sessions actives de jeu avec timers
- **Champs clés** : equipe_id, lieu_id, session_id, token_validation, temps_restant
- **Index** : session_id, token_validation, statut

### 5. Table `logs_activite`
- **Rôle** : Traçabilité des actions des équipes
- **Champs clés** : equipe_id, lieu_id, action, details, ip_address
- **Index** : equipe_id, lieu_id, action, created_at

## 🔧 Structure détaillée

### Relations entre tables
```
equipes (1) ←→ (N) parcours (N) ←→ (1) lieux
sessions_jeu ←→ equipes (N:1)
sessions_jeu ←→ lieux (N:1)
logs_activite ←→ equipes (N:1)
logs_activite ←→ lieux (N:1)
```

### Contraintes de sécurité
- Clés étrangères avec CASCADE DELETE
- Tokens uniques pour chaque accès
- Validation des sessions actives
- Traçabilité complète des actions

## 🧪 Données de test

### Équipes créées
- **Rouge** (mot de passe: Egour2023#!)
- **Bleu** (mot de passe: Uelb2023#!)
- **Vert** (mot de passe: Trev2023#!)
- **Jaune** (mot de passe: Enuaj2023#!)

### Lieux créés
20 lieux avec ordre de visite et temps limites configurés :
- Accueil (2 min)
- Cantine (5 min)
- CDI (7 min)
- Cour (3 min)
- Direction (6 min)
- Etc.

## ✅ Test de validation

### URL de test
```
http://localhost:8888/scripts/init_database.php
```

### Vérifications à effectuer
1. ✅ Connexion à MySQL réussie
2. ✅ Base de données 'cyberchasse' créée
3. ✅ 5 tables créées avec succès
4. ✅ 4 équipes insérées
5. ✅ 20 lieux insérés
6. ✅ Structure des tables conforme
7. ✅ Index et contraintes créés

### Résultat attendu
- Page d'initialisation avec design moderne
- Toutes les tables créées sans erreur
- Données de test insérées
- Statistiques affichées
- Message de succès final

## 🚀 Prochaines étapes

### ÉTAPE 2 : Système d'Authentification et Sessions
- Vérifier le système de sessions existant
- Tester la connexion des équipes
- Valider la gestion des sessions

### ÉTAPE 3 : Gestion des Lieux et Parcours
- Interface d'administration pour configurer les parcours
- Création des relations équipe-lieu
- Gestion de l'ordre de visite

##  Notes techniques

### Configuration requise
- MySQL/MariaDB avec support InnoDB
- PHP 7.4+ avec extension PDO
- Serveur web (Apache/Nginx) configuré

### Sécurité
- Mots de passe hashés avec PASSWORD_DEFAULT
- Tokens uniques pour chaque accès
- Validation des sessions côté serveur
- Logs d'activité pour audit

### Performance
- Index sur les champs de recherche fréquents
- Relations optimisées avec clés étrangères
- Structure normalisée pour éviter la redondance

##  Résumé de l'ÉTAPE 1

J'ai créé un script complet d'initialisation de la base de données qui :

✅ **Crée 5 tables essentielles** : equipes, lieux, parcours, sessions_jeu, logs_activite
✅ **Insère les données de test** : 4 équipes et 20 lieux existants
✅ **Configure les relations** : Clés étrangères et contraintes de sécurité
✅ **Interface visuelle moderne** : Design responsive et informatif
✅ **Documentation complète** : Guide détaillé dans `/docs`

###  URL de test
**http://localhost:8888/scripts/init_database.php**

Ce script peut être exécuté depuis votre navigateur et créera toute la structure nécessaire pour le système de cyberchasse. Une fois cette étape validée, nous pourrons passer à l'**ÉTAPE 2** : Système d'Authentification et Sessions.

Le script est maintenant prêt à être exécuté ! Voulez-vous que je procède à l'exécution ou préférez-vous d'abord le tester ?
