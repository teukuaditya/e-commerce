@extends('layouts.main')

@section('content')
    <!-- breadcrumb -->
    <div class="container">
        <div class="bread-crumb flex-w p-r-15 p-t-30 p-lr-0-lg">
            <a href="{{ route('user.cart.index') }}" class="stext-109 cl8 hov-cl1 trans-04">
                Cart
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>

            <span class="stext-109 cl4">
                Checkout
            </span>
        </div>
    </div>
    <section class="bg0 m-t-45 p-b-60">
        <div class="container">
            <div class="row">
                <!-- Kolom Kiri: Alamat dan Shipping -->
                <div class="col-lg-7">
                    <div class="bor10 p-lr-40 p-t-30 p-b-30 m-lr-0-xl p-lr-15-sm">
                        <h3 class="mtext-109 cl2 p-b-30">Delivery</h3>
                        <form action="#" id="address-form">
                            <!-- Alamat Pengiriman -->
                            <div class="size-204 respon6-next">

                                <!-- Menampilkan Alamat yang Tersimpan -->
                                @if (auth()->user()->address)
                                    @php
                                        $address = auth()->user()->address;

                                        preg_match(
                                            '/Nama Penerima: (.*?), No\. Telepon: (.*?), Alamat: (.*?), Destination: (.*?), Destination ID: (.*)/',
                                            $address,
                                            $matches,
                                        );

                                        $recipientName = $matches[1] ?? '';
                                        $recipientPhone = $matches[2] ?? '';
                                        $streetAddress = $matches[3] ?? '';
                                        $destinationName = $matches[4] ?? '';
                                        $destinationId = $matches[5] ?? '';
                                    @endphp
                                    <div class="d-flex flex-column align-items-start">
                                        <h6 class="mtext-106 cl2 mb-2" style="font-weight: bold; font-size: 16px;">
                                            {{ $recipientName }}
                                        </h6>
                                        <p class="stext-101 cl2 mb-2" style="font-size: 14px;">
                                            {{ $recipientPhone }}
                                        </p>
                                        <p class="stext-104 cl2 mb-2">
                                            {{ $streetAddress }}
                                        </p>
                                        <p class="stext-102 cl3">
                                            {{ $destinationName }}
                                        </p>
                                    </div>
                                    <input type="hidden" id="destination_id" name="destination_id"
                                        value="{{ $destinationId }}">
                                @else
                                    <div class="stext-102 cl2">
                                        <p id="no-address-text">No address available. Please add a new address.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Tampilkan tombol untuk menambah alamat jika belum ada -->
                            @if (!auth()->user()->address)
                                <button type="button" id="add-address-btn"
                                    class="flex-c-m stext-102 cl0 size-127 bg1 mt-3 bor14 hov-btn5 p-lr-15 trans-04 pointer"
                                    style="font-size: 12px; min-width: 10%; height: 30px" data-toggle="modal"
                                    data-target="#addAddressModal">
                                    Add Address
                                </button>
                            @endif

                            @if (auth()->user()->address)
                                <button type="button" id="edit-address-btn"
                                    class="flex-c-m stext-102 cl0 size-127 bg1 mt-3 bor14 hov-btn5 p-lr-15 trans-04 pointer"
                                    style="font-size: 12px; min-width: 10%; height: 30px" data-toggle="modal"
                                    data-target="#editAddressModal">
                                    Edit Address
                                </button>
                            @endif

                            <!-- Tombol Hapus Alamat -->
                            @if (auth()->user()->address)
                                <button type="button" id="delete-address-btn"
                                    class="flex-c-m stext-102 cl0 size-127 bg10 mt-3 bor14 hov-btn4 p-lr-15 trans-04 pointer"
                                    style="font-size: 12px; min-width: 10%; height: 30px">
                                    Delete Address
                                </button>
                            @endif

                            <!-- Modal Edit Address -->
                            <div class="modal fade" id="editAddressModal" tabindex="-1" role="dialog"
                                aria-labelledby="editAddressModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title mtext-103 cl2" id="editAddressModalLabel">Edit Address
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body stext-102 cl2">
                                            <form id="edit-address-form" action="{{ route('user.address.update') }}"
                                                method="POST">
                                                @csrf
                                                <!-- Recipient Name -->
                                                <div class="form-group">
                                                    <label for="edit-recipient-name">Recipient Name</label>
                                                    <input type="text" class="form-control" id="edit-recipient-name"
                                                        name="recipient_name" value="{{ $recipientName ?? '' }}">
                                                </div>

                                                <!-- Phone Number -->
                                                <div class="form-group">
                                                    <label for="edit-recipient-phone">Phone Number</label>
                                                    <input type="text" class="form-control" id="edit-recipient-phone"
                                                        name="recipient_phone" value="{{ $recipientPhone ?? '' }}">
                                                </div>

                                                <!-- Street Address -->
                                                <div class="form-group">
                                                    <label for="edit-address">Full Address</label>
                                                    <textarea class="form-control" id="edit-address" name="address" rows="2">{{ $streetAddress ?? '' }}</textarea>
                                                </div>

                                                <!-- Destination -->
                                                <div class="form-group mb-4">
                                                    <label for="edit-destination-name" class="mtext-102">Destination</label>
                                                    <input type="text" id="edit-destination-name" name="destination_name"
                                                        class="form-control" value="{{ $destinationName ?? '' }}" required
                                                        autocomplete="off">
                                                    <input type="hidden" id="edit-destination-id" name="destination_id"
                                                        value="{{ $destinationId ?? '' }}">
                                                </div>
                                            </form>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button"
                                                class="flex-c-m stext-102 cl0 size-304 bg10 bor14 hov-btn4 p-lr-15 trans-04 pointer"
                                                data-dismiss="modal">Cancel</button>
                                            <button type="submit" id="save-edit-address-btn"
                                                class="flex-c-m stext-102 cl0 size-304 bg1 bor14 hov-btn5 p-lr-15 trans-04 pointer"
                                                form="edit-address-form">Save Changes</button>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <!-- Modal Add Address -->
                            <div class="modal fade" id="addAddressModal" tabindex="-1" role="dialog"
                                aria-labelledby="addAddressModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title mtext-103 cl2" id="addAddressModalLabel">Add Address
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body stext-102 cl2">
                                            <form id="new-address-form">
                                                <div class="form-group">
                                                    <label for="recipient-name">Recipient Name</label>
                                                    <input type="text" class="form-control" id="recipient-name"
                                                        name="recipient_name">
                                                </div>

                                                <div class="form-group">
                                                    <label for="recipient-phone">Phone Number</label>
                                                    <input type="text" class="form-control" id="recipient-phone"
                                                        name="recipient_phone">
                                                </div>

                                                <div class="form-group">
                                                    <label for="new-address">Full Address</label>
                                                    <textarea class="form-control" id="new-address" name="address" rows="2"></textarea>
                                                </div>

                                                <div class="form-group mb-4">
                                                    <label for="new-destination-name"
                                                        class="mtext-102">Destination</label>
                                                    <input type="text" id="new-destination-name"
                                                        name="destination_name" class="form-control" required
                                                        autocomplete="off">
                                                    <input type="hidden" id="new-destination-id" name="destination_id">
                                                </div>
                                            </form>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button"
                                                class="flex-c-m stext-102 cl0 size-304 bg10 bor14 hov-btn4 p-lr-15 trans-04 pointer"
                                                data-dismiss="modal">Cancel</button>
                                            <button type="button" id="save-address-btn"
                                                class="flex-c-m stext-102 cl0 size-304 bg1 bor14 hov-btn5 p-lr-15 trans-04 pointer">Save
                                                Address</button>
                                        </div>

                                    </div>
                                </div>
                            </div>



                            <!-- Style untuk menonaktifkan tombol -->
                            <style>
                                /* Mengubah tombol save edit addres saat dinonaktifkan */
                                #save-edit-address-btn:disabled {
                                    opacity: 0.5;
                                    /* Membuat tombol tampak lebih transparan */
                                    cursor: not-allowed;
                                    /* Menonaktifkan pointer */
                                    pointer-events: none;
                                    /* Tidak bisa diklik */
                                }

                                /* Menimpa efek hover dan pointer saat tombol sedang dalam status "Menyimpan..." */
                                #save-edit-address-btn.saving {
                                    opacity: 0.5;
                                    /* Membuat tombol tampak transparan */
                                    cursor: not-allowed;
                                    /* Menonaktifkan pointer */
                                    pointer-events: none;
                                    /* Tidak bisa diklik */
                                    background-color: #dcdcdc;
                                    /* Ganti warna latar belakang saat sedang menyimpan */
                                }

                                /* Menimpa hover saat tombol sedang menyimpan */
                                #save-edit-address-btn.saving:hover {
                                    background-color: #f0f0f0;
                                    /* Menjaga warna latar belakang */
                                }

                                /* Mengubah tombol save addres saat dinonaktifkan */
                                #save-address-btn:disabled {
                                    opacity: 0.5;
                                    /* Membuat tombol tampak lebih transparan */
                                    cursor: not-allowed;
                                    /* Menonaktifkan pointer */
                                    pointer-events: none;
                                    /* Tidak bisa diklik */
                                }

                                /* Menimpa efek hover dan pointer saat tombol sedang dalam status "Menyimpan..." */
                                #save-address-btn.saving {
                                    opacity: 0.5;
                                    /* Membuat tombol tampak transparan */
                                    cursor: not-allowed;
                                    /* Menonaktifkan pointer */
                                    pointer-events: none;
                                    /* Tidak bisa diklik */
                                    background-color: #dcdcdc;
                                    /* Ganti warna latar belakang saat sedang menyimpan */
                                }

                                /* Menimpa hover saat tombol sedang menyimpan */
                                #save-address-btn.saving:hover {
                                    background-color: #f0f0f0;
                                    /* Menjaga warna latar belakang */
                                }
                            </style>

                            <style>
                                /* CSS untuk Modal */
                                .modal-backdrop {
                                    z-index: 1200 !important;
                                    /* Pastikan backdrop lebih tinggi dari top bar */
                                }

                                .modal {
                                    z-index: 1300 !important;
                                    /* Modal harus berada di atas backdrop */
                                }

                                /* Memberikan margin atas pada modal */
                                .modal-dialog {
                                    margin-top: 80px;
                                    /* Atur sesuai dengan kebutuhan, semakin besar maka semakin ke bawah */
                                }
                            </style>
                    </div>

                    <!-- Shipping Method -->
                    <div class="bor10 p-lr-40 p-t-30 p-b-30 m-lr-0-xl p-lr-15-sm m-t-30">
                        <h3 class="mtext-109 cl2 p-b-30">Shipping Method</h3>
                        <div class="size-204 respon6-next">
                            <label for="courier" class="stext-102 cl2">Courier</label>
                            <div class="rs1-select2 bor8 bg0">
                                <select class="js-select2 form-control" id="courier">
                                    <option value="">Select Courier</option>
                                    <option value="jne">JNE</option>
                                    <option value="sicepat">SiCepat Express</option>
                                    <option value="pos">POS Indonesia</option>
                                </select>
                                <div class="dropDownSelect2"></div>
                            </div>
                        </div>

                        <!-- Dropdown untuk memilih layanan pengiriman berdasarkan kurir -->
                        <div class="size-204 respon6-next mt-3">
                            <label for="shipping-service" class="stext-102 cl2">Shipping Service</label>
                            <div class="rs1-select2 bor8 bg0">
                                <select class="js-select2 form-control" id="shipping-service" disabled>
                                    <option value="">Select Service</option>
                                </select>
                                <div class="dropDownSelect2"></div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>

                <!-- Kolom Kanan: Your Order dan Order Summary -->
                <div class="col-lg-5">
                    <div class="bor10 p-lr-40 p-t-30 p-b-30 m-lr-0-xl p-lr-15-sm">
                        <h4 class="mtext-109 cl2 p-b-30">Your Order</h4>
                        @foreach ($cartItems as $item)
                            <div
                                class="checkout-item d-flex align-items-center justify-content-between mb-4 p-3 shadow-sm rounded">
                                <div class="checkout-item-img" style="flex-shrink: 0; margin-right: 10px;">
                                    <img src="{{ asset('storage/products/' . $item->product->image[0]) }}"
                                        alt="{{ $item->product->title }}" class="rounded" width="100">
                                </div>
                                <div class="checkout-item-info flex-grow-1"
                                    style="flex-grow: 1; margin-left: 20px; display: flex; flex-direction: column;">
                                    <h6 class="stext-102 cl2" style="font-weight: bold; font-size: 15px;">
                                        {{ $item->product->brand }}
                                    </h6>
                                    <p class="stext-102 cl2 mb-1">{{ $item->product->title }}</p>
                                    <span class="mtext-107 cl2" style="font-size: 15px; font-weight: bold;">
                                        Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                    </span>
                                    <p class="stext-102 cl2 mb-1" style="font-size: 12px;">
                                        Variant: {{ $item->size ?? 'No Size' }}
                                    </p>
                                    <p class="stext-102 cl2" style="font-size: 12px;">
                                        x {{ $item->quantity }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="bor10 p-lr-40 p-t-30 p-b-30 m-lr-0-xl p-lr-15-sm m-t-30">
                        <div class="order-summary">
                            <h4 class="mtext-109 cl2 p-b-30">Order Summary</h4>
                            <div class="d-flex justify-content-between bor12 p-b-13 mb-3">
                                <span class="stext-110 cl2">Subtotal:</span>
                                <span class="mtext-107 cl2" id="cart-subtotal">Rp 0</span> <!-- Inisialisasi Subtotal -->
                            </div>

                            <div class="d-flex justify-content-between bor12 p-b-13 mb-3">
                                <span class="stext-110 cl2">Shipping:</span>
                                <span id="shipping-fee" class="mtext-107 cl2">Rp 0</span>
                            </div>

                            <div class="d-flex justify-content-between p-t-27 p-b-33">
                                <span class="mtext-101 cl2"><strong>Total:</strong></span>
                                <span class="mtext-110 cl2"><strong id="total-amount">Rp 0</strong></span>
                                <!-- Inisialisasi Total -->
                            </div>
                        </div>
                        <a href="#" id="checkout-btn"
                            class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                            Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Menghitung total berat produk dalam keranjang
        $(document).ready(function() {
            const origin = 17549; // ID Kota Jakarta
            const userCityId = $('#destination_id').val(); // ID Kota pengguna
            let totalWeight = 0;

            @foreach ($cartItems as $item)
                totalWeight += {{ $item->product->weight }} * {{ $item->quantity }};
            @endforeach

            // Memuat Destinasi Alamat
            $("#new-destination-name").autocomplete({
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
                    $('#new-destination-name').val(ui.item.label);
                    $('#new-destination-id').val(ui.item.data.id);
                    return false;
                }
            });

            // Mengedit Destinasi Alamat
            $("#edit-destination-name").autocomplete({
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
                    $('#edit-destination-name').val(ui.item.label);
                    $('#edit-destination-id').val(ui.item.data.id);
                    return false;
                }
            });
            // Simpan alamat baru
            $('#save-address-btn').on('click', function() {
                const name = $('#recipient-name').val();
                const phone = $('#recipient-phone').val();
                const address = $('#new-address').val();
                const destinationName = $('#new-destination-name').val();
                const destinationId = $('#new-destination-id').val();

                if (name && phone && address && destinationName && destinationId) {
                    $(this).html('Saving...').attr('disabled', true);

                    $.ajax({
                        url: '/user/update-address',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            recipient_name: name,
                            recipient_phone: phone,
                            address: address,
                            destination_name: destinationName,
                            destination_id: destinationId
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Failed to save. Please try again.');
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('An error occurred.');
                        },
                        complete: function() {
                            $('#save-address-btn').html('Save Address').attr('disabled', false);
                        }
                    });
                } else {
                    alert('Please fill in all fields.');
                }
            });

            //Mengupdate Alamat
            $('#save-edit-address-btn').on('click', function() {
                const name = $('#edit-recipient-name').val();
                const phone = $('#edit-recipient-phone').val();
                const address = $('#edit-address').val();
                const destinationName = $('#edit-destination-name').val();
                const destinationId = $('#edit-destination-id').val();

                if (name && phone && address && destinationName) {
                    $(this).html('Saving...').attr('disabled', true);

                    $.ajax({
                        url: '/user/update-address',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            recipient_name: name,
                            recipient_phone: phone,
                            address: address,
                            destination_name: destinationName,
                            destination_id: destinationId
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Failed to update. Please try again.');
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('An error occurred.');
                        },
                        complete: function() {
                            $('#save-edit-address-btn').html('Save Changes').attr('disabled',
                                false);
                        }
                    });
                } else {
                    alert('Please fill in all fields.');
                }
            });
            // Fungsi untuk mereset status tombol dan indikator loading
            function resetButtonState() {
                $('#save-address-btn').html('Save Address')
                    .removeClass('saving') // Menghapus kelas 'saving'
                    .prop('disabled', false); // Mengaktifkan tombol kembali
            }

            // Event listener untuk memilih kurir
            $('#courier').on('change', function() {
                const courier = $(this).val();

                if (courier && userCityId) {
                    $('#shipping-service').html('<option value="">Loading...</option>').prop('disabled',
                        true);

                    $.ajax({
                        url: '/api/cost',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            origin: 17549,
                            destination: userCityId,
                            weight: totalWeight,
                            courier: courier
                        },
                        success: function(data) {
                            let servicesOptions = '<option value="">Select Service</option>';

                            if (data.services && data.services.length > 0) {
                                data.services.forEach(function(service) {
                                    servicesOptions += `<option
                            value="${service.service}"
                            data-cost="${service.cost}"
                            data-etd="${service.etd}">
                            ${service.service} - Rp ${service.cost} - Est ${service.etd}
                        </option>`;
                                });
                            } else {
                                servicesOptions +=
                                    '<option value="">No shipping services available</option>';
                            }

                            $('#shipping-service').html(servicesOptions).prop('disabled',
                                false);
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                        }
                    });
                }
            });


            // Variabel untuk controller pembatalan fetch
            let controller = new AbortController();

            // Fungsi untuk mereset tombol
            function resetButtonState() {
                $('#save-edit-address-btn').html('Save Changes').attr('disabled', false);
            }

            // Fungsi untuk memuat kota berdasarkan provinsi yang dipilih
            function loadCitiesByProvince(provinceName) {
                let citySelect = document.getElementById('edit-city');

                // Reset opsi kota dan nonaktifkan sementara dropdown kota
                citySelect.innerHTML = '<option value="">Select City</option>';
                citySelect.disabled = true;

                if (provinceName) {
                    // Membatalkan request sebelumnya jika masih berlangsung
                    if (controller) {
                        controller.abort(); // Membatalkan fetch sebelumnya
                    }
                    // Membuat controller baru untuk fetch berikutnya
                    controller = new AbortController();

                    // Fetch data kota berdasarkan provinsi
                    fetch(`/user/getCitiesByProvince?province_name=${provinceName}`, {
                            signal: controller.signal // Menggunakan signal untuk mengontrol request
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Tambahkan opsi kota baru
                                data.cities.forEach(city => {
                                    let option = document.createElement('option');
                                    option.value = city.city_name;
                                    option.textContent = city.city_name;

                                    // Tentukan apakah kota yang ada pada database sudah terpilih
                                    if (city.city_name === "{{ $city ?? '' }}") {
                                        option.selected = true;
                                    }

                                    citySelect.appendChild(option);
                                });

                                // Aktifkan kembali dropdown kota setelah data berhasil dimuat
                                citySelect.disabled = false;
                            } else {
                                alert('Failed to load city');
                            }
                        })
                        .catch(error => {
                            if (error.name !== 'AbortError') { // Pastikan tidak error karena fetch dibatalkan
                                console.error('Error fetching cities:', error);
                                alert('Error fetching cities.');
                            }
                        });
                } else {
                    // Jika tidak ada provinsi yang dipilih, pastikan dropdown kota tetap nonaktif
                    citySelect.disabled = true;
                }
            }

            // Hapus alamat yang tersimpan
            $('#delete-address-btn').on('click', function() {
                if (confirm('Are you sure you want to delete this address?')) {
                    $.ajax({
                        url: '/user/delete-address',
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload(); // Refresh the page after deleting address
                            } else {
                                alert('An error occurred while deleting the address.');
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('Failed to delete address. Please try again.');
                        }
                    });
                }
            });
        });

        $(document).ready(function() {
            // Mengambil nilai subtotal dari backend
            let cartTotal = parseFloat("{{ $cartTotal }}".replace(/\./g, '').replace(',', '.'));

            // Inisialisasi Subtotal
            $('#cart-subtotal').text(formatCurrency(cartTotal));

            // Fungsi untuk menghitung dan memperbarui total
            function updateTotal(shippingCost) {
                let total = cartTotal; // Mulai dengan subtotal

                // Jika ada biaya pengiriman yang lebih dari 0, tambahkan ke total
                if (shippingCost > 0) {
                    total += shippingCost;
                }

                // Menampilkan total dengan format yang benar
                $('#total-amount').text(formatCurrency(total));
            }

            // Fungsi untuk format mata uang
            function formatCurrency(amount) {
                return "Rp " + amount.toLocaleString('id-ID');
            }

            // Ketika memilih layanan pengiriman
            $('#shipping-service').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                let shippingCost = selectedOption.data('cost'); // Ambil data-cost

                // Jika ada biaya pengiriman
                if (shippingCost) {
                    shippingCost = parseFloat(shippingCost.replace(/\./g, '').replace(',',
                        '.'));
                    $('#shipping-fee').text(formatCurrency(shippingCost));

                    // Update total dengan biaya pengiriman
                    updateTotal(shippingCost);
                } else {
                    // Jika biaya pengiriman adalah Rp 0
                    $('#shipping-fee').text("Rp 0");
                    // Update total hanya dengan subtotal (tanpa biaya pengiriman)
                    updateTotal(0); // Pengiriman Rp 0, total tetap dari subtotal
                }
            });

            // Jika pengiriman sudah di-set ke Rp 0, pastikan total tetap sesuai subtotal
            if ($('#shipping-fee').text() === 'Rp 0') {
                updateTotal(0); // Pengiriman Rp 0, hanya subtotal yang dihitung
            }
        });
        $(document).ready(function() {
            // Cek jika user sudah menambahkan alamat
            if (!{{ auth()->user()->address ? 'true' : 'false' }}) {
                $('#courier').prop('disabled', true); // Disable select if address is not added
            }
            // Misalnya, jika alamat sudah ditambahkan, enable select
            // Ini bisa terjadi setelah proses AJAX atau halaman di-refresh
            $('#add-address-btn').on('click', function() {
                // Jika user menambahkan alamat, enable select courier
                $('#courier').prop('disabled', false);
            });
        });

        // Ketika Klik Checkout
        $('#checkout-btn').on('click', function() {
            const orderId = "order-" + Math.random().toString(36).substr(2, 9); // ID Order unik
            const grossAmount = parseInt("{{ $cartTotal }}".replace(/\./g, '').replace(',',
                '.')); // Total dari keranjang
            const customerDetails = {
                first_name: "{{ auth()->user()->name }}",
                email: "{{ auth()->user()->email }}",
                phone: "{{ auth()->user()->phone }}"
            };

            // Menyusun array items dari produk
            let items = [];
            @foreach ($cartItems as $item)
                items.push({
                    id: "{{ $item->product->id }}",
                    product_brand: "{{ $item->product->brand }}",
                    product_title: "{{ $item->product->title }}",
                    product_size: "{{ $item->size }}",
                    quantity: {{ $item->quantity }},
                    price: "{{ $item->product->price }}",
                });
            @endforeach
            console.log('Items to be sent:', items);

            // Shipping details
            const selectedShippingOption = $('#shipping-service option:selected');
            let shippingCost = selectedShippingOption.data('cost');
            if (shippingCost) {
                shippingCost = parseFloat(shippingCost.replace(/\./g, '').replace(',', '.'));
            } else {
                shippingCost = 0;
            }

            const etd = selectedShippingOption.data('etd'); // Estimasi waktu pengiriman
            const courier = $('#courier').val().toUpperCase(); // Courier (e.g., JNE)
            const service = selectedShippingOption.val(); // Mendapatkan layanan (e.g., REG)

            if (!service || !shippingCost) {
                alert('Please select a shipping service.');
                return;
            }
            // Menambahkan biaya pengiriman ke total gross_amount
            const totalAmount = grossAmount + shippingCost; // Menambahkan biaya pengiriman

            // Data request untuk token Snap
            const requestData = {
                order_id: orderId,
                gross_amount: totalAmount, // Total yang sudah termasuk biaya pengiriman
                customer_details: customerDetails,
                shipping_cost: shippingCost, // Kirimkan biaya pengiriman ke backend
                courier: courier, // Kirimkan kurir
                service: service, // Kirimkan layanan pengiriman
                etd: etd, // Kirimkan estimasi waktu
                items: items, // Menambahkan items ke dalam request
                _token: '{{ csrf_token() }}'
            };

            console.log('Request data:', requestData);

            // Panggil API untuk mendapatkan Snap Token
            $.ajax({
                url: '/api/midtrans/snap', // Endpoint untuk mendapatkan Snap Token
                type: 'POST',
                data: requestData,
                success: function(response) {
                    if (response.token) {
                        console.log('Snap Token received:', response.token);

                        // Tampilkan popup pembayaran Snap
                        snap.pay(response.token, {
                            onSuccess: function(result) {
                                console.log('Payment success:', result);
                                alert('Payment successful!');

                                // Kirimkan data status pembayaran ke backend
                                $.ajax({
                                    url: '/user/createTransaction', // Ganti dengan endpoint createTransaction
                                    type: 'POST',
                                    data: {
                                        order_id: result
                                            .order_id, // ID order dari hasil transaksi
                                        payment_type: result
                                            .payment_type, // Jenis pembayaran (credit_card, ewallet, dll)
                                        transaction_status: result
                                            .transaction_status, // Status transaksi (settlement, pending, dll)
                                        transaction_id: result
                                            .transaction_id, // ID transaksi
                                        gross_amount: result
                                            .gross_amount, // Jumlah pembayaran
                                        customer_name: "{{ auth()->user()->name }}",
                                        courier: $('#courier')
                                            .val(), // Kirimkan kurir (contoh: JNE)
                                        courier_service: $(
                                                '#shipping-service option:selected')
                                            .val(), // Kirimkan layanan pengiriman (contoh: REG)
                                        snap_token: response.token,
                                        items: items,
                                        _token: '{{ csrf_token() }}' // CSRF token untuk keamanan
                                    },
                                    success: function(response) {
                                        // Redirect atau aksi lain setelah transaksi berhasil diproses di backend
                                        window.location.href =
                                            "/user/order-history"; // Redirect ke halaman checkout setelah pembayaran sukses
                                    },
                                    error: function(xhr) {
                                        console.error('Error:', xhr
                                            .responseText);
                                        alert(
                                            'Failed to update payment status.'
                                        );
                                    }
                                });
                            },
                            onPending: function(result) {
                                console.log('Payment pending:', result);
                                alert('Payment pending. Please complete the payment.');

                                // Kirimkan data status pembayaran ke backend saat pembayaran dipilih, meskipun belum selesai
                                $.ajax({
                                    url: '/user/createTransaction', // Ganti dengan endpoint createTransaction
                                    type: 'POST',
                                    data: {
                                        order_id: result
                                            .order_id, // ID order dari hasil transaksi
                                        payment_type: result
                                            .payment_type, // Jenis pembayaran (credit_card, ewallet, dll)
                                        transaction_id: result
                                            .transaction_id, // ID transaksi
                                        gross_amount: result
                                            .gross_amount, // Jumlah pembayaran
                                        customer_name: "{{ auth()->user()->name }}",
                                        courier: $('#courier')
                                            .val(), // Kirimkan kurir (contoh: JNE)
                                        courier_service: $(
                                                '#shipping-service option:selected')
                                            .val(), // Kirimkan layanan pengiriman (contoh: REG)
                                        snap_token: response.token,
                                        items: items,
                                        _token: '{{ csrf_token() }}' // CSRF token untuk keamanan
                                    },
                                    success: function(response) {
                                        console.log(
                                            'Transaction status updated as pending.'
                                        );
                                        window.location.href =
                                            "/user/order-history"; // Redirect ke halaman checkout setelah pembayaran
                                    },
                                    error: function(xhr) {
                                        console.error('Error:', xhr
                                            .responseText);
                                        alert(
                                            'Failed to update payment status.'
                                        );
                                    }
                                });
                            },
                            onError: function(result) {
                                console.error('Payment error:', result);
                                alert('Payment failed. Please try again.');
                            },
                            onClose: function() {
                                alert('Payment popup closed. Transaction cancelled.');
                            }
                        });
                    } else {
                        alert('Failed to get Snap token. Please try again.');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    alert('Failed to connect to the server. Please try again.');
                }
            });
        });
    </script>
@endpush
