@extends('layouts.admin')

@section('admin_title', 'Kelola Kategori')

@section('content')
    <div class="container-fluid">

        <div class="adm-page-header">
            <div>
                <div class="adm-page-header__eyebrow"><i class="fas fa-tags"></i> Catalog</div>
                <h1 class="adm-page-header__title">Manage Categories</h1>
                <p class="adm-page-header__sub">Atur kategori produk agar katalog tetap rapi dan mudah dijelajahi.</p>
            </div>
            <div class="adm-page-header__actions">
                <button type="button" class="adm-btn adm-btn-primary" data-toggle="modal" data-target="#categoryModal">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            </div>
        </div>

        <x-flash-alert />

        <div class="adm-table-card">
            <div class="adm-table-card__header">
                <h2 class="adm-table-card__title">
                    <span class="adm-table-card__title-icon"><i class="fas fa-folder-open"></i></span>
                    Category List
                </h2>
                <span class="adm-table-card__meta">{{ $categories->count() }} categories</span>
            </div>
            <div class="adm-table-card__body">
                <div class="table-responsive">
                    <table class="table adm-table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Products</th>
                                <th style="width:110px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td class="adm-num adm-id">#{{ $category->id }}</td>
                                    <td>
                                        <div class="adm-product-cell">
                                            @if ($category->image)
                                                <img src="{{ asset('storage/categories/' . $category->image) }}" alt="{{ $category->category_name }}" class="adm-thumb">
                                            @else
                                                <span class="adm-thumb-empty"><i class="fas fa-image"></i></span>
                                            @endif
                                            <div>
                                                <div class="adm-text-strong">{{ $category->category_name }}</div>
                                                <div class="adm-text-sm">Slug: {{ \Illuminate\Support\Str::slug($category->category_name) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="adm-badge adm-badge--info adm-badge--no-dot">{{ $category->products_count ?? 0 }} items</span>
                                    </td>
                                    <td>
                                        <div class="dropdown adm-action-dropdown">
                                            <button class="btn btn-adm-action dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i> Actions
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item text-warning" data-toggle="modal"
                                                    data-target="#categoryModal" data-id="{{ $category->id }}"
                                                    data-category_name="{{ $category->category_name }}"
                                                    data-image="{{ $category->image }}">
                                                    <i class="fas fa-pen"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                                    method="POST" class="m-0"
                                                    data-confirm="Are you sure you want to delete this category?" data-confirm-title="Hapus Kategori">
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
                                    <td colspan="4">
                                        <div class="adm-empty">
                                            <i class="fas fa-inbox"></i>
                                            <strong>Belum ada data</strong>
                                            <p>Data kategori akan tampil di sini begitu tersedia.</p>
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

    <!-- Modal untuk Create dan Edit Category -->
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 560px;">
            <div class="modal-content" style="border: 0; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(15,23,42,.18);">
                <form id="categoryForm" action="{{ route('admin.categories.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="id" id="category_id">

                    <div class="modal-header" style="padding: 24px 28px 18px; border-bottom: 1px solid #EEF2F7; background: linear-gradient(180deg, #F8FAFC 0%, #FFFFFF 100%);">
                        <div>
                            <div style="display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;background:#EEF2FF;color:#4F46E5;font-size:11px;font-weight:700;letter-spacing:.04em;margin-bottom:10px;">
                                <i class="fas fa-layer-group" style="font-size:10px;"></i> CATEGORY
                            </div>
                            <h5 class="modal-title" id="categoryModalLabel" style="font-size: 1.35rem; font-weight: 700; color: #0F172A; margin: 0;">Add Category</h5>
                            <p style="margin:6px 0 0; color:#64748B; font-size:13px;">Atur nama dan gambar kategori dengan tampilan yang rapi.</p>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity:.6; font-size: 28px; line-height: 1; padding: 0; margin: -4px 0 0;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body" style="padding: 24px 28px;">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="category_name" style="font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 8px;">Category Name</label>
                            <input type="text" class="form-control" name="category_name" id="category_name" required
                                placeholder="Contoh: Sneakers"
                                style="height: 46px; border-radius: 12px; border: 1px solid #DDE5F0; padding: 10px 14px; box-shadow: none;">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 8px;">Category Image</label>
                            <div class="adm-file-picker">
                                <label for="image" class="adm-file-btn"><i class="fas fa-cloud-upload-alt"></i> Choose File</label>
                                <span class="adm-file-name" data-file-name-for="image">No file chosen</span>
                                <input type="file" class="adm-file-input" name="image" id="image" accept="image/*">
                            </div>
                            <div style="font-size: 12px; color:#64748B; margin-top:8px;">PNG/JPG, disarankan rasio gambar konsisten.</div>
                        </div>

                        <div id="image-preview" style="margin-top: 14px;"></div>
                    </div>

                    <div class="modal-footer" style="padding: 18px 28px 24px; border-top: 1px solid #EEF2F7; background: #FCFDFF; gap: 10px;">
                        <button type="button" class="btn adm-modal-btn-cancel" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn adm-modal-btn-save">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>

        $('#categoryModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button yang men-trigger modal
            var id = button.data('id');
            var name = button.data('category_name');
            var image = button.data('image');

            var modal = $(this);
            modal.find('.modal-title').text(id ? 'Edit Category' : 'Add Category');
            modal.find('#category_id').val(id); // Set ID untuk Edit
            modal.find('#category_name').val(name);

            // Cek jika ada gambar
            if (image) {
                modal.find('#image-preview').html(
                    '<div style="border:1px solid #E2E8F0;border-radius:14px;padding:10px;background:#F8FAFC;">' +
                    '<div style="font-size:12px;font-weight:600;color:#475569;margin-bottom:8px;">Current Image</div>' +
                    '<img src="{{ asset('storage/categories/') }}/' + image + '" alt="Category Image" style="width:120px;height:120px;object-fit:cover;border-radius:10px;border:1px solid #E2E8F0;">' +
                    '</div>'
                );
            } else {
                modal.find('#image-preview').html('');
            }

            // Ubah form action untuk Edit atau Add
            if (id) {
                modal.find('form').attr('action', '/admin/categories/' + id); // URL untuk update
                modal.find('form').append('@method('PUT')'); // Method spoofing untuk PUT (Edit)
            } else {
                modal.find('form').attr('action', '{{ route('admin.categories.store') }}'); // URL untuk store
                modal.find('form').find('input[name="_method"]').remove(); // Hapus method spoofing jika tambah
            }
        });
    </script>
@endpush
