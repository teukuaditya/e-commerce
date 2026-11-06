{{-- Alert flash sukses bersama (Tahap 7) — satu sumber markup & auto-hide,
    dipakai panel admin dan halaman user. Perilaku identik dengan versi
    inline sebelumnya: id "success-alert", fade 0.5s setelah 2 detik. --}}
@props(['class' => ''])

@if (session('success'))
    <div id="success-alert" class="{{ $class }} alert alert-success">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
    </div>
    <script>
        setTimeout(function () {
            var alertElement = document.getElementById('success-alert');
            if (alertElement) {
                alertElement.style.transition = 'opacity 0.5s ease';
                alertElement.style.opacity = 0;
                setTimeout(function () { alertElement.remove(); }, 500);
            }
        }, 2000);
    </script>
@endif
