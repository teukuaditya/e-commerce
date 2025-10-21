@extends('layouts.main')

@section('content')
    <section class="bg0 m-t-45 p-b-60">
        <div class="container">

            <!-- Success message -->
            @if (session('success'))
                <div id="success-alert" class="stext-102 alert alert-success">
                    <i class="fas fa-check-circle mr-2"></i> <!-- Ikon centang -->
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-4 col-md-4">
                    @include('layouts.partials.user-nav')
                </div>

                <!-- Edit User Form -->
                <div class="col-lg-8 col-md-8">
                    <div class="mtext-109 cl2 p-b-30 bor10 p-lr-40 p-t-30 p-b-30 m-lr-0-xl p-lr-15-sm mb-4">
                        Profile
                    </div>

                    <!-- Tampilkan Data Pengguna -->
                    <div class="bor10 p-lr-40 p-t-30 p-b-30 m-lr-0-xl p-lr-15-sm">
                        <div id="user-info">
                            <div class="stext-102 cl2 form-group mb-4">
                                <label class="mtext-102" for="name">Name</label>
                                <p>{{ auth()->user()->name }}</p>
                            </div>

                            <div class="stext-102 cl2 form-group mb-4">
                                <label class="mtext-102" for="phone">Phone</label>
                                <p>{{ auth()->user()->phone ?? '-' }}</p>
                            </div>

                            <div class="stext-102 cl2 form-group mb-4">
                                <label class="mtext-102" for="email">Email</label>
                                <p>{{ auth()->user()->email }}</p>
                            </div>

                            <!-- Tombol Edit -->
                            <button type="button"
                                class="flex-c-m stext-102 cl0 size-127 bg1 mt-3 bor14 hov-btn5 p-lr-15 trans-04 pointer"
                                style="font-size: 12px; min-width: 10%; height: 30px" id="edit-button">Edit Profile</button>
                        </div>

                        <!-- Form untuk mengedit data user (tersembunyi) -->
                        <div id="edit-form" style="display: none;">
                            <form action="{{ route('user.profile') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Name -->
                                <div class="stext-102 cl2 form-group mb-4">
                                    <label class="mtext-102" for="name">Name</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ auth()->user()->name }}" required>
                                </div>

                                <!-- Phone -->
                                <div class="stext-102 cl2 form-group mb-4">
                                    <label class="mtext-102" for="phone">Phone</label>
                                    <input type="text" id="phone" name="phone" class="form-control"
                                        value="{{ auth()->user()->phone }}">
                                </div>

                                <!-- Email -->
                                <div class="stext-102 cl2 form-group mb-4">
                                    <label class="mtext-102" for="email">Email</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        value="{{ auth()->user()->email }}" required>
                                </div>
                                <div class="d-flex justify-content-start gap-2">
                                    <button type="button"
                                        class="flex-c-m stext-102 cl0 size-127 bg10 mt-3 bor14 hov-btn4 p-lr-15 trans-04 pointer mr-2"
                                        style="font-size: 12px; min-width: 10%; height: 30px"
                                        id="cancel-button">Cancel</button>
                                    <button type="submit"
                                        class="flex-c-m stext-102 cl0 size-127 bg1 mt-3 bor14 hov-btn5 p-lr-15 trans-04 pointer"
                                        style="font-size: 12px; min-width: 10%; height: 30px">Update Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Menghilangkan pesan sukses setelah 5 detik
        setTimeout(function() {
            let alertElement = document.getElementById('success-alert');
            if (alertElement) {
                alertElement.style.transition = "opacity 0.5s ease";
                alertElement.style.opacity = 0; // Perlahan menghilang
                setTimeout(() => alertElement.remove(), 500); // Hapus setelah transisi selesai
            }
        }, 2000); // Waktu dalam milidetik
        $(document).ready(function() {
            // Menampilkan form edit setelah tombol "Edit Profile" diklik
            $('#edit-button').click(function() {
                $('#user-info').hide(); // Sembunyikan data user
                $('#edit-form').show(); // Tampilkan form untuk mengedit
            });

            // Membatalkan edit dan kembali ke tampilan data pengguna
            $('#cancel-button').click(function() {
                $('#edit-form').hide(); // Sembunyikan form edit
                $('#user-info').show(); // Tampilkan data user kembali
            });
        });
    </script>
@endpush
