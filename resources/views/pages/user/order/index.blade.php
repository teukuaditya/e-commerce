@extends('layouts.main')

@section('content')
<section class="bg0 p-b-60 account-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 mb-4 mb-lg-0">
                @include('layouts.partials.user-nav')
            </div>

            <div class="col-lg-8 col-md-8">
                <div class="account-card">
                    <div class="account-card__header">
                        <span class="account-card__eyebrow">Purchases</span>
                        <h2 class="account-card__title">Order History</h2>
                        <p style="margin:10px 0 0;color:#6B7280;font-size:14px;">Lacak status pesanan dan lanjutkan pembayaran yang tertunda.</p>
                    </div>
                    <div class="account-card__body">
                        <div class="order-history-scroll">
                        @forelse($transactions as $transaction)
                            @php
                                $status = $transaction->transaction_status ?? 'pending';
                                $statusClass = match($status) {
                                    'settlement', 'capture' => 'is-success',
                                    'pending' => 'is-warning',
                                    'expire', 'failed', 'cancel', 'deny' => 'is-danger',
                                    default => 'is-info',
                                };
                            @endphp
                            <article class="order-card">
                                <div class="order-card__head">
                                    <div style="min-width:0;">
                                        <span class="order-status {{ $statusClass }}">{{ ucfirst($status) }}</span>
                                        <p class="order-card__id">{{ $transaction->order_id }}</p>
                                    </div>
                                    <p class="order-card__time">{{ $transaction->transaction_time }}</p>
                                </div>

                                <div class="order-card__items">
                                    @foreach($transaction->details as $detail)
                                        @php
                                            $hasImg = !empty($detail->product?->image) && isset($detail->product->image[0]);
                                            $imgUrl = $hasImg ? asset('storage/products/' . $detail->product->image[0]) : asset('images/product-01.jpg');
                                        @endphp
                                        <div class="order-item">
                                            <img src="{{ $imgUrl }}" alt="{{ $detail->product->title ?? 'Product' }}" class="order-item__img">
                                            <div class="order-item__info">
                                                @if(!empty($detail->product->brand))
                                                    <p class="order-item__brand">{{ $detail->product->brand }}</p>
                                                @endif
                                                <p class="order-item__title">{{ $detail->product->title ?? 'Item' }}</p>
                                                <div class="order-item__meta">
                                                    <span class="order-item__price">Rp {{ number_format($detail->price, 0, ',', '.') }}</span>
                                                    <span class="order-item__qty">x{{ $detail->quantity }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="order-card__foot">
                                    <div>
                                        <span class="order-card__total-label">Total Pembayaran</span>
                                        <p class="order-card__total">Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="order-card__action">
                                        @if(($transaction->transaction_status ?? 'pending') == 'pending' && $transaction->snap_token)
                                            <button class="continue-payment account-btn account-btn-primary" style="padding:10px 16px;font-size:13px;" data-order-id="{{ $transaction->order_id }}">
                                                Continue Payment
                                            </button>
                                        @elseif(in_array($transaction->transaction_status ?? 'pending', ['expire', 'failed']))
                                            <span class="order-action-note is-danger">Payment Failed or Expired</span>
                                        @else
                                            <span class="order-action-note is-success">Payment Completed</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="account-info-item account-info-item--full" style="text-align:center;padding:40px 20px;">
                                <p class="account-info-value" style="color:#6B7280;">Belum ada riwayat pesanan.</p>
                            </div>
                        @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@push('scripts')
{{-- Midtrans Snap SDK — hanya di halaman order (tombol Bayar Sekarang) --}}
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ config('midtrans.client_key') }}"></script>
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
  .account-page { padding-top: 24px; }
  .order-history-scroll {
    max-height: 72vh;
    overflow-y: auto;
    padding-right: 6px;
    scroll-behavior: smooth;
  }
  .order-history-scroll::-webkit-scrollbar { width: 8px; }
  .order-history-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 999px; }
  .order-history-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
  .order-history-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .order-card { background:#fff; border:1px solid #E5E7EB; border-radius:16px; padding:20px; margin-bottom:16px; }
  .order-card:last-child { margin-bottom:0; }
  .order-card__head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; padding-bottom:16px; margin-bottom:16px; border-bottom:1px solid #EEF2F7; }
  .order-status { display:inline-flex; align-items:center; padding:5px 10px; border-radius:999px; font-size:11px; font-weight:700; letter-spacing:.03em; text-transform:capitalize; }
  .order-status.is-warning { background:#FEF3C7; color:#92400E; }
  .order-status.is-success { background:#DCFCE7; color:#166534; }
  .order-status.is-danger { background:#FEE2E2; color:#991B1B; }
  .order-status.is-info { background:#E0F2FE; color:#075985; }
  .order-card__id { margin:10px 0 0; font-size:14px; font-weight:700; color:#111827; word-break:break-word; }
  .order-card__time { margin:0; font-size:12px; color:#6B7280; white-space:nowrap; }
  .order-card__items { display:flex; flex-direction:column; gap:14px; padding-bottom:16px; margin-bottom:16px; border-bottom:1px solid #EEF2F7; }
  .order-item { display:flex; gap:14px; align-items:center; }
  .order-item__img { width:76px; height:76px; object-fit:cover; border-radius:12px; background:#F3F4F6; flex:0 0 76px; }
  .order-item__info { min-width:0; flex:1; }
  .order-item__brand { margin:0; font-size:12px; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:.04em; }
  .order-item__title { margin:2px 0 6px; font-size:14px; color:#111827; font-weight:500; line-height:1.4; }
  .order-item__meta { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .order-item__price { font-size:14px; font-weight:700; color:#111827; }
  .order-item__qty { font-size:13px; color:#6B7280; }
  .order-card__foot { display:flex; align-items:center; justify-content:space-between; gap:16px; }
  .order-card__total-label { display:block; font-size:12px; color:#6B7280; margin-bottom:4px; }
  .order-card__total { margin:0; font-size:18px; font-weight:700; color:#111827; }
  .order-card__action { display:flex; align-items:center; }
  .order-action-note { font-size:13px; font-weight:600; }
  .order-action-note.is-danger { color:#DC2626; }
  .order-action-note.is-success { color:#16A34A; }
  @media (max-width: 575.98px) {
    .order-card__head, .order-card__foot { flex-direction:column; align-items:flex-start; }
    .order-card__time { white-space:normal; }
    .order-card__action, .order-card__action .account-btn { width:100%; }
    .order-item__img { width:64px; height:64px; flex-basis:64px; }
  }
</style>
@endpush
