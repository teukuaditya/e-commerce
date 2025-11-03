@extends('layouts.main')

@section('title', 'Tentang | DRVN')

@section('content')
<!-- Hero Section -->
<div class="bg-light py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <h1 class="display-4 fw-bold mb-3">Tentang {{ $company['name'] ?? 'DRVN' }}</h1>
        <p class="lead mb-0">{{ $company['tagline'] ?? 'Pakaian esensial untuk kebutuhan sehari-hari' }}</p>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="container py-5">
  <!-- Story Section -->
  <div class="row align-items-center mb-5">
    <div class="col-lg-6">
      <img src="{{ asset('images/drvn-logo.png') }}"
           class="img-fluid rounded-3 shadow-lg hover-scale"
           alt="DRVN Studio"
           loading="lazy">
    </div>
    <div class="col-lg-6 ps-lg-5">
      <h2 class="h3 fw-bold mb-4">Cerita Kami</h2>
      <p class="text-muted">Drive Venture lahir dari kebutuhan pakaian simpel namun tahan lama. Kami fokus pada bahan nyaman,
        potongan bersih, dan detail fungsional yang menjadikan setiap produk esensial dalam lemari pakaian harian Anda.</p>
      <hr class="my-4">
      <div class="row g-4">
        <div class="col-6">
          <h5 class="fw-bold mb-3">Misi</h5>
          <p class="small text-muted mb-0">Menciptakan pakaian esensial berkualitas dengan harga terjangkau</p>
        </div>
        <div class="col-6">
          <h5 class="fw-bold mb-3">Visi</h5>
          <p class="small text-muted mb-0">Menjadi brand lokal terdepan dalam kategori pakaian esensial</p>
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
          <h4 class="h5 fw-bold mb-3">Bahan Berkualitas</h4>
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
          <h4 class="h5 fw-bold mb-3">Produksi Lokal</h4>
          <p class="text-muted small mb-0">Diproduksi lokal dengan standar kontrol kualitas yang ketat untuk hasil terbaik</p>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card h-100 border-0 shadow-sm hover-shadow">
        <div class="card-body p-4">
          <div class="feature-icon mb-3">
            <i class="fas fa-tags fa-2x text-primary"></i>
          </div>
          <h4 class="h5 fw-bold mb-3">Harga Terjangkau</h4>
          <p class="text-muted small mb-0">Harga transparan dan terjangkau untuk kualitas premium</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Contact Section -->
  <div class="bg-light rounded-3 p-5">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h3 class="fw-bold mb-4">Hubungi Kami</h3>
        <div class="row g-4">
          <div class="col-md-6">
            <h5 class="fw-bold mb-3">Email</h5>
            <a href="mailto:{{ $company['email'] ?? '-' }}" class="text-decoration-none d-flex align-items-center">
              <i class="fas fa-envelope me-3 text-dark"></i>
              <span>{{ $company['email'] ?? '-' }}</span>
            </a>
          </div>
          <div class="col-md-6">
            <h5 class="fw-bold mb-3">Alamat</h5>
            <p class="d-flex align-items-center mb-0">
              <i class="fas fa-map-marker-alt me-3 text-dark mb-4"></i>
              <span>{{ $company['address'] ?? '-' }}</span>
            </p>
          </div>
        </div>
      </div>
      <div class="col-lg-4 text-center">
        <img src="{{ asset('images/drvn-logo.png') }}" alt="Logo DRVN" class="img-fluid" style="max-width: 200px;">
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<!-- Google Fonts: Poppins -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
  :root{
    --ff-sans: "Poppins", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  }

  /* Terapkan Poppins ke seluruh halaman */
  html, body{ font-family: var(--ff-sans) !important; }

  /* Paksa komponen umum ikut Poppins */
  h1,h2,h3,h4,h5,h6,
  .display-1,.display-2,.display-3,.display-4,
  .lead, p, a, span, small,
  .btn, .nav-link, .dropdown-item,
  .card, .modal, .alert,
  input, textarea, select, .form-control, .form-select{
    font-family: var(--ff-sans) !important;
  }

  /* Utilitas halaman */
  .breadcrumb{ background: transparent; padding-left: 0; }
  .hover-scale{ transition: transform .3s ease; }
  .hover-scale:hover{ transform: scale(1.02); }
  .hover-shadow{ transition: box-shadow .3s ease; }
  .hover-shadow:hover{ box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
  .feature-icon{
    width:50px;height:50px;display:flex;align-items:center;justify-content:center;
    border-radius:50%;background-color:rgba(113,127,224,.1);
  }
  .bg-light{ background-color:#f8f9fa!important; }
</style>
@endpush
