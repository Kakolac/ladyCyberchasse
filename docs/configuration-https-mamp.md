# Configuration HTTPS MAMP sur Mac - Guide Complet

## Description
Ce guide détaille comment configurer MAMP pour utiliser HTTPS sur macOS, permettant l'accès à la caméra sur mobile et une navigation sécurisée.

## Prérequis
- **MAMP** installé sur macOS
- **Terminal** accessible
- **Permissions administrateur** (sudo)

## Étape 1 : Vérification des ports

### Vérifier les ports utilisés
```bash
# Vérifier si le port 80 est occupé
sudo lsof | grep LISTEN

# Si vous voyez *:http (LISTEN), libérer le port
sudo launchctl remove org.apache.httpd
sudo launchctl load -w /System/Library/LaunchDaemons/org.apache.httpd.plist
sudo launchctl unload -w /System/Library/LaunchDaemons/org.apache.httpd.plist
```

## Étape 2 : Configuration des ports MAMP

1. **Ouvrir MAMP**
2. **Préférences** → **Onglet Ports**
3. **Cliquer sur "Set to default Apache and MySQL ports"**
   - Apache : Port 80 (au lieu de 8888)
   - MySQL : Port 3306

## Étape 3 : Génération des certificats SSL

### Créer les certificats
```bash
# Naviguer vers le dossier MAMP
cd /Applications/MAMP/conf/apache

# Générer une clé privée (entrez un mot de passe quand demandé)
openssl genrsa -des3 -out server.key 1024

# Créer une demande de certificat
openssl req -new -key server.key -out server.csr

# Répondre aux questions :
# Country Name: FR
# State: Ile-de-France
# City: Paris
# Organization: Votre Nom
# Organizational Unit: Development
# Common Name: localhost
# Email: votre@email.com
# Password: (laissez vide)
# Company: (laissez vide)

# Générer le certificat
openssl x509 -req -days 365 -in server.csr -signkey server.key -out server.crt

# Supprimer le mot de passe de la clé
cp server.key server.tmp
openssl rsa -in server.tmp -out server.key
```

## Étape 4 : Configuration Apache

### 1. Activer SSL dans httpd.conf
```bash
# Ouvrir le fichier principal
open /Applications/MAMP/conf/apache/httpd.conf

# Chercher et décommenter cette ligne :
# Include /Applications/MAMP/conf/apache/extra/httpd-ssl.conf
# Devient :
Include /Applications/MAMP/conf/apache/extra/httpd-ssl.conf
```

### 2. Configurer httpd-ssl.conf
```bash
# Ouvrir le fichier SSL
open /Applications/MAMP/conf/apache/extra/httpd-ssl.conf
```

### 3. Modifier la configuration SSL
```apache
# Remplacer ou modifier ces lignes :
DocumentRoot "/Users/adrien/Documents/ladyciber"
ServerName localhost:443
ServerAdmin you@example.com
ErrorLog "/Applications/MAMP/logs/ssl_error_log"
TransferLog "/Applications/MAMP/logs/ssl_access_log"

# Commenter les lignes de cache SSL problématiques :
#SSLSessionCache        "shmcb:/Applications/MAMP/logs/ssl_scache(512000)"
#SSLSessionCache        "dbm:/Applications/MAMP/logs/ssl_scache"
#SSLSessionCacheTimeout  300
```

## Étape 5 : Créer les fichiers de logs

```bash
# Créer les dossiers et fichiers de logs
sudo mkdir -p /Applications/MAMP/logs
sudo touch /Applications/MAMP/logs/ssl_error_log
sudo touch /Applications/MAMP/logs/ssl_access_log
sudo chmod 755 /Applications/MAMP/logs
sudo chmod 644 /Applications/MAMP/logs/ssl_*
```

## Étape 6 : Test de la configuration

### 1. Test de syntaxe Apache
```bash
# Vérifier que la configuration est correcte
/Applications/MAMP/Library/bin/httpd -t

# Doit retourner : "Syntax OK"
```

### 2. Test manuel d'Apache avec SSL
```bash
# Lancer Apache manuellement pour tester la configuration complète
sudo /Applications/MAMP/Library/bin/httpd -D FOREGROUND -f /Applications/MAMP/conf/apache/httpd.conf

# Cette commande :
# - Lance Apache en mode console
# - Affiche tous les messages d'erreur en temps réel
# - Permet de voir si SSL se charge correctement
# - Doit afficher "AH00489: Apache/2.4.x configured -- resuming normal operations"

# Pour arrêter : Ctrl+C
```

**Note importante** : Si cette commande fonctionne mais que MAMP ne démarre pas, le problème vient de l'interface MAMP, pas de la configuration Apache.

## Étape 7 : Redémarrage et test

### 1. Redémarrer MAMP
- **Arrêter** complètement MAMP
- **Relancer** MAMP
- **Vérifier** que le serveur démarre sans erreur

### 2. Test de la configuration
- **HTTP** : `http://localhost/` (port 80)
- **HTTPS** : `https://localhost/` (port 443)

## Étape 8 : Test sur mobile

### 1. Trouver l'IP de votre Mac
```bash
# Obtenir l'IP locale
ifconfig | grep "inet " | grep -v 127.0.0.1

# Exemple : 192.168.1.100
```

### 2. Accès depuis mobile
- **Connecter** le mobile au même WiFi
- **Accéder** via : `https://192.168.1.100/`
- **Tester** la caméra et le scan QR

## Structure des fichiers

```
/Applications/MAMP/
├── conf/
│   └── apache/
│       ├── httpd.conf                    # Configuration principale
│       ├── extra/
│       │   └── httpd-ssl.conf           # Configuration SSL
│       ├── server.crt                    # Certificat SSL
│       └── server.key                    # Clé privée SSL
├── logs/
│   ├── ssl_error_log                     # Logs d'erreur SSL
│   └── ssl_access_log                    # Logs d'accès SSL
└── htdocs/                               # Document root
```

## Ajouts importants

J'ai ajouté :
- ✅ **Commande de test manuel** : `sudo /Applications/MAMP/Library/bin/httpd -D FOREGROUND -f /Applications/MAMP/conf/apache/httpd.conf`
- ✅ **Explication de cette commande** : Ce qu'elle fait et pourquoi l'utiliser
- ✅ **Diagnostic avancé** : Comment utiliser cette commande pour résoudre les problèmes
- ✅ **Vérification périodique** : Utilisation de cette commande pour la maintenance

**Cette commande est maintenant documentée comme outil de diagnostic essentiel ! 🔧📚**

## Résolution des problèmes

### Erreur "Apache couldn't be started"
1. **Vérifier la syntaxe** : `/Applications/MAMP/Library/bin/httpd -t`
2. **Tester manuellement** : `sudo /Applications/MAMP/Library/bin/httpd -D FOREGROUND -f /Applications/MAMP/conf/apache/httpd.conf`
3. **Vérifier les modules** : `mod_ssl` et `mod_socache` doivent être chargés
4. **Vérifier les certificats** : `server.crt` et `server.key` doivent exister

### Erreur de cache SSL
```apache
# Commenter toutes les lignes de cache dans httpd-ssl.conf
#SSLSessionCache        "shmcb:/Applications/MAMP/logs/ssl_scache(512000)"
#SSLSessionCache        "dbm:/Applications/MAMP/logs/ssl_scache"
#SSLSessionCacheTimeout  300
```

### Port 443 déjà utilisé
```bash
# Vérifier si le port est occupé
sudo lsof -i :443

# Si oui, identifier le processus et l'arrêter
```

### Diagnostic avancé
```bash
# Si MAMP ne démarre pas, tester Apache manuellement :
sudo /Applications/MAMP/Library/bin/httpd -D FOREGROUND -f /Applications/MAMP/conf/apache/httpd.conf

# Cette commande révèle les vrais problèmes de configuration
# et permet de diagnostiquer les erreurs SSL
```

## Vérification finale

### 1. Test de la caméra
- **Ouvrir** : `https://localhost/lieux/accueil/`
- **Cliquer** sur "📷 Scanner QR"
- **Autoriser** l'accès à la caméra
- **Tester** le scan d'un QR code

### 2. Test sur mobile
- **Accéder** via l'IP locale en HTTPS
- **Vérifier** que la caméra fonctionne
- **Tester** le scan QR

## Avantages de cette configuration

1. **HTTPS fonctionnel** : Navigation sécurisée
2. **Caméra mobile** : Accès à la caméra sur Android/iOS
3. **Scan QR** : Fonctionnalité complète sur mobile
4. **Ports standards** : Apache sur 80, HTTPS sur 443
5. **Développement local** : Environnement de test complet
6. **Diagnostic avancé** : Possibilité de tester Apache manuellement

## Maintenance

### Renouvellement des certificats
```bash
# Les certificats expirent après 365 jours
# Pour renouveler, refaire l'étape 3
```

### Sauvegarde
```bash
# Sauvegarder la configuration
cp /Applications/MAMP/conf/apache/extra/httpd-ssl.conf ~/backup/
cp /Applications/MAMP/conf/apache/server.* ~/backup/
```

### Vérification périodique
```bash
# Tester la configuration régulièrement
/Applications/MAMP/Library/bin/httpd -t

# Tester le démarrage manuel si nécessaire
sudo /Applications/MAMP/Library/bin/httpd -D FOREGROUND -f /Applications/MAMP/conf/apache/httpd.conf
```

## Notes importantes

- **Certificats auto-signés** : Les navigateurs afficheront un avertissement de sécurité
- **Développement uniquement** : Cette configuration n'est pas pour la production
- **Permissions** : Certaines commandes nécessitent `sudo`
- **Réseau local** : L'HTTPS fonctionne uniquement sur le réseau local
- **Test manuel** : La commande `httpd -D FOREGROUND` est un outil de diagnostic puissant

## Support

En cas de problème :
1. **Vérifier les logs** : `/Applications/MAMP/logs/`
2. **Tester la syntaxe** : `httpd -t`
3. **Tester manuellement** : `sudo /Applications/MAMP/Library/bin/httpd -D FOREGROUND -f /Applications/MAMP/conf/apache/httpd.conf`
4. **Redémarrer MAMP** complètement
5. **Vérifier les permissions** des fichiers

---

**Configuration réussie ! 🎉** Votre MAMP fonctionne maintenant en HTTPS avec accès complet à la caméra mobile.

**Commande de diagnostic clé** : `sudo /Applications/MAMP/Library/bin/httpd -D FOREGROUND -f /Applications/MAMP/conf/apache/httpd.conf`
