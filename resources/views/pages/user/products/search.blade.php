@extends('layouts.main')

@section('content')
<div class="container search-hero">
    <!-- Breadcrumb -->
    <div class="bread-crumb flex-w p-r-15 p-t-30 p-lr-0-lg">
        <a href="{{ route('user.products.index') }}" class="stext-109 cl8 hov-cl1 trans-04">
            Products
            <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
        </a>
        <span class="stext-109 cl4">
            Search Results
        </span>
    </div>

    <!-- Search Info -->
    <div class="p-t-30 p-b-30">
        <h4 class="mtext-109 cl2" id="searchTitle">
            Search Results for: "{{ $query }}"
        </h4>
        <p class="stext-102 cl3 p-t-10" id="searchCount">
            Found {{ $total }} {{ Str::plural('product', $total) }}
        </p>
    </div>

    <!-- Kotak filter: produk ter-filter langsung saat mengetik (live search) -->
    <div class="flex-c-m flex-w p-b-25">
        <input
            class="plh3 stext-106 cl6 size-106 bor4 p-l-20 p-r-20"
            type="text"
            id="liveSearchInput"
            name="query"
            placeholder="Cari produk lain..."
            value="{{ request('query', $query) }}"
            autocomplete="off">
    </div>

    <!-- Products (grid di-render lewat partial — markup yang sama dengan AJAX live search) -->
    <div class="row isotope-grid" id="productGrid">
        @include('pages.user.products._partials.grid-items', ['products' => $products])
    </div>

    <!-- Pagination -->
    <div id="paginationBlock" class="flex-c-m flex-w w-full p-t-45">
        {{ $products->appends(['query' => $query])->links() }}
    </div>

    @if($products->count() === 0)
    <!-- No Results (tampilan awal hasil server) -->
    <div class="flex-c-m p-t-30 p-b-100">
        <div class="text-center">
            <i class="zmdi zmdi-search" style="font-size: 80px; color: #ccc;"></i>
            <h4 class="mtext-109 cl2 p-t-20 p-b-10">No Products Found</h4>
            <p class="stext-102 cl3">
                Sorry, we couldn't find any products matching "{{ $query }}"
            </p>
            <a href="{{ route('user.home') }}" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-t-30">
                Back to Home
            </a>
        </div>
    </div>
    @endif

    <!-- No Results (dipakai juga oleh AJAX live search saat hasilnya 0) -->
    <div id="emptyBlock" class="flex-c-m p-t-30 p-b-100" style="{{ $products->count() === 0 ? '' : 'display:none' }}">
        <div class="text-center">
            <i class="zmdi zmdi-search" style="font-size: 80px; color: #ccc;"></i>
            <h4 class="mtext-109 cl2 p-t-20 p-b-10">No Products Found</h4>
            <p class="stext-102 cl3">
                Sorry, we couldn't find any products matching "<span class="empty-query">{{ $query }}</span>"
            </p>
            <a href="{{ route('user.home') }}" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-t-30">
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        /* ==== LIVE SEARCH di halaman hasil pencarian ====
           Grid tetap Bootstrap grid murni (tanpa Isotope) — sama seperti
           halaman products — agar kolom tidak di-override jadi absolute. */
        var searchTimer = null;
        var searchXhr = null;
        var $input = $('#liveSearchInput');
        var $grid = $('#productGrid');
        var $pagination = $('#paginationBlock');
        var $empty = $('#emptyBlock');
        var lastQuery = $input.val();

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
                    $('#searchCount').text('Found ' + res.count + (res.count === 1 ? ' product' : ' products'));

                    // Hasil kosong lewat AJAX → tampilkan empty state
                    if (res.count === 0) {
                        $empty.find('.empty-query').text(q);
                        $empty.show();
                        $pagination.hide();
                    } else {
                        $empty.hide();
                        $pagination.hide(); // hasil live search tidak dipaginasi
                    }
                },
                error: function(xhr) {
                    if (xhr.statusText === 'abort') return;
                    console.error('Live search gagal:', xhr.responseText);
                }
            });
        }

        // Debounce 250 ms: tunggu user berhenti mengetik sebelum filter
        $input.on('keyup input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(liveSearch, 250);
        });
    });
</script>
@endpush
