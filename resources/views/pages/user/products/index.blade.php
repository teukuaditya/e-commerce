@extends('layouts.main')

@section('content')
<div class="bg0 m-t-23 p-b-140">
    <div class="container">
        <div class="flex-w flex-sb-m p-b-52">
            <!-- Tombol Filter Kategori -->
            <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1" data-filter="*">
                    All Products
                </button>
                @foreach ($categories as $category)
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5"
                    data-filter=".{{ strtolower($category->category_name) }}">
                    {{ $category->category_name }}
                </button>
                @endforeach
            </div>

            <!-- Tombol Filter dan Search -->
            <div class="flex-w flex-c-m m-tb-10">
                <form action="{{ route('products.search') }}" method="GET" class="flex-w m-tb-4">
                    <input
                        class="plh3 stext-106 cl6 size-105 bor4 p-l-20 p-r-20 m-r-8"
                        type="text"
                        name="query"
                        placeholder="Search products..."
                        value="{{ request('query') }}"
                        autocomplete="off">
                    <button class="flex-c-m stext-106 cl6 size-105 bor4 hov-btn3 trans-04" type="submit">
                        <i class="icon-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
                        Search
                    </button>
                </form>
            </div>
        </div>

        <!-- Produk -->
        <div class="row isotope-grid">
            @foreach ($products as $product)
            <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item {{ strtolower($product->category->category_name) }}">
                <!-- Block2 -->
                <div class="block2">
                    <div class="block2-pic hov-img0">
                        <!-- Gambar Produk, klik gambar ini akan mengarahkan ke halaman detail -->
                        <a href="{{ route('user.products.show', $product->id) }}">
                            <img src="{{ asset('storage/products/' . (is_array($product->image) ? $product->image[0] : $product->image)) }}" alt="{{ $product->title }}">
                        </a>

                        <!-- Tombol Quick View -->
                        <a href="{{ route('user.products.show', $product->id) }}" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                            Add To Cart
                        </a>
                    </div>

                    <div class="block2-txt flex-w flex-t p-t-14">
                        <div class="block2-txt-child1 flex-col-l">
                            <!-- Klik Brand untuk masuk ke halaman detail produk -->
                            <a href="{{ route('user.products.show', $product->id) }}" class="stext-106 cl5 hov-cl1 trans-04 m-b-3" style="font-weight: bold;">
                                {{ $product->brand }}
                            </a>

                            <!-- Nama produk, klik nama ini akan mengarahkan ke halaman detail -->
                            <a href="{{ route('user.products.show', $product->id) }}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                {{ $product->title }}
                            </a>

                            <!-- Klik Harga untuk masuk ke halaman detail produk -->
                            <a href="{{ route('user.products.show', $product->id) }}" class="stext-105 cl5 hov-cl1 trans-04">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </a>
                        </div>

                        <div class="block2-txt-child2 flex-r p-t-3">
                            <a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
                                <img class="icon-heart1 dis-block trans-04" src="{{ asset('images/icons/icon-heart-01.png') }}" alt="ICON">
                                <img class="icon-heart2 dis-block trans-04 ab-t-l" src="{{ asset('images/icons/icon-heart-02.png') }}" alt="ICON">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Mengatur filter berdasarkan kategori
        $('.filter-tope-group button').click(function() {
            var filterValue = $(this).attr('data-filter');

            // Menambahkan kelas aktif ke tombol yang dipilih
            $('.filter-tope-group button').removeClass('how-active1');
            $(this).addClass('how-active1');

            // Menampilkan produk yang sesuai dengan kategori yang dipilih
            $('.isotope-grid').isotope({
                filter: filterValue
            });
        });
    });
</script>
@endpush