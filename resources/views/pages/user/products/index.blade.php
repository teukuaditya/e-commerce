@extends('layouts.main')

@section('title', 'Koleksi Produk | DRVN')

@section('content')
<div class="bg0 m-t-23 p-b-140 product-hero">
    <div class="container">

        <!-- Page Head -->
        <div class="product-page-head p-b-30">
            <span class="product-page-eyebrow">DRVN Collection</span>
            <h1 class="product-page-title">All Products</h1>
            <span class="product-page-accent" aria-hidden="true"></span>
        </div>

        <!-- Top Header & Filters Bar -->
        <div class="flex-w flex-sb-m p-b-40 flex-column flex-md-row gap-3 m-b-10">

            <!-- Tombol Filter Kategori -->
            @php($activeCategory = is_string(request('category')) ? trim(request('category')) : '')
            <div class="flex-w flex-l-m filter-tope-group m-tb-10" id="categoryFilterGroup" role="tablist" aria-label="Filter produk">
                <button class="stext-106 cl6 trans-04 m-r-32 m-tb-5 home-filter-btn {{ $activeCategory === '' ? 'how-active1' : '' }}"
                        data-filter="*" data-category="" role="tab" aria-selected="{{ $activeCategory === '' ? 'true' : 'false' }}">
                    All Products
                </button>
                @foreach ($categories as $category)
                <button class="stext-106 cl6 trans-04 m-r-32 m-tb-5 home-filter-btn {{ ($activeCategory !== '' && strcasecmp($activeCategory, $category->category_name) === 0) ? 'how-active1' : '' }}"
                        data-filter=".{{ strtolower($category->category_name) }}"
                        data-category="{{ $category->category_name }}" role="tab" aria-selected="{{ ($activeCategory !== '' && strcasecmp($activeCategory, $category->category_name) === 0) ? 'true' : 'false' }}">
                    {{ $category->category_name }}
                </button>
                @endforeach
            </div>

            <!-- In-Place Search Box -->
            <div class="flex-w flex-c-m m-tb-10 position-relative">
                <form action="{{ route('user.products.index') }}" method="GET" class="flex-w m-tb-4 position-relative" id="productSearchForm">
                    <div class="position-relative d-flex align-items-center">
                        <input
                            class="plh3 stext-106 cl6 size-105 bor4 p-l-20 p-r-40"
                            type="text"
                            id="liveSearchInput"
                            name="query"
                            placeholder="Cari produk..."
                            value="{{ $query ?? '' }}"
                            autocomplete="off"
                            style="border-radius: 999px; height: 44px; width: 280px; transition: all 0.25s ease;">

                        <button type="button"
                                id="btnClearSearch"
                                class="position-absolute {{ ($query ?? '') !== '' ? '' : 'd-none' }}"
                                style="right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94A3B8; font-size: 16px; cursor: pointer; padding: 2px 6px;"
                                title="Hapus pencarian">
                            <i class="zmdi zmdi-close"></i>
                        </button>
                    </div>

                    <button class="flex-c-m stext-106 cl0 size-105 bor4 trans-04 m-l-8"
                            type="submit"
                            id="btnSearchSubmit"
                            style="border-radius: 999px; height: 44px; padding: 0 20px; background-color: #111827; color: #FFFFFF; font-weight: 600; border: none; box-shadow: 0 4px 14px rgba(17,24,39,0.18);">
                        <i class="icon-search cl0 m-r-6 fs-15 zmdi zmdi-search"></i>
                        Cari
                    </button>
                </form>
            </div>

        </div>

        <!-- Info Hasil Pencarian Aktif -->
        <div id="searchInfoBar" class="p-b-20 {{ ($query ?? '') !== '' ? '' : 'd-none' }}">
            <div class="d-flex align-items-center justify-content-between p-3" style="background: #F8FAFC; border-radius: 14px; border: 1px solid #E5E7EB;">
                <span class="stext-102 cl2 font-weight-bold" id="searchInfoText">
                    Menampilkan hasil untuk: "<span style="color:#111827;" id="searchQueryLabel">{{ $query ?? '' }}</span>"
                </span>
                <button type="button" class="btn btn-sm text-muted font-weight-bold btn-reset-search" style="font-size: 0.8rem;">
                    Reset Filter &times;
                </button>
            </div>
        </div>

        <!-- Produk Grid: 4 kolom CSS murni, animasi filter via fade -->
        <div class="row product-item-grid product-grid-transition" id="productGrid" aria-live="polite" aria-busy="false">
            @include('pages.user.products._partials.grid-items', ['products' => $products])
        </div>

        <!-- Skeleton / shimmer loader (ditampilkan selama request AJAX berlangsung) -->
        <template id="gridSkeletonTemplate">
            @include('pages.user.products._partials.skeleton')
        </template>

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
(function () {
    'use strict';

    /* =====================================================================
       FILTER KATEGORI & SEARCH TANPA RELOAD (AJAX)
       - fetch + AbortController : request lama dibatalkan (anti race condition)
       - debounce pencarian 300ms
       - URL selalu sinkron via pushState/replaceState -> tombol back & refresh aman
       - state awal dibaca dari URL (?category=Baju&q=tee)
       - skeleton shimmer tampil selama memuat
       - hormati prefers-reduced-motion
       ===================================================================== */

    var DEBOUNCE_MS = 300;   // jeda pencarian saat mengetik
    var FADE_MS     = 250;   // durasi fade-out grid lama (lalu skeleton fade-in)
    // Path saja (tanpa origin) -> selalu same-origin apa pun host yang dibuka
    var INDEX_URL   = '{{ parse_url(route('user.products.index'), PHP_URL_PATH) }}';

    var grid       = document.getElementById('productGrid');
    var form       = document.getElementById('productSearchForm');
    var input      = document.getElementById('liveSearchInput');
    var btnClear   = document.getElementById('btnClearSearch');
    var pagination = document.getElementById('paginationBlock');
    var infoBar    = document.getElementById('searchInfoBar');
    var queryLabel = document.getElementById('searchQueryLabel');
    var catGroup   = document.getElementById('categoryFilterGroup');
    var skeleton   = document.getElementById('gridSkeletonTemplate');

    if (!grid || !form || !catGroup) return;

    // main.js memanggil plugin isotope yang TIDAK dimuat di halaman ini.
    // Beri padanan kosong supaya klik kategori tidak melempar TypeError di console.
    if (window.jQuery && !window.jQuery.fn.isotope) {
        window.jQuery.fn.isotope = function () { return this; };
    }

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var controller    = null;   // AbortController yang sedang aktif
    var debounceTimer = null;
    var skeletonTimer = null;

    var state = { q: '', category: '' };   // filter yang sedang aktif

    /* ---------------------------- util URL ---------------------------- */
    function buildUrl() {
        var p = new URLSearchParams();
        if (state.q) p.set('q', state.q);
        if (state.category) p.set('category', state.category);
        var qs = p.toString();
        return INDEX_URL + (qs ? '?' + qs : '');
    }

    function pushUrl(url, push) {
        try {
            var s = { q: state.q, category: state.category };
            if (push) history.pushState(s, '', url);
            else history.replaceState(s, '', url);
        } catch (e) { /* noop */ }
    }

    function readUrlIntoState() {
        var p = new URLSearchParams(window.location.search);
        state.q = (p.get('q') || p.get('query') || '').trim();
        state.category = (p.get('category') || '').trim();
    }

    function setActiveTab(btn) {
        if (!btn) return;
        var buttons = catGroup.querySelectorAll('button');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove('how-active1');
            buttons[i].setAttribute('aria-selected', 'false');
        }
        btn.classList.add('how-active1');
        btn.setAttribute('aria-selected', 'true');
    }

    function allButton() {
        return catGroup.querySelector('button[data-filter="*"]') || catGroup.querySelector('button');
    }

    function toggleClear() {
        if (!btnClear) return;
        btnClear.classList.toggle('d-none', input.value.trim().length === 0);
    }

    /* --------------------- sinkron UI dari URL ------------------------ */
    function syncUiFromUrl() {
        readUrlIntoState();
        input.value = state.q;
        toggleClear();

        var target = null;
        if (state.category) {
            var buttons = catGroup.querySelectorAll('button');
            for (var i = 0; i < buttons.length; i++) {
                var c = (buttons[i].getAttribute('data-category') || '').trim();
                if (c.toLowerCase() === state.category.toLowerCase()) { target = buttons[i]; break; }
            }
        }
        if (!target) target = allButton();
        setActiveTab(target);

        if (state.q) { queryLabel.textContent = state.q; infoBar.classList.remove('d-none'); }
        else infoBar.classList.add('d-none');
    }

    /* --------------------------- animasi grid ------------------------- */
    function playEnter() { /* grid-level slide handles transition */ }

    function enterGrid() {
        if (reduceMotion) {
            grid.classList.remove('is-leaving', 'is-enter-start');
            return;
        }
        grid.classList.add('is-enter-start');
        grid.classList.remove('is-leaving');
        void grid.offsetWidth; // reflow: snap ke posisi kanan
        grid.classList.remove('is-enter-start'); // transisi ke posisi normal
    }

    function showSkeleton() {
        if (skeleton) grid.innerHTML = skeleton.innerHTML;
        enterGrid();
        grid.setAttribute('aria-busy', 'true');
    }

    function startLoading() {
        clearTimeout(skeletonTimer);
        grid.setAttribute('aria-busy', 'true');
        if (reduceMotion || !skeleton) { showSkeleton(); return; }
        grid.classList.add('is-leaving');       // geser keluar ke kiri
        grid.classList.remove('is-enter-start');
        skeletonTimer = setTimeout(showSkeleton, FADE_MS);
    }

    /* --------------------------- fetch produk ------------------------- */
    function fetchProducts(url, push) {
        if (controller) controller.abort();              // batalkan request lama
        controller = new AbortController();
        var mine = controller;

        startLoading();

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: mine.signal
        })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                if (mine !== controller) return;         // sudah ada request lebih baru
                render(data);
                pushUrl(url, push === true);
            })
            .catch(function (err) {
                if (err && err.name === 'AbortError') return;   // dibatalkan -> diam
                if (mine !== controller) return;
                clearTimeout(skeletonTimer);
                grid.classList.remove('is-leaving', 'is-enter-start');
                grid.setAttribute('aria-busy', 'false');
                console.error('Gagal memuat produk:', err);
            });
    }

    function render(data) {
        clearTimeout(skeletonTimer);
        grid.innerHTML = data.html || '';
        enterGrid();
        grid.setAttribute('aria-busy', 'false');

        pagination.innerHTML = data.pagination || '';

        if (data.query) { queryLabel.textContent = data.query; infoBar.classList.remove('d-none'); }
        else infoBar.classList.add('d-none');
    }

    /* --------------------------- aksi filter -------------------------- */
    function applyFilters(push) {
        state.q = input.value.trim();
        fetchProducts(buildUrl(), push === true);
    }

    /* ============================ EVENT =============================== */

    // 1) Search: debounce 300ms (ganti URL via replaceState supaya history tidak penuh)
    input.addEventListener('input', function () {
        toggleClear();
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () { applyFilters(false); }, DEBOUNCE_MS);
    });

    // 2) Submit (Enter / tombol Cari) -> pushState
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearTimeout(debounceTimer);
        applyFilters(true);
    });

    // 3) Tombol Clear (x)
    if (btnClear) {
        btnClear.addEventListener('click', function () {
            input.value = '';
            toggleClear();
            input.focus();
            clearTimeout(debounceTimer);
            applyFilters(true);
        });
    }

    // 4) Reset (info bar & empty state)
    document.addEventListener('click', function (e) {
        if (!e.target || !e.target.closest) return;
        var btn = e.target.closest('.btn-reset-search');
        if (!btn) return;

        input.value = '';
        state.q = '';
        state.category = '';
        toggleClear();
        infoBar.classList.add('d-none');
        setActiveTab(allButton());
        clearTimeout(debounceTimer);
        applyFilters(true);
    });

    // 5) Tab kategori -> pushState
    catGroup.addEventListener('click', function (e) {
        if (!e.target || !e.target.closest) return;
        var btn = e.target.closest('button');
        if (!btn || !catGroup.contains(btn)) return;
        e.preventDefault();
        clearTimeout(debounceTimer);
        setActiveTab(btn);
        state.category = (btn.getAttribute('data-category') || '').trim();
        applyFilters(true);
    });

    // 6) Pagination AJAX (perpindahan halaman instan tanpa reload)
    pagination.addEventListener('click', function (e) {
        if (!e.target || !e.target.closest) return;
        var a = e.target.closest('a');
        if (!a) return;
        e.preventDefault();
        var href = a.getAttribute('href');
        if (!href) return;
        clearTimeout(debounceTimer);
        fetchProducts(href, true);
    });

    // 7) Tombol back / forward browser
    window.addEventListener('popstate', function () {
        clearTimeout(debounceTimer);
        syncUiFromUrl();
        fetchProducts(window.location.href, false);
    });

    /* ======================= INIT DARI URL ============================ */
    // Konten sudah dirender server sesuai URL, jadi cukup sinkronkan UI saja.
    syncUiFromUrl();

    // Wishlist toggle (AJAX, delegation agar tetap jalan setelah AJAX render)
    if (window.jQuery) {
        window.jQuery(document).on('click', '.js-wishlist-toggle', function (e) {
            e.preventDefault();
            var $btn = window.jQuery(this);
            if ($btn.data('loading')) return;
            $btn.data('loading', true);
            window.jQuery.ajax({
                url: $btn.data('url'),
                method: 'POST',
                data: { _token: window.jQuery('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    var wished = !!res.wishlisted;
                    $btn.toggleClass('is-wishlisted', wished);
                    $btn.attr('data-wishlisted', wished ? '1' : '0');
                    $btn.attr('aria-pressed', wished ? 'true' : 'false');
                    $btn.attr('title', wished ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist');
                    $btn.find('i').attr('class', 'zmdi ' + (wished ? 'zmdi-favorite' : 'zmdi-favorite-outline'));
                    window.jQuery('a.icon-header-noti[href*="wishlist"]').each(function () {
                        var n = parseInt(window.jQuery(this).attr('data-notify') || '0', 10) || 0;
                        window.jQuery(this).attr('data-notify', Math.max(0, n + (wished ? 1 : -1)));
                    });
                },
                error: function (xhr) {
                    if (xhr && xhr.status === 401) window.location.href = '{{ route("login") }}';
                },
                complete: function () { $btn.data('loading', false); }
            });
        });
    }
})();
</script>
@endpush

@push('styles')
<style>
  .product-hero {
    margin-top: 24px;
  }

  /* ============ GRID: transisi fade + cegah layout shift ============ */
  #productGrid {
    min-height: 420px;
    transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: opacity, transform;
  }
  #productGrid.is-leaving {
    opacity: 0;
    transform: translateX(-20px);
    pointer-events: none;
  }
  #productGrid.is-enter-start {
    transition: none !important;
    opacity: 0;
    transform: translateX(20px);
  }

  /* ============ EMPTY STATE: fade-up halus ============ */
  .empty-products-state {
    animation: emptyStateEnter 0.3s ease both;
  }
  @keyframes emptyStateEnter {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: none; }
  }

  /* ============ SKELETON / SHIMMER ============ */
  .product-skeleton .skeleton-pic {
    display: block;
    height: 260px;
    border-radius: 8px;
  }
  .product-skeleton .skeleton-line {
    display: block;
    border-radius: 6px;
  }
  .shimmer {
    position: relative;
    overflow: hidden;
    background: #E9EDF4;
  }
  .shimmer::after {
    content: "";
    position: absolute;
    inset: 0;
    transform: translateX(-100%);
    background: linear-gradient(90deg,
      rgba(255,255,255,0) 0%,
      rgba(255,255,255,0.75) 50%,
      rgba(255,255,255,0) 100%);
    animation: shimmerSweep 1.2s linear infinite;
  }
  @keyframes shimmerSweep {
    100% { transform: translateX(100%); }
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

  /* ============ FILTER PILLS (sama seperti home) =================== */
  .home-filter-btn {
    border: 1px solid #d1d5db !important;
    border-radius: 999px;
    padding: 9px 20px !important;
    margin-right: 10px !important;
    background: #fff;
    color: #4b5563;
    line-height: 1.2;
  }
  .home-filter-btn:hover {
    border-color: #111827 !important;
    color: #111827;
  }
  .home-filter-btn.how-active1 {
    background: #111827 !important;
    border-color: #111827 !important;
    color: #fff !important;
  }
  .home-filter-btn:focus-visible {
    outline: 2px solid #4F46E5;
    outline-offset: 2px;
  }

  /* ============ MODERN PAGE POLISH ================================= */
  .product-page-head {
    margin-bottom: 8px;
  }
  .product-page-eyebrow {
    display: inline-block;
    font-family: Poppins-Medium, Poppins, sans-serif;
    font-size: 11px;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: #6b7280;
    margin-bottom: 10px;
  }
  .product-page-title {
    margin: 0;
    font-family: Poppins-SemiBold, Poppins, sans-serif;
    font-size: clamp(2rem, 4.5vw, 3.25rem);
    line-height: 1.05;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    color: #111827;
  }
  .product-page-accent {
    display: block;
    width: 48px;
    height: 3px;
    background: #222;
    margin-top: 14px;
  }
  .product-page-sub {
    margin: 14px 0 0;
    font-size: 0.95rem;
    line-height: 1.6;
    color: #6b7280;
    max-width: 560px;
  }

  .product-toolbar {
    background: #fff;
    border: 1px solid #eceff3;
    border-radius: 20px;
    padding: 18px 20px;
    box-shadow: 0 8px 30px rgba(15, 23, 42, 0.03);
  }

  #liveSearchInput:focus {
    border-color: #6366F1 !important;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
    outline: none;
  }

  #btnSearchSubmit:hover {
    background-color: #000 !important;
    box-shadow: 0 6px 18px rgba(17,24,39,0.28) !important;
    transform: translateY(-1px);
  }
  #btnSearchSubmit:active {
    transform: translateY(0);
  }
  #btnSearchSubmit:focus-visible {
    outline: 2px solid #4F46E5;
    outline-offset: 2px;
  }

  /* Card polish */
  .product-item-grid .block2-pic {
    border-radius: 14px;
    overflow: hidden;
    background: #f3f4f6;
  }
  .product-item-grid .block2-pic img {
    transition: transform 0.35s ease;
  }
  .product-item-grid .block2-pic:hover img {
    transform: scale(1.04);
  }
  .product-item-grid .block2-txt-child1 > a.stext-104 {
    display: -webkit-box !important;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 41px;
  }

  #paginationBlock .page-item.active .page-link {
    background: #111827 !important;
    border-color: #111827 !important;
    box-shadow: 0 4px 12px rgba(17, 24, 39, 0.2);
  }

  @media (max-width: 767px) {
    .home-filter-btn { margin-right: 8px !important; padding: 8px 14px !important; font-size: 12px; }
    .product-toolbar { padding: 14px; border-radius: 16px; }
    #liveSearchInput { width: 100% !important; min-width: 0; }
  }

  /* ============ CARD ACTIONS (modern overlay) ====================== */
  .home-prod-pic { position: relative; overflow: hidden; background: #f3f4f6; }
  .home-prod-pic .home-prod-img { transition: transform 0.35s ease; }
  .home-prod-pic:hover .home-prod-img { transform: scale(1.04); }
  .home-prod-actions {
    position: absolute;
    top: 12px;
    right: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    opacity: 1;
    transform: none;
    transition: opacity 0.3s ease, transform 0.3s ease;
    z-index: 5;
  }
  .home-prod-action-btn {
    width: 38px; height: 38px; border-radius: 50%; border: none;
    background: rgba(255,255,255,0.96); color: #111827;
    display: inline-flex; align-items: center; justify-content: center;
    padding: 0; line-height: 1; font-size: 18px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08); cursor: pointer;
    transition: background 0.25s ease, color 0.25s ease, transform 0.25s ease;
    text-decoration: none;
  }
  .home-prod-action-btn i { display:block; line-height:1; }
  .home-prod-action-btn:hover { background: #4F46E5; color: #fff; transform: scale(1.04); text-decoration: none; }
  .home-prod-action-btn.is-wishlisted { color: #e53935; }
  .home-prod-action-btn.is-wishlisted:hover { background:#e53935; color:#fff; }
  .home-prod-cat {
    display:block; font-family: Poppins-Medium, Poppins, sans-serif;
    font-size: 10px; letter-spacing: 0.14em; color: #6b7280 !important;
    line-height: 22px; min-height: 22px;
  }
  .home-prod-title {
    display: -webkit-box !important; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden; min-height: 41px;
    font-family: Poppins-SemiBold, Poppins, sans-serif; color: #1f2937 !important;
  }
  .home-prod-quickview { z-index: 4; }
  .wishlist-inline-btn {
    background: none; border: none; padding: 0; cursor: pointer;
    line-height: 1; font-size: 20px; color: #999;
    transition: color 0.2s ease, transform 0.2s ease;
  }
  .wishlist-inline-btn:hover { color: #e53935; transform: scale(1.08); }
  .wishlist-inline-btn.is-wishlisted { color: #e53935; }
  .wishlist-inline-btn i { display: block; line-height: 1; }

  @media (hover: none), (pointer: coarse) {
    .home-prod-actions { opacity: 1; transform: none; }
  }

  /* ============ HORMATI prefers-reduced-motion ====================== */
  @media (prefers-reduced-motion: reduce) {
    #productGrid { transition: none !important; }
    #productGrid.is-leaving,
    #productGrid.is-enter-start { opacity: 1 !important; transform: none !important; transition: none !important; }
    .empty-products-state { animation: none !important; }
    .shimmer::after { animation: none !important; }
    #paginationBlock .page-item .page-link { transition: none; }
    .home-filter-btn, .home-prod-pic .home-prod-img, .home-prod-actions, .home-prod-action-btn { transition: none !important; transform: none !important; }
  }
</style>
@endpush
