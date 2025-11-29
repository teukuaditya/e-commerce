@extends('layouts.main')

@section('content')
<div class="bg0 p-t-80 p-b-50 category-hero">
    <h3 class="ltext-103 cl5 mb-4 text-center">
                CATEGORIES
            </h3>  
    <div class="container">
        <div class="row">
            @foreach($categories as $category)
            <div class="col-md-6 col-xl-4 p-b-30 m-lr-auto">
                <div class="block1 wrap-pic-w">
                    <img src="{{ asset('storage/categories/' . $category->image) }}" alt="{{ $category->category_name }}">

                    <a href="{{ route('user.categories.products', $category->id) }}" class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
                        <div class="block1-txt-child1 flex-col-l">
                            <span class="block1-name ltext-102 trans-04 p-b-8">
                                {{ $category->category_name }}
                            </span>

                            <span class="block1-info stext-102 trans-04">
                                {{ $category->description }}
                            </span>
                        </div>

                        <div class="block1-txt-child2 p-b-4 trans-05">
                            <div class="block1-link stext-101 cl0 trans-09">
                                Explore
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
    /* Supaya hero About tidak ketimpa navbar */
    .category-hero {
        margin-top: 100px;
        /* coba 80–120px, silakan disesuaikan */
    }

    @media (max-width: 991.98px) {

        /* Di mobile biasanya header lebih kecil, jadi jaraknya bisa dikurangi */
        .category-hero {
            margin-top: 70px;
        }
    }
</style>
@endpush