# Headers et Footers Spécifiques aux Lieux

## 📋 Description

Système de headers et footers dédiés aux lieux du lycée pour résoudre les problèmes de chemins et faciliter les personnalisations futures.

## 🏗️ Structure Créée

### Fichiers principaux
- **`lieux/header.php`** - Header principal identique à l'original (chemins corrigés)
- **`lieux/footer.php`** - Footer principal identique à l'original (chemins corrigés)

### Fichiers par lieu
- **`lieux/[nom_du_lieu]/header.php`** - Header spécifique au lieu (chemins corrigés)
- **`lieux/[nom_du_lieu]/footer.php`** - Footer spécifique au lieu (chemins corrigés)

## 🔧 Chemins Corrigés

### Header principal des lieux
```php
// CSS : chemin relatif depuis lieux/
<link rel="stylesheet" href="../styles/style.css">

// Logout : chemin relatif depuis lieux/
<a href="../logout.php" class="logout-btn">Déconnexion</a>
```

### Headers spécifiques aux lieux
```php
// CSS : chemin relatif depuis lieux/[nom_du_lieu]/
<link rel="stylesheet" href="../../styles/style.css">

// Logout : chemin relatif depuis lieux/[nom_du_lieu]/
<a href="../../logout.php" class="logout-btn">Déconnexion</a>
```

## 🚀 Utilisation

### Dans un lieu principal
```php
<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

include '../header.php';
?>

<!-- Contenu de la page -->

<?php include '../footer.php'; ?>
```

### Dans un sous-lieu
```php
<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../../login.php');
    exit();
}

include '../../header.php';
?>

<!-- Contenu de la page -->

<?php include '../../footer.php'; ?>
```

## 🎯 Avantages

### ✅ Résolution des problèmes
- **Chemins corrects** vers les images et CSS
- **Image de fond** `bg.jpg` visible dans tous les lieux
- **Headers et footers identiques** à l'original
- **Styles cohérents** avec le reste de l'application

### 🔮 Flexibilité future
- **Personnalisation** possible par lieu
- **Modifications** locales sans affecter les autres lieux
- **Évolution** indépendante de chaque lieu

### 🛠️ Maintenance
- **Structure claire** et organisée
- **Debugging** facilité par lieu
- **Tests** isolés par lieu

## 📱 Script de Création

### Lancement
```
http://localhost:8888/scripts/create_lieux_headers.php
```

### Fonctionnalités
- Création automatique des headers et footers
- Gestion des chemins relatifs
- Vérification de la structure existante
- Messages de confirmation

## 🔍 Vérification

### Après création
1. **Vérifier** que l'image `bg.jpg` s'affiche
2. **Tester** la navigation entre lieux
3. **Contrôler** que les styles CSS s'appliquent
4. **Valider** que la déconnexion fonctionne

### Problèmes courants
- **Chemins incorrects** : Vérifier la structure des dossiers
- **CSS manquant** : Contrôler les liens vers `style.css`
- **Images absentes** : Vérifier le chemin vers `images/bg.jpg`

## 📚 Exemples d'Utilisation

### Lieu simple (accueil)
```php
include '../header.php';
// Contenu
include '../footer.php';
```

### Lieu avec énigme
```php
include '../header.php';
// Contenu de l'énigme
include '../footer.php';
```

### Lieu avec navigation
```php
include '../header.php';
// Navigation et contenu
include '../footer.php';
```

## 🔄 Évolution Future

### Personnalisations possibles
- **Couleurs** spécifiques par lieu
- **Images** de fond différentes
- **Menus** de navigation adaptés
- **Styles** CSS personnalisés

### Ajout de fonctionnalités
- **Timers** par lieu
- **Progression** visuelle
- **Animations** spécifiques
- **Sons** ou effets

---

**Note** : Cette structure permet de maintenir la cohérence visuelle tout en offrant la flexibilité nécessaire pour les développements futurs de chaque lieu.
