<!-- Outils d'administration -->
<div class="col-12">
    <div class="card admin-card">
        <div class="card-header bg-primary text-white">
            <h4><i class="fas fa-tools"></i> Outils d'Administration</h4>
        </div>
        <div class="card-body">
            <!-- Première rangée : Outils principaux -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="text-muted mb-3"><i class="fas fa-star"></i> Outils Principaux</h6>
                </div>
                
                <!-- Gestion des équipes -->
                <div class="col-md-3 mb-3">
                    <div class="card tool-card h-100 border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h6>Gestion des Équipes</h6>
                            <p class="text-muted small">Créer, modifier et gérer les équipes participantes</p>
                            <button class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#equipesModal">
                                <i class="fas fa-cog"></i> Gérer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Gestion des parcours -->
                <div class="col-md-3 mb-3">
                    <div class="card tool-card h-100 border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-route fa-3x text-success mb-3"></i>
                            <h6>Gestion des Parcours</h6>
                            <p class="text-muted small">Configurer et suivre les parcours des équipes</p>
                            <a href="parcours.php" class="btn btn-success btn-sm w-100">
                                <i class="fas fa-cog"></i> Configurer
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Gestion des lieux -->
                <div class="col-md-3 mb-3">
                    <div class="card tool-card h-100 border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-map-marker-alt fa-3x text-warning mb-3"></i>
                            <h6>Gestion des Lieux</h6>
                            <p class="text-muted small">Administrer les lieux et énigmes du jeu</p>
                            <a href="lieux.php" class="btn btn-warning btn-sm w-100">
                                <i class="fas fa-edit"></i> Administrer
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Génération QR Codes -->
                <div class="col-md-3 mb-3">
                    <div class="card tool-card h-100 border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-qrcode fa-3x text-info mb-3"></i>
                            <h6>Génération QR Codes</h6>
                            <p class="text-muted small">Créer les QR codes pour les équipes</p>
                            <a href="generate_qr.php" class="btn btn-info btn-sm w-100">
                                <i class="fas fa-qrcode"></i> Générer
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deuxième rangée : Outils de contenu -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="text-muted mb-3"><i class="fas fa-puzzle-piece"></i> Gestion du Contenu</h6>
                </div>
                
                <!-- Gestion des énigmes -->
                <div class="col-md-3 mb-3">
                    <div class="card tool-card h-100 border-purple">
                        <div class="card-body text-center">
                            <i class="fas fa-question-circle fa-3x text-purple mb-3"></i>
                            <h6>Gestion des Énigmes</h6>
                            <p class="text-muted small">Créer et configurer les énigmes du jeu</p>
                            <a href="enigmes.php" class="btn btn-purple btn-sm w-100">
                                <i class="fas fa-puzzle-piece"></i> Configurer
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Types d'énigmes -->
                <div class="col-md-3 mb-3">
                    <div class="card tool-card h-100 border-indigo">
                        <div class="card-body text-center">
                            <i class="fas fa-puzzle-piece fa-3x text-indigo mb-3"></i>
                            <h6>Types d'Énigmes</h6>
                            <p class="text-muted small">Gérer les différents types d'énigmes disponibles</p>
                            <a href="types_enigmes.php" class="btn btn-indigo btn-sm w-100">
                                <i class="fas fa-cogs"></i> Gérer
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistiques des indices -->
                <div class="col-md-3 mb-3">
                    <div class="card tool-card h-100 border-teal">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-bar fa-3x text-teal mb-3"></i>
                            <h6>Statistiques des Indices</h6>
                            <p class="text-muted small">Analyser l'utilisation des indices par les équipes</p>
                            <a href="indices_stats.php" class="btn btn-teal btn-sm w-100">
                                <i class="fas fa-chart-line"></i> Analyser
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sauvegarde de la base de données -->
                <div class="col-md-3 mb-3">
                    <div class="card tool-card h-100 border-dark">
                        <div class="card-body text-center">
                            <i class="fas fa-database fa-3x text-dark mb-3"></i>
                            <h6>Sauvegarde BDD</h6>
                            <p class="text-muted small">Sauvegarder et restaurer la base de données</p>
                            <a href="savBDD.php" class="btn btn-dark btn-sm w-100">
                                <i class="fas fa-save"></i> Sauvegarder
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
