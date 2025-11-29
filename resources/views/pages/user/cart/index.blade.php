@extends('layouts.main')

@section('content')
    <!-- breadcrumb -->
    <div class="container cart-hero">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
            <a href="{{ route('user.products.index') }}" class="stext-109 cl8 hov-cl1 trans-04">
                Product
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>

            <span class="stext-109 cl4">
                Cart
            </span>
        </div>
    </div>
    <section class="bg0 m-t-45 p-b-60">
        <div class="container">
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8 m-b-50">
                    <div class="bor10 p-lr-40 p-t-30 p-b-30 m-lr-0-xl p-lr-15-sm">
                        <!-- Select All Checkbox & Remove Selected Button -->
                        <div class="d-flex align-items-center justify-content-between bor12 p-b-20 mb-3">
                            <div class="d-flex align-items-center">
                                <input type="checkbox"
                                    style="cursor: pointer; accent-color: #717fe0; transform: scale(1.2);" id="select-all"
                                    class="mr-2" style="transform: scale(1.2);">
                                <label for="select-all" class="stext-102 cl2 m-0">Select All</label>
                            </div>
                            <button class="stext-102 cl2 hov-cl2" id="remove-selected">Remove</button>
                        </div>
                        <style>
                            @media (max-width: 768px) {
                                .cart-item {
                                    display: flex;
                                    flex-wrap: wrap;
                                    margin-bottom: 20px;
                                }

                                /* Gambar Produk */
                                .cart-item-img {
                                    width: 1000px; /* Ukuran gambar */
                                    flex-shrink: 0; /* Gambar tidak akan mengecil */
                                    margin: 0; /* Menghilangkan jarak antara gambar dan info produk */
                                    order: 1; /* Gambar tetap di posisi pertama */
                                }

                                /* Informasi Produk */
                                .cart-item-info {
                                    flex: 1 1 100%; /* Ambil ruang yang tersisa dan pastikan berada di bawah gambar */
                                    margin-bottom: 10px;
                                    order: 2; /* Info produk berada setelah gambar */
                                }

                                .cart-item-info h3 {
                                    margin: 0;
                                    font-weight: bold;
                                }

                                .cart-item-info p {
                                    white-space: wrap; /* Membuat teks tetap dalam satu baris */
                                    overflow: hidden; /* Sembunyikan teks yang melebihi batas */
                                }

                                /* Quantity Selector & Tombol Hapus */
                                .cart-item-actions {
                                    display: flex;
                                    flex-direction: column; /* Menyusun elemen secara vertikal */
                                    order: 3; /* Pastikan actions (quantity dan tombol hapus) berada di bawah informasi produk */
                                    width: 100%;
                                }

                                .cart-item-quantity {
                                    display: flex;
                                    align-items: center;
                                    margin-bottom: 10px; /* Jarak antara quantity dan tombol hapus */
                                    margin-left: 15px;
                                    order: 3;
                                }

                                .cart-item-quantity input.num-product {
                                    width: 50px;
                                    text-align: center;
                                    border: 1px solid #ccc;
                                    margin: 0 5px;
                                }

                                .btn-num-product-down,
                                .btn-num-product-up {
                                    border: 1px solid #ccc;
                                    background-color: #f9f9f9;
                                    padding: 4px;
                                    cursor: pointer;
                                }

                                /* Tombol Hapus */
                                .cart-item-remove {
                                    width: 1000px;
                                    color: red;
                                    cursor: pointer;
                                    font-size: 18px;
                                    margin-left: 10px;
                                    order: 3;
                                }
                            }
                        </style>


                        <!-- Cart Items -->
                        <div class="cart-items">
                            @foreach ($cartItems->sortByDesc('created_at') as $item)
                                <div
                                    class="cart-item d-flex align-items-center justify-content-between mb-4 p-3 shadow-sm rounded">
                                    <!-- Checkbox -->
                                    <div class="cart-item-checkbox">
                                        <input type="checkbox"
                                            style="cursor: pointer; accent-color: #717fe0; transform: scale(1.2);"
                                            class="item-checkbox" value="{{ $item->id }}"
                                            data-price="{{ $item->product->price }}" style="transform: scale(1.2);">
                                    </div>
                                    <!-- Product Image -->
                                    <div class="cart-item-img"
                                        style="flex-shrink: 0; margin-right: 15px; margin-left: 30px;">
                                        <img src="{{ asset('storage/products/' . $item->product->image[0]) }}"
                                            alt="{{ $item->product->title }}" class="rounded" width="100">
                                    </div>
                                    <!-- Product Info -->
                                    <div class="cart-item-info flex-grow-1 ml-3" style="flex-grow: 1; margin-left: 0;">
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
                                    </div>

                                    <!-- Quantity Selector -->
                                    <div class="cart-item-quantity d-flex align-items-center">
                                        <div class="btn-num-product-down cl8 hov-btn3 trans-02 flex-c-m">
                                            <i class="fs-16 zmdi zmdi-minus"></i>
                                        </div>
                                        <input class="mtext-104 cl3 txt-center num-product" type="number" name="quantity"
                                            data-item-id="{{ $item->id }}" value="{{ $item->quantity }}" min="1"
                                            max="{{ $item->product->stock }}">
                                        <div class="btn-num-product-up cl8 hov-btn3 trans-02 flex-c-m">
                                            <i class="fs-16 zmdi zmdi-plus"></i>
                                        </div>
                                    </div>
                                    <!-- Remove Button -->
                                    <div class="cart-item-remove"> 
                                        <form action="{{ route('user.cart.remove', $item->id) }}" method="POST"
                                            onsubmit="return confirmDelete(event)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                style="background-color: transparent; border: none; color: #dc3545; padding: 5px 10px; cursor: pointer; transition: all 0.3s ease; border-radius: 5px;"
                                                onmouseover="this.style.backgroundColor='#dc3545'; this.style.color='white';"
                                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#dc3545';">
                                                <i class="fa fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Cart Totals -->
                <div class="col-lg-4 m-b-50">
                    <div class="bor10 p-lr-40 p-t-30 p-b-40 m-lr-0-xl p-lr-15-sm">
                        <h4 class="mtext-109 cl2 p-b-30">Cart Totals</h4>

                        <!-- Selected Item Count -->
                        <div class="d-flex justify-content-between bor12 p-b-13 mb-3">
                            <span class="stext-110 cl2">Selected :</span>
                            <span class="mtext-107 cl2" id="selected-items-count">0</span>
                        </div>

                        <!-- Subtotal -->
                        <div class="d-flex justify-content-between bor12 p-b-13 mb-3">
                            <span class="stext-110 cl2">Subtotal :</span>
                            <span class="mtext-107 cl2" id="cart-subtotal">Rp 0</span>
                        </div>

                        <!-- Total -->
                        <div class="d-flex justify-content-between p-t-27 p-b-33">
                            <span class="mtext-101 cl2"><strong>Total :</strong></span>
                            <span class="mtext-110 cl2" id="cart-total" style="font-weight: bold;">Rp 0</span>
                        </div>

                        <!-- Tombol Proceed to Checkout -->
                        <form action="{{ route('user.checkout.index') }}" method="GET" id="checkout-form">
                            @csrf
                            <input type="hidden" name="cartItems" id="cart-items-data">
                            <input type="hidden" name="cartTotal" id="cart-total-data">
                            <button type="submit"
                                class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                                Proceed to Checkout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAllCheckbox = document.getElementById('select-all');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const selectedItemsCount = document.getElementById('selected-items-count');
            const cartSubtotal = document.getElementById('cart-subtotal');
            const cartTotal = document.getElementById('cart-total');
            const removeSelectedButton = document.getElementById('remove-selected');
            const checkoutForm = document.getElementById('checkout-form');
            const cartItemsDataInput = document.getElementById('cart-items-data');
            const cartTotalDataInput = document.getElementById('cart-total-data');

            let selectedItems = [];

            // Fungsi untuk menghitung total harga dan jumlah barang yang dipilih
            const updateTotals = () => {
                let totalPrice = 0;
                let selectedQuantity = 0;
                let selectedItems = [];

                itemCheckboxes.forEach(checkbox => {
                    // Cek jika checkbox dicentang
                    if (checkbox.checked) {
                        const quantityInput = document.querySelector(
                            `input[name="quantity"][data-item-id="${checkbox.value}"]`);
                        const quantity = parseInt(quantityInput.value, 10);
                        const itemPrice = parseFloat(checkbox.dataset.price);
                        totalPrice += itemPrice * quantity;
                        selectedQuantity += quantity;
                        selectedItems.push(checkbox.value); // Tambahkan ID produk ke array
                    }
                });

                // Update jumlah selected items dan subtotal
                selectedItemsCount.textContent = selectedQuantity;
                cartSubtotal.textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;

                // Total = hanya Subtotal (tidak ada biaya pengiriman)
                cartTotal.textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;

                // Update nilai input hidden yang dikirim ke checkout form
                cartItemsDataInput.value = selectedItems.join(',');
                cartTotalDataInput.value = totalPrice;
            };

            // Event untuk mengubah status semua checkbox berdasarkan *select all*
            selectAllCheckbox.addEventListener('change', () => {
                const isChecked = selectAllCheckbox.checked;
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateTotals();
            });

            // Event untuk memperbarui status *select all* jika ada perubahan di item checkbox
            itemCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateTotals);
            });

            // Event untuk form checkout saat disubmit
            checkoutForm.addEventListener('submit', (event) => {
                // Cek apakah ada checkbox yang dicentang
                const isAnyItemChecked = Array.from(itemCheckboxes).some(checkbox => checkbox.checked);

                if (!isAnyItemChecked) {
                    event.preventDefault(); // Batalkan pengiriman form
                    alert('Please select at least one item to proceed with checkout.');
                }
            });

            // Event untuk mengupdate kuantitas menggunakan input manual
            document.querySelectorAll('.num-product').forEach(input => {
                input.addEventListener('change', (event) => {
                    const itemId = event.target.getAttribute('data-item-id');
                    let newQuantity = parseInt(event.target.value, 10);

                    // Validasi input kuantitas agar tidak kurang dari 1
                    if (newQuantity < 1 || isNaN(newQuantity)) {
                        newQuantity = 1;
                    }

                    // Kirim data ke server untuk memperbarui kuantitas
                    fetch(`{{ route('user.cart.update', ':cartId') }}`.replace(':cartId',
                            itemId), {
                            method: 'PUT', // Pastikan menggunakan PUT
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                quantity: newQuantity
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update Cart Totals setelah perubahan kuantitas
                                updateTotals();
                            } else {
                                alert('Gagal memperbarui kuantitas');
                            }
                        })
                        .catch(() => alert('Terjadi kesalahan dalam memperbarui kuantitas'));
                });
            });

            // Event untuk mengubah kuantitas dengan tombol plus/minus
            document.querySelectorAll('.btn-num-product-up, .btn-num-product-down').forEach(button => {
                button.addEventListener('click', (event) => {
                    const quantityInput = event.target.closest('.cart-item-quantity').querySelector(
                        '.num-product');
                    let currentQuantity = parseInt(quantityInput.value, 10);

                    // Tentukan perubahan kuantitas (+1 atau -1)
                    if (event.target.closest('.btn-num-product-up')) {
                        currentQuantity += 0; // Tambah 1
                    } else if (event.target.closest('.btn-num-product-down')) {
                        currentQuantity -= 0; // Kurangi 1
                    }

                    // Validasi kuantitas agar tidak melebihi stok dan tidak kurang dari 1
                    const maxQuantity = parseInt(quantityInput.max);
                    const minQuantity = parseInt(quantityInput.min);
                    if (currentQuantity < minQuantity) {
                        currentQuantity = minQuantity; // Jangan kurang dari minQuantity
                    }
                    if (currentQuantity > maxQuantity) {
                        currentQuantity = maxQuantity; // Jangan lebih dari maxQuantity
                    }

                    // Update nilai input kuantitas
                    quantityInput.value = currentQuantity;

                    // Kirim data ke server untuk memperbarui kuantitas
                    const itemId = quantityInput.getAttribute('data-item-id');
                    fetch(`{{ route('user.cart.update', ':cartId') }}`.replace(':cartId',
                            itemId), {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                quantity: currentQuantity
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update Cart Totals setelah perubahan kuantitas
                                updateTotals();
                            } else {
                                alert('Gagal memperbarui kuantitas');
                            }
                        })
                        .catch(() => alert('Terjadi kesalahan dalam memperbarui kuantitas'));
                });
            });

            // Event untuk menghapus item yang dipilih
            removeSelectedButton.addEventListener('click', () => {
                const selectedIds = Array.from(itemCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

                if (selectedIds.length === 0) {
                    alert('No items selected to remove.');
                    return;
                }

                if (confirm('Are you sure you want to remove the selected items?')) {
                    fetch('{{ route('user.cart.index') }}/bulk-remove', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                ids: selectedIds
                            })
                        })
                        .then(response => {
                            if (response.ok) {
                                location.reload();
                            } else {
                                alert('Failed to remove items.');
                            }
                        })
                        .catch(() => alert('Failed to remove items.'));
                }
            });

            // Pastikan cart total di-update saat halaman dimuat
            updateTotals();
        });

        function confirmDelete(event) {
            if (!confirm('Are you sure you want to remove the items?')) {
                event.preventDefault(); // Batalkan form submit jika tidak setuju
            }
        }
    </script>
@endpush

@push('styles')
<style>
  /* Supaya hero About tidak ketimpa navbar */
  .cart-hero {
    margin-top: 120px;
    /* coba 80–120px, silakan disesuaikan */
  }

  @media (max-width: 991.98px) {

    /* Di mobile biasanya header lebih kecil, jadi jaraknya bisa dikurangi */
    .product-hero {
      margin-top: 70px;
    }
  }
</style>
@endpush
