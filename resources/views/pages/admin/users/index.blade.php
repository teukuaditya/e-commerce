@extends('layouts.admin')

@section('admin_title', 'Kelola Pengguna')

@section('content')
    <div class="container-fluid">

        <div class="adm-page-header">
            <div>
                <div class="adm-page-header__eyebrow"><i class="fas fa-users"></i> User Management</div>
                <h1 class="adm-page-header__title">Manage Users</h1>
                <p class="adm-page-header__sub">Kelola akun pengguna, peran, dan akses admin.</p>
            </div>
            <div class="adm-page-header__actions">
                <button type="button" class="adm-btn adm-btn-primary" data-toggle="modal" data-target="#userModal">
                    <i class="fas fa-plus"></i> Add User
                </button>
            </div>
        </div>

        <x-flash-alert />

        <div class="adm-table-card">
            <div class="adm-table-card__header">
                <h2 class="adm-table-card__title">
                    <span class="adm-table-card__title-icon"><i class="fas fa-user-friends"></i></span>
                    User List
                </h2>
                <span class="adm-table-card__meta">{{ $users->count() }} total users</span>
            </div>
            <div class="adm-table-card__body">
                <div class="table-responsive">
                    <table class="table adm-table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th style="width:110px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                @php
                                    $nameParts = preg_split('/\s+/', trim($user->name ?? 'U'));
                                    $initials = strtoupper(substr($nameParts[0] ?? 'U', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
                                    $role = strtolower($user->role ?? 'user');
                                @endphp
                                <tr>
                                    <td class="adm-num adm-id">#{{ $user->id }}</td>
                                    <td>
                                        <div class="adm-user-cell">
                                            <span class="adm-avatar">{{ $initials }}</span>
                                            <div>
                                                <div class="adm-user-cell__name">{{ $user->name }}</div>
                                                <div class="adm-user-cell__sub">ID #{{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->phone)
                                            <span class="adm-num">{{ $user->phone }}</span>
                                        @else
                                            <span class="adm-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($role === 'admin')
                                            <span class="adm-badge adm-badge--primary">Admin</span>
                                        @else
                                            <span class="adm-badge adm-badge--gray">User</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown adm-action-dropdown">
                                            <button class="btn btn-adm-action dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i> Actions
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item text-warning" data-toggle="modal"
                                                    data-target="#userModal" data-id="{{ $user->id }}"
                                                    data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                                    data-phone="{{ $user->phone }}" data-role="{{ $user->role }}">
                                                    <i class="fas fa-pen"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                    method="POST" class="m-0"
                                                    data-confirm="Are you sure you want to delete this user?" data-confirm-title="Hapus User">
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
                                    <td colspan="6">
                                        <div class="adm-empty">
                                            <i class="fas fa-inbox"></i>
                                            <strong>Belum ada data</strong>
                                            <p>Data user akan tampil di sini begitu tersedia.</p>
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
