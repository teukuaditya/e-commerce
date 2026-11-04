{{-- Partial: daftar item kartu produk (tampak di halaman products & search) --}}
{{-- Digunakan oleh: pages.user.products.index, pages.user.products.search,        --}}
{{-- ProductController@search (AJAX live search). Dibungkus <div class="row isotope-grid"> --}}
{{-- oleh halaman memanggil; AJAX mengganti INNER-HTML div tersebut, bukan elemennya,   --}}
{{-- agar referensi plugin isotope tetap valid.        --}}

@foreach ($products as $product)
<div class="col-sm-6 col-md-3 p-b-35 isotope-item {{ strtolower($product->category->category_name ?? '') }}">
    <!-- Block2 -->
    <div class="block2">
        <div class="block2-pic hov-img0">
            <!-- Gambar Produk, klik gambar ini akan mengarahkan ke halaman detail -->
            <a href="{{ route('user.products.show', $product->id) }}">
                <img src="{{ asset('storage/products/' . (is_array($product->image) ? ($product->image[0] ?? '') : $product->image)) }}" alt="{{ $product->title }}">
            </a>

            <!-- Tombol Quick View -->
            <a href="{{ route('user.products.show', $product->id) }}" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                Add To Cart
            </a>
        </div>

        <div class="block2-txt flex-w flex-t p-t-14">
            <div class="block2-txt-child1 flex-col-l">
                <!-- Klik Brand untuk masuk ke halaman detail produk -->
                <a href="{{ route('user.products.show', $product->id) }}" class="stext-106 cl5 hov-cl1 trans-04 m-b-3" style="font-weight: bold;">
                    {{ $product->brand }}
                </a>

                <!-- Nama produk, klik nama ini akan mengarahkan ke halaman detail -->
                <a href="{{ route('user.products.show', $product->id) }}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                    {{ $product->title }}
                </a>

                <!-- Harga -->
                <a href="{{ route('user.products.show', $product->id) }}" class="stext-105 cl5 hov-cl1 trans-04">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach
