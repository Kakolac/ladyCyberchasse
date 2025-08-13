<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyberchasse - Lieu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../styles/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../accueil/">
                🏫 Cyberchasse
            </a>
            
            <div class="navbar-nav ms-auto">
                <!-- Bouton Caméra QR Code -->
                <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                    📷 Scanner QR
                </button>
                
                <!-- Menu utilisateur -->
                <div class="navbar-nav">
                    <span class="navbar-text me-3">
                         Équipe: <?php echo isset($_SESSION['team_name']) ? $_SESSION['team_name'] : 'Non connecté'; ?>
                    </span>
                    <a class="nav-link" href="../../logout.php">🚪 Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Modal Scanner QR Code (même code que dans le header principal) -->
    <div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrScannerModalLabel">
                        📷 Scanner QR Code
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Zone de la caméra -->
                    <div id="cameraContainer" class="text-center mb-3">
                        <video id="camera" autoplay playsinline style="max-width: 100%; border-radius: 8px;"></video>
                    </div>
                    
                    <!-- Indicateur de scan -->
                    <div id="scanIndicator" class="text-center">
                        <div class="alert alert-info">
                            🔍 Pointez la caméra vers un QR code
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
                    </div>
                    
                    <!-- Résultat du scan -->
                    <div id="scanResult" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeCameraBtn">
                        🔒 Fermer la Caméra
                    </button>
                    <button type="button" class="btn btn-primary" id="openDetectedPage" style="display: none;">
                        🚀 Aller sur la page détectée
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages de debug -->
    <div id="debugMessages" style="display: none;"></div>

    <!-- Scripts et code scanner (même code) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ... même code JavaScript ... -->
    <script>
    // Charger jsQR depuis CDN
    (function loadJSQR() {
        if (typeof jsQR === 'undefined') {
            console.log('Chargement de jsQR...');
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js';
            script.onload = function() {
                console.log('jsQR chargé avec succès');
                initQRScanner();
            };
            script.onerror = function() {
                console.error('Erreur lors du chargement de jsQR');
                showDebugMessage('❌ Erreur: jsQR non chargé');
            };
            document.head.appendChild(script);
        } else {
            console.log('jsQR déjà disponible');
            initQRScanner();
        }
    })();

    function showDebugMessage(message) {
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
        
        const messages = debugDiv.querySelectorAll('div');
        if (messages.length > 10) {
            messages[0].remove();
        }
    }

    function initQRScanner() {
        let stream = null;
        let canvas = null;
        let ctx = null;
        let scanning = false;
        let detectedUrl = null;

        // Écouter l'ouverture du modal
        document.getElementById('qrScannerModal').addEventListener('show.bs.modal', function() {
            showDebugMessage('📱 Modal scanner ouvert');
            startCamera();
        });

        // Écouter la fermeture du modal
        document.getElementById('qrScannerModal').addEventListener('hidden.bs.modal', function() {
            showDebugMessage(' Modal scanner fermé');
            stopCamera();
        });

        function startCamera() {
            showDebugMessage('📹 Démarrage de la caméra...');
            
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                showDebugMessage('❌ API caméra non disponible');
                alert('Votre navigateur ne supporte pas l\'accès à la caméra');
                return;
            }

            const constraints = {
                video: {
                    facingMode: 'environment',
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                }
            };

            navigator.mediaDevices.getUserMedia(constraints)
            .then(mediaStream => {
                stream = mediaStream;
                const camera = document.getElementById('camera');
                camera.srcObject = stream;
                
                showDebugMessage('✅ Caméra activée');
                
                camera.onloadedmetadata = function() {
                    showDebugMessage(`📐 Vidéo: ${camera.videoWidth}x${camera.videoHeight}`);
                    setupScanning();
                };
            })
            .catch(err => {
                const error = `Erreur caméra: ${err.name} - ${err.message}`;
                showDebugMessage(`❌ ${error}`);
                
                if (err.name === 'NotAllowedError') {
                    alert('Accès à la caméra refusé. Vérifiez les permissions.');
                } else if (err.name === 'NotFoundError') {
                    alert('Aucune caméra trouvée.');
                } else {
                    alert('Erreur d\'accès à la caméra: ' + err.message);
                }
            });
        }

        function setupScanning() {
            showDebugMessage('🔧 Configuration du scanner...');
            
            canvas = document.createElement('canvas');
            canvas.style.display = 'none';
            document.body.appendChild(canvas);
            ctx = canvas.getContext('2d');
            
            startScanning();
        }

        function startScanning() {
            scanning = true;
            showDebugMessage('🚀 Démarrage du scan QR...');
            scanFrame();
        }

        function scanFrame() {
            if (!scanning) return;
            
            try {
                const camera = document.getElementById('camera');
                if (!camera.videoWidth) {
                    requestAnimationFrame(scanFrame);
                    return;
                }

                canvas.width = camera.videoWidth;
                canvas.height = camera.videoHeight;
                ctx.drawImage(camera, 0, 0, canvas.width, canvas.height);

                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                
                if (typeof jsQR !== 'undefined') {
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    
                    if (code) {
                        showDebugMessage(`🎯 QR Code détecté: ${code.data}`);
                        handleQRCode(code.data);
                        return;
                    }
                }

                if (scanning) {
                    requestAnimationFrame(scanFrame);
                }
                
            } catch (error) {
                showDebugMessage(`❌ Erreur scan: ${error.message}`);
                if (scanning) {
                    requestAnimationFrame(scanFrame);
                }
            }
        }

        function handleQRCode(data) {
            scanning = false;
            detectedUrl = data;
            
            // Masquer l'indicateur de scan
            document.getElementById('scanIndicator').style.display = 'none';
            
            // Afficher le résultat
            const resultDiv = document.getElementById('scanResult');
            resultDiv.innerHTML = `
                <div class="alert alert-success text-center">
                    <h5>🎯 QR Code détecté !</h5>
                    <p><strong>URL détectée:</strong></p>
                    <code class="d-block p-2 bg-light mb-3">${data}</code>
                    <p>Voulez-vous aller sur cette page ?</p>
                </div>
            `;
            resultDiv.style.display = 'block';
            
            // Afficher le bouton d'ouverture
            document.getElementById('openDetectedPage').style.display = 'inline-block';
        }

        function stopCamera() {
            if (scanning) {
                scanning = false;
            }
            
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            
            if (canvas) {
                document.body.removeChild(canvas);
                canvas = null;
                ctx = null;
            }
            
            // Réinitialiser l'interface
            document.getElementById('scanIndicator').style.display = 'block';
            document.getElementById('scanResult').style.display = 'none';
            document.getElementById('openDetectedPage').style.display = 'none';
            detectedUrl = null;
            
            showDebugMessage(' Caméra arrêtée');
        }

        // Gestion des boutons
        document.getElementById('closeCameraBtn').addEventListener('click', function() {
            stopCamera();
            const modal = bootstrap.Modal.getInstance(document.getElementById('qrScannerModal'));
            modal.hide();
        });

        document.getElementById('openDetectedPage').addEventListener('click', function() {
            if (detectedUrl) {
                showDebugMessage(`🚀 Navigation vers: ${detectedUrl}`);
                window.location.href = detectedUrl;
            }
        });
    }
    </script>