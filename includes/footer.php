</div><!-- Fermeture du container -->
    
    <?php if(isset($_SESSION['team_name'])): ?>
        <div id="timer" class="position-fixed">00:00:00</div>
    
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Cyberchasse - Tous droits réservés</p>
        </footer>

        <!-- Scripts -->
        <script src="js/game-timer.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var display = document.querySelector('#timer');
                if(display) {
                    startTimer(display);
                }
            });
        </script>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>