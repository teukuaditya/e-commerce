@extends('layouts.main')

@section('content')
    <section class="bg0 m-t-45 p-b-60">
        <div class="container">
            @if (session('success'))
                <div id="success-alert" class="stext-102 alert alert-success">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="col-lg-4 col-md-4">
                    @include('layouts.partials.user-nav')
                </div>

                <div class="col-lg-8 col-md-8">
                    <div class="mtext-109 cl2 p-b-30 bor10 p-lr-40 p-t-30 m-lr-0-xl p-lr-15-sm mb-4">
                        Address
                    </div>

                    <div class="bor10 p-lr-40 p-t-30 p-b-30 m-lr-0-xl p-lr-15-sm">
                        <div id="address-display">
                            <div class="d-flex flex-column align-items-start">
                                @if (auth()->user()->address)
                                    <h6 class="mtext-106 cl2 mb-2" style="font-weight: bold;">
                                        {{ $recipientName ?? 'N/A' }}
                                    </h6>
                                    <p class="stext-101 cl2 mb-2">{{ $recipientPhone ?? 'N/A' }}</p>
                                    <p class="stext-104 cl2 mb-2">{{ $streetAddress ?? 'N/A' }}</p>
                                    <p class="stext-102 cl3">{{ $destinationName ?? 'N/A' }}</p>
                                @else
                                    <div class="stext-102 cl2">
                                        <p id="no-address-text">No address available. Please add a new address.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <button id="edit-address-btn"
                            class="flex-c-m stext-102 cl0 size-127 bg1 mt-3 bor14 hov-btn5 p-lr-15 trans-04 pointer"
                            style="font-size: 12px; min-width: 10%; height: 30px">
                            {{ auth()->user()->address ? 'Edit Address' : 'Add Address' }}
                        </button>

                        <div id="address-form" style="display:none;">
                            <form action="{{ route('user.address.update') }}" method="POST">
                                @csrf
                                <div class="form-group mb-4">
                                    <label for="recipient_name" class="mtext-102">Recipient Name</label>
                                    <input type="text" id="recipient_name" name="recipient_name" class="form-control"
                                        value="{{ $recipientName ?? '' }}" required>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="recipient_phone" class="mtext-102">Recipient Phone</label>
                                    <input type="text" id="recipient_phone" name="recipient_phone" class="form-control"
                                        value="{{ $recipientPhone ?? '' }}" required>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="address" class="mtext-102">Address</label>
                                    <textarea id="address" name="address" class="form-control" rows="4" required>{{ $streetAddress ?? '' }}</textarea>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="destination_name" class="mtext-102">Destination</label>
                                    <input type="text" id="destination_name" name="destination_name" class="form-control"
                                        value="{{ $destinationName ?? '' }}" required autocomplete="off">
                                    <input type="hidden" id="destination_id" name="destination_id"
                                        value="{{ $destinationId ?? '' }}">
                                </div>

                                <div class="d-flex justify-content-start gap-2">
                                    <button type="button" id="cancel-edit-btn"
                                        class="flex-c-m stext-102 cl0 size-127 bg10 mt-3 bor14 hov-btn4 p-lr-15 trans-04 pointer"
                                        style="font-size: 12px; height: 30px;">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="flex-c-m stext-102 cl0 size-127 bg1 mt-3 bor14 hov-btn5 p-lr-15 trans-04 pointer"
                                        style="font-size: 12px; height: 30px;">
                                        Save Address
                                    </button>
                                </div>
                            </form>
                        </div>

                        @if (auth()->user()->address)
                            <form action="/user/address/delete" method="POST" style="margin-top: 15px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex-c-m stext-102 cl0 size-127 bg10 mt-2 bor14 hov-btn4 p-lr-15 trans-04 pointer"
                                    style="font-size: 12px; height: 30px;">
                                    Delete Address
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        setTimeout(function() {
            let alertElement = document.getElementById('success-alert');
            if (alertElement) {
                alertElement.style.transition = "opacity 0.5s ease";
                alertElement.style.opacity = 0;
                setTimeout(() => alertElement.remove(), 500);
            }
        }, 2000);

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
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    $('#destination_name').val(ui.item.label);
                    $('#destination_id').val(ui.item.data.id);
                    return false;
                }
            });
        });
    </script>
@endpush
