<!DOCTYPE html>
<html>
<head>
    <title>Test QR Codes</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>🧪 Test des QR Codes</h1>
        <p>Scannez ces QR codes avec votre caméra pour tester :</p>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>🏠 Accueil</h5>
                        <img src="https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=https://localhost/lieux/accueil/" 
                             alt="QR Accueil" class="img-fluid">
                        <p class="mt-2">https://localhost/lieux/accueil/</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>📚 CDI</h5>
                        <img src="https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=https://localhost/lieux/cdi/" 
                             alt="QR CDI" class="img-fluid">
                        <p class="mt-2">https://localhost/lieux/cdi/</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>💻 Salle Info</h5>
                        <img src="https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=https://localhost/lieux/salle_info/" 
                             alt="QR Salle Info" class="img-fluid">
                        <p class="mt-2">https://localhost/lieux/salle_info/</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info">
            <h5>📱 Instructions de test :</h5>
            <ol>
                <li>Ouvrez la page d'accueil : <a href="lieux/accueil/">lieux/accueil/</a></li>
                <li>Cliquez sur "Ouvrir la Caméra"</li>
                <li>Scannez un des QR codes ci-dessus</li>
                <li>Vous devriez être redirigé vers le bon lieu</li>
            </ol>
        </div>
    </div>
</body>
</html>
