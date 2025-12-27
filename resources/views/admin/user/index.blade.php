@extends('template')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="fw-bold text-dark mb-1">Manajemen User</h3>
                        <p class="text-muted mb-0">Kelola hak akses dan informasi pengguna sistem</p>
                    </div>
                    <button class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center gap-2 rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modalAddUser">
                        <i class="bi bi-person-plus-fill"></i>
                        <span>Tambah User</span>
                    </button>
                </div>

                @if (session('success'))
                    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Daftar Pengguna</h5>
                        <div class="d-flex align-items-center gap-3">
                            <label class="text-muted small fw-bold mb-0">Filter Role:</label>
                            <select id="filterRole" class="form-select form-select-sm border-light-subtle shadow-sm"
                                style="width: 150px">
                                <option value="">Semua Role</option>
                                <option value="admin">Admin</option>
                                <option value="petugas">Petugas</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle w-100" id="datatable">
                                <thead class="bg-light text-secondary">
                                    <tr>
                                        <th class="ps-3" width="5%">#</th>
                                        <th>NAMA</th>
                                        <th>EMAIL</th>
                                        <th>ROLE</th>
                                        <th class="text-center" width="15%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $item)
                                        <tr>
                                            <td class="ps-3 text-muted small">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $item->name }}</div>
                                            </td>
                                            <td>{{ $item->email }}</td>
                                            <td>
                                                @php
                                                    $badges = [
                                                        'admin' => 'bg-danger-subtle text-danger border-danger-subtle',
                                                        'petugas' =>
                                                            'bg-warning-subtle text-warning border-warning-subtle',
                                                        'siswa' =>
                                                            'bg-success-subtle text-success border-success-subtle',
                                                    ];
                                                    $cls = $badges[$item->role] ?? 'bg-secondary-subtle';
                                                @endphp
                                                <span
                                                    class="badge {{ $cls }} border px-3 py-2 rounded-pill text-uppercase"
                                                    style="font-size: 0.65rem;">
                                                    {{ $item->role }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button
                                                        class="btn btn-sm btn-outline-primary btn-edit rounded-pill px-3"
                                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                        data-email="{{ $item->email }}" data-role="{{ $item->role }}">
                                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                                    </button>

                                                    <button
                                                        class="btn btn-sm btn-outline-danger btn-delete rounded-pill px-3"
                                                        data-id="{{ $item->id }}">
                                                        <i class="bi bi-trash3 me-1"></i> Hapus
                                                    </button>

                                                    <form id="formDelete{{ $item->id }}"
                                                        action="{{ route('users.delete', $item->id) }}" method="POST"
                                                        hidden>
                                                        @csrf @method('DELETE')
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

    @include('admin.user.modal.add')
    @include('admin.user.modal.edit')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const table = $('#datatable').DataTable({
                pageLength: 10,
                ordering: true,
                language: {
                    search: "",
                    searchPlaceholder: "Cari user...",
                    lengthMenu: "_MENU_ data per halaman",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ user",
                    paginate: {
                        previous: "<i class='bi bi-chevron-left'></i>",
                        next: "<i class='bi bi-chevron-right'></i>"
                    }
                },
                dom: "<'row mb-3'<'col-md-6'l><'col-md-6'f>>t<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
            });

            $('#filterRole').on('change', function() {
                table.column(3).search($(this).val()).draw();
            });

            $(document).on('click', '.btn-edit', function() {

                const id = $(this).data('id');
                const name = $(this).data('name');
                const email = $(this).data('email');
                const role = $(this).data('role');

                $('#formEditUser').attr('action', "{{ url('users') }}/" + id);
                $('#editName').val(name);
                $('#editEmail').val(email);
                $('#editRole').val(role);

                $('#modalEditUser').modal('show');
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                Swal.fire({
                    title: "Hapus User?",
                    text: "Data ini akan dihapus secara permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal",
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'rounded-pill px-4',
                        cancelButton: 'rounded-pill px-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#formDelete' + id).submit();
                    }
                });
            });
        });
    </script>
@endpush
