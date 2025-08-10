function startTimer(display) {
    // Récupérer le temps stocké dans localStorage ou commencer à 0
    let seconds = parseInt(localStorage.getItem('timer_seconds')) || 0;
    
    let timer = setInterval(function () {
        let hours = Math.floor(seconds / 3600);
        let minutes = Math.floor((seconds % 3600) / 60);
        let remainingSeconds = seconds % 60;

        hours = hours < 10 ? "0" + hours : hours;
        minutes = minutes < 10 ? "0" + minutes : minutes;
        remainingSeconds = remainingSeconds < 10 ? "0" + remainingSeconds : remainingSeconds;

        display.textContent = hours + ":" + minutes + ":" + remainingSeconds;
        
        // Sauvegarder dans localStorage
        localStorage.setItem('timer_seconds', seconds);
        
        seconds++;
        
        if (seconds >= 7200) { // 2 heures
            clearInterval(timer);
            alert("Temps maximum atteint !");
            window.location.href = 'timeout.php';
        }
    }, 1000);
}