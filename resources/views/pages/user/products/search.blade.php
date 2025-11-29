@extends('layouts.main')

@section('content')
<div class="container search-hero">
    <!-- Breadcrumb -->
    <div class="bread-crumb flex-w p-r-15 p-t-30 p-lr-0-lg">
        <a href="{{ route('user.home') }}" class="stext-109 cl8 hov-cl1 trans-04">
            Home
            <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
        </a>
        <span class="stext-109 cl4">
            Search Results
        </span>
    </div>

    <!-- Search Info -->
    <div class="p-t-30 p-b-30">
        <h4 class="mtext-109 cl2">
            Search Results for: "{{ $query }}"
        </h4>
        <p class="stext-102 cl3 p-t-10">
            Found {{ $total }} {{ Str::plural('product', $total) }}
        </p>
    </div>

    <!-- Products -->
    @if($products->count() > 0)
    <div class="row isotope-grid">
        @foreach($products as $product)
        <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item">
            <!-- Product Card -->
            <div class="block2">
                <div class="block2-pic hov-img0">
                    <img src="{{ asset('storage/products/' . $product->image[0]) }}" alt="{{ $product->title }}">

                    <a href="{{ route('user.products.show', $product->id) }}" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                        Quick View
                    </a>
                </div>

                <div class="block2-txt flex-w flex-t p-t-14">
                    <div class="block2-txt-child1 flex-col-l">
                        <a href="{{ route('user.products.show', $product->id) }}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                            <span class="mtext-100 cl2 js-name-detail p-b-5" style="display:block; font-weight: bold;">
                                {{ $product->brand }}
                            </span>
                            {{ $product->title }}
                        </a>
                        <span class="stext-105 cl3">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
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

    <!-- Pagination -->
    <div class="flex-c-m flex-w w-full p-t-45">
        {{ $products->appends(['query' => $query])->links() }}
    </div>
    @else
    <!-- No Results -->
    <div class="flex-c-m p-t-50 p-b-100">
        <div class="text-center">
            <i class="zmdi zmdi-search" style="font-size: 80px; color: #ccc;"></i>
            <h4 class="mtext-109 cl2 p-t-20 p-b-10">No Products Found</h4>
            <p class="stext-102 cl3">
                Sorry, we couldn't find any products matching "{{ $query }}"
            </p>
            <a href="{{ route('user.home') }}" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-t-30">
                Back to Home
            </a>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* Supaya hero About tidak ketimpa navbar */
    .search-hero {
        margin-top: 100px;
        /* coba 80–120px, silakan disesuaikan */
    }

    @media (max-width: 991.98px) {

        /* Di mobile biasanya header lebih kecil, jadi jaraknya bisa dikurangi */
        .search-hero {
            margin-top: 70px;
        }
    }
</style>
@endpush