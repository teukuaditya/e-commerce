@extends('layouts.main')

@section('title', 'About | DRVN')

@section('content')
<!-- Hero Section -->
<div class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-4 fw-bold mb-3">About {{ $company['name'] ?? 'DRVN' }}</h1>
                <p class="lead mb-0">{{ $company['tagline'] ?? 'Streetwear for everyday moves.' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-5">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">About</li>
        </ol>
    </nav>

    <!-- Story Section -->
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <img src="{{ asset('images/about/atelier.jpg') }}" 
                 class="img-fluid rounded-3 shadow-lg hover-scale" 
                 alt="DRVN Atelier"
                 loading="lazy">
        </div>
        <div class="col-lg-6 ps-lg-5">
            <h2 class="h3 fw-bold mb-4">Our Story</h2>
            <p class="text-muted">DRVN lahir dari kebutuhan pakaian simpel namun durable. Kami fokus pada bahan nyaman,
               potongan clean, dan detail fungsional yang menjadikan setiap piece essential dalam wardrobe harian Anda.</p>
            <hr class="my-4">
            <div class="row g-4">
                <div class="col-6">
                    <h5 class="fw-bold mb-3">Mission</h5>
                    <p class="small text-muted mb-0">Menciptakan essentials wear berkualitas dengan harga terjangkau</p>
                </div>
                <div class="col-6">
                    <h5 class="fw-bold mb-3">Vision</h5>
                    <p class="small text-muted mb-0">Menjadi brand lokal terdepan dalam kategori essential wear</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row g-4 mb-5">
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-tshirt fa-2x text-primary"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-3">Quality Materials</h4>
                    <p class="text-muted small mb-0">Riset mendalam untuk memilih bahan berkualitas yang nyaman dipakai sehari-hari</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-award fa-2x text-primary"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-3">Local Production</h4>
                    <p class="text-muted small mb-0">Diproduksi lokal dengan standar QC ketat untuk hasil terbaik</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-tags fa-2x text-primary"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-3">Fair Price</h4>
                    <p class="text-muted small mb-0">Harga transparan dan terjangkau untuk kualitas premium</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="bg-light rounded-3 p-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-4">Get in Touch</h3>
                <div class="row g-4">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Email</h5>
                        <a href="mailto:{{ $company['email'] ?? '-' }}" 
                           class="text-decoration-none d-flex align-items-center">
                            <i class="fas fa-envelope me-2 text-primary"></i>
                            {{ $company['email'] ?? '-' }}
                        </a>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Address</h5>
                        <p class="d-flex align-items-center mb-0">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                            {{ $company['address'] ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <img src="{{ asset('images/logo.png') }}" 
                     alt="DRVN Logo" 
                     class="img-fluid" 
                     style="max-width: 200px;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .breadcrumb {
        background: transparent;
        padding-left: 0;
    }
    
    .hover-scale {
        transition: transform 0.3s ease;
    }
    
    .hover-scale:hover {
        transform: scale(1.02);
    }
    
    .hover-shadow {
        transition: box-shadow 0.3s ease;
    }
    
    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    
    .feature-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: rgba(113, 127, 224, 0.1);
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endpush
