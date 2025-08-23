# 🚀 Documentation Technique - Système Cyberchasse

## 📚 **Création de `docs/cyberdoc.md`**

```markdown:docs/cyberdoc.md
# 🚀 Documentation Technique - Système Cyberchasse

##  Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture du Système](#architecture-du-système)
3. [Structure de la Base de Données](#structure-de-la-base-de-données)
4. [Modules Administratifs](#modules-administratifs)
5. [Workflows Utilisateur](#workflows-utilisateur)
6. [Interfaces et Composants](#interfaces-et-composants)
7. [Sécurité et Validation](#sécurité-et-validation)
8. [Gestion des Fichiers](#gestion-des-fichiers)
9. [API et Endpoints](#api-et-endpoints)
10. [Maintenance et Déploiement](#maintenance-et-déploiement)

---

##  Vue d'ensemble

Le **Système Cyberchasse** est une plateforme de gestion de parcours d'énigmes géolocalisées, conçue pour organiser des jeux de piste avec QR codes. Le système permet aux administrateurs de créer des parcours, configurer des lieux avec énigmes, et gérer des équipes de participants.

### **Fonctionnalités Principales**
- ️ **Gestion des Parcours** : Création et configuration de parcours d'énigmes
- 📍 **Gestion des Lieux** : Création de lieux avec énigmes et génération automatique des fichiers
- 👥 **Gestion des Équipes** : Création et assignation d'équipes aux parcours
- 🔑 **Système de Tokens** : Gestion des accès sécurisés aux lieux
-  **QR Codes** : Génération automatique des codes d'accès
- 📊 **Suivi en Temps Réel** : Monitoring de la progression des équipes

---

## 🏗️ Architecture du Système

### **Structure Modulaire**
```
ladyciber/
├── admin/                          # Interface d'administration
│   ├── admin2.php                 # Page principale simplifiée
│   └── modules/                   # Modules spécialisés
│       ├── lieux/                 # Gestion des lieux
│       ├── parcours/              # Gestion des parcours
│       └── equipes/               # Gestion des équipes
├── config/                         # Configuration système
├── lieux/                         # Fichiers générés des lieux
├── templates/                      # Templates de lieux
└── docs/                          # Documentation
```
<code_block_to_apply_changes_from>
```
┌─────────────────────────────────────┐
│        Gestion du Parcours         │
├─────────────────────────────────────┤
│   Lieux    │   Équipes        │
│  • Ajouter   │  • Assigner        │
│  • Réorganiser│  • Gérer statuts   │
│  • Configurer│  • Suivre progrès   │
└─────────────────────────────────────┘
```

### **Module Équipes (`admin/modules/equipes/`)**

#### **Fonctionnalités**
- ✅ **Création d'équipes** avec identité visuelle
- ✅ **Interface moderne** avec sélecteur de couleur
- ✅ **Gestion des statuts** (active/inactive/disqualifiée)
- ✅ **Validation des doublons** et sécurité
- ✅ **Hashage automatique** des mots de passe

#### **Fichiers Principaux**
- `index.php` : Gestion complète des équipes

#### **Sécurité des Mots de Passe**
```php
// Hashage automatique avec PASSWORD_DEFAULT
$mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

// Vérification lors de la connexion
if (password_verify($mot_de_passe_saisi, $hash_stocke)) {
    // Connexion autorisée
}
```

---

## 🔄 Workflows Utilisateur

### **Workflow de Création d'un Parcours**
```
1. Créer un parcours
   ↓
2. Ajouter des lieux dans l'ordre souhaité
   ↓
3. Configurer les temps limites par lieu
   ↓
4. Assigner des équipes au parcours
   ↓
5. Générer les tokens d'accès
   ↓
6. Créer les QR codes
   ↓
7. Lancer le jeu
```

### **Workflow de Gestion des Lieux**
```
1. Créer un lieu
   ↓
2. Choisir le type (standard/démarrage/fin)
   ↓
3. Configurer les paramètres (temps, indices)
   ↓
4. Affecter une énigme (si type standard)
   ↓
5. Génération automatique des fichiers
   ↓
6. Test du lieu créé
```

### **Workflow de Gestion des Équipes**
```
1. Créer une équipe
   ↓
2. Configurer l'identité visuelle (couleur)
   ↓
3. Assigner un parcours
   ↓
4. Suivre la progression
   ↓
5. Gérer les statuts (en cours/terminé/abandonné)
```

---

## 🎨 Interfaces et Composants

### **Design System**
- **Framework** : Bootstrap 5
- **Icônes** : Font Awesome
- **Couleurs** : Palette cohérente avec badges colorés
- **Responsive** : Adaptation mobile et desktop

### **Composants Principaux**
- **Badges cliquables** : Remplacement des boutons traditionnels
- **Modals interactifs** : Formulaires et confirmations
- **Tableaux dynamiques** : Tri et filtrage des données
- **Barres de progression** : Suivi visuel de l'avancement

### **Navigation et UX**
- **Fil d'Ariane** : Navigation claire entre les modules
- **Actions contextuelles** : Boutons adaptés au contexte
- **Feedback utilisateur** : Messages de succès/erreur
- **Validation en temps réel** : Vérification des formulaires

---

## 🔒 Sécurité et Validation

### **Authentification**
- **Sessions PHP** sécurisées
- **Vérification des droits** d'administration
- **Protection des routes** sensibles

### **Validation des Données**
- **Sanitisation** des entrées utilisateur
- **Validation des types** et formats
- **Protection contre les injections SQL** (PDO prepared statements)

### **Gestion des Erreurs**
- **Logs d'erreur** détaillés
- **Messages utilisateur** appropriés
- **Gestion des exceptions** avec try/catch

---

## 📁 Gestion des Fichiers

### **Structure des Templates**
```
templates/
├── TemplateLieu/           # Lieux standard avec énigme
│   ├── index.php          # Page principale du lieu
│   ├── header.php         # En-tête du lieu
│   ├── footer.php         # Pied de page du lieu
│   ├── style.css          # Styles spécifiques
│   └── enigme.php         # Interface de l'énigme
├── TemplateLieuDemarrage/ # Lieux de démarrage
└── TemplateLieuFin/       # Lieux de fin
```

### **Génération Automatique**
- **Copie des templates** selon le type de lieu
- **Remplacement des variables** (direction, liens)
- **Création des dossiers** avec permissions appropriées
- **Gestion des erreurs** silencieuse

### **Sécurité des Fichiers**
- **Permissions restrictives** (755 pour les dossiers)
- **Validation des chemins** pour éviter les traversées
- **Nettoyage automatique** lors de la suppression

---

##  API et Endpoints

### **Endpoints Principaux**

#### **Gestion des Énigmes**
```php
// GET /admin/modules/lieux/get_enigme_details.php?id={id}
// Récupère les détails d'une énigme
{
    "success": true,
    "enigme": {
        "id": 1,
        "titre": "Énigme du CDI",
        "type_nom": "QCM",
        "donnees": {...}
    }
}
```

#### **Gestion des Parcours**
```php
// POST /admin/modules/parcours/manage_parcours.php
// Actions : add_lieu, update_ordre, remove_lieu, assign_equipe
```

### **Format des Réponses**
- **JSON** pour les API
- **Messages d'erreur** standardisés
- **Codes de statut HTTP** appropriés

---

## ️ Maintenance et Déploiement

### **Sauvegarde de la Base de Données**
```bash
# Sauvegarde complète
mysqldump -u [user] -p [database] > backup_$(date +%Y%m%d_%H%M%S).sql

# Restauration
mysql -u [user] -p [database] < backup_file.sql
```

### **Gestion des Versions**
- **Contrôle de version** avec Git
- **Tags de version** pour les releases
- **Documentation des changements** dans CHANGELOG.md

### **Monitoring et Logs**
- **Logs d'erreur** PHP
- **Logs d'activité** utilisateur
- **Surveillance des performances** de la base de données

### **Mise à Jour du Système**
1. **Sauvegarde** de la base de données
2. **Mise à jour** des fichiers
3. **Migration** de la base de données si nécessaire
4. **Test** des fonctionnalités
5. **Déploiement** en production

---

## 🚀 Prochaines Étapes de Développement

### **Phase 1 : Finalisation du Core**
- [ ] Module de progression en temps réel
- [ ] Système de tokens complet
- [ ] Génération des QR codes

### **Phase 2 : Interface de Jeu**
- [ ] Interface mobile pour les équipes
- [ ] Scanner QR code intégré
- [ ] Validation des énigmes

### **Phase 3 : Analytics et Reporting**
- [ ] Tableau de bord administrateur
- [ ] Statistiques de performance
- [ ] Rapports d'activité

### **Phase 4 : Fonctionnalités Avancées**
- [ ] Système de points et classements
- [ ] Indices dynamiques
- [ ] Mode multijoueur en temps réel

---

##  Support et Contact

### **Documentation Technique**
- **Ce fichier** : `docs/cyberdoc.md`
- **Documentation utilisateur** : `docs/user_guide.md`
- **Changelog** : `docs/CHANGELOG.md`

### **Maintenance**
- **Développeur** : [Nom du développeur]
- **Contact** : [Email de contact]
- **Repository** : [URL du repository Git]

---

*Dernière mise à jour : [Date]*
*Version du document : 1.0*
```

Cette documentation technique complète couvre tous les aspects du système refactorisé. Elle peut être utilisée par les développeurs pour comprendre l'architecture et par les administrateurs pour maintenir le système.

adyciber/
├── admin/ # Interface d'administration
│ ├── admin2.php # Page principale simplifiée
│ └── modules/ # Modules spécialisés
│ ├── lieux/ # Gestion des lieux
│ ├── parcours/ # Gestion des parcours
│ └── equipes/ # Gestion des équipes
├── config/ # Configuration système
├── lieux/ # Fichiers générés des lieux
├── templates/ # Templates de lieux
└── docs/ # Documentation