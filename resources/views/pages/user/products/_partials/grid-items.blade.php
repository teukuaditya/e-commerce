{{-- Partial: daftar item kartu produk (4 produk ke kanan pada md/laptop/desktop) --}}
@forelse ($products as $product)
<div class="col-6 col-sm-6 col-md-3 p-b-35 product-grid-item isotope-item {{ strtolower($product->category->category_name ?? '') }}">
    <!-- Block2 -->
    <div class="block2">
        <div class="block2-pic hov-img0">
            <!-- Gambar Produk -->
            <a href="{{ route('user.products.show', $product->id) }}">
                <img src="{{ asset('storage/products/' . (is_array($product->image) ? ($product->image[0] ?? '') : $product->image)) }}" alt="{{ $product->title }}" loading="lazy">
            </a>

            <!-- Tombol Detail / Add To Cart -->
            <a href="{{ route('user.products.show', $product->id) }}" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                Lihat Detail
            </a>
        </div>

        <div class="block2-txt flex-w flex-t p-t-14">
            <div class="block2-txt-child1 flex-col-l">
                <a href="{{ route('user.products.show', $product->id) }}" class="stext-106 cl5 hov-cl1 trans-04 m-b-3" style="font-weight: bold;">
                    {{ $product->brand }}
                </a>

                <a href="{{ route('user.products.show', $product->id) }}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                    {{ $product->title }}
                </a>

                <a href="{{ route('user.products.show', $product->id) }}" class="stext-105 cl5 hov-cl1 trans-04" style="font-weight: 700; color: #4F46E5;">
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
