<?php
session_start();
if (!isset($_SESSION['team_name'])) {
    header('Location: ../../login.php');
    exit();
}

include './header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>🏫 Carte du Lycée - Cyberchasse</h2>
                </div>
                <div class="card-body">
                    <!-- Bouton Caméra -->
                    <div class="text-center mb-4">
                        <button id="cameraBtn" class="btn btn-primary btn-lg">
                            📷 Ouvrir la Caméra
                        </button>
                    </div>
                    
                    <!-- Zone d'affichage de la caméra -->
                    <div id="cameraContainer" class="text-center mb-4" style="display: none;">
                        <video id="camera" autoplay playsinline style="max-width: 100%; border-radius: 8px;"></video>
                        <div class="mt-2">
                            <button id="closeCameraBtn" class="btn btn-secondary">Fermer la Caméra</button>
                        </div>
                    </div>
                    
                    <p>Choisissez un lieu pour commencer votre exploration :</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>📚 Lieux principaux</h5>
                            <div class="list-group mb-3">
                                <a href="../cdi/" class="list-group-item list-group-item-action">
                                    📚 CDI - Centre de Documentation
                                </a>
                                <a href="../salle_info/" class="list-group-item list-group-item-action">
                                    💻 Salle Informatique
                                </a>
                                <a href="../vie_scolaire/" class="list-group-item list-group-item-action">
                                    👥 Vie Scolaire
                                </a>
                                <a href="../labo_physique/" class="list-group-item list-group-item-action">
                                    ⚡ Laboratoire de Physique
                                </a>
                                <a href="../labo_chimie/" class="list-group-item list-group-item-action">
                                    🧪 Laboratoire de Chimie
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>🔍 Autres lieux</h5>
                            <div class="list-group mb-3">
                                <a href="../labo_svt/" class="list-group-item list-group-item-action">
                                    🌱 Laboratoire SVT
                                </a>
                                <a href="../salle_arts/" class="list-group-item list-group-item-action">
                                    🎨 Salle d'Arts
                                </a>
                                <a href="../salle_musique/" class="list-group-item list-group-item-action">
                                    🎵 Salle de Musique
                                </a>
                                <a href="../gymnase/" class="list-group-item list-group-item-action">
                                    🏃 Gymnase
                                </a>
                                <a href="../cantine/" class="list-group-item list-group-item-action">
                                    🍽️ Cantine
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="list-group mb-3">
                                <a href="../direction/" class="list-group-item list-group-item-action">
                                    🏢 Direction
                                </a>
                                <a href="../secretariat/" class="list-group-item list-group-item-action">
                                    📝 Secrétariat
                                </a>
                                <a href="../salle_reunion/" class="list-group-item list-group-item-action">
                                    🤝 Salle de Réunion
                                </a>
                                <a href="../salle_profs/" class="list-group-item list-group-item-action">
                                    👨‍🏫 Salle des Profs
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="list-group mb-3">
                                <a href="../atelier_techno/" class="list-group-item list-group-item-action">
                                    ⚙️ Atelier Techno
                                </a>
                                <a href="../salle_langues/" class="list-group-item list-group-item-action">
                                    🌍 Salle de Langues
                                </a>
                                <a href="../internat/" class="list-group-item list-group-item-action">
                                    🏠 Internat
                                </a>
                                <a href="../infirmerie/" class="list-group-item list-group-item-action">
                                    🏥 Infirmerie
                                </a>
                                <a href="../cour/" class="list-group-item list-group-item-action">
                                    🌳 Cour
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Charger jsQR depuis CDN
(function loadJSQR() {
    if (typeof jsQR === 'undefined') {
        console.log('Chargement de jsQR...');
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js';
        script.onload = function() {
            console.log('jsQR chargé avec succès');
            initScanner();
        };
        script.onerror = function() {
            console.error('Erreur lors du chargement de jsQR');
            showDebugMessage('❌ Erreur: jsQR non chargé');
        };
        document.head.appendChild(script);
    } else {
        console.log('jsQR déjà disponible');
        initScanner();
    }
})();

function showDebugMessage(message) {
    // Créer ou mettre à jour le message de debug
    let debugDiv = document.getElementById('debugMessages');
    if (!debugDiv) {
        debugDiv = document.createElement('div');
        debugDiv.id = 'debugMessages';
        debugDiv.style.cssText = `
            position: fixed;
            top: 10px;
            left: 10px;
            right: 10px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            z-index: 9999;
            max-height: 200px;
            overflow-y: auto;
        `;
        document.body.appendChild(debugDiv);
    }
    
    const timestamp = new Date().toLocaleTimeString();
    debugDiv.innerHTML += `<div>[${timestamp}] ${message}</div>`;
    
    // Garder seulement les 10 derniers messages
    const messages = debugDiv.querySelectorAll('div');
    if (messages.length > 10) {
        messages[0].remove();
    }
}

function initScanner() {
    document.addEventListener('DOMContentLoaded', function() {
        const cameraBtn = document.getElementById('cameraBtn');
        const cameraContainer = document.getElementById('cameraContainer');
        const camera = document.getElementById('camera');
        const closeCameraBtn = document.getElementById('closeCameraBtn');
        
        // Détection de l'environnement
        const isLocalhost = window.location.hostname === 'localhost';
        const isSecure = window.location.protocol === 'https:';
        const isAndroid = /Android/i.test(navigator.userAgent);
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        
        showDebugMessage(`📱 Environnement: ${isAndroid ? 'Android' : isIOS ? 'iOS' : 'Desktop'}, HTTPS: ${isSecure}`);
        showDebugMessage(`🌐 User Agent: ${navigator.userAgent.substring(0, 100)}...`);
        
        // Ajouter l'événement au bouton original
        if (cameraBtn) {
            cameraBtn.addEventListener('click', tryCamera);
            showDebugMessage('✅ Bouton caméra configuré');
        } else {
            showDebugMessage('❌ Bouton caméra non trouvé');
        }
        
        let stream = null;
        let canvas = null;
        let ctx = null;
        let scanning = false;
        
        function tryCamera() {
            showDebugMessage(' Tentative d\'accès à la caméra...');
            
            // Vérifier les capacités du navigateur
            if (!navigator.mediaDevices) {
                const error = 'navigator.mediaDevices non disponible';
                showDebugMessage(`❌ ${error}`);
                alert(error);
                return;
            }
            
            if (!navigator.mediaDevices.getUserMedia) {
                const error = 'getUserMedia non disponible';
                showDebugMessage(`❌ ${error}`);
                alert(error);
                return;
            }
            
            showDebugMessage('✅ API caméra disponible');
            
            // Essayer d'abord avec des contraintes simples
            const constraints = {
                video: {
                    facingMode: 'environment',
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                }
            };
            
            showDebugMessage(' Demande d\'accès à la caméra...');
            
            navigator.mediaDevices.getUserMedia(constraints)
            .then(mediaStream => {
                stream = mediaStream;
                camera.srcObject = stream;
                cameraContainer.style.display = 'block';
                if (cameraBtn) cameraBtn.style.display = 'none';
                
                showDebugMessage('✅ Stream caméra obtenu');
                
                // Attendre que la vidéo soit chargée
                camera.onloadedmetadata = function() {
                    showDebugMessage(` Vidéo chargée: ${camera.videoWidth}x${camera.videoHeight}`);
                    setupQRScanning();
                };
                
                camera.onerror = function(error) {
                    showDebugMessage(`❌ Erreur vidéo: ${error.message}`);
                };
                
                camera.oncanplay = function() {
                    showDebugMessage('▶️ Vidéo prête à être lue');
                };
                
            })
            .catch(err => {
                const error = `Erreur caméra: ${err.name} - ${err.message}`;
                showDebugMessage(`❌ ${error}`);
                console.error('Erreur caméra:', err);
                
                if (err.name === 'NotAllowedError') {
                    showDebugMessage(' Accès refusé - Vérifiez les permissions');
                    alert('Accès à la caméra refusé.\n\nSur mobile, vérifiez que vous avez autorisé l\'accès à la caméra dans les paramètres de votre navigateur.');
                } else if (err.name === 'NotFoundError') {
                    showDebugMessage(' Aucune caméra trouvée');
                    alert('Aucune caméra trouvée sur votre appareil.');
                } else if (err.name === 'NotSupportedError') {
                    showDebugMessage('❌ Format non supporté');
                    alert('Format vidéo non supporté par votre appareil.');
                } else if (err.name === 'NotReadableError') {
                    showDebugMessage(' Caméra non lisible');
                    alert('La caméra est utilisée par une autre application.');
                } else {
                    showDebugMessage(`❌ Erreur inconnue: ${err.name}`);
                    alert('Erreur d\'accès à la caméra: ' + err.message);
                }
            });
        }
        
        function setupQRScanning() {
            showDebugMessage('🔧 Configuration du scanner...');
            
            // Créer le canvas pour l'analyse
            canvas = document.createElement('canvas');
            canvas.style.display = 'none';
            document.body.appendChild(canvas);
            ctx = canvas.getContext('2d');
            
            // Ajouter l'indicateur de scan
            const scanIndicator = document.createElement('div');
            scanIndicator.id = 'scanIndicator';
            scanIndicator.innerHTML = `
                <div class="text-center mt-3">
                    <div class="alert alert-success">
                        🔍 Scan en cours... Pointez la caméra vers un QR code
                    </div>
                    <div class="qr-frame" style="
                        border: 2px solid #28a745;
                        border-radius: 10px;
                        padding: 10px;
                        margin: 10px auto;
                        max-width: 300px;
                        text-align: center;
                    ">
                        <div style="font-size: 24px;">📱</div>
                        <div>Zone de scan</div>
                    </div>
                    <div class="mt-2">
                        <button id="testScanBtn" class="btn btn-warning">
                            Test Scan (Debug)
                        </button>
                        <button id="clearDebugBtn" class="btn btn-secondary ml-2">
                            🗑️ Effacer Debug
                        </button>
                        <button id="testCameraBtn" class="btn btn-info ml-2">
                            📹 Test Caméra
                        </button>
                    </div>
                </div>
            `;
            cameraContainer.appendChild(scanIndicator);
            
            // Bouton de test pour debug
            document.getElementById('testScanBtn').addEventListener('click', testScan);
            document.getElementById('clearDebugBtn').addEventListener('click', clearDebug);
            document.getElementById('testCameraBtn').addEventListener('click', testCamera);
            
            // Démarrer le scan automatique
            startQRScanning();
        }
        
        function testCamera() {
            showDebugMessage('📹 Test de la caméra...');
            
            if (stream) {
                const tracks = stream.getTracks();
                showDebugMessage(`📊 Nombre de tracks: ${tracks.length}`);
                
                tracks.forEach((track, index) => {
                    showDebugMessage(` Track ${index}: ${track.kind} - ${track.label}`);
                    showDebugMessage(`📐 Résolution: ${track.getSettings().width}x${track.getSettings().height}`);
                });
            } else {
                showDebugMessage('❌ Aucun stream disponible');
            }
            
            if (camera.videoWidth > 0) {
                showDebugMessage(`📐 Dimensions vidéo: ${camera.videoWidth}x${camera.videoHeight}`);
            } else {
                showDebugMessage('❌ Dimensions vidéo non disponibles');
            }
        }
        
        function startQRScanning() {
            scanning = true;
            showDebugMessage('🚀 Démarrage du scan QR...');
            scanFrame();
        }
        
        function scanFrame() {
            if (!scanning || !camera.videoWidth) {
                showDebugMessage('⏸️ Scan arrêté ou vidéo non prête');
                return;
            }
            
            try {
                // Dessiner la vidéo sur le canvas
                canvas.width = camera.videoWidth;
                canvas.height = camera.videoHeight;
                ctx.drawImage(camera, 0, 0, canvas.width, canvas.height);
                
                // Analyser l'image avec jsQR
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                
                if (typeof jsQR !== 'undefined') {
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    
                    if (code) {
                        // QR code détecté !
                        showDebugMessage(`🎯 QR Code détecté: ${code.data}`);
                        handleQRCode(code.data);
                        return;
                    }
                } else {
                    showDebugMessage('❌ jsQR non disponible');
                    return;
                }
                
                // Continuer le scan
                if (scanning) {
                    requestAnimationFrame(scanFrame);
                }
                
            } catch (error) {
                showDebugMessage(`❌ Erreur scan: ${error.message}`);
                // Continuer le scan malgré l'erreur
                if (scanning) {
                    requestAnimationFrame(scanFrame);
                }
            }
        }
        
        function testScan() {
            showDebugMessage('🧪 Test de scan manuel...');
            
            if (canvas && ctx && camera.videoWidth) {
                canvas.width = camera.videoWidth;
                canvas.height = camera.videoHeight;
                ctx.drawImage(camera, 0, 0, canvas.width, canvas.height);
                
                showDebugMessage(`📐 Canvas: ${canvas.width}x${canvas.height}`);
                
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                
                if (typeof jsQR !== 'undefined') {
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    
                    if (code) {
                        showDebugMessage(`✅ QR détecté: ${code.data}`);
                        handleQRCode(code.data);
                    } else {
                        showDebugMessage('❌ Aucun QR code détecté');
                        alert('Aucun QR code détecté dans cette image');
                    }
                } else {
                    showDebugMessage('❌ jsQR non disponible');
                    alert('jsQR n\'est pas disponible');
                }
            } else {
                showDebugMessage('❌ Caméra ou canvas non prêt');
                alert('Caméra ou canvas non prêt');
            }
        }
        
        function clearDebug() {
            const debugDiv = document.getElementById('debugMessages');
            if (debugDiv) {
                debugDiv.innerHTML = '';
            }
        }
        
        function handleQRCode(data) {
            // Arrêter le scan
            scanning = false;
            
            // Afficher le résultat
            const resultDiv = document.createElement('div');
            resultDiv.innerHTML = `
                <div class="alert alert-success mt-3">
                    <h5>🎯 QR Code détecté !</h5>
                    <p><strong>Contenu:</strong> ${data}</p>
                    <button class="btn btn-primary" onclick="window.location.href='${data}'">
                        Accéder au lieu
                    </button>
                    <button class="btn btn-secondary" onclick="restartScan()">
                        Scanner un autre QR
                    </button>
                </div>
            `;
            
            // Remplacer l'indicateur de scan
            const scanIndicator = document.getElementById('scanIndicator');
            if (scanIndicator) {
                scanIndicator.replaceWith(resultDiv);
            }
        }
        
        function restartScan() {
            // Supprimer le résultat
            const resultDiv = document.querySelector('.alert-success');
            if (resultDiv) {
                resultDiv.remove();
            }
            
            // Recréer l'indicateur de scan
            setupQRScanning();
        }
        
        closeCameraBtn.addEventListener('click', function() {
            if (scanning) {
                scanning = false;
            }
            
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            
            if (canvas) {
                document.body.removeChild(canvas);
            }
            
            cameraContainer.style.display = 'none';
            if (cameraBtn) cameraBtn.style.display = 'inline-block';
            
            // Nettoyer les éléments ajoutés
            const scanIndicator = document.getElementById('scanIndicator');
            if (scanIndicator) scanIndicator.remove();
            
            const resultDiv = document.querySelector('.alert-success');
            if (resultDiv) resultDiv.remove();
            
            showDebugMessage('🔄 Scanner fermé');
        });
    });
}
</script>

<?php include './footer.php'; ?>
