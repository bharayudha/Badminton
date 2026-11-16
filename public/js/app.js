document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('menuToggle');

    toggle?.addEventListener('click', () => sidebar?.classList.toggle('open'));

    document.addEventListener('click', (event) => {
        if (window.innerWidth <= 760 && sidebar?.classList.contains('open')
            && !sidebar.contains(event.target) && !toggle?.contains(event.target)) {
            sidebar.classList.remove('open');
        }
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) event.preventDefault();
        });
    });
});
