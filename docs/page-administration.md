# Page d'Administration - Cyberchasse

## Sommaire
1. [Vue d'ensemble](#vue-densemble)
2. [Fonctionnalités principales](#fonctionnalités-principales)
3. [Outils disponibles](#outils-disponibles)
4. [Actions rapides](#actions-rapides)
5. [Monitoring et statistiques](#monitoring-et-statistiques)
6. [Sécurité et accès](#sécurité-et-accès)
7. [Utilisation](#utilisation)

## Vue d'ensemble
La page d'administration (`admin/admin.php`) est le centre de contrôle principal de l'application Cyberchasse. Elle permet aux administrateurs de gérer tous les aspects du jeu, des équipes aux parcours, en passant par la maintenance de la base de données.

## Fonctionnalités principales

### 📊 Tableau de bord
- **Statistiques en temps réel** : Nombre d'équipes, parcours actifs, lieux visités
- **Vue d'ensemble** : État général de l'application
- **Activités récentes** : Dernières actions des équipes

### ��️ Outils de gestion
- **Gestion des équipes** : Création, modification, suivi des équipes
- **Gestion des parcours** : Configuration et suivi des parcours
- **Gestion des lieux** : Administration des énigmes et lieux
- **Base de données** : Maintenance et vérification
- **Scripts utilitaires** : Outils de développement
- **Monitoring** : Surveillance en temps réel

## Outils disponibles

### 1. Gestion des Équipes
- **Accès** : Bouton "Générer QR Codes"
- **Fonctionnalités** : Création d'équipes, génération de QR codes, gestion des mots de passe

### 2. Gestion des Parcours
- **Accès** : Bouton "Configurer"
- **Fonctionnalités** : Configuration des parcours, suivi des équipes, validation des étapes

### 3. Gestion des Lieux
- **Accès** : Bouton "Administrer"
- **Fonctionnalités** : Création d'énigmes, configuration des lieux, gestion des accès

### 4. Base de Données
- **Accès** : Bouton "Maintenir"
- **Fonctionnalités** : Vérification, réparation, optimisation de la base

### 5. Scripts Utilitaires
- **Accès** : Bouton "Exécuter"
- **Fonctionnalités** : Outils de maintenance, nettoyage, tests

### 6. Monitoring
- **Accès** : Bouton "Surveiller"
- **Fonctionnalités** : Surveillance des activités, logs, alertes

## Actions rapides

### �� Réinitialiser tous les jeux
- **Fonction** : Remet à zéro tous les parcours et sessions
- **Utilisation** : Bouton "Réinitialiser tous les jeux"
- **Confirmation** : Demande de confirmation avant exécution

### 📱 Générer tous les QR codes
- **Fonction** : Crée les QR codes pour toutes les équipes
- **Utilisation** : Bouton "Générer tous les QR codes"
- **Résultat** : QR codes générés pour toutes les équipes

### 💾 Sauvegarder la base
- **Fonction** : Crée une sauvegarde complète de la base de données
- **Utilisation** : Bouton "Sauvegarder la base"
- **Résultat** : Fichier de sauvegarde créé

### �� Nettoyer les logs
- **Fonction** : Supprime les anciens logs et fichiers temporaires
- **Utilisation** : Bouton "Nettoyer les logs"
- **Confirmation** : Demande de confirmation avant nettoyage

## Monitoring et statistiques

### 📈 Statistiques en temps réel
- **Total des équipes** : Nombre total d'équipes enregistrées
- **Équipes actives** : Équipes actuellement connectées
- **Total des parcours** : Nombre total de parcours créés
- **Parcours terminés** : Parcours complétés avec succès

### �� Activités récentes
- **Suivi des équipes** : Dernières actions de chaque équipe
- **Statut des sessions** : État des sessions de jeu
- **Lieux visités** : Lieux récemment explorés par les équipes

## Sécurité et accès

### �� Authentification
- **Session admin** : Vérification des droits d'administration
- **Redirection** : Accès refusé si non authentifié
- **Logs de sécurité** : Traçabilité des actions d'administration

### ��️ Contrôles d'accès
- **Vérification des droits** : Seuls les administrateurs peuvent accéder
- **Protection des routes** : Accès sécurisé aux fonctionnalités sensibles
- **Audit trail** : Enregistrement de toutes les actions

## Utilisation

### 🚀 Accès à la page