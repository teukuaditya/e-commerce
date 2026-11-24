<div class="profile-sidebar bor10 p-lr-30 p-t-20 p-b-20">
    <h5 class="stext-110 cl2" style="font-weight: bold; font-size: 18px;">{{ Auth::user()->name }}</h5>
    <!-- Nama pengguna -->
    <p class="stext-104 cl4" style="font-size: 14px;">{{ Auth::user()->email }}</p> <!-- Email pengguna -->
    <hr style="margin: 10px 0;"> <!-- Menambahkan margin agar ada jarak antara elemen -->

    <!-- Bagian My Details -->
    <div class="section-title stext-110 cl2" style="font-weight: bold; font-size: 14px; margin-top: 20px;">My Details
    </div>
    <ul class="list-group">
        <a href="{{ route('user.profile.edit') }}"
            class="list-group-item stext-104 cl2 hover-effect {{ request()->routeIs('user.edit') ? 'active' : '' }}">
            <i class="fas fa-user fa-fw mr-2"></i> My Profile
        </a>
        <a href="{{ route('user.address.edit') }}"
            class="list-group-item stext-104 cl2 hover-effect {{ request()->routeIs('user.address.edit') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt fa-fw mr-2"></i> My Address
        </a>
    </ul>

    <!-- Bagian My Purchases -->
    <div class="section-title stext-110 cl2" style="font-weight: bold; font-size: 14px; margin-top: 20px;">My Purchases
    </div>
    <ul class="list-group">
        <a href="{{ route('user.order.index') }}"
            class="list-group-item stext-104 cl2 hover-effect {{ request()->routeIs('user.order.index') ? 'active' : '' }}">
            <i class="fas fa-shopping-bag fa-fw mr-2"></i> Order History
        </a>
    </ul>

    <!-- Bagian Account Management -->
    <div class="section-title stext-110 cl2" style="font-weight: bold; font-size: 14px; margin-top: 20px;">Account
        Management</div>
    <ul class="list-group">
        <a href="#" class="list-group-item stext-104 cl2 hover-effect"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt fa-fw mr-2"></i> Sign Out
        </a>
        <a href="#" class="list-group-item stext-104 hover-effect delete-account-link"
            data-toggle="modal" data-target="#deleteAccountModal">
            <i class="fas fa-user-times fa-fw mr-2"></i> Hapus Akun Saya
        </a>
    </ul>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

</div>


<style>
    /* Menghilangkan border pada item dan menambahkan efek hover */
    .list-group-item {
        border: none;
        /* Menghapus border */
        padding: 15px;
        /* Padding sesuai kebutuhan */
        padding-left: 15px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Hover effect untuk tombol */
    .hover-effect:hover {
        background-color: rgba(224, 224, 224, 0.3);
        /* Warna background saat hover */
        color: #333;
        /* Warna teks saat hover */
    }

    .list-group-item.active {
        background-color: rgba(224, 224, 224, 0.3);
        /* Warna aktif */
        color: #333;
        /* Warna teks saat aktif */
    }

    .delete-account-link {
        color: #e53935 !important;
    }

    .delete-account-link:hover {
        color: #c62828 !important;
        background-color: rgba(229, 57, 53, 0.08) !important;
    }
</style>

<!-- Modal Konfirmasi Hapus Akun -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="border-bottom: 1px solid #eee;">
                <h5 class="modal-title mtext-109 cl2" id="deleteAccountModalLabel" style="font-size: 18px;">Hapus Akun</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('user.profile.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body" style="padding: 24px;">
                    <p class="stext-102 cl6" style="line-height: 1.7; margin-bottom: 16px;">
                        Apakah Anda yakin ingin menghapus akun <strong>{{ auth()->user()->name }}</strong>?
                        Semua data akun akan dihapus permanen dan Anda akan keluar dari sesi ini.
                    </p>

                    @if(empty(auth()->user()->google_id))
                        <div class="form-group mb-0">
                            <label class="stext-102 cl2" for="delete-account-password" style="font-weight: 600;">
                                Masukkan password untuk konfirmasi
                            </label>
                            <input type="password"
                                   id="delete-account-password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="current-password"
                                   required>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <p class="stext-102" style="color: #888; margin-bottom: 0;">
                            Anda login menggunakan Google, jadi konfirmasi password tidak diperlukan.
                        </p>
                    @endif
                </div>
                <div class="modal-footer" style="border-top: 1px solid #eee;">
                    <button type="button" class="btn btn-light stext-102" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn stext-102" style="background: #e53935; color: #fff; font-weight: 600;">
                        Ya, Hapus Akun Saya
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        var $modal = $('#deleteAccountModal');
        if ($modal.length && $modal.parent()[0].tagName !== 'BODY') {
            $modal.appendTo('body');
        }
        @if($errors->has('password') && old('_method') === 'DELETE')
            $modal.modal('show');
        @endif
    });
</script>
@endpush
