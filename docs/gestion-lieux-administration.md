# Gestion des Lieux - Interface d'Administration

## Sommaire
1. [Vue d'ensemble](#vue-densemble)
2. [Fonctionnalités](#fonctionnalités)
3. [Utilisation](#utilisation)
4. [Structure des fichiers générés](#structure-des-fichiers-générés)
5. [Base de données](#base-de-données)
6. [Sécurité](#sécurité)

## Vue d'ensemble

La fonctionnalité de gestion des lieux permet aux administrateurs de créer de nouveaux lieux complets et fonctionnels en quelques clics. Chaque lieu créé inclut automatiquement tous les fichiers nécessaires (PHP, CSS, structure) basés sur le template du lieu "direction".

## Fonctionnalités

### ✨ Création automatique complète
- **Répertoire** : Création automatique du dossier `lieux/[slug]/`
- **Fichiers** : Génération de tous les fichiers nécessaires
- **Base de données** : Ajout automatique dans les tables `lieux` et `enigmes`
- **Fonctionnalité immédiate** : Le lieu est opérationnel dès sa création

### 🎯 Types d'énigmes supportés
- **QCM** : Questions à choix multiples (actuellement implémenté)
- **Texte libre** : Réponses textuelles (préparé pour l'avenir)
- **Code** : Énigmes de programmation (préparé pour l'avenir)
- **Image** : Énigmes visuelles (préparé pour l'avenir)

### �� Personnalisation
- Nom du lieu
- Slug unique (identifiant URL)
- Ordre dans le parcours
- Question de l'énigme
- 4 réponses possibles
- Réponse correcte

## Utilisation

### 1. Accès à l'interface
```
http://localhost:8888/admin/lieux.php
```

### 2. Création d'un nouveau lieu
1. **Remplir le formulaire** :
   - Nom du lieu (ex: "Salle Informatique")
   - Slug auto-généré (ex: "salle_info")
   - Ordre dans le parcours
   - Type d'énigme
   - Question principale
   - 4 réponses possibles
   - Réponse correcte

2. **Cliquer sur "Créer le Lieu Complet"**

3. **Validation automatique** :
   - Création du répertoire
   - Génération des fichiers
   - Ajout en base de données
   - Message de succès

### 3. Vérification
- Le lieu apparaît dans la liste des lieux existants
- Le répertoire est créé dans `lieux/[slug]/`
- Tous les fichiers sont générés et fonctionnels

## Structure des fichiers générés

Chaque lieu créé contient automatiquement :

### 📁 `index.php`
- Page principale du lieu
- Vérification des sessions et permissions
- Affichage de l'état de l'énigme
- Bouton de lancement de l'énigme
- Navigation vers d'autres lieux
- Timer de 12 minutes

### 📁 `header.php`
- En-tête HTML avec métadonnées
- Navigation principale
- Bouton scanner QR
- Intégration des styles et scripts

### 📁 `enigme.php`
- Interface de l'énigme
- Question et réponses personnalisées
- Validation des réponses
- Mise à jour du parcours
- Redirection après résolution

### 📁 `footer.php`
- Fermeture des balises HTML
- Scripts Bootstrap et personnalisés

### 📁 `style.css`
- Styles spécifiques au lieu
- Animations et transitions
- Design responsive
- Cohérence visuelle

## Base de données

### Table `lieux`
```sql
- id (AUTO_INCREMENT)
- nom (VARCHAR)
- slug (VARCHAR, UNIQUE)
- ordre (INT)
```

### Table `enigmes`
```sql
- id (AUTO_INCREMENT)
- lieu_id (FOREIGN KEY)
- type (ENUM: qcm, texte_libre, code, image)
- question (TEXT)
- reponse_correcte (VARCHAR)
- reponses (JSON)
```

## Sécurité

### ✅ Mesures implémentées
- **Vérification des sessions admin** : Seuls les administrateurs peuvent créer des lieux
- **Validation des entrées** : Vérification des champs obligatoires
- **Slug sécurisé** : Caractères autorisés uniquement (a-z, 0-9, _)
- **Échappement HTML** : Protection contre les injections XSS
- **Préparations SQL** : Protection contre les injections SQL

###  Contrôles d'accès
- Session admin requise
- Vérification des permissions
- Validation côté serveur

## Exemple d'utilisation

### Création d'un lieu "Salle Informatique"
1. **Nom** : Salle Informatique
2. **Slug** : salle_info (auto-généré)
3. **Question** : "Quel est le meilleur moyen de protéger ses données ?"
4. **Réponses** :
   - A) Sauvegarde régulière
   - B) Mot de passe simple
   - C) Partager ses fichiers
   - D) Ne rien faire
5. **Réponse correcte** : A

### Résultat
- Répertoire `lieux/salle_info/` créé
- 5 fichiers générés automatiquement
- Lieu accessible via `lieux/salle_info/`
- Énigme fonctionnelle immédiatement

## Maintenance

### 🔄 Mise à jour du template
Pour modifier la structure de tous les lieux futurs :
1. Modifier les fonctions de génération dans `admin/lieux.php`
2. Les nouveaux lieux utiliseront le nouveau template
3. Les lieux existants ne sont pas affectés

###  Logs et monitoring
- Messages de succès/erreur affichés
- Vérification de la création des fichiers
- Validation de l'intégrité de la base de données

---

**Note** : Cette fonctionnalité respecte l'architecture existante et n'affecte aucun lieu ou fonctionnalité déjà en place.

## Résumé

J'ai créé une fonctionnalité complète de gestion des lieux qui :

1. **Ajoute une section "Gestion des Lieux"** dans l'interface d'administration existante
2. **Génère automatiquement** tous les fichiers nécessaires basés sur le template "direction"
3. **Crée le répertoire** et la structure complète du lieu
4. **Intègre la base de données** avec les tables `lieux` et `enigmes`
5. **Respecte l'existant** sans modifier aucune fonctionnalité existante

### 🚀 Pour utiliser la fonctionnalité :
**URL d'accès** : `http://localhost:8888/admin/lieux.php`

La fonctionnalité est maintenant prête et permettra de créer des lieux complets et fonctionnels en quelques clics, en respectant parfaitement la structure existante du projet !
