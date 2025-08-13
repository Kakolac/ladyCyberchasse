<?php
/**
 * Script pour ajouter le composant QR scanner sur tous les headers des lieux
 * Lancez depuis : http://localhost:8888/scripts/add_qr_scanner_to_lieux_headers.php
 */

// Configuration
$baseDir = dirname(__DIR__);
$lieuxDir = $baseDir . '/lieux';

// Composant QR scanner à ajouter dans la section user-info
$qrScannerComponent = '
                    <button id="qrScannerBtn" class="btn btn-outline-light me-2" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 8px 16px; border-radius: 8px; font-size: 14px;">
                        📷 Scanner QR
                    </button>
';

// Composant QR scanner overlay à ajouter avant la fermeture de </body>
$qrScannerOverlay = '
    <!-- Overlay Scanner QR Code - CSS optimisé mobile -->
    <div id="qrScannerOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 9999; overflow-y: auto;">
        <div style="position: relative; min-height: 100vh; padding: 20px; box-sizing: border-box;">
            <!-- Bouton fermer - Position fixe en haut -->
            <button id="closeScannerBtn" style="position: fixed; top: 15px; right: 15px; z-index: 10000; background: #dc3545; color: white; border: none; padding: 12px; border-radius: 50%; font-size: 18px; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">✕</button>
            
            <!-- Contenu du scanner - Centré et responsive -->
            <div style="position: relative; top: 60px; text-align: center; color: white; max-width: 100%;">
                <h3 style="margin-bottom: 20px; font-size: 24px;">📱 Scanner QR Code</h3>
                
                <!-- Zone de la caméra - Responsive -->
                <div id="cameraContainer" style="margin-bottom: 20px;">
                    <video id="camera" autoplay playsinline style="max-width: 100%; width: 100%; max-width: 400px; border-radius: 12px; border: 3px solid white; box-shadow: 0 4px 20px rgba(255,255,255,0.2);"></video>
                </div>
                
                <!-- Indicateur de scan - Optimisé mobile -->
                <div id="scanIndicator">
                    <div style="background: rgba(0,123,255,0.9); padding: 15px; border-radius: 12px; margin: 15px 0; font-size: 16px;">
                        🔍 Pointez la caméra vers un QR code
                    </div>
                    <div style="border: 3px solid #28a745; border-radius: 15px; padding: 20px; margin: 15px auto; max-width: 300px; background: rgba(40,167,69,0.1);">
                        <div style="font-size: 28px;">📱</div>
                        <div style="font-size: 16px;">Zone de scan</div>
                    </div>
                </div>
                
                <!-- Résultat du scan - Optimisé mobile -->
                <div id="scanResult" style="display: none; margin: 20px 0;"></div>
                
                <!-- Boutons - Responsive et accessibles -->
                <div style="margin-top: 25px; display: flex; flex-direction: column; align-items: center; gap: 15px;">
                    <button id="closeCameraBtn" class="btn btn-secondary" style="min-width: 120px; padding: 12px 20px; font-size: 16px;">🔒 Fermer</button>
                    <button id="openDetectedPage" class="btn btn-primary" style="display: none; min-width: 200px; padding: 15px 25px; font-size: 16px; background: #28a745; border-color: #28a745;">🚀 Se téléporter sur le lieu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages de debug -->
    <div id="debugMessages" style="display: none;"></div>

    <!-- Scanner QR Code - JavaScript -->
    <script>
    // Attendre que TOUT soit chargé
    window.addEventListener("load", function() {
        console.log("Page complètement chargée, initialisation du scanner...");
        
        // Attendre encore un peu pour être sûr
        setTimeout(function() {
            initQRScanner();
        }, 100);
    });

    // Variables globales
    let qrScanner = null;
    let detectedUrl = null;
    let scannerInitialized = false;

    function initQRScanner() {
        if (scannerInitialized) {
            console.log("Scanner déjà initialisé");
            return;
        }

        console.log("🚀 Initialisation du scanner QR...");
        
        // Vérifier que tous les éléments existent
        const qrScannerBtn = document.getElementById("qrScannerBtn");
        const closeScannerBtn = document.getElementById("closeScannerBtn");
        const closeCameraBtn = document.getElementById("closeCameraBtn");
        const openDetectedPageBtn = document.getElementById("openDetectedPage");
        
        if (!qrScannerBtn || !closeScannerBtn || !closeCameraBtn || !openDetectedPageBtn) {
            console.error("❌ Éléments manquants, réessai dans 500ms...");
            setTimeout(initQRScanner, 500);
            return;
        }

        // Ajouter les événements
        qrScannerBtn.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("📱 Bouton scanner cliqué");
            openQRScanner();
        });

        closeScannerBtn.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("📱 Bouton fermer cliqué");
            closeQRScanner();
        });

        closeCameraBtn.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("📱 Bouton fermer caméra cliqué");
            closeQRScanner();
        });

        openDetectedPageBtn.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("📱 Bouton aller sur page cliqué");
            goToDetectedPage();
        });

        scannerInitialized = true;
        console.log("✅ Scanner QR initialisé avec succès");
        
        // Afficher un message de confirmation
        showDebugMessage("✅ Scanner QR prêt à l\'emploi");
    }

    function showDebugMessage(message) {
        console.log(message);
        
        let debugDiv = document.getElementById("debugMessages");
        if (!debugDiv) {
            debugDiv = document.createElement("div");
            debugDiv.id = "debugMessages";
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
        
        const messages = debugDiv.querySelectorAll("div");
        if (messages.length > 10) {
            messages[0].remove();
        }
    }

    function openQRScanner() {
        console.log("🔓 Ouverture du scanner...");
        showDebugMessage("🔓 Ouverture du scanner...");
        
        document.getElementById("qrScannerOverlay").style.display = "block";
        
        // Empêcher le scroll de la page
        document.body.style.overflow = "hidden";
        
        // Charger jsQR si pas déjà fait
        if (typeof jsQR === "undefined") {
            loadJSQR();
        } else {
            startCamera();
        }
    }

    function closeQRScanner() {
        console.log("🔒 Fermeture du scanner...");
        showDebugMessage("🔒 Fermeture du scanner...");
        
        document.getElementById("qrScannerOverlay").style.display = "none";
        
        // Restaurer le scroll de la page
        document.body.style.overflow = "auto";
        
        stopCamera();
    }

    function loadJSQR() {
        console.log("📦 Chargement de jsQR...");
        showDebugMessage("📦 Chargement de jsQR...");
        
        const script = document.createElement("script");
        script.src = "https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js";
        script.onload = function() {
            console.log("✅ jsQR chargé, démarrage de la caméra...");
            showDebugMessage("✅ jsQR chargé, démarrage de la caméra...");
            startCamera();
        };
        script.onerror = function() {
            console.error("❌ Erreur chargement jsQR");
            showDebugMessage("❌ Erreur chargement jsQR");
            alert("Erreur lors du chargement du scanner");
        };
        document.head.appendChild(script);
    }

    function startCamera() {
        console.log("📹 Démarrage de la caméra...");
        showDebugMessage("📹 Démarrage de la caméra...");
        
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showDebugMessage("❌ API caméra non disponible");
            alert("Votre navigateur ne supporte pas l\'accès à la caméra");
            return;
        }

        const constraints = {
            video: {
                facingMode: "environment",
                width: { ideal: 640 },
                height: { ideal: 480 }
            }
        };

        navigator.mediaDevices.getUserMedia(constraints)
        .then(mediaStream => {
            const camera = document.getElementById("camera");
            camera.srcObject = mediaStream;
            
            qrScanner = {
                stream: mediaStream,
                scanning: false
            };
            
            showDebugMessage("✅ Caméra démarrée, début du scan...");
            startScanning();
        })
        .catch(err => {
            const error = `Erreur caméra: ${err.name} - ${err.message}`;
            console.error(error);
            showDebugMessage(`❌ ${error}`);
            
            if (err.name === "NotAllowedError") {
                alert("Accès à la caméra refusé. Vérifiez les permissions.");
            } else if (err.name === "NotFoundError") {
                alert("Aucune caméra trouvée.");
            } else {
                alert("Erreur d\'accès à la caméra: " + err.message);
            }
        });
    }

    function startScanning() {
        if (!qrScanner) return;
        
        qrScanner.scanning = true;
        showDebugMessage("🚀 Scan démarré...");
        scanFrame();
    }

    function scanFrame() {
        if (!qrScanner || !qrScanner.scanning) return;
        
        try {
            const camera = document.getElementById("camera");
            if (!camera.videoWidth) {
                requestAnimationFrame(scanFrame);
                return;
            }

            // Créer un canvas temporaire pour l\'analyse
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            canvas.width = camera.videoWidth;
            canvas.height = camera.videoHeight;
            
            // Dessiner la vidéo sur le canvas
            ctx.drawImage(camera, 0, 0, camera.videoWidth, camera.videoHeight);
            
            // Analyser l\'image
            const imageData = ctx.getImageData(0, 0, camera.videoWidth, camera.videoHeight);
            
            if (typeof jsQR !== "undefined") {
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                
                if (code) {
                    showDebugMessage(`🎯 QR Code détecté: ${code.data}`);
                    handleQRCode(code.data);
                    return;
                }
            }

            // Continuer le scan
            if (qrScanner.scanning) {
                requestAnimationFrame(scanFrame);
            }
            
        } catch (error) {
            showDebugMessage(`❌ Erreur scan: ${error.message}`);
            if (qrScanner.scanning) {
                requestAnimationFrame(scanFrame);
            }
        }
    }

    function handleQRCode(data) {
        if (!qrScanner) return;
        
        qrScanner.scanning = false;
        detectedUrl = data;
        
        // Masquer l\'indicateur de scan
        document.getElementById("scanIndicator").style.display = "none";
        
        // Analyser l\'URL pour extraire le nom du lieu
        const lieuInfo = extractLieuInfo(data);
        
        // Afficher le résultat - CSS optimisé mobile
        const resultDiv = document.getElementById("scanResult");
        resultDiv.innerHTML = `
            <div style="background: rgba(40,167,69,0.95); padding: 20px; border-radius: 15px; margin: 20px 0; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                <h4 style="margin-bottom: 15px; font-size: 20px;">🎯 QR Code détecté !</h4>
                <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 15px 0; text-align: center;">
                    <div style="font-size: 24px; margin-bottom: 10px;">${lieuInfo.icon}</div>
                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">${lieuInfo.nom}</div>
                    <div style="font-size: 14px; opacity: 0.9;">${lieuInfo.description}</div>
                </div>
                <p style="margin-bottom: 20px; font-size: 16px;">Voulez-vous vous téléporter sur ce lieu ?</p>
            </div>
        `;
        resultDiv.style.display = "block";
        
        // Afficher le bouton d\'ouverture - Plus visible sur mobile
        const openDetectedPageBtn = document.getElementById("openDetectedPage");
        openDetectedPageBtn.style.display = "inline-block";
        openDetectedPageBtn.innerHTML = "🚀 Se téléporter sur le lieu";
        
        // Scroll vers le bouton pour qu\'il soit visible
        openDetectedPageBtn.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    function extractLieuInfo(url) {
        try {
            // Analyser l\'URL pour extraire le lieu
            const urlObj = new URL(url);
            const lieu = urlObj.searchParams.get("lieu");
            
            if (lieu) {
                // Mapping des lieux avec leurs informations
                const lieuxMapping = {
                    "accueil": { nom: "Hall d\'entrée", description: "Point de départ de la cyberchasse", icon: "🏠" },
                    "cantine": { nom: "Cantine", description: "Zone de restauration", icon: "🍽️" },
                    "cdi": { nom: "CDI", description: "Centre de Documentation et d\'Information", icon: "📚" },
                    "cour": { nom: "Cour", description: "Espace extérieur", icon: "🌳" },
                    "direction": { nom: "Direction", description: "Bureau de la direction", icon: "👔" },
                    "gymnase": { nom: "Gymnase", description: "Salle de sport", icon: "⚽" },
                    "infirmerie": { nom: "Infirmerie", description: "Zone médicale", icon: "🏥" },
                    "internat": { nom: "Internat", description: "Zone d\'hébergement", icon: "🏠" },
                    "labo_chimie": { nom: "Laboratoire de Chimie", description: "Expériences chimiques", icon: "🧪" },
                    "labo_physique": { nom: "Laboratoire de Physique", description: "Expériences physiques", icon: "⚡" },
                    "labo_svt": { nom: "Laboratoire SVT", description: "Sciences de la vie", icon: "🔬" },
                    "salle_arts": { nom: "Salle d\'Arts", description: "Arts plastiques", icon: "🎨" },
                    "salle_info": { nom: "Salle Informatique", description: "Cybersécurité et informatique", icon: "💻" },
                    "salle_langues": { nom: "Salle de Langues", description: "Apprentissage des langues", icon: "🌍" },
                    "salle_musique": { nom: "Salle de Musique", description: "Pratique musicale", icon: "🎵" },
                    "salle_profs": { nom: "Salle des Professeurs", description: "Espace enseignant", icon: "👨‍🏫" },
                    "salle_reunion": { nom: "Salle de Réunion", description: "Espace de réunion", icon: "🤝" },
                    "secretariat": { nom: "Secrétariat", description: "Bureau administratif", icon: "📋" },
                    "vie_scolaire": { nom: "Vie Scolaire", description: "Gestion des élèves", icon: "👥" },
                    "atelier_techno": { nom: "Atelier Technologique", description: "Technologies et innovation", icon: "⚙️" }
                };
                
                const lieuInfo = lieuxMapping[lieu];
                if (lieuInfo) {
                    return lieuInfo;
                }
            }
            
            // Fallback si le lieu n\'est pas reconnu
            return {
                nom: "Lieu inconnu",
                description: "Lieu non identifié",
                icon: "❓"
            };
            
        } catch (error) {
            // Fallback en cas d\'erreur d\'URL
            return {
                nom: "Lieu inconnu",
                description: "Erreur de lecture du QR code",
                icon: "❓"
            };
        }
    }

    function goToDetectedPage() {
        if (detectedUrl) {
            showDebugMessage(`🚀 Téléportation vers: ${detectedUrl}`);
            
            // Corriger l\'URL si elle contient une double adresse
            let correctedUrl = detectedUrl;
            
            // Si l\'URL commence par http://localhost:8888/lieux/, on la simplifie
            if (correctedUrl.includes("/lieux/")) {
                const urlParts = correctedUrl.split("/lieux/");
                if (urlParts.length > 1) {
                    correctedUrl = "./lieux/" + urlParts[1];
                }
            }
            
            // Si l\'URL est relative et commence par lieux/, on l\'ajuste
            if (correctedUrl.startsWith("lieux/")) {
                correctedUrl = "./" + correctedUrl;
            }
            
            showDebugMessage(`🔧 URL corrigée: ${correctedUrl}`);
            window.location.href = correctedUrl;
        }
    }

    function stopCamera() {
        if (qrScanner) {
            if (qrScanner.scanning) {
                qrScanner.scanning = false;
            }
            
            if (qrScanner.stream) {
                qrScanner.stream.getTracks().forEach(track => track.stop());
            }
            
            qrScanner = null;
        }
        
        // Réinitialiser l\'interface
        document.getElementById("scanIndicator").style.display = "block";
        document.getElementById("scanResult").style.display = "none";
        document.getElementById("openDetectedPage").style.display = "none";
        detectedUrl = null;
        
        showDebugMessage("📹 Caméra arrêtée");
    }
    </script>
';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Ajout Scanner QR sur Headers Lieux - Cyberchasse</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .progress { height: 25px; border-radius: 15px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        .warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class='container mt-5'>
        <div class='card'>
            <div class='card-header text-center'>
                <h1>📱 Ajout du Scanner QR sur tous les Headers des Lieux</h1>
                <p class='mb-0'>Script d\'automatisation pour intégrer le composant QR scanner dans la section user-info</p>
            </div>
            <div class='card-body'>
                <div class='alert alert-info'>
                    <h5>🎯 Objectif</h5>
                    <p>Ajouter automatiquement le composant QR scanner dans la section user-info existante de chaque lieu, à côté du bouton de déconnexion.</p>
                </div>
                
                <div class='progress mb-4'>
                    <div id='progressBar' class='progress-bar progress-bar-striped progress-bar-animated' role='progressbar' style='width: 0%'>0%</div>
                </div>
            </div>
        </div>";

// Récupérer tous les dossiers de lieux
$lieux = [];
$dirs = scandir($lieuxDir);
foreach ($dirs as $dir) {
    if ($dir !== '.' && $dir !== '..' && is_dir($lieuxDir . '/' . $dir) && $dir !== 'lieux') {
        $lieux[] = $dir;
    }
}

$totalLieux = count($lieux);
$updatedCount = 0;

echo "<div class='card mt-4'>
        <div class='card-header'>
            <h3>📁 Traitement des lieux ($totalLieux trouvés)</h3>
        </div>
        <div class='card-body'>";

foreach ($lieux as $lieu) {
    $headerFile = $lieuxDir . '/' . $lieu . '/header.php';
    
    echo "<div class='card mb-3'>
            <div class='card-body'>
                <h5 class='card-title'>🏫 $lieu</h5>";
    
    if (file_exists($headerFile)) {
        $headerContent = file_get_contents($headerFile);
        
        // Vérifier si le composant QR est déjà présent
        if (strpos($headerContent, 'qrScannerBtn') !== false) {
            echo "<span class='info'>ℹ️ Composant QR déjà présent</span>";
        } else {
            // Ajouter le bouton QR dans la section user-info existante
            $updatedContent = $headerContent;
            
            // Chercher la section user-info et ajouter le bouton QR avant le bouton de déconnexion
            if (preg_match('/(<div class="user-info">.*?<span class="team-name">.*?<\/span>)/s', $updatedContent, $matches)) {
                $userInfoStart = $matches[1];
                $updatedContent = str_replace(
                    $userInfoStart,
                    $userInfoStart . "\n                    " . $qrScannerComponent,
                    $updatedContent
                );
                
                // Ajouter le composant overlay avant </body>
                if (strpos($updatedContent, '</body>') !== false) {
                    $updatedContent = str_replace(
                        '</body>',
                        $qrScannerOverlay . "\n</body>",
                        $updatedContent
                    );
                } else {
                    // Si pas de </body>, ajouter à la fin
                    $updatedContent .= $qrScannerOverlay;
                }
                
                // Sauvegarder le fichier modifié
                if (file_put_contents($headerFile, $updatedContent)) {
                    echo "<span class='success'>✅ Composant QR ajouté dans user-info avec succès</span>";
                    $updatedCount++;
                } else {
                    echo "<span class='error'>❌ Erreur lors de la sauvegarde</span>";
                }
            } else {
                // Si pas de section user-info, essayer de la créer
                if (preg_match('/(<header class="bg-header">.*?<div class="header-content">.*?<h1>.*?<\/h1>)/s', $updatedContent, $matches)) {
                    $headerContent = $matches[1];
                    $userInfoSection = '
            <?php if (isset($_SESSION[\'team_name\'])): ?>
                <div class="user-info">
                    <span class="team-name">Équipe: <?php echo htmlspecialchars($_SESSION[\'team_name\']); ?></span>
                    ' . $qrScannerComponent . '
                    <a href="../../logout.php" class="logout-btn">Déconnexion</a>
                </div>
            <?php endif; ?>';
                    
                    $updatedContent = str_replace(
                        $headerContent,
                        $headerContent . $userInfoSection,
                        $updatedContent
                    );
                    
                    // Ajouter le composant overlay avant </body>
                    if (strpos($updatedContent, '</body>') !== false) {
                        $updatedContent = str_replace(
                            '</body>',
                            $qrScannerOverlay . "\n</body>",
                            $updatedContent
                        );
                    } else {
                        $updatedContent .= $qrScannerOverlay;
                    }
                    
                    // Sauvegarder le fichier modifié
                    if (file_put_contents($headerFile, $updatedContent)) {
                        echo "<span class='success'>✅ Section user-info créée avec composant QR</span>";
                        $updatedCount++;
                    } else {
                        echo "<span class='error'>❌ Erreur lors de la sauvegarde</span>";
                    }
                } else {
                    echo "<span class='warning'>⚠️ Structure header non reconnue, ajout manuel requis</span>";
                }
            }
        }
    } else {
        echo "<span class='warning'>⚠️ Fichier header.php non trouvé</span>";
    }
    
    echo "</div></div>";
    
    // Mise à jour de la barre de progression
    $progress = round((($updatedCount + 1) / $totalLieux) * 100);
    echo "<script>
        document.getElementById('progressBar').style.width = '$progress%';
        document.getElementById('progressBar').textContent = '$progress%';
    </script>";
    
    // Petite pause pour l'effet visuel
    usleep(100000); // 0.1 seconde
}

echo "</div></div>";

echo "<div class='card mt-4'>
        <div class='card-header'>
            <h3>📊 Résumé de l'opération</h3>
        </div>
        <div class='card-body'>
            <div class='alert alert-success'>
                <h4>🎉 Opération terminée !</h4>
                <p><strong>$updatedCount</strong> headers ont été mis à jour avec le composant QR scanner.</p>
                <p>Le composant QR scanner est maintenant intégré dans la section user-info de chaque lieu, à côté du bouton de déconnexion.</p>
            </div>
            
            <div class='row'>
                <div class='col-md-6'>
                    <h5>✅ Fonctionnalités ajoutées</h5>
                    <ul>
                        <li>Bouton scanner QR dans user-info</li>
                        <li>Interface de scan optimisée mobile</li>
                        <li>Détection intelligente des lieux</li>
                        <li>Navigation automatique corrigée</li>
                        <li>Interface utilisateur améliorée</li>
                    </ul>
                </div>
                <div class='col-md-6'>
                    <h5>🎯 Utilisation</h5>
                    <ul>
                        <li>Cliquer sur 📷 Scanner QR dans user-info</li>
                        <li>Pointer la caméra vers un QR code</li>
                        <li>Confirmer la téléportation</li>
                        <li>Navigation automatique vers le lieu</li>
                    </ul>
                </div>
            </div>
            
            <div class='alert alert-info mt-3'>
                <h6>📍 Position du composant</h6>
                <p>Le bouton QR scanner est maintenant positionné dans la section <code>user-info</code> de chaque lieu, à côté du bouton de déconnexion, comme demandé.</p>
            </div>
        </div>
    </div>
    
    <div class='text-center mt-4 mb-4'>
        <a href='../lieux/accueil/' class='btn btn-primary btn-lg me-3'>🏠 Tester sur l\'accueil</a>
        <a href='../admin/' class='btn btn-secondary btn-lg'>⚙️ Administration</a>
    </div>
</div>

<script>
// Animation de la barre de progression
document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.getElementById('progressBar');
    progressBar.style.transition = 'width 0.5s ease-in-out';
});
</script>

</body>
</html>";
?>
