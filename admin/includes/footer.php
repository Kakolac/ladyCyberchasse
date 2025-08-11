</div>
    </main>

    <!-- Scripts communs -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fonctions communes pour toutes les pages admin
        
        // Réinitialiser tous les jeux
        function resetAllGames() {
            Swal.fire({
                title: '⚠️ Attention !',
                text: 'Voulez-vous vraiment réinitialiser tous les jeux ? Cette action est irréversible.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, réinitialiser',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Appel AJAX pour réinitialiser
                    fetch('reset_games.php', { method: 'POST' })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('✅ Succès', 'Tous les jeux ont été réinitialisés', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                Swal.fire('❌ Erreur', data.message, 'error');
                            }
                        });
                }
            });
        }

        // Générer tous les QR codes
        function generateAllQRCodes() {
            Swal.fire({
                title: '🔄 Génération en cours...',
                text: 'Génération de tous les QR codes pour les équipes',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('generate_all_qr.php', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('✅ Succès', 'Tous les QR codes ont été générés', 'success');
                    } else {
                        Swal.fire('❌ Erreur', data.message, 'error');
                    }
                });
        }

        // Afficher les notifications
        function showNotifications() {
            Swal.fire({
                title: '🔔 Notifications',
                html: `
                    <div class="text-start">
                        <div class="alert alert-info mb-2">
                            <strong>Nouvelle équipe :</strong> L'équipe "Orange" s'est inscrite
                        </div>
                        <div class="alert alert-warning mb-2">
                            <strong>Parcours terminé :</strong> L'équipe "Rouge" a fini son parcours
                        </div>
                        <div class="alert alert-success mb-2">
                            <strong>Système :</strong> Mise à jour automatique effectuée
                        </div>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Fermer'
            });
        }

        // Charger les statistiques rapides
        function loadQuickStats() {
            fetch('get_stats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('quick-stats-teams').textContent = data.stats.teams || '-';
                        document.getElementById('quick-stats-parcours').textContent = data.stats.parcours || '-';
                        document.getElementById('quick-stats-finished').textContent = data.stats.finished || '-';
                    }
                })
                .catch(error => {
                    console.log('Erreur lors du chargement des statistiques:', error);
                });
        }

        // Actualisation automatique des statistiques
        setInterval(loadQuickStats, 30000); // Toutes les 30 secondes

        // Charger les statistiques au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadQuickStats();
        });

        // Auto-fermeture des alertes après 5 secondes
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Gestion du menu mobile
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggler = document.querySelector('.navbar-toggler');
            const navbarCollapse = document.querySelector('.navbar-collapse');
            
            if (navbarToggler && navbarCollapse) {
                navbarToggler.addEventListener('click', function() {
                    navbarCollapse.classList.toggle('show');
                });
            }
        });
    </script>
</body>
</html>