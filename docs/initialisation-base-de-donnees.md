# Initialisation de la Base de Données - Cyberchasse

## Sommaire
1. [Vue d'ensemble](#vue-densemble)
2. [Prérequis](#prérequis)
3. [Structure de la base de données](#structure-de-la-base-de-données)
4. [Utilisation du script](#utilisation-du-script)
5. [Sécurité](#sécurité)
6. [Dépannage](#dépannage)

## Vue d'ensemble

Ce document décrit le processus d'initialisation de la base de données pour l'application Cyberchasse. Le script crée automatiquement la base de données et les tables nécessaires pour la gestion des utilisateurs.

## Prérequis

- Serveur MySQL/MariaDB en cours d'exécution
- PHP avec l'extension PDO MySQL activée
- Utilisateur MySQL avec privilèges de création de base de données
- MAMP/XAMPP ou serveur MySQL local configuré

## Structure de la base de données

### Base de données
- **Nom**: `cyberchasse`
- **Encodage**: UTF-8 (utf8mb4)
- **Collation**: utf8mb4_unicode_ci

### Table `users`
| Champ | Type | Description |
|-------|------|-------------|
| `id` | INT AUTO_INCREMENT | Identifiant unique de l'utilisateur |
| `username` | VARCHAR(50) | Nom d'utilisateur (unique) |
| `password` | VARCHAR(255) | Hash du mot de passe |
| `email` | VARCHAR(100) | Adresse email (optionnelle) |
| `created_at` | TIMESTAMP | Date de création du compte |
| `updated_at` | TIMESTAMP | Date de dernière modification |

## Utilisation du script

### 1. Exécution du script
```bash
cd scripts
php init_database.php
```

### 2. Vérification de l'installation
Le script affichera des messages de confirmation pour chaque étape :
- ✅ Connexion au serveur MySQL
- ✅ Création de la base de données
- ✅ Création de la table users
- ✅ Création de l'utilisateur de test

### 3. Utilisateur de test créé
- **Username**: `admin`
- **Mot de passe**: `admin123`
- **Email**: `admin@cyberchasse.local`

## Sécurité

### Hachage des mots de passe
- Utilisation de `password_hash()` avec l'algorithme par défaut de PHP
- Mots de passe stockés sous forme de hash, jamais en clair

### Validation des données
- Contraintes UNIQUE sur le nom d'utilisateur
- Index sur le champ username pour optimiser les recherches

### Privilèges MySQL
- Utilisation d'un utilisateur avec privilèges limités en production
- Éviter l'utilisation de l'utilisateur `root` en production

## Dépannage

### Erreur de connexion
```
❌ Erreur lors de l'initialisation : SQLSTATE[HY000] [1045] Access denied
```
**Solution**: Vérifier les identifiants MySQL dans le script

### Erreur de privilèges
```
❌ Erreur lors de l'initialisation : SQLSTATE[42000] [1044] Access denied
```
**Solution**: Vérifier que l'utilisateur MySQL a les privilèges CREATE et INSERT

### Erreur de caractères
```
❌ Erreur lors de l'initialisation : SQLSTATE[42000] [1115] Unknown character set
```
**Solution**: Vérifier que MySQL supporte utf8mb4 (MySQL 5.5.3+)

## Maintenance

### Sauvegarde
```bash
mysqldump -u root -p cyberchasse > backup_cyberchasse.sql
```

### Restauration
```bash
mysql -u root -p cyberchasse < backup_cyberchasse.sql
```

### Ajout d'utilisateurs
```sql
INSERT INTO users (username, password, email) 
VALUES ('nouveau_user', 'hash_du_mot_de_passe', 'email@example.com');
```

## Notes importantes

- **Ne jamais** stocker les mots de passe en clair
- **Toujours** utiliser des requêtes préparées pour les insertions/updates
- **Sauvegarder** régulièrement la base de données
- **Tester** le script dans un environnement de développement avant la production
```

Le script d'initialisation est maintenant créé et documenté. Il va :

1. **Se connecter** au serveur MySQL
2. **Créer** la base de données `cyberchasse` si elle n'existe pas
3. **Créer** la table `users` avec tous les champs nécessaires
4. **Créer** un utilisateur de test `admin` avec le mot de passe `admin123`
5. **Afficher** la structure et le contenu de la table

Pour l'utiliser, exécutez simplement :
```bash
cd scripts
php init_database.php
```

Le script est sécurisé avec le hachage des mots de passe et inclut des vérifications pour éviter les doublons. La documentation complète est disponible dans `/docs/initialisation-base-de-donnees.md` comme demandé dans les règles du workspace.
