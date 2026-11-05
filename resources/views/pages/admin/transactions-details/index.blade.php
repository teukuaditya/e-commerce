@extends('layouts.admin')

@section('admin_title', 'Detail Transaksi')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Transaction Details</h1>

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
                                <th>Transaction ID</th>
                                <th>Product ID</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                                <th>Transaction Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions_details as $transactions)
                                <tr>
                                    <td>{{ $transactions->id }}</td>
                                    <td>{{ $transactions->transaction_id }}</td>
                                    <td>{{ $transactions->product_id }}</td>
                                    <td>{{ $transactions->quantity }}</td>
                                    <td>{{ number_format($transactions->price, 2) }}</td>
                                    <td>{{ number_format($transactions->subtotal, 2) }}</td>
                                    <td>{{ $transactions->created_at }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8">
                                    <div class="adm-empty">
                                        <i class="fas fa-inbox"></i>
                                        <strong>Belum ada data</strong>
                                        <p>Data Transaction Detail akan tampil di sini begitu tersedia.</p>
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
