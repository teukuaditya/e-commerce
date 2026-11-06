/* Form auth (Tahap 7) — toggle show/hide password untuk halaman login &
   register. Dipindah dari script inline duplikat; delegasi, 1 listener
   untuk semua field .pass-toggle (data-target). */
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.pass-toggle');
    if (!btn) return;
    var input = document.getElementById(btn.getAttribute('data-target'));
    if (!input) return;
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.innerHTML = '<i class="fa ' + (show ? 'fa-eye-slash' : 'fa-eye') + '"></i>';
    btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
    input.focus({ preventScroll: true });
});
