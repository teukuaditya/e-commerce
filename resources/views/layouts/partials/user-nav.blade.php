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
</style>
