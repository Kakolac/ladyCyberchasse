<?php
include 'includes/header.php';
?>

<section class="intro">
    <h2>Cyberchasse</h2>
    <p>Votre lycée a été victime d'une cyberattaque ! En tant que futurs experts en cybersécurité, vous devez infiltrer tous les secteurs du lycée, collecter les informations secrètes et déjouer les pièges informatiques cachés dans chaque lieu.</p>
</section>
<section class="instructions">
    <h2>Instructions</h2>
    <ul>
        <li>Restez toujours en équipe de 4.</li>
        <li>Vous avez 12 minutes maximum par lieu.</li>
        <li>Respectez les espaces et les personnes.</li>
        <li>Validez chaque étape en scannant le QR code.</li>
        <li>Retour obligatoire à 11h45.</li>
        <li>En cas de problème, contactez un animateur.</li>
    </ul>
</section>
<section class="commencer">
    <h2>Êtes-vous prêt?</h2>
    <p>La chasse peut commencer <br> Cliquez sur le bouton ci-dessous pour commencer votre mission.</p>
    <div class="button-container">
    <button onclick="window.location.href='login.php'">Commencer</button>
    </div>
</section>

<?php
include 'includes/footer.php';
?>
