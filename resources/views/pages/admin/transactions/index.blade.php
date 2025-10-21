@extends('layouts.admin')

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
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>{{ $transaction->order_id }}</td>
                                    <td>{{ $transaction->customer_name }}</td>
                                    <td>{{ number_format($transaction->gross_amount, 2) }}</td>
                                    <td>{{ $transaction->courier }}</td>
                                    <td>{{ $transaction->courier_service }}</td>
                                    <td>{{ $transaction->transaction_time }}</td>
                                    <td>{{ $transaction->payment_type }}</td>
                                    <td>{{ ucfirst($transaction->transaction_status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
