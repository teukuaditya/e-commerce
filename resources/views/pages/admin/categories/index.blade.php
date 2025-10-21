@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Manage Categories</h1>

        <!-- Button to open the Create Category modal -->
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#categoryModal">
            Add Category
        </button>

        <!-- Success message -->
        @if (session('success'))
            <div id="success-alert" class="alert alert-success">
                <i class="fas fa-check-circle mr-2"></i> <!-- Ikon centang -->
                {{ session('success') }}
            </div>
        @endif

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Category List</h6>
            </div>
            <div class="card-body text-gray-900">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $category->category_name }}</td>
                                    <td>{{ $category->description }}</td>
                                    <td>
                                        @if ($category->image)
                                            <img src="{{ asset('storage/categories/' . $category->image) }}" alt="{{ $category->category_name }}" width="100" class="img-thumbnail">
                                        @else
                                            No image
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Dropdown Button for Action -->
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                                id="actionButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="actionButton">
                                                <!-- Button to open the Edit modal -->
                                                <a class="dropdown-item text-warning" data-toggle="modal"
                                                    data-target="#categoryModal" data-id="{{ $category->id }}"
                                                    data-category_name="{{ $category->category_name }}"
                                                    data-description="{{ $category->description }}"
                                                    data-image="{{ $category->image }}"><i
                                                        class="fas fa-edit mr-2"></i> Edit
                                                </a>
                                                <!-- Button to delete category -->
                                                <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                                    method="POST" style="display:inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash mr-2"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Create dan Edit Category -->
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="categoryForm" action="{{ route('admin.categories.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST') <!-- Default untuk tambah kategori -->

                    <!-- Hidden field untuk id jika mengedit kategori -->
                    <input type="hidden" name="id" id="category_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Category Name -->
                        <div class="form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" class="form-control" name="category_name" id="category_name" required>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description"></textarea>
                        </div>

                        <!-- Image -->
                        <div class="form-group">
                            <label for="image">Category Image</label>
                            <input type="file" class="form-control" name="image" id="image">
                        </div>

                        <!-- Preview Image for Editing -->
                        <div id="image-preview"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        // Menghilangkan pesan sukses setelah 5 detik
        setTimeout(function() {
            let alertElement = document.getElementById('success-alert');
            if (alertElement) {
                alertElement.style.transition = "opacity 0.5s ease";
                alertElement.style.opacity = 0; // Perlahan menghilang
                setTimeout(() => alertElement.remove(), 500); // Hapus setelah transisi selesai
            }
        }, 2000); // Waktu dalam milidetik

        $('#categoryModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button yang men-trigger modal
            var id = button.data('id');
            var name = button.data('category_name');
            var description = button.data('description');
            var image = button.data('image');

            var modal = $(this);
            modal.find('.modal-title').text(id ? 'Edit Category' : 'Add Category');
            modal.find('#category_id').val(id); // Set ID untuk Edit
            modal.find('#category_name').val(name);
            modal.find('#description').val(description);

            // Cek jika ada gambar
            if (image) {
                modal.find('#image-preview').html('<img src="{{ asset('storage/categories/') }}/' + image +
                    '" alt="Category Image" width="100">');
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
