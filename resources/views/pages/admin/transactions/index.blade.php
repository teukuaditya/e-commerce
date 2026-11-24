@extends('layouts.admin')

@section('admin_title', 'Transaksi')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Transactions</h1>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Transaction List</h6>
            </div>
            <div class="card-body text-gray-900">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Gross Amount</th>
                                <th>Courier</th>
                                <th>Service</th>
                                <th>Transaction Time</th>
                                <th>Payment Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>{{ $transaction->order_id }}</td>
                                    <td>{{ $transaction->customer_name }}</td>
                                    <td>{{ number_format($transaction->gross_amount, 2) }}</td>
                                    <td>{{ $transaction->courier }}</td>
                                    <td>{{ $transaction->courier_service }}</td>
                                    <td>{{ $transaction->transaction_time }}</td>
                                    <td>{{ $transaction->payment_type }}</td>
                                    <td>
                                        @if($transaction->transaction_status === 'settlement')
                                            <span class="badge badge-success px-2 py-1" style="font-size: 0.8rem; background-color: #10B981; color: #fff;">Settlement</span>
                                        @elseif($transaction->transaction_status === 'pending')
                                            <span class="badge badge-warning px-2 py-1" style="font-size: 0.8rem; background-color: #F59E0B; color: #fff;">Pending</span>
                                        @elseif(in_array($transaction->transaction_status, ['expire', 'cancel', 'failure']))
                                            <span class="badge badge-danger px-2 py-1" style="font-size: 0.8rem; background-color: #EF4444; color: #fff;">{{ ucfirst($transaction->transaction_status) }}</span>
                                        @else
                                            <span class="badge badge-secondary px-2 py-1" style="font-size: 0.8rem;">{{ ucfirst($transaction->transaction_status ?? '-') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10">
                                    <div class="adm-empty">
                                        <i class="fas fa-inbox"></i>
                                        <strong>Belum ada data</strong>
                                        <p>Data Transaction akan tampil di sini begitu tersedia.</p>
                                    </div>
                                </td></tr>
                            @endforelse
</tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
