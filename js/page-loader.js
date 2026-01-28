/**
 * Page Loader Script
 * Hides the loading animation after page loads
 */

window.addEventListener('load', function() {
    const loader = document.getElementById('pageLoader');
    if (loader) {
        // Random duration between 3-4.5 seconds
        const duration = Math.floor(Math.random() * 1500) + 3000;
        setTimeout(function() {
            loader.classList.add('hidden');
        }, duration);
    }
});
