@extends('layouts.main')

@section('content')
<section class="bg0 p-b-60 account-page">
    <div class="container">
        <x-flash-alert class="stext-102 mb-4" />
        <div class="row">
            <div class="col-lg-4 col-md-4 mb-4 mb-lg-0">
                @include('layouts.partials.user-nav')
            </div>

            <div class="col-lg-8 col-md-8">
                <div class="account-card">
                    <div class="account-card__header">
                        <span class="account-card__eyebrow">Shipping</span>
                        <h2 class="account-card__title">My Address</h2>
                        <p style="margin:10px 0 0;color:#6B7280;font-size:14px;">Simpan alamat pengiriman agar checkout lebih cepat dan akurat.</p>
                    </div>
                    <div class="account-card__body">
                        <div id="address-display" style="{{ $errors->any() ? 'display:none;' : '' }}">
                            @if (auth()->user()->address)
                                <div class="account-info-grid">
                                    <div class="account-info-item">
                                        <span class="account-info-label">Recipient</span>
                                        <p class="account-info-value">{{ $recipientName ?? 'N/A' }}</p>
                                        <p style="margin:6px 0 0;color:#6B7280;font-size:14px;">{{ $recipientPhone ?? 'N/A' }}</p>
                                    </div>
                                    <div class="account-info-item">
                                        <span class="account-info-label">Destination</span>
                                        <p class="account-info-value">{{ $destinationName ?? 'N/A' }}</p>
                                    </div>
                                    <div class="account-info-item account-info-item--full">
                                        <span class="account-info-label">Address</span>
                                        <p class="account-info-value">{!! nl2br(e($streetAddress ?? 'N/A')) !!}</p>
                                    </div>
                                </div>
                            @else
                                <div class="account-info-item account-info-item--full" style="text-align:center;padding:32px 20px;">
                                    <p id="no-address-text" class="account-info-value" style="color:#6B7280;">Belum ada alamat. Tambahkan alamat pengirimanmu.</p>
                                </div>
                            @endif
                        </div>

                        <button id="edit-address-btn" class="account-btn account-btn-primary mt-4" style="{{ $errors->any() ? 'display:none;' : '' }}">
                            <i class="fas {{ auth()->user()->address ? 'fa-pen' : 'fa-plus' }}" style="font-size:12px;"></i>
                            {{ auth()->user()->address ? 'Edit Address' : 'Add Address' }}
                        </button>

                        <div id="address-form" style="{{ $errors->any() ? 'display:block;' : 'display:none;' }}">
                            <form action="{{ route('user.address.update') }}" method="POST" class="account-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 form-group mb-4">
                                        <label for="recipient_name">Recipient Name</label>
                                        <input type="text" id="recipient_name" name="recipient_name" class="form-control" value="{{ old('recipient_name', $recipientName ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6 form-group mb-4">
                                        <label for="recipient_phone">Recipient Phone</label>
                                        <input type="text" id="recipient_phone" name="recipient_phone" class="form-control" value="{{ old('recipient_phone', $recipientPhone ?? '') }}" required>
                                    </div>
                                    <div class="col-12 form-group mb-4">
                                        <label for="address">Address</label>
                                        <textarea id="address" name="address" class="form-control" rows="4" required>{{ old('address', $streetAddress ?? '') }}</textarea>
                                    </div>
                                    <div class="col-12 form-group mb-4">
                                        <label for="destination_name">Destination</label>
                                        <input type="text" id="destination_name" name="destination_name" class="form-control" value="{{ old('destination_name', $destinationName ?? '') }}" required autocomplete="off">
                                        <input type="hidden" id="destination_id" name="destination_id" value="{{ old('destination_id', $destinationId ?? '') }}">
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap" style="gap:10px;">
                                    <button type="button" id="cancel-edit-btn" class="account-btn account-btn-secondary">Cancel</button>
                                    <button type="submit" class="account-btn account-btn-primary">Save Address</button>
                                </div>
                            </form>
                        </div>

                        @if (auth()->user()->address)
                        <form action="/user/address/delete" method="POST" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="account-btn account-btn-danger">Delete Address</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
    {{-- jQuery UI (autocomplete) hanya di halaman ini — module berat CSS 32KB + JS 297KB --}}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    .address-hero {
        margin-top: 24px;
    }
</style>
@endpush

@push('scripts')
{{-- jQuery UI JS (autocomplete) — hanya di halaman address --}}
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>

    document.getElementById('edit-address-btn').addEventListener('click', function() {
        document.getElementById('address-display').style.display = 'none';
        document.getElementById('address-form').style.display = 'block';
        this.style.display = 'none';
    });

    document.getElementById('cancel-edit-btn').addEventListener('click', function() {
        document.getElementById('address-display').style.display = 'block';
        document.getElementById('address-form').style.display = 'none';
        document.getElementById('edit-address-btn').style.display = 'inline-block';
    });

    $(function() {
        $("#destination_name").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "/api/search-destinations",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.label,
                                value: item.label,
                                data: item
                            };
                        }));
                    },
                    error: function() {
                        response([]);
                    }
                });
            },
            minLength: 1,
            delay: 400,
            select: function(event, ui) {
                $('#destination_name').val(ui.item.label);
                $('#destination_id').val(ui.item.data.id);
                return false;
            }
        });
    });
</script>
@endpush