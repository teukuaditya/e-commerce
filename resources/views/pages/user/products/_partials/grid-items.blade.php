{{-- Partial: daftar item kartu produk (4 produk ke kanan pada md/laptop/desktop) --}}
@forelse ($products as $product)
@php
    $img = is_array($product->image) ? ($product->image[0] ?? '') : $product->image;
    $catName = optional($product->category)->category_name ?? '';
    $isWished = in_array($product->id, $wishlistedIds ?? []);
@endphp
<div class="col-6 col-sm-6 col-md-3 p-b-35 product-grid-item grid-enter isotope-item {{ strtolower($catName) }}"
    style="animation-delay:{{ $loop->index * 30 }}ms">
    <div class="block2 home-product-card">
        <div class="block2-pic hov-img0 home-prod-pic" style="border-radius:14px; overflow:hidden; background:#f3f4f6;">
            <a href="{{ route('user.products.show', $product->id) }}">
                <img src="{{ asset('storage/products/' . $img) }}" alt="{{ $product->title }}" loading="lazy" class="home-prod-img">
            </a>

            <div class="home-prod-actions" aria-label="Aksi cepat">
                @auth
                    <button type="button"
                        class="home-prod-action-btn js-wishlist-toggle {{ $isWished ? 'is-wishlisted' : '' }}"
                        data-url="{{ route('user.wishlist.toggle', $product->id) }}"
                        data-wishlisted="{{ $isWished ? '1' : '0' }}"
                        title="{{ $isWished ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}"
                        aria-label="{{ $isWished ? 'Hapus' : 'Simpan' }} {{ $product->title }} dari wishlist"
                        aria-pressed="{{ $isWished ? 'true' : 'false' }}">
                        <i class="zmdi {{ $isWished ? 'zmdi-favorite' : 'zmdi-favorite-outline' }}"></i>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="home-prod-action-btn" title="Login untuk Wishlist" aria-label="Login untuk menyimpan {{ $product->title }}">
                        <i class="zmdi zmdi-favorite-outline"></i>
                    </a>
                @endauth
                @auth
                    <form action="{{ route('user.cart.add', $product->id) }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="home-prod-action-btn" title="Tambah ke Keranjang" aria-label="Tambah {{ $product->title }} ke keranjang">
                            <i class="zmdi zmdi-shopping-cart"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('user.products.show', $product->id) }}" class="home-prod-action-btn" title="Lihat Detail" aria-label="Lihat {{ $product->title }}">
                        <i class="zmdi zmdi-shopping-cart"></i>
                    </a>
                @endauth
            </div>

            <a href="{{ route('user.products.show', $product->id) }}" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 home-prod-quickview">
                Lihat Detail
            </a>
        </div>

        <div class="block2-txt flex-w flex-t p-t-14">
            <div class="block2-txt-child1 flex-col-l">
                @if($catName)
                    <span class="stext-106 cl6 trans-04 m-b-3 home-prod-cat">{{ strtoupper($catName) }}</span>
                @endif

                <a href="{{ route('user.products.show', $product->id) }}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6 home-prod-title">
                    {{ $product->title }}
                </a>

                <a href="{{ route('user.products.show', $product->id) }}" class="stext-105 cl5 hov-cl1 trans-04 home-prod-price" style="font-weight:700; color:#111827;">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </a>
            </div>
        </div>
    </div>
</div>
@empty
<div class="col-12 py-5 text-center empty-products-state">
    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 72px; height: 72px; background: #F1F5F9; color: #94A3B8;">
        <i class="zmdi zmdi-search" style="font-size: 32px;"></i>
    </div>
    <h4 class="mtext-109 cl2 mb-2 font-weight-bold">Produk Tidak Ditemukan</h4>
    <p class="stext-102 cl6 mb-4">
        Tidak ada produk yang cocok dengan kata kunci atau filter saat ini.
    </p>
    <button type="button" class="btn btn-primary px-4 py-2 font-weight-bold btn-reset-search" style="border-radius: 12px; background: #6366F1; border-color: #4F46E5;">
        Tampilkan Semua Produk
    </button>
</div>
@endforelse
