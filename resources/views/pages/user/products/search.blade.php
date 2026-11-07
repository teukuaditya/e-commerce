@extends('layouts.main')

@section('title', 'Search Results | DRVN')

@section('content')
<div class="container search-hero p-b-100">
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
    <div class="p-t-30 p-b-20">
        <h4 class="mtext-109 cl2 font-weight-bold" id="searchTitle">
            Search Results for: "<span class="text-indigo-600">{{ $query }}</span>"
        </h4>
        <p class="stext-102 cl3 p-t-6" id="searchCount">
            Found {{ $total }} {{ Str::plural('product', $total) }}
        </p>
    </div>

    <!-- Kotak Live Search in-place -->
    <div class="flex-c-m flex-w p-b-35">
        <form action="{{ route('products.search') }}" method="GET" class="flex-w position-relative" id="searchResultForm">
            <input
                class="plh3 stext-106 cl6 size-106 bor4 p-l-20 p-r-20"
                type="text"
                id="liveSearchInput"
                name="query"
                placeholder="Cari produk lain..."
                value="{{ request('query', $query) }}"
                autocomplete="off"
                style="border-radius: 999px; height: 44px; width: 320px;">
            <button class="flex-c-m stext-106 cl0 size-105 bor4 hov-btn1 trans-04 m-l-8"
                    type="submit"
                    style="border-radius: 999px; height: 44px; padding: 0 20px; background-color: #6366F1; color: #FFFFFF; font-weight: 600; border: none;">
                <i class="icon-search cl0 m-r-6 fs-15 zmdi zmdi-search"></i>
                Cari
            </button>
        </form>
    </div>

    <!-- Products (4 Produk ke kanan di layar laptop/desktop) -->
    <div class="row isotope-grid product-grid-transition" id="productGrid">
        @include('pages.user.products._partials.grid-items', ['products' => $products])
    </div>

    <!-- Fast AJAX Pagination -->
    <div id="paginationBlock" class="flex-c-m flex-w w-full p-t-45">
        @if ($products->hasPages())
            {{ $products->appends(['query' => $query])->links() }}
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/isotope/isotope.pkgd.min.js') }}"></script>
<script>
    $(document).ready(function() {
        var searchTimer = null;
        var searchXhr = null;
        var $input = $('#liveSearchInput');
        var $form = $('#searchResultForm');
        var $grid = $('#productGrid');
        var $pagination = $('#paginationBlock');

        function fetchSearch(url, q, shouldScroll) {
            if (searchXhr) searchXhr.abort();
            $grid.addClass('is-loading');

            searchXhr = $.ajax({
                url: url || '{{ route("user.products.search") }}',
                type: 'GET',
                data: { query: q },
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(res) {
                    $grid.html(res.html);
                    $('#searchCount').text('Found ' + res.count + (res.count === 1 ? ' product' : ' products'));
                    if (res.pagination) {
                        $pagination.html(res.pagination);
                    }

                    if (shouldScroll) {
                        $('html, body').animate({ scrollTop: $grid.offset().top - 120 }, 250);
                    }
                },
                error: function(xhr) {
                    if (xhr.statusText === 'abort') return;
                },
                complete: function() {
                    $grid.removeClass('is-loading');
                }
            });
        }

        $input.on('input keyup', function(e) {
            if (e.key === 'Enter') return;
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                var q = $input.val().trim();
                fetchSearch('{{ route("user.products.search") }}', q, false);
            }, 250);
        });

        $form.on('submit', function(e) {
            e.preventDefault();
            clearTimeout(searchTimer);
            var q = $input.val().trim();
            fetchSearch('{{ route("user.products.search") }}', q, false);
        });

        $(document).on('click', '#paginationBlock a', function(e) {
            e.preventDefault();
            var targetUrl = $(this).attr('href');
            if (!targetUrl) return;
            fetchSearch(targetUrl, $input.val().trim(), true);
        });
    });
</script>
@endpush

@push('styles')
<style>
  .search-hero { margin-top: 24px; }
  .product-grid-transition { transition: opacity 0.2s ease; }
  .product-grid-transition.is-loading { opacity: 0.45; pointer-events: none; }

  /* 4 produk per baris mulai md samaris dengan halaman catalog (permintaan user) */
  @media (min-width: 768px) {
    #productGrid .product-grid-item,
    .isotope-grid .product-grid-item {
      flex: 0 0 25% !important;
      max-width: 25% !important;
      width: 25% !important;
    }
  }
  @media (max-width: 767.98px) {
    #productGrid .product-grid-item,
    .isotope-grid .product-grid-item {
      flex: 0 0 50% !important;
      max-width: 50% !important;
      width: 50% !important;
    }
  }
</style>
@endpush
