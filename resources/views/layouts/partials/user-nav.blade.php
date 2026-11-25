<div class="account-sidebar">
    <div class="account-sidebar__profile">
        <div class="account-sidebar__avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        <div style="min-width:0;">
            <h5 class="account-sidebar__name">{{ Auth::user()->name }}</h5>
            <p class="account-sidebar__email">{{ Auth::user()->email }}</p>
        </div>
    </div>

    <div class="account-sidebar__group">
        <div class="account-sidebar__label">My Details</div>
        <a href="{{ route('user.profile.edit') }}" class="account-sidebar__link {{ request()->routeIs('user.profile.edit') || request()->routeIs('user.edit') ? 'is-active' : '' }}">
            <span class="account-sidebar__icon"><i class="fas fa-user"></i></span><span>My Profile</span>
        </a>
        <a href="{{ route('user.address.edit') }}" class="account-sidebar__link {{ request()->routeIs('user.address.edit') ? 'is-active' : '' }}">
            <span class="account-sidebar__icon"><i class="fas fa-map-marker-alt"></i></span><span>My Address</span>
        </a>
    </div>

    <div class="account-sidebar__group">
        <div class="account-sidebar__label">My Purchases</div>
        <a href="{{ route('user.order.index') }}" class="account-sidebar__link {{ request()->routeIs('user.order.index') ? 'is-active' : '' }}">
            <span class="account-sidebar__icon"><i class="fas fa-shopping-bag"></i></span><span>Order History</span>
        </a>
    </div>

    <div class="account-sidebar__group" style="margin-bottom:0;">
        <div class="account-sidebar__label">Account</div>
        <a href="#" class="account-sidebar__link" onclick="event.preventDefault(); window.confirmLogout ? window.confirmLogout('logout-form') : document.getElementById('logout-form').submit();">
            <span class="account-sidebar__icon"><i class="fas fa-sign-out-alt"></i></span><span>Sign Out</span>
        </a>
        <a href="#" class="account-sidebar__link account-sidebar__link--danger" data-toggle="modal" data-target="#deleteAccountModal">
            <span class="account-sidebar__icon"><i class="fas fa-user-times"></i></span><span>Hapus Akun Saya</span>
        </a>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
</div>

<style>
    .account-sidebar { background:#fff; border:1px solid #E5E7EB; border-radius:20px; padding:24px; box-shadow:0 10px 30px rgba(15,23,42,.04); }
    .account-sidebar__profile { display:flex; align-items:center; gap:14px; padding-bottom:20px; margin-bottom:20px; border-bottom:1px solid #EEF2F7; }
    .account-sidebar__avatar { width:52px; height:52px; border-radius:16px; background:linear-gradient(135deg,#111827,#4B5563); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:20px; flex:0 0 52px; }
    .account-sidebar__name { margin:0; font-size:16px; font-weight:700; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .account-sidebar__email { margin:4px 0 0; font-size:13px; color:#6B7280; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .account-sidebar__group { margin-bottom:22px; }
    .account-sidebar__label { font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#9CA3AF; margin-bottom:10px; padding:0 10px; }
    .account-sidebar__link { display:flex; align-items:center; gap:12px; padding:12px 12px; border-radius:12px; color:#374151; text-decoration:none !important; font-size:14px; font-weight:500; transition:all .2s ease; margin-bottom:4px; }
    .account-sidebar__link:hover { background:#F3F4F6; color:#111827; }
    .account-sidebar__link.is-active { background:#111827; color:#fff; }
    .account-sidebar__link.is-active .account-sidebar__icon { color:#fff; background:rgba(255,255,255,.12); }
    .account-sidebar__icon { width:34px; height:34px; border-radius:10px; background:#F3F4F6; color:#6B7280; display:inline-flex; align-items:center; justify-content:center; font-size:13px; flex:0 0 34px; transition:all .2s ease; }
    .account-sidebar__link--danger { color:#DC2626; }
    .account-sidebar__link--danger .account-sidebar__icon { background:#FEF2F2; color:#DC2626; }
    .account-sidebar__link--danger:hover { background:#FEF2F2; color:#B91C1C; }

    .account-page { margin-top: 24px; }
    .account-card { background:#fff; border:1px solid #E5E7EB; border-radius:20px; box-shadow:0 10px 30px rgba(15,23,42,.04); }
    .account-card__header { padding:28px 32px 20px; border-bottom:1px solid #EEF2F7; }
    .account-card__eyebrow { display:inline-block; font-size:11px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#6B7280; margin-bottom:8px; }
    .account-card__title { margin:0; font-size:28px; font-weight:700; color:#111827; line-height:1.2; }
    .account-card__body { padding:28px 32px; }
    .account-info-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:20px; }
    .account-info-item { padding:18px; background:#F9FAFB; border:1px solid #EEF2F7; border-radius:14px; }
    .account-info-item--full { grid-column:1 / -1; }
    .account-info-label { display:block; font-size:12px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; color:#6B7280; margin-bottom:8px; }
    .account-info-value { margin:0; font-size:15px; color:#111827; font-weight:500; line-height:1.6; word-break:break-word; }
    .account-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:12px; padding:11px 18px; font-size:13px; font-weight:600; border:1px solid transparent; transition:all .2s ease; cursor:pointer; text-decoration:none !important; }
    .account-btn-primary { background:#111827; color:#fff; }
    .account-btn-primary:hover { background:#000; color:#fff; transform:translateY(-1px); }
    .account-btn-secondary { background:#F3F4F6; color:#374151; border-color:#E5E7EB; }
    .account-btn-secondary:hover { background:#E5E7EB; color:#111827; }
    .account-btn-danger { background:#FEF2F2; color:#DC2626; border-color:#FECACA; }
    .account-btn-danger:hover { background:#FEE2E2; color:#B91C1C; }
    .account-form .form-control { height:48px; border-radius:12px; border:1px solid #DDE5F0; padding:10px 14px; box-shadow:none; }
    .account-form textarea.form-control { height:auto; min-height:110px; }
    .account-form .form-control:focus { border-color:#111827; box-shadow:0 0 0 3px rgba(17,24,39,.08); }
    .account-form label { font-size:13px; font-weight:600; color:#374151; margin-bottom:8px; }
    @media (max-width: 767.98px) {
        .account-card__header, .account-card__body { padding:22px 20px; }
        .account-card__title { font-size:24px; }
        .account-info-grid { grid-template-columns:1fr; }
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
