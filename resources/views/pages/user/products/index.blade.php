@extends('layouts.main')

@section('content')
<div class="bg0 m-t-23 p-b-140 product-hero">
    <div class="container">
        <div class="flex-w flex-sb-m p-b-52">
            <!-- Tombol Filter Kategori -->
            <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1" data-filter="*">
                    All Products
                </button>
                @foreach ($categories as $category)
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5"
                    data-filter=".{{ strtolower($category->category_name) }}">
                    {{ $category->category_name }}
                </button>
                @endforeach
            </div>

            <!-- Tombol Search -->
            <div class="flex-w flex-c-m m-tb-10">
                <form action="{{ route('products.search') }}" method="GET" class="flex-w m-tb-4" id="searchForm">
                    <input
                        class="plh3 stext-106 cl6 size-105 bor4 p-l-20 p-r-20 m-r-8"
                        type="text"
                        id="liveSearchInput"
                        name="query"
                        placeholder="Search products..."
                        value="{{ request('query') }}"
                        autocomplete="off">
                    <button class="flex-c-m stext-106 cl6 size-105 bor4 hov-btn3 trans-04" type="submit">
                        <i class="icon-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
                        Search
                    </button>
                </form>
            </div>
        </div>

        <!-- Produk (grid di-render lewat partial — markup yang sama dengan AJAX live search).
             class "isotope-grid" memungkinkan main.js (Isotope) menganimasikan filter kategori,
             sama seperti homepage kategori & halaman hasil search. -->
        <div class="row isotope-grid" id="productGrid">
            @include('pages.user.products._partials.grid-items', ['products' => $products])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        /* ==== LIVE SEARCH (filter langsung saat mengetik) ==== */
        var searchTimer = null;
        var searchXhr = null;
        var $input = $('#liveSearchInput');
        var $grid = $('#productGrid');
        var lastQuery = $input.val();

        // Inisialisasi ulang Isotope setelah hasil live-search mengganti innerHTML,
        // lalu terapkan ulang filter tombol yang sedang aktif — agar transisi
        // tetap halus seperti halaman kategori.
        function refreshIsotope() {
            if ($grid.data('isotope')) {
                $grid.isotope('destroy');
            }

            $grid.isotope({
                itemSelector: '.isotope-item',
                layoutMode: 'fitRows',
                percentPosition: true,
                transitionDuration: '0.35s'
            });

            var active = $('.filter-tope-group button.how-active1').attr('data-filter');
            if (active && active !== '*') {
                $grid.isotope({ filter: active });
            }
        }

        function liveSearch() {
            var q = $input.val().trim();

            if (q === lastQuery) return;
            lastQuery = q;

            if (searchXhr) searchXhr.abort();

            searchXhr = $.ajax({
                url: '{{ route("user.products.search") }}',
                type: 'GET',
                data: { query: q },
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(res) {
                    $grid.html(res.html);
                    refreshIsotope();
                },
                error: function(xhr) {
                    if (xhr.statusText === 'abort') return;
                    console.error('Live search gagal:', xhr.responseText);
                }
            });
        }

        // Debounce 250 ms
        $input.on('keyup input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(liveSearch, 250);
        });
    });
</script>
@endpush

@push('styles')
<style>
  .product-hero {
    margin-top: 24px;
  }
</style>
@endpush
