document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar) return;

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'mobile-menu-toggle';
    button.setAttribute('aria-label', 'Buka menu');
    button.setAttribute('aria-expanded', 'false');
    button.innerHTML = '<i class="bi bi-list"></i>';

    const backdrop = document.createElement('div');
    backdrop.className = 'mobile-menu-backdrop';

    document.body.prepend(backdrop);
    document.body.prepend(button);

    function openMenu() {
        sidebar.classList.add('mobile-menu-open');
        backdrop.classList.add('mobile-menu-visible');
        button.classList.add('mobile-menu-active');
        button.setAttribute('aria-expanded', 'true');
        button.innerHTML = '<i class="bi bi-x-lg"></i>';
    }

    function closeMenu() {
        sidebar.classList.remove('mobile-menu-open');
        backdrop.classList.remove('mobile-menu-visible');
        button.classList.remove('mobile-menu-active');
        button.setAttribute('aria-expanded', 'false');
        button.innerHTML = '<i class="bi bi-list"></i>';
    }

    button.addEventListener('click', function () {
        if (sidebar.classList.contains('mobile-menu-open')) closeMenu();
        else openMenu();
    });

    backdrop.addEventListener('click', closeMenu);

    sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            closeMenu();
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeMenu();
    });
});
