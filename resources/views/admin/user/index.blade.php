@extends('template')
@section('content')
    <div class="container">
        <div class="page-inner">

            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4 mt-3">
                <div>
                    <h3 class="fw-bold mb-3">Manajemen User</h3>
                    <span class="text-muted">Kelola user dengan mudah</span>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row mt-2">
                <div class="col-12">
                    <div class="card shadow-lg border-0 mt-2">

                        <div
                            class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                            <h4 class="card-title fw-bold mb-0">Data User</h4>
                            <div class="d-flex align-items-center gap-2">

                                <!-- FILTER ROLE -->
                                <select id="filterRole" class="form-select form-select-sm" style="min-width: 140px">
                                    <option value="">Semua Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="petugas">Petugas</option>
                                    <option value="siswa">Siswa</option>
                                </select>

                                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                                    data-bs-target="#modalAddUser">
                                    <i class="fa fa-plus"></i>
                                    <span>Tambah User</span>
                                </button>

                            </div>

                        </div>

                        <div class="card-body pt-0">

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle" id="datatable">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($users as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->email }}</td>

                                                <td>
                                                    @if ($item->role === 'admin')
                                                        <span class="badge bg-danger text-white px-2 py-1">
                                                            {{ ucfirst($item->role) }}
                                                        </span>
                                                    @elseif ($item->role === 'petugas')
                                                        <span class="badge bg-warning text-dark px-2 py-1">
                                                            {{ ucfirst($item->role) }}
                                                        </span>
                                                    @elseif ($item->role === 'siswa')
                                                        <span class="badge bg-success text-white px-2 py-1">
                                                            {{ ucfirst($item->role) }}
                                                        </span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <div class="d-flex gap-2">

                                                        <button class="btn btn-sm btn-outline-info btn-edit"
                                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                            data-email="{{ $item->email }}"
                                                            data-role="{{ $item->role }}" data-bs-toggle="modal"
                                                            data-bs-target="#modalEditUser">
                                                            Edit
                                                        </button>

                                                        <button class="btn btn-sm btn-outline-danger btn-delete"
                                                            data-id="{{ $item->id }}">
                                                            Hapus
                                                        </button>

                                                        <form id="formDelete{{ $item->id }}"
                                                            action="{{ route('users.delete', $item->id) }}" method="POST"
                                                            hidden>
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>

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
            </div>
        </div>
    </div>
    </div>

    {{-- MODAL ADD USER --}}
    <div class="modal fade" id="modalAddUser" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-xl-down">
            <div class="modal-content bg-white">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" placeholder="Masukkan nama" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">-- pilih role --</option>
                                <option value="admin">Admin</option>
                                <option value="petugas">Petugas</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditUser" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-xl-down">
            <div class="modal-content bg-white">

                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" id="formEditUser">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" id="editName" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password (kosongkan jika tidak diubah)</label>
                            <input type="password" name="password" id="editPassword" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" id="editRole" class="form-select">
                                <option value="admin">Admin</option>
                                <option value="petugas">Petugas</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const = $('#datatable').DataTable({
            pageLength: 10,
            ordering: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ User",
                zeroRecords: "Tidak ada data ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ User",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });

        // FILTER ROLE
        $('#filterRole').on('change', function() {
            let role = $(this).val();
            table.column(3).search(role).draw(); // kolom role
        });

        // FILL MODAL EDIT
        document.addEventListener('DOMContentLoaded', () => {
            const editButtons = document.querySelectorAll('.btn-edit');
            const editForm = document.querySelector('#formEditUser');

            editButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    let id = this.dataset.id;
                    let name = this.dataset.name;
                    let email = this.dataset.email;
                    let role = this.dataset.role;

                    editForm.action = "{{ url('users/update') }}/" + id;
                    document.getElementById('editName').value = name;
                    document.getElementById('editEmail').value = email;
                    document.getElementById('editRole').value = role;
                });
            });
        });

        // DELETE CONFIRMATION
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete')) {
                e.preventDefault();

                const id = e.target.closest('.btn-delete').dataset.id;

                Swal.fire({
                    title: "Hapus User?",
                    text: "Data ini tidak bisa dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('formDelete' + id).submit();
                    }
                });
            }
        });
    </script>
@endpush
