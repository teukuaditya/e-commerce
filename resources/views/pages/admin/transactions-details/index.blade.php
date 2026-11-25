@extends('layouts.admin')

@section('admin_title', 'Detail Transaksi')

@section('content')
    <div class="container-fluid">

        <div class="adm-page-header">
            <div>
                <div class="adm-page-header__eyebrow"><i class="fas fa-list-ul"></i> Sales</div>
                <h1 class="adm-page-header__title">Transaction Details</h1>
                <p class="adm-page-header__sub">Rincian item per transaksi, harga, qty, dan subtotal.</p>
            </div>
        </div>

        <div class="adm-table-card">
            <div class="adm-table-card__header">
                <h2 class="adm-table-card__title">
                    <span class="adm-table-card__title-icon"><i class="fas fa-receipt"></i></span>
                    Detail List
                </h2>
                <span class="adm-table-card__meta">{{ $transactions_details->count() }} items</span>
            </div>
            <div class="adm-table-card__body">
                <div class="table-responsive">
                    <table class="table adm-table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Transaction</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions_details as $detail)
                                @php
                                    $product = $detail->product ?? null;
                                    $trx = $detail->transaction ?? null;
                                    $pImg = $product ? (is_array($product->image) ? ($product->image[0] ?? null) : $product->image) : null;
                                @endphp
                                <tr>
                                    <td class="adm-num adm-id">#{{ $detail->id }}</td>
                                    <td>
                                        <div class="adm-text-strong adm-num">{{ $trx->order_id ?? ('#' . $detail->transaction_id) }}</div>
                                        <div class="adm-text-sm">TRX ID #{{ $detail->transaction_id }}</div>
                                    </td>
                                    <td>
                                        <div class="adm-product-cell">
                                            @if ($pImg)
                                                <img src="{{ asset('storage/products/' . $pImg) }}" alt="{{ $product->title ?? 'Product' }}" class="adm-thumb">
                                            @else
                                                <span class="adm-thumb-empty"><i class="fas fa-box"></i></span>
                                            @endif
                                            <div>
                                                <div class="adm-text-strong">{{ $product->title ?? ('Product #' . $detail->product_id) }}</div>
                                                <div class="adm-text-sm">Product ID #{{ $detail->product_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="adm-num">{{ (int) ($detail->quantity ?? 0) }}</td>
                                    <td class="adm-num adm-nowrap">Rp {{ number_format((float) ($detail->price ?? 0), 0, ',', '.') }}</td>
                                    <td class="adm-num adm-nowrap adm-text-strong">Rp {{ number_format((float) ($detail->subtotal ?? 0), 0, ',', '.') }}</td>
                                    <td class="adm-nowrap adm-text-sm">{{ optional($detail->created_at)->format('d M Y, H:i') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="adm-empty">
                                            <i class="fas fa-inbox"></i>
                                            <strong>Belum ada data</strong>
                                            <p>Data detail transaksi akan tampil di sini begitu tersedia.</p>
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
