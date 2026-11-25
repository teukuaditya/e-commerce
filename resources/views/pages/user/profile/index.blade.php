@extends('layouts.main')

@section('content')
<section class="bg0 p-b-60 account-page">
    <div class="container">
        <x-flash-alert class="stext-102 mb-4" />
        <div class="row">
            <div class="col-lg-4 col-md-4 mb-4 mb-lg-0">
                @include('layouts.partials.user-nav')
            </div>

            <div class="col-lg-8 col-md-8">
                <div class="account-card">
                    <div class="account-card__header">
                        <span class="account-card__eyebrow">Account</span>
                        <h2 class="account-card__title">My Profile</h2>
                        <p style="margin:10px 0 0;color:#6B7280;font-size:14px;">Kelola informasi pribadi kamu agar pengalaman belanja lebih nyaman.</p>
                    </div>
                    <div class="account-card__body">
                        <div id="user-info">
                            <div class="account-info-grid">
                                <div class="account-info-item">
                                    <span class="account-info-label">Name</span>
                                    <p class="account-info-value">{{ auth()->user()->name }}</p>
                                </div>
                                <div class="account-info-item">
                                    <span class="account-info-label">Phone</span>
                                    <p class="account-info-value">{{ auth()->user()->phone ?? '-' }}</p>
                                </div>
                                <div class="account-info-item account-info-item--full">
                                    <span class="account-info-label">Email</span>
                                    <p class="account-info-value">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="button" class="account-btn account-btn-primary" id="edit-button">
                                    <i class="fas fa-pen" style="font-size:12px;"></i> Edit Profile
                                </button>
                            </div>
                        </div>

                        <div id="edit-form" style="display:none;">
                            <form action="{{ route('user.profile.update') }}" method="POST" class="account-form">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 form-group mb-4">
                                        <label for="name">Name</label>
                                        <input type="text" id="name" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                                    </div>
                                    <div class="col-md-6 form-group mb-4">
                                        <label for="phone">Phone</label>
                                        <input type="text" id="phone" name="phone" class="form-control" value="{{ auth()->user()->phone }}">
                                    </div>
                                    <div class="col-12 form-group mb-4">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap" style="gap:10px;">
                                    <button type="button" class="account-btn account-btn-secondary" id="cancel-button">Cancel</button>
                                    <button type="submit" class="account-btn account-btn-primary">Update Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .account-page { padding-top: 24px; }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Menampilkan form edit setelah tombol "Edit Profile" diklik
        $('#edit-button').click(function() {
            $('#user-info').hide();
            $('#edit-form').show();
        });

        // Membatalkan edit dan kembali ke tampilan data pengguna
        $('#cancel-button').click(function() {
            $('#edit-form').hide();
            $('#user-info').show();
        });
    });
</script>
@endpush