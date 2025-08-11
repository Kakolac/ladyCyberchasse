# Bouton de Déconnexion - Documentation

## Description
Implémentation d'un bouton de déconnexion visible sur toutes les pages de l'application Cyberchasse.

## Fonctionnement

### 1. Affichage conditionnel
- Le bouton de déconnexion n'apparaît que lorsque l'utilisateur est connecté
- Il est affiché dans le header en haut à droite de la page
- Affiche également le nom de l'équipe connectée

### 2. Positionnement
- **Desktop** : Positionné en haut à droite du header
- **Mobile** : Centré sous le titre principal pour une meilleure accessibilité

### 3. Sécurité
- Détruit complètement la session lors de la déconnexion
- Supprime les cookies de session
- Redirige vers la page d'accueil après déconnexion

## Fichiers modifiés

### `includes/header.php`
- Ajout de la logique conditionnelle pour afficher les informations utilisateur
- Structure HTML pour le bouton de déconnexion

### `logout.php` (nouveau fichier)
- Script de déconnexion sécurisé
- Destruction de la session et redirection

### `styles/style.css`
- Styles pour le bouton de déconnexion
- Design responsive
- Animations et effets visuels

## Utilisation

### Pour l'utilisateur
1. Se connecter avec son équipe
2. Le bouton de déconnexion apparaît automatiquement dans le header
3. Cliquer sur "Déconnexion" pour se déconnecter
4. Être redirigé vers la page d'accueil

### Pour le développeur
- Le bouton est automatiquement visible sur toutes les pages qui incluent `includes/header.php`
- Aucune modification supplémentaire n'est nécessaire sur les autres pages
- Le système de session gère automatiquement l'affichage

## Test

### URL de test
- **Connexion** : `http://localhost:8888/login.php`
- **Déconnexion** : `http://localhost:8888/logout.php` (accessible via le bouton)

### Scénario de test
1. Aller sur la page de connexion
2. Se connecter avec une équipe
3. Naviguer sur différentes pages pour vérifier la présence du bouton
4. Cliquer sur déconnexion
5. Vérifier la redirection vers la page d'accueil

## Maintenance

### Ajout de nouvelles pages
- Inclure simplement `includes/header.php` dans les nouvelles pages
- Le bouton de déconnexion sera automatiquement disponible

### Modification du style
- Éditer les classes CSS dans `styles/style.css`
- Les modifications s'appliquent à toutes les pages

## Sécurité

- Session complètement détruite lors de la déconnexion
- Cookies de session supprimés
- Redirection sécurisée vers la page d'accueil
- Protection contre les attaques de session
