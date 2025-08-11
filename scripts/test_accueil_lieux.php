<?php
/**
 * Script de test de la page d'accueil des lieux
 * Lancez depuis : http://localhost:8888/scripts/test_accueil_lieux.php
 */

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Accueil Lieux - Cyberchasse</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-8'>
                <div class='card'>
                    <div class='card-header bg-primary text-white text-center'>
                        <h2>🧪 Test de la Page d'Accueil des Lieux</h2>
                    </div>
                    <div class='card-body'>
                        <h4>🔗 Liens de test</h4>
                        
                        <div class='alert alert-info'>
                            <strong>Page d'accueil des lieux :</strong><br>
                            <a href='../lieux/accueil/' target='_blank' class='btn btn-success btn-lg mt-2'>
                                🏠 Tester l'accueil des lieux
                            </a>
                        </div>
                        
                        <div class='alert alert-warning'>
                            <strong>Vérifications à faire :</strong>
                            <ul>
                                <li>L'image de fond bg.jpg s'affiche-t-elle dans le header ?</li>
                                <li>Les styles CSS s'appliquent-ils (couleurs, polices) ?</li>
                                <li>La liste des lieux s'affiche-t-elle correctement ?</li>
                                <li>Les liens vers les autres lieux fonctionnent-ils ?</li>
                            </ul>
                        </div>
                        
                        <hr>
                        
                        <h4>🔧 Diagnostic des chemins</h4>
                        <div class='row'>
                            <div class='col-md-6'>
                                <h6>Structure des dossiers :</h6>
                                <ul>
                                    <li><code>lieux/</code> ← Dossier principal</li>
                                    <li><code>lieux/header.php</code> ← Header des lieux</li>
                                    <li><code>lieux/footer.php</code> ← Footer des lieux</li>
                                    <li><code>lieux/accueil/index.php</code> ← Page d'accueil</li>
                                </ul>
                            </div>
                            <div class='col-md-6'>
                                <h6>Chemins dans index.php :</h6>
                                <ul>
                                    <li><code>include '../header.php'</code> → <code>lieux/header.php</code></li>
                                    <li><code>include '../footer.php'</code> → <code>lieux/footer.php</code></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class='text-center mt-4'>
                            <a href='../lieux/accueil/' class='btn btn-success btn-lg' target='_blank'>🏠 Tester maintenant</a>
                            <a href='../' class='btn btn-primary btn-lg'> Retour au projet</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
