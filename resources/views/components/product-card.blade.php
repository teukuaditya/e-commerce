{{--
    x-product-card — kartu produk untuk section NEW ARRIVALS.
    Gaya disamakan persis dengan halaman Product (grid-items.blade.php):
      - nama brand   : tebal
      - nama produk  : abu-abu
      - harga        : ungu indigo #4F46E5, tebal
    Class .block2* dipakai ulang supaya ikut CSS template yang sudah ada.
--}}
@props(['product', 'delay' => 0])

@php
    // Accessor Product::getImageAttribute() mengembalikan ARRAY (json_decode).
    $rawImage = $product->image;
    $imagePath = is_array($rawImage) ? ($rawImage[0] ?? '') : ($rawImage ?? '');
@endphp

<div class="na-card" data-reveal style="--reveal-delay: {{ (int) $delay }}ms">
    <div class="block2">
        <div class="block2-pic hov-img0">
            {{-- [LAMA, ditambah .fade-img] Gambar produk --}}
            <a href="{{ route('user.products.show', $product->id) }}">
                <img class="fade-img"
                     src="{{ asset('storage/products/' . $imagePath) }}"
                     alt="{{ $product->title }}"
                     loading="lazy">
            </a>

            {{-- Tombol "Lihat Detail" (muncul saat hover, milik template) --}}
            <a href="{{ route('user.products.show', $product->id) }}"
               class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                Lihat Detail
            </a>
        </div>

        <div class="block2-txt flex-w flex-t p-t-14">
            <div class="block2-txt-child1 flex-col-l">
                {{-- Brand: tebal --}}
                <a href="{{ route('user.products.show', $product->id) }}"
                   class="stext-106 cl5 hov-cl1 trans-04 m-b-3" style="font-weight: bold;">
                    {{ $product->brand }}
                </a>

                {{-- Nama produk: abu-abu --}}
                <a href="{{ route('user.products.show', $product->id) }}"
                   class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                    {{ $product->title }}
                </a>

                {{-- Harga: ungu indigo --}}
                <a href="{{ route('user.products.show', $product->id) }}"
                   class="stext-105 cl5 hov-cl1 trans-04" style="font-weight: 700; color: #4F46E5;">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </a>
            </div>
        </div>
    </div>
</div>
