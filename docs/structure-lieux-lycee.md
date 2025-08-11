# Structure des Lieux - Cyberchasse Lycée

## Sommaire
1. [Vue d'ensemble](#vue-densemble)
2. [Organisation des lieux](#organisation-des-lieux)
3. [Structure des fichiers](#structure-des-fichiers)
4. [Navigation entre lieux](#navigation-entre-lieux)
5. [Thèmes et énigmes](#thèmes-et-énigmes)
6. [Maintenance et évolution](#maintenance-et-évolution)

## Vue d'ensemble

Cette structure organise l'application Cyberchasse autour des lieux physiques d'un lycée. Chaque lieu contient ses propres fichiers (pages, styles, scripts) et propose des énigmes liées à la cybersécurité dans un contexte éducatif.

## Organisation des lieux

### Zone Administrative
- **accueil/** - Hall d'entrée, point de départ de la chasse
- **vie_scolaire/** - Gestion des élèves et discipline
- **direction/** - Direction du lycée
- **secretariat/** - Secrétariat administratif
- **salle_profs/** - Salle des professeurs
- **salle_reunion/** - Salle de réunion

### Zone Pédagogique
- **cdi/** - Centre de Documentation et d'Information
- **salle_info/** - Salle informatique (cybersécurité)
- **labo_physique/** - Laboratoire de physique
- **labo_chimie/** - Laboratoire de chimie
- **labo_svt/** - Laboratoire SVT
- **salle_arts/** - Salle d'arts plastiques
- **salle_musique/** - Salle de musique
- **salle_langues/** - Salle de langues
- **atelier_techno/** - Atelier technologique

### Zone Vie Quotidienne
- **gymnase/** - Activités sportives
- **cantine/** - Restaurant scolaire
- **internat/** - Hébergement (si applicable)
- **infirmerie/** - Soins et santé
- **cour/** - Cour de récréation

## Structure des fichiers

### Structure type d'un lieu
```
lieux/nom_du_lieu/
├── index.php          # Page principale du lieu
├── enigme.php         # Énigme à résoudre
├── validation.php     # Validation de la réponse
├── style.css          # Styles spécifiques au lieu
├── script.js          # JavaScript spécifique
└── images/            # Images du lieu (optionnel)
```

### Fichiers communs
- **includes/header.php** - En-tête avec navigation
- **includes/footer.php** - Pied de page
- **styles/global.css** - Styles communs
- **js/game-timer.js** - Timer de jeu global

## Navigation entre lieux

### Système de progression
1. **Accueil** → Point de départ obligatoire
2. **Lieux obligatoires** → CDI, Salle info, Vie scolaire
3. **Lieux optionnels** → Autres lieux selon le temps
4. **Validation** → Chaque lieu doit être validé

### Contrôles d'accès
- Session utilisateur active
- Progression dans l'ordre logique
- Timer de 12 minutes par lieu
- Validation obligatoire avant passage au suivant

## Thèmes et énigmes

### CDI (Centre de Documentation)
- **Thème** : Recherche documentaire, fake news
- **Énigme** : Identifier des sources fiables vs trompeuses

### Salle Informatique
- **Thème** : Cybersécurité, mots de passe
- **Énigme** : Décrypter un message, tester la force des mots de passe

### Laboratoire de Physique
- **Thème** : Cryptographie, codes secrets
- **Énigme** : Décoder un message avec des principes physiques

### Vie Scolaire
- **Thème** : Protection des données personnelles
- **Énigme** : Identifier les bonnes pratiques RGPD

## Maintenance et évolution

### Ajout d'un nouveau lieu
1. Créer le répertoire `lieux/nouveau_lieu/`
2. Créer les fichiers de base (index.php, enigme.php, etc.)
3. Ajouter le lieu dans la navigation
4. Mettre à jour la base de données si nécessaire

### Modification d'un lieu existant
- Toujours conserver la compatibilité
- Tester la navigation après modification
- Mettre à jour la documentation

### Suppression d'un lieu
- Vérifier les dépendances
- Mettre à jour la navigation
- Archiver les fichiers si nécessaire

## Utilisation

### Pour les développeurs
- Respecter la structure des fichiers
- Utiliser les composants communs (header, footer)
- Tester la navigation complète

### Pour les animateurs
- Chaque lieu est accessible via son URL
- Validation automatique des réponses
- Suivi du temps et de la progression

### Pour les joueurs
- Navigation intuitive entre les lieux
- Énigmes contextualisées dans l'environnement scolaire
- Progression claire et motivante

Laissez-moi créer cette structure et la documentation correspondante :

```php:lieux/accueil/index.php
<?php
session_start();
include '../../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2>🏫 Hall d'Entrée - Cyberchasse Lycée</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h4>🎯 Mission : Infiltration Cybernétique</h4>
                        <p>Votre lycée a été victime d'une cyberattaque ! En tant que futurs experts en cybersécurité, vous devez infiltrer tous les secteurs du lycée, collecter les informations secrètes et déjouer les pièges informatiques cachés dans chaque lieu.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5> Instructions</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">✅ Restez toujours en équipe de 4</li>
                                <li class="list-group-item">⏰ 12 minutes maximum par lieu</li>
                                <li class="list-group-item">🔒 Respectez les espaces et les personnes</li>
                                <li class="list-group-item"> Validez chaque étape en scannant le QR code</li>
                                <li class="list-group-item"> Retour obligatoire à 11h45</li>
                                <li class="list-group-item"> En cas de problème, contactez un animateur</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5> Lieux à explorer</h5>
                            <div class="list-group">
                                <a href="../cdi/" class="list-group-item list-group-item-action">
                                    📚 CDI - Centre de Documentation
                                </a>
                                <a href="../salle_info/" class="list-group-item list-group-item-action">
                                     Salle Informatique - Cybersécurité
                                </a>
                                <a href="../vie_scolaire/" class="list-group-item list-group-item-action">
                                    👥 Vie Scolaire - Administration
                                </a>
                                <a href="../labo_physique/" class="list-group-item list-group-item-action">
                                    ⚡ Laboratoire de Physique
                                </a>
                                <a href="../cantine/" class="list-group-item list-group-item-action">
                                    🍽️ Cantine - Restaurant Scolaire
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <h4> Êtes-vous prêt ?</h4>
                        <p>La chasse peut commencer ! Choisissez votre premier lieu d'investigation.</p>
                        <div class="btn-group" role="group">
                            <a href="../cdi/" class="btn btn-success btn-lg">📚 Commencer par le CDI</a>
                            <a href="../salle_info/" class="btn btn-info btn-lg">💻 Commencer par l'Info</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
```

```css:lieux/accueil/style.css
/* Styles spécifiques au Hall d'Entrée */
.card-header.bg-primary {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1, #bee5eb);
    border: 2px solid #17a2b8;
}

.list-group-item:hover {
    transform: translateX(5px);
    transition: transform 0.3s ease;
}

.btn-group .btn {
    margin: 0 5px;
    transition: all 0.3s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Animation d'entrée */
.card {
    animation: slideInUp 0.8s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```

Maintenant, créons un lieu clé - le CDI :

```php:lieux/cdi/index.php
<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h2>📚 CDI - Centre de Documentation et d'Information</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5>🚨 Alerte Sécurité !</h5>
                        <p>Des informations sensibles ont été disséminées dans la documentation du CDI. Votre mission : identifier les documents fiables des fake news et collecter les indices de cybersécurité.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5> Mission en cours</h5>
                            <p>Explorez les ressources du CDI pour :</p>
                            <ul>
                                <li>Identifier les sources d'information fiables</li>
                                <li>Détecter les tentatives de désinformation</li>
                                <li>Trouver les documents sur la cybersécurité</li>
                                <li>Collecter les indices cachés</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>⏱️ Temps restant</h5>
                            <div id="timer" class="display-4 text-danger"></div>
                            <p class="text-muted">Vous avez 12 minutes pour cette mission</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <h4> Prêt à commencer l'enquête ?</h4>
                        <a href="enigme.php" class="btn btn-primary btn-lg"> Commencer l'énigme</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5>🗺️ Navigation</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="../accueil/" class="list-group-item list-group-item-action">
                             Retour à l'accueil
                        </a>
                        <a href="../salle_info/" class="list-group-item list-group-item-action">
                             Salle Informatique
                        </a>
                        <a href="../vie_scolaire/" class="list-group-item list-group-item-action">
                            👥 Vie Scolaire
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5>📊 Progression</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: 25%">25%</div>
                    </div>
                    <small class="text-muted">1/4 lieux explorés</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../js/game-timer.js"></script>
<script>
    // Timer spécifique au CDI
    startTimer(720, 'timer'); // 12 minutes = 720 secondes
</script>

<?php include '../../includes/footer.php'; ?>
```

```php:lieux/cdi/enigme.php
<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h2>🔍 Énigme CDI - Chasse aux Fake News</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>📖 Contexte</h5>
                        <p>Le CDI a été infiltré par des informations trompeuses sur la cybersécurité. Votre équipe doit identifier les vraies informations des fausses pour accéder au prochain niveau.</p>
                    </div>
                    
                    <div class="enigme-content">
                        <h4>🎯 Question principale</h4>
                        <p class="lead">Parmi ces affirmations sur la cybersécurité, laquelle est <strong>FAUSSE</strong> ?</p>
                        
                        <div class="options mt-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option1" value="A">
                                <label class="form-check-label" for="option1">
                                    <strong>A)</strong> Un mot de passe fort doit contenir au moins 12 caractères avec des majuscules, minuscules, chiffres et symboles.
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option2" value="B">
                                <label class="form-check-label" for="option2">
                                    <strong>B)</strong> La double authentification (2FA) ajoute une couche de sécurité supplémentaire en demandant un code en plus du mot de passe.
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option3" value="C">
                                <label class="form-check-label" for="option3">
                                    <strong>C)</strong> Il est sécurisé de partager ses identifiants de connexion avec ses amis de confiance.
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="answer" id="option4" value="D">
                                <label class="form-check-label" for="option4">
                                    <strong>D)</strong> Les mises à jour de sécurité des logiciels doivent être installées dès qu'elles sont disponibles.
                                </label>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-success btn-lg" onclick="validateAnswer()">
                                ✅ Valider ma réponse
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateAnswer() {
    const selectedAnswer = document.querySelector('input[name="answer"]:checked');
    
    if (!selectedAnswer) {
        alert('⚠️ Veuillez sélectionner une réponse avant de valider.');
        return;
    }
    
    con
