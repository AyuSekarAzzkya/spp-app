@extends('template')

@section('content')
    <div class="container">
        <div class="page-inner">

            <div class="d-flex align-items-left flex-column flex-md-row justify-content-between pt-2 pb-3 mt-3">
                <div>
                    <h3 class="fw-bold mb-1">Daftar Kelas</h3>
                    <span class="text-muted">Kelola kelas, jurusan, dan tingkat dengan mudah</span>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
            @endif

            <div class="card shadow-lg border-0 mt-2" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 fw-semibold">Daftar Kelas</h4>
                    <button class="btn btn-primary shadow-sm px-4 py-3" data-bs-toggle="modal"
                        data-bs-target="#modalAddClass">
                        <i class="fa fa-plus me-1"></i> Tambah Kelas
                    </button>
                </div>

                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Filter Jurusan</label>
                        <select id="filterJurusan" class="form-select" style="max-width: 250px;">
                            <option value="">Semua Jurusan</option>

                            @php
                                $majors = $classes->pluck('major')->unique()->filter()->values();
                            @endphp

                            @foreach ($majors as $major)
                                <option value="{{ $major }}">{{ $major }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle" id="datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Kelas</th>
                                    <th>Jurusan</th>
                                    <th>Tingkat</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classes as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $item->name }}</td>
                                        <td>{{ $item->major ?? '-' }}</td>
                                        <td>{{ $item->grade_level ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('classes.students', $item->id) }}"
                                                    class="btn btn-sm btn-outline-info">
                                                    Siswa
                                                </a>

                                                <button class="btn btn-sm btn-outline-primary btn-edit"
                                                    data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                    data-major="{{ $item->major }}" data-level="{{ $item->grade_level }}"
                                                    data-bs-toggle="modal" data-bs-target="#modalEditClass">
                                                    Edit
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger btn-delete"
                                                    data-id="{{ $item->id }}">
                                                    Hapus
                                                </button>

                                                <form id="formDelete{{ $item->id }}"
                                                    action="{{ route('classes.delete', $item->id) }}" method="POST"
                                                    class="d-none">
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

    @include('admin.classes.modal.add')
    @include('admin.classes.modal.edit')
@endsection

@push('scripts')
    <script>
        const table = $('#datatable').DataTable({
            pageLength: 10,
            ordering: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ Kelas",
                zeroRecords: "Tidak ada data ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ Kelas",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });


        $('#filterJurusan').on('change', function() {
            table.column(2).search(this.value).draw();
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                document.querySelector('#formEditClass').action = "/classes/update/" + id;
                document.querySelector('#editName').value = this.dataset.name;
                document.querySelector('#editMajor').value = this.dataset.major;
                document.querySelector('#editLevel').value = this.dataset.level;
            });
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete')) {
                e.preventDefault();
                const id = e.target.closest('.btn-delete').dataset.id;

                Swal.fire({
                    title: "Hapus Data?",
                    text: "Data yang dihapus tidak dapat dikembalikan.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, hapus",
                    cancelButtonText: "Batal"
                }).then(result => {
                    if (result.isConfirmed) {
                        document.getElementById('formDelete' + id).submit();
                    }
                });
            }
        });
    </script>
@endpush
