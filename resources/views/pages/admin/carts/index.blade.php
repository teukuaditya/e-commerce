@extends('layouts.admin')

@section('admin_title', 'Keranjang')

@section('content')
    <div class="container-fluid">

        <div class="adm-page-header">
            <div>
                <div class="adm-page-header__eyebrow"><i class="fas fa-shopping-cart"></i> Operations</div>
                <h1 class="adm-page-header__title">Carts</h1>
                <p class="adm-page-header__sub">Pantau item yang masih ada di keranjang pengguna.</p>
            </div>
        </div>

        <div class="adm-table-card">
            <div class="adm-table-card__header">
                <h2 class="adm-table-card__title">
                    <span class="adm-table-card__title-icon"><i class="fas fa-shopping-basket"></i></span>
                    Cart List
                </h2>
                <span class="adm-table-card__meta">{{ $carts->count() }} items</span>
            </div>
            <div class="adm-table-card__body">
                <div class="table-responsive">
                    <table class="table adm-table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Product</th>
                                <th>Size</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($carts as $cart)
                                @php
                                    $user = $cart->user ?? null;
                                    $product = $cart->product ?? null;
                                    $pImg = $product ? (is_array($product->image) ? ($product->image[0] ?? null) : $product->image) : null;
                                    $price = (float) ($product->price ?? 0);
                                    $qty = (int) ($cart->quantity ?? 0);
                                    $subtotal = $price * $qty;
                                    $nameParts = preg_split('/\s+/', trim($user->name ?? 'U'));
                                    $initials = strtoupper(substr($nameParts[0] ?? 'U', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
                                @endphp
                                <tr>
                                    <td class="adm-num adm-id">#{{ $cart->id }}</td>
                                    <td>
                                        <div class="adm-user-cell">
                                            <span class="adm-avatar">{{ $user ? $initials : '?' }}</span>
                                            <div>
                                                <div class="adm-user-cell__name">{{ $user->name ?? ('User #' . $cart->user_id) }}</div>
                                                <div class="adm-user-cell__sub">{{ $user->email ?? ('ID #' . $cart->user_id) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="adm-product-cell">
                                            @if ($pImg)
                                                <img src="{{ asset('storage/products/' . $pImg) }}" alt="{{ $product->title ?? 'Product' }}" class="adm-thumb">
                                            @else
                                                <span class="adm-thumb-empty"><i class="fas fa-box"></i></span>
                                            @endif
                                            <div>
                                                <div class="adm-text-strong">{{ $product->title ?? ('Product #' . $cart->product_id) }}</div>
                                                <div class="adm-text-sm adm-num">Rp {{ number_format($price, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($cart->size)
                                            <span class="adm-badge adm-badge--gray adm-badge--no-dot">{{ $cart->size }}</span>
                                        @else
                                            <span class="adm-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="adm-num">{{ $qty }}</td>
                                    <td class="adm-num adm-nowrap adm-text-strong">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="adm-empty">
                                            <i class="fas fa-inbox"></i>
                                            <strong>Belum ada data</strong>
                                            <p>Data keranjang akan tampil di sini begitu tersedia.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
