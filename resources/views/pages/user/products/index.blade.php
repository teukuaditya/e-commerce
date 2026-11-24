@extends('layouts.main')

@section('title', 'Koleksi Produk | DRVN')

@section('content')
<div class="bg0 m-t-23 p-b-140 product-hero">
    <div class="container">

        <!-- Top Header & Filters Bar -->
        <div class="flex-w flex-sb-m p-b-40 flex-column flex-md-row gap-3">

            <!-- Tombol Filter Kategori -->
            @php($activeCategory = is_string(request('category')) ? trim(request('category')) : '')
            <div class="flex-w flex-l-m filter-tope-group m-tb-10" id="categoryFilterGroup">
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-24 m-tb-5 {{ $activeCategory === '' ? 'how-active1' : '' }}"
                        data-filter="*" data-category="">
                    All Products
                </button>
                @foreach ($categories as $category)
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-24 m-tb-5 {{ ($activeCategory !== '' && strcasecmp($activeCategory, $category->category_name) === 0) ? 'how-active1' : '' }}"
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
                            value="{{ $query ?? '' }}"
                            autocomplete="off"
                            style="border-radius: 999px; height: 44px; width: 280px; transition: all 0.25s ease;">

                        <!-- Tombol Clear Search (x) -->
                        <button type="button"
                                id="btnClearSearch"
                                class="position-absolute {{ ($query ?? '') !== '' ? '' : 'd-none' }}"
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
        <div id="searchInfoBar" class="p-b-20 {{ ($query ?? '') !== '' ? '' : 'd-none' }}">
            <div class="d-flex align-items-center justify-content-between p-3 rounded-2xl" style="background: #F1F5F9; border-radius: 14px;">
                <span class="stext-102 cl2 font-weight-bold" id="searchInfoText">
                    Menampilkan hasil untuk: "<span class="text-indigo-600" id="searchQueryLabel">{{ $query ?? '' }}</span>"
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
        for (var i = 0; i < buttons.length; i++) buttons[i].classList.remove('how-active1');
        btn.classList.add('how-active1');
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
    function playEnter() {
        if (reduceMotion) return;
        var cards = grid.querySelectorAll('.product-grid-item');
        for (var i = 0; i < cards.length; i++) {
            (function (el, idx) {
                el.classList.remove('grid-enter');
                void el.offsetWidth;                          // reflow -> animasi diulang
                el.style.animationDelay = Math.min(idx * 30, 600) + 'ms';
                el.classList.add('grid-enter');
            })(cards[i], i);
        }
    }

    function showSkeleton() {
        if (skeleton) grid.innerHTML = skeleton.innerHTML;
        grid.classList.remove('is-fading');   // skeleton tampil dengan fade-in halus
        grid.setAttribute('aria-busy', 'true');
    }

    function startLoading() {
        clearTimeout(skeletonTimer);
        grid.setAttribute('aria-busy', 'true');
        if (reduceMotion || !skeleton) { showSkeleton(); return; }
        grid.classList.add('is-fading');       // fade-out grid LAMA (~250ms)
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
                grid.classList.remove('is-fading');       // jangan biarkan grid transparan
                grid.setAttribute('aria-busy', 'false');
                console.error('Gagal memuat produk:', err);
            });
    }

    function render(data) {
        clearTimeout(skeletonTimer);
        grid.innerHTML = data.html || '';
        grid.classList.remove('is-fading');     // fade-in konten baru
        grid.setAttribute('aria-busy', 'false');
        playEnter();

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
    transition: opacity 0.25s ease, transform 0.25s ease;
    will-change: opacity, transform;
  }
  #productGrid.is-fading {
    opacity: 0;
    transform: translateY(-8px);
    pointer-events: none;
  }

  /* ============ KARTU MASUK: fade + translateY bertahap ============ */
  .product-grid-item.grid-enter {
    animation: productGridEnter 0.35s cubic-bezier(0.16, 0.84, 0.44, 1) both;
  }
  @keyframes productGridEnter {
    from { opacity: 0; transform: translateY(18px) scale(0.98); }
    to   { opacity: 1; transform: none; }
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

  /* ============ HORMATI prefers-reduced-motion ====================== */
  @media (prefers-reduced-motion: reduce) {
    #productGrid { transition: none !important; }
    #productGrid.is-fading { opacity: 1 !important; transform: none !important; }
    .product-grid-item.grid-enter { animation: none !important; }
    .empty-products-state { animation: none !important; }
    .shimmer::after { animation: none !important; }
    #paginationBlock .page-item .page-link { transition: none; }
  }
</style>
@endpush
