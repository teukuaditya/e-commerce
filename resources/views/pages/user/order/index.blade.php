@extends('layouts.main')

@section('content')
<section class="bg0 m-t-45 p-b-60 order-hero">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-4 col-md-4">
                @include('layouts.partials.user-nav')
            </div>

            <!-- Order History -->
            <div class="col-lg-8 col-md-8">
                <div class="mtext-109 cl2 p-b-30 bor10 p-lr-40 p-t-30 p-b-30 m-lr-0-xl p-lr-15-sm mb-4">Order History</div>
                @foreach($transactions as $transaction)
                    <div class="mb-4">
                        <div class="bor10 p-lr-40 p-t-30 p-b-30 m-lr-0-xl p-lr-15-sm">
                            <div class="d-flex align-items-center justify-content-between bor12 mb-3">
                                <div class="d-flex flex-column mb-2">
                                    <!-- Order ID -->
                                    <p class="badge {{
                                        ($transaction->transaction_status ?? 'pending') == 'pending' ? 'badge-warning' :
                                        (($transaction->transaction_status ?? 'pending') == 'settlement' ? 'badge-success' :
                                        (($transaction->transaction_status ?? 'pending') == 'expire' ? 'badge-danger' :
                                        (($transaction->transaction_status ?? 'pending') == 'failed' ? 'badge-danger' : 'badge-primary')))
                                    }}">
                                        {{ ucfirst($transaction->transaction_status ?? 'pending') }}
                                    </p>
                                    <p class="stext-102 cl2 mt-2 font-weight-bold">
                                        {{ $transaction->order_id }}
                                    </p>
                                </div>
                                <!-- Transaction Time -->
                                <p class="stext-102 cl2" style="font-size: 12px; margin-top: 0;">
                                    {{ $transaction->transaction_time }}
                                </p>
                            </div>

                            <!-- Product List -->
                            <div class="bor12 mb-3">
                                <div class="order-products mb-3">
                                    @foreach($transaction->details as $detail)
                                        <div class="cart-item d-flex align-items-center mb-3">
                                            <!-- Product Image -->
                                            <div class="cart-item-img" style="flex-shrink: 0; margin-right: 15px;">
                                                <img src="{{ asset('storage/products/' . $detail->product->image[0]) }}"
                                                     alt="{{ $detail->product->title }}" class="rounded" width="100">
                                            </div>

                                            <!-- Product Info -->
                                            <div class="cart-item-info flex-grow-1 ml-3">
                                                <h6 class="stext-102 cl2 font-weight-bold" style="font-size: 15px;">
                                                    {{ $detail->product->brand }}
                                                </h6>
                                                <p class="stext-102 cl2 mb-1" style="font-size: 12px;">
                                                    {{ $detail->product->title }}
                                                </p>
                                                <span class="mtext-107 cl2" style="font-size: 15px; font-weight: bold;">
                                                    Rp {{ number_format($detail->price, 0, ',', '.') }}
                                                </span>
                                                <p class="stext-102 cl2 mb-1" style="font-size: 12px;">
                                                    x{{ $detail->quantity }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Total Amount and Payment Type -->
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="order-summary">
                                    <p class="mtext-107 cl2" style="font-size: 15px; font-weight: bold;">
                                        Total: Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}
                                    </p>
                                </div>
                                @if(($transaction->transaction_status ?? 'pending') == 'pending' && $transaction->snap_token)
                                    <!-- Button to continue payment if pending -->
                                    <button class="continue-payment flex-c-m stext-102 cl0 size-127 bg1 ml-4 bor14 hov-btn5 p-lr-15 trans-04 pointer" style="font-size: 12px;" data-order-id="{{ $transaction->order_id }}">
                                        Continue Payment
                                    </button>
                                @elseif(in_array($transaction->transaction_status ?? 'pending', ['expire', 'failed']))
                                    <span class="stext-102 cl13" style="font-size: 12px; color: #dc3545">Payment Failed or Expired</span>
                                @else
                                    <span class="stext-102 cl13" style="font-size: 12px; color: #198754">Payment Completed</span>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection


@push('scripts')
<script>
$(document).ready(function() {
    // Handler untuk tombol Continue Payment
    $('.continue-payment').click(function() {
        var orderId = $(this).data('order-id');  // Ambil order_id dari tombol yang diklik

        // Ambil snap_token dari server untuk melanjutkan pembayaran
        $.ajax({
            url: '/user/orders/snap-token', // Endpoint untuk mendapatkan Snap Token
            type: 'GET',
            data: { order_id: orderId }, // Kirimkan order_id untuk mengambil snap_token
            success: function(response) {
                if (response.snap_token) {
                    console.log('Snap Token retrieved:', response.snap_token);

                    // Tampilkan popup pembayaran Snap
                    snap.pay(response.snap_token, {
                        onSuccess: function(result) {
                            console.log('Payment success:', result);
                            alert('Payment successful!');

                            // Kirimkan data status pembayaran ke backend
                            $.ajax({
                                url: '/user/orders/payment-success',
                                type: 'POST',
                                data: {
                                    order_id: result.order_id,
                                    payment_type: result.payment_type,
                                    transaction_status: result.transaction_status,
                                    transaction_id: result.transaction_id,
                                    gross_amount: result.gross_amount,
                                    customer_name: "{{ auth()->user()->name }}",
                                    courier: $('#courier').val(),
                                    courier_service: $('#shipping-service option:selected').val(),
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    window.location.href = "/user/orders/order-history"; // Redirect setelah pembayaran sukses
                                },
                                error: function(xhr) {
                                    alert('Failed to update payment status.');
                                }
                            });
                        },
                        onPending: function(result) {
                            alert('Payment pending. Please complete the payment.');
                        },
                        onError: function(result) {
                            alert('Payment failed. Please try again.');
                        }
                    });
                } else {
                    alert('Snap token not available. Please try again.');
                }
            },
            error: function(xhr) {
                console.error('Error fetching Snap token:', xhr.responseText);
                alert('Failed to fetch Snap token. Please try again.');
            }
        });
    });
});


</script>
@endpush

@push('styles')
<style>
  .order-hero {
    margin-top: 120px;
  }

  @media (max-width: 991.98px) {

    /* Di mobile biasanya header lebih kecil, jadi jaraknya bisa dikurangi */
    .order-hero {
      margin-top: 70px;
    }
  }
</style>
@endpush
