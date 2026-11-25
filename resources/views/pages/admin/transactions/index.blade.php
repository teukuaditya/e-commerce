@extends('layouts.admin')

@section('admin_title', 'Transaksi')

@section('content')
    <div class="container-fluid">

        <div class="adm-page-header">
            <div>
                <div class="adm-page-header__eyebrow"><i class="fas fa-receipt"></i> Sales</div>
                <h1 class="adm-page-header__title">Transactions</h1>
                <p class="adm-page-header__sub">Pantau transaksi, pembayaran, dan status pesanan.</p>
            </div>
        </div>

        <div class="adm-table-card">
            <div class="adm-table-card__header">
                <h2 class="adm-table-card__title">
                    <span class="adm-table-card__title-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                    Transaction List
                </h2>
                <span class="adm-table-card__meta">{{ $transactions->count() }} transactions</span>
            </div>
            <div class="adm-table-card__body">
                <div class="table-responsive">
                    <table class="table adm-table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Shipping</th>
                                <th>Payment</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                @php
                                    $status = strtolower($transaction->transaction_status ?? '');
                                    $statusClass = match (true) {
                                        in_array($status, ['settlement', 'capture', 'paid', 'completed', 'success']) => 'adm-badge--success',
                                        $status === 'pending' => 'adm-badge--warning',
                                        in_array($status, ['expire', 'cancel', 'cancelled', 'failure', 'deny', 'denied']) => 'adm-badge--danger',
                                        default => 'adm-badge--gray',
                                    };
                                @endphp
                                <tr>
                                    <td class="adm-num adm-id">#{{ $transaction->id }}</td>
                                    <td>
                                        <div class="adm-text-strong adm-num">{{ $transaction->order_id ?? '—' }}</div>
                                        <div class="adm-text-sm">Invoice / Order</div>
                                    </td>
                                    <td>
                                        <div class="adm-text-strong">{{ $transaction->customer_name ?? '—' }}</div>
                                    </td>
                                    <td class="adm-num adm-nowrap">Rp {{ number_format((float) ($transaction->gross_amount ?? 0), 0, ',', '.') }}</td>
                                    <td>
                                        @if($transaction->courier || $transaction->courier_service)
                                            <div class="adm-text-strong">{{ strtoupper($transaction->courier ?? '-') }}</div>
                                            <div class="adm-text-sm">{{ $transaction->courier_service ?? '—' }}</div>
                                        @else
                                            <span class="adm-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->payment_type)
                                            <span class="adm-badge adm-badge--info adm-badge--no-dot">{{ strtoupper(str_replace('_', ' ', $transaction->payment_type)) }}</span>
                                        @else
                                            <span class="adm-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="adm-nowrap adm-text-sm">{{ $transaction->transaction_time ?? '—' }}</td>
                                    <td>
                                        <span class="adm-badge {{ $statusClass }}">{{ ucfirst($status ?: 'unknown') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="adm-empty">
                                            <i class="fas fa-inbox"></i>
                                            <strong>Belum ada data</strong>
                                            <p>Data transaksi akan tampil di sini begitu tersedia.</p>
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
