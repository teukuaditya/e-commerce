@extends('layouts.main')

@section('title', 'Wishlist | DRVN')

@section('content')
<section class="bg0 p-t-80 p-b-60">
    <div class="container">
        <div class="p-b-30">
            <span class="stext-106 cl6 d-inline-block" style="letter-spacing:0.18em; text-transform:uppercase; font-size:11px; margin-bottom:8px;">Your Saved Items</span>
            <h2 class="ltext-103 cl5" style="margin:0;">Wishlist</h2>
            <span style="display:block;width:48px;height:3px;background:#222;margin-top:14px;"></span>
        </div>

        @if($wishlists->isEmpty())
            <div class="txt-center p-t-40 p-b-40 bor10" style="background:#fafafa;">
                <i class="zmdi zmdi-favorite-outline" style="font-size:56px;color:#c7c7c7;"></i>
                <p class="stext-106 cl6 p-t-15">Wishlist kamu masih kosong.</p>
                <a href="{{ route('user.products.index') }}" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 m-t-20" style="max-width:220px;margin-left:auto;margin-right:auto;">
                    Jelajahi Produk
                </a>
            </div>
        @else
            <div class="row" id="wishlistGrid">
                @foreach($wishlists as $item)
                    @php $product = $item->product; @endphp
                    @continue(!$product)
                    @php
                        $img = is_array($product->image) ? ($product->image[0] ?? '') : $product->image;
                        $catName = optional($product->category)->category_name ?? '';
                        $isOutOfStock = (int) ($product->stock ?? 0) <= 0;
                    @endphp
                    <div class="col-6 col-md-3 p-b-35 wishlist-item" data-product-id="{{ $product->id }}">
                        <div class="block2 home-product-card">
                            <div class="block2-pic hov-img0 home-prod-pic" style="border-radius:14px; overflow:hidden; background:#f3f4f6;">
                                <a href="{{ route('user.products.show', $product->id) }}">
                                    <img src="{{ asset('storage/products/' . $img) }}" alt="{{ $product->title }}" loading="lazy" class="home-prod-img">
                                </a>
                                @if($isOutOfStock)
                                    <span style="position:absolute;top:12px;left:12px;z-index:3;background:#DC2626;color:#fff;font-size:11px;font-weight:700;padding:5px 10px;border-radius:999px;letter-spacing:.02em;">Out of Stock</span>
                                @endif

                                <div class="home-prod-actions" aria-label="Aksi cepat">
                                    <button type="button"
                                        class="home-prod-action-btn js-wishlist-toggle is-wishlisted"
                                        data-url="{{ route('user.wishlist.toggle', $product->id) }}"
                                        data-wishlisted="1"
                                        title="Hapus dari Wishlist"
                                        aria-label="Hapus {{ $product->title }} dari wishlist"
                                        aria-pressed="true">
                                        <i class="zmdi zmdi-favorite"></i>
                                    </button>
                                    @if($isOutOfStock)
                                        <button type="button" class="home-prod-action-btn" title="Stok Habis" aria-label="{{ $product->title }} stok habis"
                                            onclick="window.tuToast ? window.tuToast('Produk ini sedang habis stok', 'err') : alert('Produk ini sedang habis stok');"
                                            style="opacity:.55;cursor:not-allowed;">
                                            <i class="zmdi zmdi-shopping-cart"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('user.cart.add', $product->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="home-prod-action-btn" title="Tambah ke Keranjang" aria-label="Tambah {{ $product->title }} ke keranjang">
                                                <i class="zmdi zmdi-shopping-cart"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <a href="{{ route('user.products.show', $product->id) }}" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 home-prod-quickview">
                                    Lihat Detail
                                </a>
                            </div>

                            <div class="block2-txt flex-w flex-t p-t-14">
                                <div class="block2-txt-child1 flex-col-l">
                                    @if($catName)
                                        <span class="stext-106 cl6 trans-04 m-b-3 home-prod-cat">{{ strtoupper($catName) }}</span>
                                    @endif

                                    <a href="{{ route('user.products.show', $product->id) }}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6 home-prod-title">
                                        {{ $product->title }}
                                    </a>

                                    <div class="d-flex align-items-center justify-content-between" style="gap:8px;width:100%;">
                                        <a href="{{ route('user.products.show', $product->id) }}" class="stext-105 cl5 hov-cl1 trans-04 home-prod-price" style="font-weight:700; color:#111827;">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </a>
                                        <span class="stext-106 cl6" style="font-size:12px;white-space:nowrap;text-align:right;">{{ (int) ($product->sold_count ?? 0) }} terjual</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
    .home-prod-pic { background:#f3f4f6; overflow:hidden; position:relative; }
    .home-prod-pic .home-prod-img { transition: transform .35s ease; }
    .home-prod-pic:hover .home-prod-img { transform: scale(1.05); }
    .home-prod-actions { position:absolute; top:12px; right:12px; display:flex; flex-direction:column; gap:8px; opacity:1; transform:none; transition:opacity .3s ease, transform .3s ease; z-index:5; }
    .home-prod-action-form { margin:0; }
    .home-prod-action-btn { width:38px; height:38px; border-radius:50%; border:none; background:rgba(255,255,255,.96); color:#111827; display:inline-flex; align-items:center; justify-content:center; padding:0; line-height:1; box-shadow:0 4px 14px rgba(0,0,0,.08); cursor:pointer; transition:background .25s ease,color .25s ease,transform .25s ease; text-decoration:none; font-size:18px; }
    .home-prod-action-btn i { display:block; line-height:1; }
    .home-prod-action-btn:hover { background:#4F46E5; color:#fff; transform:scale(1.04); text-decoration:none; }
    .home-prod-action-btn.is-wishlisted { color:#e53935; }
    .home-prod-action-btn.is-wishlisted:hover { background:#e53935; color:#fff; }
    .home-prod-action-btn:focus-visible { outline:2px solid #4F46E5; outline-offset:2px; }
    .home-prod-quickview { z-index:4; }
    .home-prod-cat { display:block; font-family:Poppins-Medium,Poppins,sans-serif; font-size:10px; letter-spacing:.14em; color:#6b7280 !important; line-height:22px; min-height:22px; }
    .home-prod-title { display:-webkit-box !important; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:41px; font-family:Poppins-SemiBold,Poppins,sans-serif; color:#1f2937 !important; }
    .wishlist-item.is-removing { opacity:0; transform:scale(.96); transition:opacity .25s ease, transform .25s ease; }
</style>
@endpush

@push('scripts')
<script>
$(function () {
    $(document).on('click', '.js-wishlist-toggle', function (e) {
        e.preventDefault();
        var $btn = $(this);
        if ($btn.data('loading')) return;
        $btn.data('loading', true);

        $.ajax({
            url: $btn.data('url'),
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                var wished = !!res.wishlisted;
                $btn.toggleClass('is-wishlisted', wished);
                $btn.attr('aria-pressed', wished ? 'true' : 'false');
                $btn.find('i').attr('class', 'zmdi ' + (wished ? 'zmdi-favorite' : 'zmdi-favorite-outline'));

                $('a.icon-header-noti[href*="wishlist"]').each(function () {
                    var n = parseInt($(this).attr('data-notify') || '0', 10) || 0;
                    $(this).attr('data-notify', Math.max(0, n + (wished ? 1 : -1)));
                });

                if (!wished) {
                    var $card = $btn.closest('.wishlist-item');
                    $card.addClass('is-removing');
                    setTimeout(function () {
                        $card.remove();
                        if ($('#wishlistGrid .wishlist-item').length === 0) {
                            location.reload();
                        }
                    }, 260);
                }
            },
            error: function (xhr) {
                if (xhr.status === 401) window.location.href = '{{ route("login") }}';
            },
            complete: function () { $btn.data('loading', false); }
        });
    });
});
</script>
@endpush
