@extends('layouts.admin')

@section('admin_title', 'Kelola Produk')

@section('content')
    <div class="container-fluid">

        <div class="adm-page-header">
            <div>
                <div class="adm-page-header__eyebrow"><i class="fas fa-box-open"></i> Catalog</div>
                <h1 class="adm-page-header__title">Manage Products</h1>
                <p class="adm-page-header__sub">Kelola produk, harga, stok, dan detail katalog.</p>
            </div>
            <div class="adm-page-header__actions">
                <button type="button" class="adm-btn adm-btn-primary" data-toggle="modal" data-target="#productModal">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>
        </div>

        <x-flash-alert />

        <div class="adm-table-card">
            <div class="adm-table-card__header">
                <h2 class="adm-table-card__title">
                    <span class="adm-table-card__title-icon"><i class="fas fa-shopping-bag"></i></span>
                    Product List
                </h2>
                <span class="adm-table-card__meta">{{ $products->count() }} products</span>
            </div>
            <div class="adm-table-card__body">
                <div class="table-responsive">
                    <table class="table adm-table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Size</th>
                                <th>Stock</th>
                                <th>Weight</th>
                                <th style="width:110px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                @php
                                    $img = is_array($product->image) ? ($product->image[0] ?? null) : $product->image;
                                    $stock = (int) ($product->stock ?? 0);
                                    $stockClass = $stock <= 0 ? 'adm-badge--danger' : ($stock <= 10 ? 'adm-badge--warning' : 'adm-badge--success');
                                    $stockLabel = $stock <= 0 ? 'Out' : $stock . ' pcs';
                                @endphp
                                <tr>
                                    <td class="adm-num adm-id">#{{ $product->id }}</td>
                                    <td>
                                        <div class="adm-product-cell">
                                            @if ($img)
                                                <img src="{{ asset('storage/products/' . $img) }}" alt="{{ $product->title }}" class="adm-thumb">
                                            @else
                                                <span class="adm-thumb-empty"><i class="fas fa-image"></i></span>
                                            @endif
                                            <div>
                                                <div class="adm-text-strong">{{ $product->title }}</div>
                                                <div class="adm-text-sm">{{ $product->brand ?? '—' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="adm-badge adm-badge--primary adm-badge--no-dot">{{ optional($product->category)->category_name ?? '—' }}</span>
                                    </td>
                                    <td class="adm-num adm-nowrap">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</td>
                                    <td>
                                        @if (!empty($product->size) && is_array($product->size))
                                            <div class="d-flex flex-wrap" style="gap:4px;">
                                                @foreach ($product->size as $size)
                                                    <span class="adm-badge adm-badge--gray adm-badge--no-dot">{{ $size }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="adm-muted">—</span>
                                        @endif
                                    </td>
                                    <td><span class="adm-badge {{ $stockClass }} adm-badge--no-dot adm-num">{{ $stockLabel }}</span></td>
                                    <td class="adm-num adm-nowrap">{{ number_format((float) ($product->weight ?? 0), 0, ',', '.') }} g</td>
                                    <td>
                                        <div class="dropdown adm-action-dropdown">
                                            <button class="btn btn-adm-action dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i> Actions
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item text-warning" data-toggle="modal"
                                                    data-target="#productModal" data-id="{{ $product->id }}"
                                                    data-category_id="{{ $product->category_id }}"
                                                    data-brand="{{ $product->brand }}" data-title="{{ $product->title }}"
                                                    data-description="{{ $product->description }}"
                                                    data-price="{{ $product->price }}"
                                                    data-size="{{ is_array($product->size) ? implode(',', $product->size) : ($product->size ?? '') }}"
                                                    data-stock="{{ $product->stock }}"
                                                    data-weight="{{ $product->weight }}"
                                                    data-image="{{ is_array($product->image) ? implode(',', $product->image) : $product->image }}">
                                                    <i class="fas fa-pen"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.products.destroy', $product->id) }}"
                                                    method="POST" class="m-0"
                                                    data-confirm="Apakah Anda yakin ingin menghapus produk ini?" data-confirm-title="Hapus Produk">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger w-100 text-left border-0 bg-transparent">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="adm-empty">
                                            <i class="fas fa-inbox"></i>
                                            <strong>Belum ada data</strong>
                                            <p>Data produk akan tampil di sini begitu tersedia.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Create dan Edit Produk -->
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;box-shadow:0 16px 48px rgba(15,23,42,.16);">
                <form id="productForm" action="{{ route('admin.products.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="id" id="product_id">

                    <div class="modal-header" style="padding:16px 20px;border-bottom:1px solid #EEF2F7;background:#F8FAFC;">
                        <div>
                            <h5 class="modal-title" id="productModalLabel" style="font-size:1.1rem;font-weight:700;color:#0F172A;margin:0;">Add Product</h5>
                            <p style="margin:2px 0 0;color:#64748B;font-size:12px;">Isi detail produk, ukuran, stok, dan gambar.</p>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity:.55;font-size:24px;line-height:1;padding:0;margin:0;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body" style="padding:18px 20px;max-height:68vh;overflow-y:auto;">
                        <div class="adm-product-grid" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;">
                            <div class="form-group" style="margin:0;">
                                <label for="category_id" style="font-size:12px;font-weight:600;color:#334155;margin-bottom:4px;">Category</label>
                                <select class="form-control" name="category_id" id="category_id" required style="height:40px;border-radius:10px;border:1px solid #DDE5F0;padding:8px 12px;font-size:13px;box-shadow:none;">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" id="category_{{ $category->id }}">
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group" style="margin:0;">
                                <label for="brand" style="font-size:12px;font-weight:600;color:#334155;margin-bottom:4px;">Brand</label>
                                <input type="text" class="form-control" name="brand" id="brand" value="{{ old('brand') }}" placeholder="Contoh: Nike"
                                    style="height:40px;border-radius:10px;border:1px solid #DDE5F0;padding:8px 12px;font-size:13px;box-shadow:none;">
                            </div>

                            <div class="form-group" style="margin:0;grid-column:1 / -1;">
                                <label for="title" style="font-size:12px;font-weight:600;color:#334155;margin-bottom:4px;">Product Name</label>
                                <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" placeholder="Nama produk"
                                    style="height:40px;border-radius:10px;border:1px solid #DDE5F0;padding:8px 12px;font-size:13px;box-shadow:none;">
                            </div>

                            <div class="form-group" style="margin:0;grid-column:1 / -1;">
                                <label for="description" style="font-size:12px;font-weight:600;color:#334155;margin-bottom:4px;">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="2" placeholder="Deskripsi produk"
                                    style="border-radius:10px;border:1px solid #DDE5F0;padding:8px 12px;font-size:13px;box-shadow:none;resize:vertical;">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group" style="margin:0;">
                                <label for="price" style="font-size:12px;font-weight:600;color:#334155;margin-bottom:4px;">Price</label>
                                <input type="number" step="0.01" class="form-control" name="price" id="price" value="{{ old('price') }}" placeholder="0"
                                    style="height:40px;border-radius:10px;border:1px solid #DDE5F0;padding:8px 12px;font-size:13px;box-shadow:none;">
                            </div>

                            <div class="form-group" style="margin:0;">
                                <label for="stock" style="font-size:12px;font-weight:600;color:#334155;margin-bottom:4px;">Stock</label>
                                <input type="number" class="form-control" name="stock" id="stock" value="{{ old('stock') }}" placeholder="0"
                                    style="height:40px;border-radius:10px;border:1px solid #DDE5F0;padding:8px 12px;font-size:13px;box-shadow:none;">
                            </div>

                            <div class="form-group" style="margin:0;">
                                <label for="weight" style="font-size:12px;font-weight:600;color:#334155;margin-bottom:4px;">Weight (grams)</label>
                                <input type="number" class="form-control" id="weight" name="weight" min="0" value="0" required placeholder="0"
                                    style="height:40px;border-radius:10px;border:1px solid #DDE5F0;padding:8px 12px;font-size:13px;box-shadow:none;">
                            </div>

                            <div class="form-group" id="sizeInput" style="margin:0;">
                                <label style="font-size:12px;font-weight:600;color:#334155;margin-bottom:4px;">Size</label>
                                <div class="adm-size-row">
                                    <input type="text" name="size[]" class="form-control" value="{{ old('size.0') }}" placeholder="Size 1"
                                        style="height:40px;border-radius:10px;border:1px solid #DDE5F0;padding:8px 12px;font-size:13px;box-shadow:none;">
                                    <button type="button" class="adm-size-remove" title="Hapus size"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="adm-size-row">
                                    <input type="text" name="size[]" class="form-control" value="{{ old('size.1') }}" placeholder="Size 2"
                                        style="height:40px;border-radius:10px;border:1px solid #DDE5F0;padding:8px 12px;font-size:13px;box-shadow:none;">
                                    <button type="button" class="adm-size-remove" title="Hapus size"><i class="fas fa-times"></i></button>
                                </div>
                                <button type="button" id="addSizeBtn" class="btn btn-sm" style="border-radius:8px;padding:6px 10px;font-size:12px;font-weight:600;color:#4F46E5;background:#EEF2FF;border:1px solid #C7D2FE;">+ Add Size</button>
                            </div>
                        </div>

                        <div style="margin-top:14px;padding-top:14px;border-top:1px solid #EEF2F7;">
                            <div style="font-size:12px;font-weight:700;color:#334155;margin-bottom:8px;">Product Images</div>
                            <div class="adm-product-images-grid" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;">
                                <div class="form-group" style="margin:0;padding:10px;border:1px dashed #CBD5E1;border-radius:10px;background:#F8FAFC;">
                                    <label style="font-size:12px;font-weight:600;color:#334155;margin-bottom:6px;">Gambar 1</label>
                                    <div class="adm-file-picker" style="padding:8px;gap:6px;">
                                        <label for="image1" class="adm-file-btn" style="padding:6px 8px;font-size:11px;"><i class="fas fa-cloud-upload-alt"></i> Pilih</label>
                                        <span class="adm-file-name" data-file-name-for="image1" style="font-size:11px;">No file</span>
                                        <input type="file" name="image1" id="image1" class="adm-file-input" accept="image/*">
                                    </div>
                                    <div id="existingImage1" class="mt-2"></div>
                                    <button type="button" id="deleteImage1" class="btn btn-sm mt-2" style="display:none;border-radius:8px;background:#FEE2E2;color:#DC2626;border:1px solid #FECACA;font-weight:600;font-size:11px;padding:4px 8px;">Delete</button>
                                </div>

                                <div class="form-group" style="margin:0;padding:10px;border:1px dashed #CBD5E1;border-radius:10px;background:#F8FAFC;">
                                    <label style="font-size:12px;font-weight:600;color:#334155;margin-bottom:6px;">Gambar 2</label>
                                    <div class="adm-file-picker" style="padding:8px;gap:6px;">
                                        <label for="image2" class="adm-file-btn" style="padding:6px 8px;font-size:11px;"><i class="fas fa-cloud-upload-alt"></i> Pilih</label>
                                        <span class="adm-file-name" data-file-name-for="image2" style="font-size:11px;">No file</span>
                                        <input type="file" name="image2" id="image2" class="adm-file-input" accept="image/*">
                                    </div>
                                    <div id="existingImage2" class="mt-2"></div>
                                    <button type="button" id="deleteImage2" class="btn btn-sm mt-2" style="display:none;border-radius:8px;background:#FEE2E2;color:#DC2626;border:1px solid #FECACA;font-weight:600;font-size:11px;padding:4px 8px;">Delete</button>
                                </div>

                                <div class="form-group" style="margin:0;padding:10px;border:1px dashed #CBD5E1;border-radius:10px;background:#F8FAFC;">
                                    <label style="font-size:12px;font-weight:600;color:#334155;margin-bottom:6px;">Gambar 3</label>
                                    <div class="adm-file-picker" style="padding:8px;gap:6px;">
                                        <label for="image3" class="adm-file-btn" style="padding:6px 8px;font-size:11px;"><i class="fas fa-cloud-upload-alt"></i> Pilih</label>
                                        <span class="adm-file-name" data-file-name-for="image3" style="font-size:11px;">No file</span>
                                        <input type="file" name="image3" id="image3" class="adm-file-input" accept="image/*">
                                    </div>
                                    <div id="existingImage3" class="mt-2"></div>
                                    <button type="button" id="deleteImage3" class="btn btn-sm mt-2" style="display:none;border-radius:8px;background:#FEE2E2;color:#DC2626;border:1px solid #FECACA;font-weight:600;font-size:11px;padding:4px 8px;">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="padding:14px 20px;border-top:1px solid #EEF2F7;background:#FCFDFF;gap:8px;">
                        <button type="button" class="btn adm-modal-btn-cancel" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn adm-modal-btn-save" id="submitBtn">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>

        // Dynamically add size input fields
        $('#addSizeBtn').click(function() {
            const row = $(
                '<div class="adm-size-row">' +
                    '<input type="text" name="size[]" class="form-control" placeholder="Ukuran baru" style="height:40px;border-radius:10px;border:1px solid #DDE5F0;padding:8px 12px;font-size:13px;box-shadow:none;">' +
                    '<button type="button" class="adm-size-remove" title="Hapus size"><i class="fas fa-times"></i></button>' +
                '</div>'
            );
            $('#addSizeBtn').before(row);
        });

        $(document).on('click', '.adm-size-remove', function () {
            var rows = $('#sizeInput .adm-size-row');
            if (rows.length <= 1) {
                $(this).closest('.adm-size-row').find('input').val('');
                return;
            }
            $(this).closest('.adm-size-row').remove();
        });

        $('#productModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button yang membuka modal
            var action = '{{ route('admin.products.store') }}'; // Default untuk menambah produk
            var method = 'POST'; // Default untuk menambah produk

            // Cek apakah kita sedang mengedit produk yang sudah ada
            if (button.data('id')) {
                action = '{{ route('admin.products.update', ':id') }}'.replace(':id', button.data('id'));
                method = 'PUT'; // Gunakan PUT untuk mengedit produk
            }

            // Set action dan method form secara dinamis
            $('#productForm').attr('action', action);
            $('#productForm').find('input[name="_method"]').val(method); // Set method dinamis

            // Set nilai form
            $('#product_id').val(button.data('id') || ''); // Set ID produk (kosong untuk produk baru)
            $('#category_id').val(button.data('category_id')); // Set kategori produk
            $('#brand').val(button.data('brand')); // Set brand produk
            $('#title').val(button.data('title')); // Set nama produk
            $('#description').val(button.data('description')); // Set deskripsi produk
            $('#price').val(button.data('price')); // Set harga produk
            var sizes = button.data('size') ? String(button.data('size')).split(',') : [];
            var $sizeWrap = $('#sizeInput');
            $sizeWrap.find('.adm-size-row').remove();
            if (sizes.length === 0) sizes = ['', ''];
            sizes.forEach(function (sz, idx) {
                var row = $(
                    '<div class="adm-size-row">' +
                        '<input type="text" name="size[]" class="form-control" placeholder="Size ' + (idx + 1) + '" style="height:40px;border-radius:10px;border:1px solid #DDE5F0;padding:8px 12px;font-size:13px;box-shadow:none;">' +
                        '<button type="button" class="adm-size-remove" title="Hapus size"><i class="fas fa-times"></i></button>' +
                    '</div>'
                );
                row.find('input').val($.trim(sz));
                $('#addSizeBtn').before(row);
            });
            $('#stock').val(button.data('stock')); // Set stok produk

            // Menampilkan gambar lama saat mengedit produk
            for (let i = 1; i <= 3; i++) {
                var image = button.data('image') ? button.data('image').split(',')[i - 1] : null;
                if (image) {
                    $('#existingImage' + i).html('<img src="{{ asset('storage/products/') }}/' + image +
                        '" alt="Product image" style="width:100%;max-width:140px;height:100px;object-fit:cover;border-radius:10px;border:1px solid #E2E8F0;">');
                    $('#deleteImage' + i).show();
                    $('#deleteImage' + i).data('image', image);
                } else {
                    $('#existingImage' + i).empty(); // Kosongkan jika tidak ada gambar
                    $('#deleteImage' + i).hide(); // Sembunyikan tombol delete jika tidak ada gambar
                }
            }

            // Set nilai berat produk
            $(this).find('#weight').val(button.data('weight') || 0); // Default 0 jika tidak ada data
        });

        // Fungsi untuk menghapus gambar
        $('[id^="deleteImage"]').click(function() {
            var imageToDelete = $(this).data('image');
            if (imageToDelete) {
                var imageIndex = $(this).attr('id').replace('deleteImage', '');
                $('#existingImage' + imageIndex).empty();
                $(this).hide();

                $('<input>').attr({
                    type: 'hidden',
                    name: 'delete_image' + imageIndex,
                    value: imageToDelete
                }).appendTo('#productForm');
            }
        });
    </script>
@endpush
