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

<?php include './footer.php'; ?>
