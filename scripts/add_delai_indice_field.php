<?php
require_once '../config/connexion.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Migration - Ajout champ delai_indice</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
</head>
<body class='bg-light'>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-8'>
                <div class='card shadow'>
                    <div class='card-header bg-primary text-white'>
                        <h3><i class='fas fa-database'></i> Migration Base de Données</h3>
                        <p class='mb-0'>Ajout du champ delai_indice à la table lieux</p>
                    </div>
                    <div class='card-body'>";

try {
    // Vérifier si le champ existe déjà
    $stmt = $pdo->prepare("SHOW COLUMNS FROM lieux LIKE 'delai_indice'");
    $stmt->execute();
    $columnExists = $stmt->fetch();
    
    if ($columnExists) {
        echo "<div class='alert alert-info'>
                <i class='fas fa-info-circle'></i> 
                <strong>Info :</strong> Le champ 'delai_indice' existe déjà dans la table 'lieux'.
              </div>";
    } else {
        // Ajouter le champ delai_indice
        $sql = "ALTER TABLE lieux ADD COLUMN delai_indice INT DEFAULT 6 COMMENT 'Délai en minutes avant disponibilité de l\'indice'";
        $pdo->exec($sql);
        
        echo "<div class='alert alert-success'>
                <i class='fas fa-check-circle'></i> 
                <strong>Succès :</strong> Champ 'delai_indice' ajouté à la table 'lieux' avec la valeur par défaut 6 minutes.
              </div>";
    }
    
    // Mettre à jour tous les lieux existants avec la valeur par défaut si nécessaire
    $stmt = $pdo->prepare("UPDATE lieux SET delai_indice = 6 WHERE delai_indice IS NULL");
    $stmt->execute();
    $updatedRows = $stmt->rowCount();
    
    if ($updatedRows > 0) {
        echo "<div class='alert alert-warning'>
                <i class='fas fa-exclamation-triangle'></i> 
                <strong>Mise à jour :</strong> $updatedRows lieux ont été mis à jour avec le délai par défaut de 6 minutes.
              </div>";
    }
    
    // Afficher la structure actuelle de la table
    echo "<h4 class='mt-4'><i class='fas fa-table'></i> Structure actuelle de la table 'lieux'</h4>
          <div class='table-responsive'>
            <table class='table table-striped table-bordered'>
                <thead class='table-dark'>
                    <tr>
                        <th>Champ</th>
                        <th>Type</th>
                        <th>Null</th>
                        <th>Clé</th>
                        <th>Défaut</th>
                        <th>Extra</th>
                    </tr>
                </thead>
                <tbody>";
    
    $stmt = $pdo->prepare("DESCRIBE lieux");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        $rowClass = $column['Field'] === 'delai_indice' ? 'table-success' : '';
        echo "<tr class='$rowClass'>
                <td><strong>{$column['Field']}</strong></td>
                <td>{$column['Type']}</td>
                <td>{$column['Null']}</td>
                <td>{$column['Key']}</td>
                <td>{$column['Default']}</td>
                <td>{$column['Extra']}</td>
              </tr>";
    }
    
    echo "</tbody></table></div>";
    
    // Afficher quelques exemples de lieux avec leurs délais
    echo "<h4 class='mt-4'><i class='fas fa-map-marker-alt'></i> Exemples de lieux avec leurs délais d'indice</h4>
          <div class='table-responsive'>
            <table class='table table-striped table-bordered'>
                <thead class='table-dark'>
                    <tr>
                        <th>Lieu</th>
                        <th>Slug</th>
                        <th>Délai Indice (min)</th>
                    </tr>
                </thead>
                <tbody>";
    
    $stmt = $pdo->prepare("SELECT nom, slug, delai_indice FROM lieux ORDER BY nom LIMIT 10");
    $stmt->execute();
    $lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($lieux as $lieu) {
        echo "<tr>
                <td>{$lieu['nom']}</td>
                <td><code>{$lieu['slug']}</code></td>
                <td><span class='badge bg-info'>{$lieu['delai_indice']} min</span></td>
              </tr>";
    }
    
    echo "</tbody></table></div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>
            <i class='fas fa-exclamation-triangle'></i> 
            <strong>Erreur :</strong> " . htmlspecialchars($e->getMessage()) . "
          </div>";
}

echo "</div></div></div></div>
      <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
      </body></html>";
?>
