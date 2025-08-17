


# Scanner QR Dynamique - Lieux Créés par l'Utilisateur

## Description
Le scanner QR a été modifié pour récupérer dynamiquement les informations des lieux depuis la base de données, au lieu d'utiliser un mapping codé en dur.

## Fonctionnement

### 1. Processus de scan
1. L'utilisateur scanne un QR code
2. Le scanner extrait le paramètre `lieu` de l'URL
3. Une requête AJAX est envoyée vers `scripts/get_lieu_info.php`
4. Le script PHP interroge la base de données
5. Les informations du lieu sont retournées au format JSON
6. Le scanner affiche les informations dynamiques

### 2. Scripts impliqués
- **`includes/qr-scanner.php`** : Interface utilisateur et logique JavaScript
- **`scripts/get_lieu_info.php`** : API PHP pour récupérer les informations des lieux

### 3. Structure des données retournées
```json
{
    "success": true,
    "lieu_info": {
        "nom": "Nom du lieu",
        "description": "Description du lieu",
        "icon": "��",
        "ordre": 1,
        "temps_limite": 300,
        "delai_indice": 6,
        "statut": "actif"
    }
}
```

## Avantages

- **Dynamique** : Les lieux sont récupérés en temps réel depuis la BDD
- **Maintenable** : Plus besoin de modifier le code JavaScript
- **Flexible** : Chaque lieu peut avoir sa propre description et icône
- **Cohérent** : Utilise la même source de données que l'administration

## Gestion des erreurs

- Lieu non trouvé → Affichage d'informations par défaut
- Erreur de communication → Fallback avec message d'erreur
- Paramètre manquant → Validation côté serveur

## Utilisation

Le scanner fonctionne automatiquement sans configuration supplémentaire. Il suffit de :
1. Créer des lieux dans l'administration
2. Générer les QR codes correspondants
3. Scanner les QR codes avec l'application

## URL du script
http://localhost:8888/scripts/get_lieu_info.php