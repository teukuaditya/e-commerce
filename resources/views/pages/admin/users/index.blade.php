@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Manage Users</h1>

        <!-- Button to open the Create User modal -->
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#userModal">
            Add User
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
                <h6 class="m-0 font-weight-bold text-primary">User List</h6>
            </div>
            <div class="card-body text-gray-900">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? '-' }}</td>
                                    <td>{{ $user->role }}</td>
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
                                                    data-target="#userModal" data-id="{{ $user->id }}"
                                                    data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                                    data-phone="{{ $user->phone }}" data-role="{{ $user->role }}"><i
                                                        class="fas fa-edit mr-2"></i> Edit
                                                </a>
                                                <!-- Button to delete user -->
                                                <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                    method="POST" style="display:inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this user?');">
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

    <!-- Modal untuk Create dan Edit User -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="userForm" action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    @method('POST') <!-- Default untuk tambah user -->

                    <!-- Hidden field untuk id jika mengedit user -->
                    <input type="hidden" name="id" id="user_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Add User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Name -->
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" name="phone" id="phone">
                        </div>

                        <!-- Role -->
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select class="form-control" name="role" id="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <!-- Password -->
                        <div class="form-group" id="passwordField">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter password (Leave blank to keep current)">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save User</button>
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

        $('#userModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button yang men-trigger modal
            var id = button.data('id');
            var name = button.data('name');
            var email = button.data('email');
            var phone = button.data('phone');
            var role = button.data('role');

            var modal = $(this);
            modal.find('.modal-title').text(id ? 'Edit User' : 'Add User');
            modal.find('#user_id').val(id); // Set ID untuk Edit
            modal.find('#name').val(name);
            modal.find('#email').val(email);
            modal.find('#phone').val(phone);
            modal.find('#role').val(role);

            // Tampilkan password field untuk edit
            if (id) {
                modal.find('#passwordField').show();
            } else {
                modal.find('#passwordField').show(); // Selalu tampilkan untuk add user
            }

            // Ubah form action untuk Edit atau Add
            if (id) {
                modal.find('form').attr('action', '/admin/users/' + id); // URL untuk update
                modal.find('form').append('@method('PUT')'); // Method spoofing untuk PUT (Edit)
            } else {
                modal.find('form').attr('action', '{{ route('admin.users.store') }}'); // URL untuk store
                modal.find('form').find('input[name="_method"]').remove(); // Hapus method spoofing jika tambah
            }
        });
    </script>
@endpush
