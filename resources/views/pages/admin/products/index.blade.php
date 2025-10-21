@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Manage Products</h1>

        <!-- Button to open the Create Product modal -->
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#productModal">
            Add Product
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
                <h6 class="m-0 font-weight-bold text-primary">Product List</h6>
            </div>
            <div class="card-body text-gray-900">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Size</th>
                                <th>Stock</th>
                                <th>Weight</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->title }}</td>
                                    <td>{{ $product->brand }}</td>
                                    <td>
                                        <!-- Menampilkan kategori dengan badge -->
                                        <span class="badge badge-primary">{{ $product->category->category_name }}</span>
                                    </td>
                                    <td>{{ $product->price }}</td>
                                    <td>
                                        @if ($product->size)
                                            @foreach ($product->size as $size)
                                                <span class="badge badge-secondary">{{ $size }}</span>
                                            @endforeach
                                        @else
                                            No size
                                        @endif
                                    </td>
                                    <td>{{ $product->stock }}</td>
                                    <td>{{ $product->weight }}</td>
                                    <td>
                                        @if ($product->image)
                                            @foreach ($product->image as $image)
                                                <img src="{{ asset('storage/products/' . $image) }}" width="100"
                                                    class="img-thumbnail">
                                            @endforeach
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
                                                    data-target="#productModal" data-id="{{ $product->id }}"
                                                    data-category_id="{{ $product->category_id }}"
                                                    data-brand="{{ $product->brand }}" data-title="{{ $product->title }}"
                                                    data-description="{{ $product->description }}"
                                                    data-price="{{ $product->price }}"
                                                    data-size="{{ implode(',', $product->size) }}"
                                                    data-stock="{{ $product->stock }}"
                                                    data-weight="{{ $product->weight }}"
                                                    data-image="{{ is_array($product->image) ? implode(',', $product->image) : $product->image }}"><i
                                                        class="fas fa-edit mr-2"></i> Edit
                                                </a>
                                                <!-- Button to delete product -->
                                                <form action="{{ route('admin.products.destroy', $product->id) }}"
                                                    method="POST" style="display:inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this product?');">
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

    <!-- Modal untuk Create dan Edit Produk -->
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="productForm" action="{{ route('admin.products.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST') <!-- Default untuk tambah produk -->

                    <!-- Hidden field untuk id jika mengedit produk -->
                    <input type="hidden" name="id" id="product_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Add Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Kategori -->
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" name="category_id" id="category_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" id="category_{{ $category->id }}">
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Brand -->
                        <div class="form-group">
                            <label for="brand">Brand</label>
                            <input type="text" class="form-control" name="brand" id="brand"
                                value="{{ old('brand') }}">
                        </div>

                        <!-- Nama Produk -->
                        <div class="form-group">
                            <label for="title">Product Name</label>
                            <input type="text" class="form-control" name="title" id="title"
                                value="{{ old('title') }}">
                        </div>

                        <!-- Deskripsi Produk -->
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description">{{ old('description') }}</textarea>
                        </div>

                        <!-- Harga -->
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" step="0.01" class="form-control" name="price" id="price"
                                value="{{ old('price') }}">
                        </div>

                        <!-- Ukuran -->
                        <div class="form-group" id="sizeInput">
                            <label for="size">Size</label>
                            <input type="text" name="size[]" class="form-control" value="{{ old('size.0') }}"
                                placeholder="First size">
                            <input type="text" name="size[]" class="form-control" value="{{ old('size.1') }}"
                                placeholder="Second size">
                        </div>

                        <!-- Button to add more size fields -->
                        <button type="button" id="addSizeBtn" class="btn btn-success btn-sm">Add Size</button>

                        <!-- Stock -->
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number" class="form-control" name="stock" id="stock"
                                value="{{ old('stock') }}">
                        </div>

                        <div class="form-group">
                            <label for="weight">Weight (grams)</label>
                            <input type="number" class="form-control" id="weight" name="weight" min="0"
                                value="0" required>
                        </div>

                        <!-- Input Gambar Produk -->
                        <!-- Gambar Produk 1 -->
                        <div class="form-group">
                            <label for="image1">Gambar Produk 1</label>
                            <input type="file" name="image1" id="image1" class="form-control" accept="image/*">
                            <div id="existingImage1" class="mt-2"></div>
                            <button type="button" id="deleteImage1" class="btn btn-danger btn-sm mt-2"
                                style="display:none;">Delete Image</button>
                        </div>

                        <!-- Gambar Produk 2 -->
                        <div class="form-group">
                            <label for="image2">Gambar Produk 2</label>
                            <input type="file" name="image2" id="image2" class="form-control" accept="image/*">
                            <div id="existingImage2" class="mt-2"></div>
                            <button type="button" id="deleteImage2" class="btn btn-danger btn-sm mt-2"
                                style="display:none;">Delete Image</button>
                        </div>

                        <!-- Gambar Produk 3 -->
                        <div class="form-group">
                            <label for="image3">Gambar Produk 3</label>
                            <input type="file" name="image3" id="image3" class="form-control" accept="image/*">
                            <div id="existingImage3" class="mt-2"></div>
                            <button type="button" id="deleteImage3" class="btn btn-danger btn-sm mt-2"
                                style="display:none;">Delete Image</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Save Product</button>
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

        // Dynamically add size input fields
        $('#addSizeBtn').click(function() {
            const newInput = $('<input type="text" name="size[]" class="form-control" placeholder="Ukuran baru">');
            $('#sizeInput').append(newInput);
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
            $('#sizeInput').find('input').each(function(index) {
                $(this).val(button.data('size') ? button.data('size').split(',')[index] :
                    ''); // Set ukuran produk
            });
            $('#stock').val(button.data('stock')); // Set stok produk

            // Menampilkan gambar lama saat mengedit produk
            for (let i = 1; i <= 3; i++) {
                var image = button.data('image') ? button.data('image').split(',')[i - 1] : null;
                if (image) {
                    $('#existingImage' + i).html('<img src="{{ asset('storage/products/') }}/' + image +
                        '" width="100" class="img-thumbnail">');
                    $('#deleteImage' + i).show(); // Menampilkan tombol delete jika ada gambar
                    $('#deleteImage' + i).data('image', image); // Menyimpan nama gambar untuk dihapus
                } else {
                    $('#existingImage' + i).empty(); // Kosongkan jika tidak ada gambar
                    $('#deleteImage' + i).hide(); // Sembunyikan tombol delete jika tidak ada gambar
                }
            }

            // Set nilai berat produk
            $(this).find('#weight').val(button.data('weight') || 0); // Default 0 jika tidak ada data
        });

        // Fungsi untuk menghapus gambar
        $('.btn-danger').click(function() {
            var imageToDelete = $(this).data('image');
            if (imageToDelete) {
                // Kirim request untuk menghapus gambar
                var imageField = $(this).attr('id').replace('deleteImage',
                    'image'); // Menentukan field gambar yang sesuai
                var imageIndex = $(this).attr('id').replace('deleteImage', ''); // Menentukan nomor gambar
                // Sembunyikan gambar dan tombol delete
                $('#existingImage' + imageIndex).empty();
                $(this).hide();

                // Tambahkan field hidden untuk memberitahu controller bahwa gambar akan dihapus
                $('<input>').attr({
                    type: 'hidden',
                    name: 'delete_image' + imageIndex,
                    value: imageToDelete
                }).appendTo('#productForm');
            }
        });
    </script>
@endpush
