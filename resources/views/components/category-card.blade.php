{{--
    x-category-card — kartu kategori.
    ---------------------------------------------------------------
    PENTING: markup hover lama (.block1 / .block1-txt / .block1-name /
    .block1-txt-child2 / .block1-link) DIPERTAHANKAN PERSIS seperti aslinya
    supaya CSS hover di public/css/main.css (baris 1994-2070) tetap berfungsi.

    Yang DITAMBAHKAN saja (murni elemen baru, tidak mengubah aturan lama):
      1. .cat-meta      -> nomor index (01/02/03) + jumlah produk di pojok kartu
      2. .cat-arrow     -> panah "→" di dalam link Explore (geser saat hover)
      3. data-reveal    -> scroll reveal bertahap (IntersectionObserver)
      4. .fade-img      -> lazy load + fade-in gambar
--}}
@props(['category', 'position' => 0, 'delay' => 0])

@php
    $indexNo = str_pad(((int) $position) + 1, 2, '0', STR_PAD_LEFT);
    $productCount = (int) ($category->products_count ?? 0);
@endphp

<div class="block1 wrap-pic-w cat-card" data-reveal style="--reveal-delay: {{ (int) $delay }}ms">
    {{-- [LAMA, ditambah .fade-img] Gambar kategori --}}
    <img class="fade-img"
         src="{{ asset('storage/categories/' . $category->image) }}"
         alt="{{ $category->category_name }}"
         loading="lazy">

    {{-- [LAMA] Overlay teks + link Explore — struktur & class tidak berubah --}}
    <a href="{{ route('user.categories.products', $category->id) }}"
       class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
        <div class="block1-txt-child1 flex-col-l">
            <span class="block1-name ltext-102 trans-04 p-b-8">
                {{ $category->category_name }}
            </span>
        </div>

        <div class="block1-txt-child2 p-b-4 trans-05">
            <div class="block1-link stext-101 cl0 trans-09">
                Explore<span class="cat-arrow" aria-hidden="true">&rarr;</span>
            </div>
        </div>
    </a>

    {{-- [BARU] Meta pojok kartu. pointer-events:none supaya hover tetap
         diteruskan ke link .block1-txt di bawahnya. --}}
    <div class="cat-meta" aria-hidden="true">
        <span class="cat-meta__idx">{{ $indexNo }}</span>
        <span class="cat-meta__count">{{ $productCount }} Produk</span>
    </div>
</div>
