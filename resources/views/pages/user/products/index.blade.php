@extends('layouts.main')

@section('title', 'Koleksi Produk | DRVN')

@section('content')
<div class="bg0 m-t-23 p-b-140 product-hero">
    <div class="container">

        <!-- Top Header & Filters Bar -->
        <div class="flex-w flex-sb-m p-b-40 flex-column flex-md-row gap-3">

            <!-- Tombol Filter Kategori -->
            <div class="flex-w flex-l-m filter-tope-group m-tb-10" id="categoryFilterGroup">
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-24 m-tb-5 how-active1" data-filter="*" data-category="">
                    All Products
                </button>
                @foreach ($categories as $category)
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-24 m-tb-5"
                        data-filter=".{{ strtolower($category->category_name) }}"
                        data-category="{{ $category->category_name }}">
                    {{ $category->category_name }}
                </button>
                @endforeach
            </div>

            <!-- In-Place Search Box (Tidak redirect ke page lain, tetap di halaman katalog) -->
            <div class="flex-w flex-c-m m-tb-10 position-relative">
                <form action="{{ route('user.products.index') }}" method="GET" class="flex-w m-tb-4 position-relative" id="productSearchForm">
                    <div class="position-relative d-flex align-items-center">
                        <input
                            class="plh3 stext-106 cl6 size-105 bor4 p-l-20 p-r-40"
                            type="text"
                            id="liveSearchInput"
                            name="query"
                            placeholder="Cari produk di sini..."
                            value="{{ request('query', $query ?? '') }}"
                            autocomplete="off"
                            style="border-radius: 999px; height: 44px; width: 280px; transition: all 0.25s ease;">

                        <!-- Tombol Clear Search (x) -->
                        <button type="button"
                                id="btnClearSearch"
                                class="position-absolute {{ request('query') ? '' : 'd-none' }}"
                                style="right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94A3B8; font-size: 16px; cursor: pointer; padding: 2px 6px;"
                                title="Hapus pencarian">
                            <i class="zmdi zmdi-close"></i>
                        </button>
                    </div>

                    <!-- Tombol Search (Submit in-place tanpa pindah halaman) -->
                    <button class="flex-c-m stext-106 cl0 size-105 bor4 hov-btn1 trans-04 m-l-8"
                            type="submit"
                            id="btnSearchSubmit"
                            style="border-radius: 999px; height: 44px; padding: 0 20px; background-color: #6366F1; color: #FFFFFF; font-weight: 600; border: none; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);">
                        <i class="icon-search cl0 m-r-6 fs-15 zmdi zmdi-search"></i>
                        Cari
                    </button>
                </form>
            </div>

        </div>

        <!-- Info Hasil Pencarian Aktif -->
        <div id="searchInfoBar" class="p-b-20 {{ request('query') ? '' : 'd-none' }}">
            <div class="d-flex align-items-center justify-content-between p-3 rounded-2xl" style="background: #F1F5F9; border-radius: 14px;">
                <span class="stext-102 cl2 font-weight-bold" id="searchInfoText">
                    Menampilkan hasil untuk: "<span class="text-indigo-600" id="searchQueryLabel">{{ request('query') }}</span>"
                </span>
                <button type="button" class="btn btn-sm text-muted font-weight-bold btn-reset-search" style="font-size: 0.8rem;">
                    Reset Filter &times;
                </button>
            </div>
        </div>

        <!-- Produk Grid: 4 kolom CSS murni, animasi filter via fade -->
        <div class="row product-item-grid product-grid-transition" id="productGrid">
            @include('pages.user.products._partials.grid-items', ['products' => $products])
        </div>

        <!-- Seamless AJAX Pagination Container (Perpindahan Halaman Instan) -->
        <div id="paginationBlock" class="flex-c-m flex-w w-full p-t-45">
            @if ($products->hasPages())
                {{ $products->links() }}
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        /* ==== IN-PLACE LIVE SEARCH & SEAMLESS AJAX PAGINATION ==== */
        var searchTimer = null;
        var searchXhr = null;
        var $input = $('#liveSearchInput');
        var $btnClear = $('#btnClearSearch');
        var $form = $('#productSearchForm');
        var $grid = $('#productGrid');
        var $pagination = $('#paginationBlock');
        var $infoBar = $('#searchInfoBar');
        var $queryLabel = $('#searchQueryLabel');
        var activeCategory = '';

        // Toggle tombol clear (x)
        function toggleClearBtn() {
            if ($input.val().trim().length > 0) {
                $btnClear.removeClass('d-none');
            } else {
                $btnClear.addClass('d-none');
            }
        }

        // Grid kini layout CSS murni (server-side filter); AJAX mengganti
        // inner-HTML #productGrid — tidak ada plugin basis-data (isotope) lagi.

        // Fungsi fetch data produk via AJAX (live search & pagination)
        function fetchProducts(targetUrl, query, category, shouldScroll) {
            if (searchXhr) searchXhr.abort();

            $grid.addClass('is-loading');

            var params = {};
            if (query !== undefined) params.query = query;
            if (category !== undefined) params.category = category;

            searchXhr = $.ajax({
                url: targetUrl || '{{ route("user.products.index") }}',
                type: 'GET',
                data: params,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(res) {
                    $grid.html(res.html);
                    $pagination.html(res.pagination || '');

                    // Update info bar
                    if (res.query && res.query.length > 0) {
                        $queryLabel.text(res.query);
                        $infoBar.removeClass('d-none');
                    } else {
                        $infoBar.addClass('d-none');
                    }

                    // Update URL browser tanpa refresh (history pushState)
                    var newUrl = new URL(window.location.href);
                    if (res.query) {
                        newUrl.searchParams.set('query', res.query);
                    } else {
                        newUrl.searchParams.delete('query');
                    }
                    if (category) {
                        newUrl.searchParams.set('category', category);
                    } else {
                        newUrl.searchParams.delete('category');
                    }
                    window.history.replaceState({}, '', newUrl.toString());

                    if (shouldScroll) {
                        $('html, body').animate({
                            scrollTop: $grid.offset().top - 120
                        }, 300);
                    }
                },
                error: function(xhr) {
                    if (xhr.statusText === 'abort') return;
                    console.error('Gagal memuat produk:', xhr.responseText);
                },
                complete: function() {
                    $grid.removeClass('is-loading');
                }
            });
        }

        // 1. Live search saat mengetik (debounced 250ms)
        $input.on('input keyup', function(e) {
            toggleClearBtn();
            if (e.key === 'Enter') return; // ditangani oleh form submit

            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                var q = $input.val().trim();
                fetchProducts('{{ route("user.products.index") }}', q, activeCategory, false);
            }, 250);
        });

        // 2. Cegah redirect form! Eksekusi in-place saat tombol Cari ditekan atau Enter
        $form.on('submit', function(e) {
            e.preventDefault();
            clearTimeout(searchTimer);
            var q = $input.val().trim();
            fetchProducts('{{ route("user.products.index") }}', q, activeCategory, false);
        });

        // 3. Tombol Clear Search (x)
        $btnClear.on('click', function() {
            $input.val('').focus();
            toggleClearBtn();
            fetchProducts('{{ route("user.products.index") }}', '', activeCategory, false);
        });

        // 4. Reset search & filter
        $(document).on('click', '.btn-reset-search', function() {
            $input.val('');
            toggleClearBtn();
            $('#categoryFilterGroup button').removeClass('how-active1');
            $('#categoryFilterGroup button[data-filter="*"]').addClass('how-active1');
            activeCategory = '';
            fetchProducts('{{ route("user.products.index") }}', '', '', true);
        });

        // 5. Category filter button
        $('#categoryFilterGroup button').on('click', function(e) {
            e.preventDefault();
            $('#categoryFilterGroup button').removeClass('how-active1');
            $(this).addClass('how-active1');

            activeCategory = $(this).attr('data-category') || '';
            var q = $input.val().trim();
            fetchProducts('{{ route("user.products.index") }}', q, activeCategory, false);
        });

        // 6. Seamless AJAX Pagination (Perpindahan halaman instan tanpa reload!)
        $(document).on('click', '#paginationBlock a', function(e) {
            e.preventDefault();
            var targetPageUrl = $(this).attr('href');
            if (!targetPageUrl) return;

            fetchProducts(targetPageUrl, undefined, undefined, true);
        });
    });
</script>
@endpush

@push('styles')
<style>
  .product-hero {
    margin-top: 24px;
  }
  .product-grid-transition {
    transition: opacity 0.2s ease, transform 0.2s ease;
  }
  .product-grid-transition.is-loading {
    opacity: 0.45;
    pointer-events: none;
    transform: scale(0.995);
  }

  /* 4 Produk ke kanan di semua layar >= md (sesuai permintaan: harus 4 per baris) */
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

  /* Styling Pagination Cepat & Modern */
  #paginationBlock .pagination {
    display: flex;
    gap: 8px;
    align-items: center;
    list-style: none;
    padding: 0;
    margin: 0;
  }
  #paginationBlock .page-item .page-link {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    border: 1px solid #E2E8F0;
    background: #FFFFFF;
    color: #475569;
    font-weight: 700;
    font-size: 0.88rem;
    transition: all 0.2s ease;
    text-decoration: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  }
  #paginationBlock .page-item.active .page-link {
    background: #6366F1 !important;
    border-color: #4F46E5 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
  }
  #paginationBlock .page-item .page-link:hover {
    background: #F1F5F9;
    border-color: #CBD5E1;
    color: #0F172A;
    transform: translateY(-1px);
  }
  #paginationBlock .page-item.disabled .page-link {
    opacity: 0.4;
    pointer-events: none;
    background: #F8FAFC;
  }
</style>
@endpush
