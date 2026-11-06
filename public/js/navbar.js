/* Script navbar (Tahap 7) — dipindah dari inline di
   resources/views/layouts/partials/navbar.blade.php. Perilaku identik:
   dropdown akun mandiri (delegasi) + navbar solid saat scroll > 50px. */
(function () {
    function closeAll(except) {
        document.querySelectorAll('.dropdown-menu.show').forEach(function (m) {
            if (m !== except) {
                m.classList.remove('show');
            }
        });
        document.querySelectorAll('[data-tu-dropdown]').forEach(function (t) {
            t.setAttribute('aria-expanded', 'false');
        });
    }

    document.addEventListener('click', function (e) {
        var toggle = e.target.closest('[data-tu-dropdown]');

        if (toggle) {
            e.preventDefault();
            var menu = toggle.parentElement.querySelector('.dropdown-menu');
            var willOpen = !menu.classList.contains('show');

            closeAll(null);
            menu.classList.toggle('show', willOpen);
            toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            return;
        }

        /* Klik di luar menu → tutup */
        if (!e.target.closest('.dropdown-menu.show')) {
            closeAll(null);
        }
    }, true);

    /* Esc menutup dropdown */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAll(null);
    });

    /* Scroll > 50px → navbar (desktop & mobile) lebih solid */
    var header = document.querySelector('header.header-v4');
    var headerMobile = document.querySelector('.wrap-header-mobile');

    function toggleScrolled() {
        var solid = (window.scrollY || window.pageYOffset) > 50;
        if (header) header.classList.toggle('scrolled', solid);
        if (headerMobile) headerMobile.classList.toggle('scrolled', solid);
    }

    window.addEventListener('scroll', toggleScrolled, { passive: true });
    toggleScrolled();
})();
