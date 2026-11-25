@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1 font-clash" style="font-weight:800;color:#0F172A;">System Settings</h1>
            <p class="mb-0 text-muted" style="font-size:.9rem;">Kelola pengaturan toko. Konfigurasi sensitif (API key, secret, password) tetap dikelola lewat <code>.env</code>.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm" style="border-radius:12px;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm" style="border-radius:12px;">
            <ul class="mb-0 pl-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-body p-4">
                        <h5 class="mb-3 font-clash" style="font-weight:700;"><i class="fas fa-store text-primary mr-2"></i>General Store</h5>
                        @foreach(['store_name','store_tagline','store_email','store_phone','store_whatsapp','store_address','operating_hours'] as $key)
                            @php $meta = $defaults[$key]; @endphp
                            <div class="form-group mb-3">
                                <label style="font-size:13px;font-weight:600;color:#334155;">{{ $meta['label'] }}</label>
                                @if($meta['type'] === 'textarea')
                                    <textarea name="{{ $key }}" class="form-control" rows="3" style="border-radius:10px;">{{ old($key, $settings[$key]) }}</textarea>
                                @else
                                    <input type="{{ $key === 'store_email' ? 'email' : 'text' }}" name="{{ $key }}" class="form-control" value="{{ old($key, $settings[$key]) }}" style="height:42px;border-radius:10px;">
                                @endif
                                <small class="text-muted">{{ $meta['desc'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-body p-4">
                        <h5 class="mb-3 font-clash" style="font-weight:700;"><i class="fas fa-file-alt text-info mr-2"></i>Konten Halaman</h5>
                        @foreach(['about_text','footer_text'] as $key)
                            @php $meta = $defaults[$key]; @endphp
                            <div class="form-group mb-3">
                                <label style="font-size:13px;font-weight:600;color:#334155;">{{ $meta['label'] }}</label>
                                <textarea name="{{ $key }}" class="form-control" rows="3" style="border-radius:10px;">{{ old($key, $settings[$key]) }}</textarea>
                                <small class="text-muted">{{ $meta['desc'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-body p-4">
                        <h5 class="mb-3 font-clash" style="font-weight:700;"><i class="fas fa-share-alt text-primary mr-2"></i>Sosial Media</h5>
                        @foreach(['social_instagram','social_facebook','social_tiktok'] as $key)
                            @php $meta = $defaults[$key]; @endphp
                            <div class="form-group mb-3">
                                <label style="font-size:13px;font-weight:600;color:#334155;">{{ $meta['label'] }}</label>
                                <input type="url" name="{{ $key }}" class="form-control" value="{{ old($key, $settings[$key]) }}" placeholder="https://..." style="height:42px;border-radius:10px;">
                                <small class="text-muted">{{ $meta['desc'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-body p-4">
                        <h5 class="mb-3 font-clash" style="font-weight:700;"><i class="fas fa-chart-line text-success mr-2"></i>Commerce & Target</h5>
                        @foreach(['currency_symbol','monthly_revenue_goal','min_order_amount','default_shipping_fallback','order_id_prefix'] as $key)
                            @php $meta = $defaults[$key]; @endphp
                            <div class="form-group mb-3">
                                <label style="font-size:13px;font-weight:600;color:#334155;">{{ $meta['label'] }}</label>
                                <input type="{{ $meta['type'] === 'number' ? 'number' : 'text' }}" name="{{ $key }}" class="form-control" value="{{ old($key, $settings[$key]) }}" {{ $meta['type'] === 'number' ? 'min=0 step=1' : '' }} style="height:42px;border-radius:10px;">
                                <small class="text-muted">{{ $meta['desc'] }}</small>
                            </div>
                        @endforeach
                        @php $showOos = old('show_out_of_stock', $settings['show_out_of_stock'] ?? '1') == '1'; @endphp
                        <div class="form-group mb-0">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="show_out_of_stock" value="0">
                                <input type="checkbox" class="custom-control-input" id="show_out_of_stock" name="show_out_of_stock" value="1" {{ $showOos ? 'checked' : '' }}>
                                <label class="custom-control-label" for="show_out_of_stock" style="font-size:13px;font-weight:600;color:#334155;">Tampilkan Produk Habis Stok</label>
                            </div>
                            <small class="text-muted">{{ $defaults['show_out_of_stock']['desc'] }}</small>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                    <div class="card-body p-4">
                        <h5 class="mb-3 font-clash" style="font-weight:700;"><i class="fas fa-image text-warning mr-2"></i>Branding</h5>
                        @foreach(['store_logo','store_favicon'] as $key)
                            @php $meta = $defaults[$key]; $val = $settings[$key] ?? ''; @endphp
                            <div class="form-group mb-3">
                                <label style="font-size:13px;font-weight:600;color:#334155;display:block;margin-bottom:6px;">{{ $meta['label'] }}</label>
                                @if($val)
                                    <div class="mb-2 p-2" style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:10px;display:inline-block;">
                                        <img src="{{ asset('storage/'.$val) }}" alt="{{ $meta['label'] }}" style="max-height:48px;max-width:140px;object-fit:contain;">
                                    </div>
                                @endif
                                <div class="adm-file-picker">
                                    <label for="{{ $key }}" class="adm-file-btn"><i class="fas fa-cloud-upload-alt"></i> Pilih File</label>
                                    <span class="adm-file-name" data-file-name-for="{{ $key }}">{{ $val ? basename($val) : 'Belum ada file dipilih' }}</span>
                                    <input type="file" name="{{ $key }}" id="{{ $key }}" class="adm-file-input" accept="image/*{{ $key === 'store_favicon' ? ',.ico' : '' }}">
                                </div>
                                <small class="text-muted d-block mt-2">{{ $meta['desc'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-body p-4">
                        <h5 class="mb-3 font-clash" style="font-weight:700;"><i class="fas fa-shield-alt text-danger mr-2"></i>Integration Status</h5>
                        <div style="font-size:13px;">
                            <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Database</span><span class="font-weight-bold">{{ config('database.default') }}</span></div>
                            <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Midtrans Mode</span><span class="font-weight-bold">{{ config('midtrans.is_production') ? 'Production' : 'Sandbox' }}</span></div>
                            <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">RajaOngkir</span><span class="font-weight-bold">{{ config('rajaongkir.api_key') ? 'Configured' : 'Not configured' }}</span></div>
                            <div class="d-flex justify-content-between py-2"><span class="text-muted">App Env</span><span class="font-weight-bold">{{ config('app.env') }}</span></div>
                        </div>
                        <small class="text-muted d-block mt-2">API key/secret/password tetap di <code>.env</code>.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4" style="gap:10px;">
            <button type="submit" class="btn adm-modal-btn-save px-4">Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection
