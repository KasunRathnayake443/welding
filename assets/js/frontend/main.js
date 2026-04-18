document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('mobileNavToggle');
    const nav = document.getElementById('siteNav');

    if (toggleBtn && nav) {
        toggleBtn.addEventListener('click', function () {
            nav.classList.toggle('active');
        });
    }
});