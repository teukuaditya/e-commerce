@extends('layouts.main') <!-- Sesuaikan dengan layout Anda -->

@section('content')
    <!-- breadcrumb -->
    <div class="container">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
            <a href="{{ route('user.products.index') }}" class="stext-109 cl8 hov-cl1 trans-04">
                Product
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>

            <span class="stext-109 cl4">
                Add To Cart
            </span>
        </div>
    </div>
    <!-- Product Detail -->
    <section class="sec-product-detail bg0 p-t-65 p-b-60">
        <div class="container">
            <div class="row">
                <!-- Product Image Section -->
                <div class="col-md-6 col-lg-7 p-b-30">
                    <div class="p-l-25 p-r-30 p-lr-0-lg">
                        <div class="wrap-slick3 flex-sb flex-w">
                            <div class="wrap-slick3-dots"></div>
                            <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

                            <div class="slick3 gallery-lb">
                                @foreach ($product->image as $image)
                                    <div class="item-slick3" data-thumb="{{ asset('storage/products/' . $image) }}">
                                        <div class="wrap-pic-w pos-relative">
                                            <img src="{{ asset('storage/products/' . $image) }}"
                                                alt="{{ $product->title }}">
                                            <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                                href="{{ asset('storage/products/' . $image) }}">
                                                <i class="fa fa-expand"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Info Section -->
                <div class="col-md-6 col-lg-5 p-b-30">
                    <div class="p-r-50 p-t-5 p-lr-0-lg">
                        <!-- Brand -->
                        <span class="mtext-105 cl2 js-name-detail p-b-14">
                            {{ $product->brand }}
                        </span>

                        <h4 class="stext-102 cl3">
                            {{ $product->title }}
                        </h4>

                        <span class="mtext-106 cl2">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>

                        <form action="{{ route('user.cart.add', $product->id) }}" method="POST" id="cart-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <!-- Size Selection -->
                            <div class="p-t-33" id="size-selection">
                                <div class="flex-w flex-r-m p-b-10">
                                    <div class="size-203 flex-c-m respon6">
                                        Size
                                    </div>

                                    <div class="size-204 respon6-next">
                                        <div class="rs1-select2 bor8 bg0">
                                            <select class="js-select2" name="size" id="size"
                                                onfocusout="updateSize()">
                                                @if ($product->size && is_array($product->size))
                                                    @foreach ($product->size as $size)
                                                        <option value="{{ trim($size) }}">{{ trim($size) }}</option>
                                                    @endforeach
                                                @else
                                                    <option>No sizes available</option>
                                                @endif
                                            </select>
                                            <div class="dropDownSelect2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Stock Display -->
                            <div class="flex-w flex-r-m p-b-10">
                                <div class="size-203 flex-c-m respon6">
                                    Stock
                                </div>
                                <div class="size-204 respon6-next">
                                    <div class="stock-display" style="padding-left: 20px;">
                                        <span id="stock">{{ $product->stock }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Quantity and Add to Cart -->
                            <div class="flex-w flex-r-m p-b-10">
                                <div class="size-204 flex-w flex-m respon6-next">
                                    <!-- Wrap both input quantity and button into a flex container -->
                                    <div class="d-flex align-items-center">
                                        <!-- Quantity input -->
                                        <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                                            <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m"
                                                onclick="decreaseQuantity()">
                                                <i class="fs-16 zmdi zmdi-minus"></i>
                                            </div>

                                            <input class="mtext-104 cl3 txt-center num-product" type="number"
                                                name="quantity" id="quantity" value="1" min="1"
                                                onchange="updateQuantity()">

                                            <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m"
                                                onclick="increaseQuantity()">
                                                <i class="fs-16 zmdi zmdi-plus"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add to Cart Button -->
                                    <button type="submit"
                                        class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 mt-2">
                                        Add to cart
                                    </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Box Deskripsi Produk (Ditempatkan setelah tombol Add to Cart) -->
            <div class="bor10 p-t-20 p-b-20 p-l-20 p-r-20 bg-light mt-2">
                <h5 class="mtext-101 cl2 p-b-10 bor12">Description</h5>
                <p class="stext-107 cl6 mt-2">
                    {!! nl2br(e($product->description)) !!}
                </p>
            </div>

            {{-- ===================== REVIEWS ===================== --}}
            <div class="pdp-reviews bor10 p-t-25 p-b-25 p-l-20 p-r-20 mt-3 bg-white">
                <div class="row">
                    {{-- Ringkasan rating --}}
                    <div class="col-md-4 p-r-20 p-r-0-lg">
                        <div class="pdp-rsummary bg0 p-all-20 bor10">
                            <div class="pdp-rsummary__avg mtext-106 cl2">
                                {{ $rating['avg'] !== null ? number_format($rating['avg'], 1) : '–' }}
                                <small class="cl6 stext-103">/ 5</small>
                            </div>
                            <div class="pdp-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa fa-star{{ ($rating['avg'] !== null && $i <= round($rating['avg'])) ? ' pdp-star--on' : '' }}"></i>
                                @endfor
                            </div>
                            <p class="stext-107 cl6 p-t-8">
                                {{ $rating['count'] }} {{ Str::plural('review', $rating['count']) }}
                            </p>
                        </div>
                    </div>

                    {{-- Daftar review --}}
                    <div class="col-md-8">
                        <h5 class="mtext-101 cl2 p-b-15">Ulasan Pembeli</h5>

                        @forelse ($product->reviews->sortByDesc('created_at') as $rv)
                            <div class="pdp-review bor8 p-all-15 mb-3">
                                <div class="pdp-widgets flex-w flex-sb-m">
                                    <div>
                                        <strong class="stext-107 cl2">{{ $rv->user->name ?? 'Pembeli' }}</strong>
                                        <span class="stext-107 cl6 p-l-10">{{ $rv->created_at->translatedFormat('d M Y') }}</span>
                                    </div>
                                    <div class="pdp-stars pdp-stars--sm">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star{{ $i <= $rv->rating ? ' pdp-star--on' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="stext-107 cl6 p-t-10">{{ $rv->comment }}</p>

                                @if (auth()->check() && auth()->id() === $rv->user_id)
                                    <div class="p-t-10">
                                        <small class="stext-101 cl6">
                                            <button type="button" class="btn btn-link btn-sm pl-0 js-edit-review">Edit review saya</button>
                                            <form action="{{ route('user.reviews.destroy', $rv->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-sm text-danger" onclick="return confirm('Hapus review Anda?')">Hapus</button>
                                            </form>
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="stext-107 cl6">
                                Belum ada review. Jadilah yang pertama memberi ulasan!
                            </p>
                        @endforelse

                        {{-- Form review (auth) --}}
                        @if (auth()->check())
                            <div class="pdp-form bor8 p-all-20 mt-3" id="reviewForm">
                                <h6 class="stext-108 cl2 m-b-10">
                                    {{ $myReview ? 'Edit review Anda' : 'Tulis review Anda' }}
                                </h6>
                                <form action="{{ route('user.reviews.store', $product->id) }}" method="POST">
                                    @csrf
                                    @if (session('success'))
                                        <div class="alert alert-success py-2 px-3 small">{{ session('success') }}</div>
                                    @endif
                                    @if (session('info'))
                                        <div class="alert alert-info py-2 px-3 small">{{ session('info') }}</div>
                                    @endif

                                    <div class="flex-w mb-2">
                                        <label class="stext-107 cl2 p-r-12">Rating:</label>
                                        <div id="starPick" class="pdp-stars pdp-stars--pick">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa fa-star {{ $myReview && $i <= $myReview->rating ? 'pdp-star--on' : '' }}"
                                                   data-value="{{ $i }}"></i>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" id="ratingInput"
                                               value="{{ $myReview ? $myReview->rating : '5' }}" required>
                                    </div>

                                    <textarea name="comment" class="w-full stext-107 cl6 bor8 p-all-15 pdp-textarea"
                                              rows="3" required
                                              placeholder="Bagaimana kualitas produknya?">{{ $myReview ? $myReview->comment : old('comment') }}</textarea>
                                    @error('comment') <div class="stext-107 cl-g small m-t-5">{{ $message }}</div> @enderror

                                    <button type="submit" class="flex-c-m stext-101 cl0 size-105 bg1 bor1 hov-btn1 p-lr-15 m-t-10">
                                        {{ $myReview ? 'Perbarui Review' : 'Kirim Review' }}
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bor8 bg-light p-all-15 mt-3 stext-107 cl6">
                                <a href="{{ route('login') }}" class="stext-107 cl2">Login</a>
                                untuk menulis review.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        </div>
        </div>
    </section>
@endsection


@push('styles')
<style>
    /* ==== Review (scoped ke elemen .pdp-*) ==== */
    .pdp-stars .fa-star { color: #d8d8dc; margin-right: 3px; transition: color .15s; }
    .pdp-stars .pdp-star--on { color: #f8b64b; }

    .pdp-rsummary__avg { font-size: 34px; line-height: 1; }

    .pdp-review { background: #fafafb; }
    .pdp-stars--sm .fa-star { font-size: 12px; }

    .pdp-stars--pick .fa-star { cursor: pointer; font-size: 22px; margin-right: 6px; }
    .pdp-textarea { resize: vertical; }

    /* Hover preview bintang di form */
    .pdp-stars--pick .fa-star:hover { color: #f8b64b; }

    /* Perlihatkan 5 bintang terpilih saat hover — minimal: mereka tetap merah muda */
    a.text-primary { text-decoration: none; }
</style>
@endpush

@push('scripts')
    <script>
        // Update the size input when the user selects a size and blur the select
        function updateSize() {
            const size = document.getElementById('size').value;
            document.getElementById('selected-size').value = size;

            // Menutup dropdown setelah kehilangan fokus
            document.getElementById('size').blur();
        }

        // Update the quantity input when the user changes the quantity manually
        function updateQuantity() {
            const quantity = document.getElementById('quantity').value;
            // No need for a hidden field, since the quantity is now directly part of the form
        }

        // Function to increase quantity
        function increaseQuantity() {
            let quantityInput = document.getElementById('quantity');
            let currentQuantity = parseInt(quantityInput.value);

            // Ambil stok dari elemen HTML yang mengandung ID 'stock'
            let stock = parseInt(document.getElementById('stock').innerText);

            // Tambahkan kuantitas jika masih dalam batas stok
            if (currentQuantity < stock) {
                quantityInput.value = currentQuantity + 0; // Increase quantity by 1
            } else {
                alert(
                    'Jumlah kuantitas tidak bisa melebihi stok yang tersedia.'
                ); // Hanya menampilkan alert jika sudah mencapai stok
            }

            updateQuantity(); // Update quantity display or perform other logic
        }

        // Function to decrease quantity
        function decreaseQuantity() {
            let quantityInput = document.getElementById('quantity');
            let currentQuantity = parseInt(quantityInput.value);

            // Pastikan kuantitas tidak kurang dari 1
            if (currentQuantity > 1) {
                quantityInput.value = currentQuantity - 0; // Decrease quantity by 1
            } else {
                alert('Kuantitas tidak bisa kurang dari 1.'); // Tidak bisa kurang dari 1
            }

            updateQuantity(); // Update quantity display or perform other logic
        }

        /* ===================== PICK RATING BINTANG ===================== */
        document.addEventListener('DOMContentLoaded', function () {
            var pick = document.getElementById('starPick');
            var input = document.getElementById('ratingInput');
            if (!pick || !input) return;

            var stars = pick.querySelectorAll('.fa-star');
            var selected = parseInt(input.value) || 0;

            function paint(count) {
                stars.forEach(function (s, idx) {
                    s.classList.toggle('pdp-star--on', idx < count);
                });
            }

            paint(selected);

            stars.forEach(function (star) {
                star.addEventListener('mouseenter', function () {
                    paint(parseInt(star.dataset.value));
                });
                star.addEventListener('mouseleave', function () {
                    paint(selected);
                });
                star.addEventListener('click', function () {
                    selected = parseInt(star.dataset.value);
                    input.value = selected;
                    paint(selected);
                });
            });

            var editBtn = document.querySelector('.js-edit-review');
            if (editBtn) {
                editBtn.addEventListener('click', function () {
                    var form = document.getElementById('reviewForm');
                    if (form) {
                        form.scrollIntoView({ behavior: 'smooth' });
                        var ta = form.querySelector('textarea');
                        if (ta) ta.focus();
                    }
                });
            }
        });
    </script>
@endpush
