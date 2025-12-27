@extends('template')

@section('content')
    <div class="container">
        <div class="page-inner">

            <div class="d-flex align-items-left flex-column flex-md-row justify-content-between pt-2 pb-3 mt-3">
                <div>
                    <h3 class="fw-bold mb-1">Academic Year</h3>
                    <span class="text-muted">Kelola tahun ajaran dengan mudah</span>
                </div>
            </div>

            {{-- Alert --}}
            @if (session('success'))
                <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
            @endif

            {{-- CARD --}}
            <div class="card shadow-lg border-0 mt-2" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 fw-semibold">Daftar Tahun Ajaran</h4>

                    <button class="btn btn-primary shadow-sm px-3 py-3" data-bs-toggle="modal" data-bs-target="#modalAddAY">
                        <i class="fa fa-plus me-1"></i> Tambah Tahun Ajaran
                    </button>
                </div>

                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table align-middle" id="datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tahun</th>
                                    <th>Status</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $item->year }}</td>
                                        <td>
                                            @if ($item->is_active)
                                                <span class="badge bg-success px-3 py-2">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary px-3 py-2">Tidak</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex gap-2">

                                                {{-- TOGGLE --}}
                                                <form action="{{ route('academic-years.toggle', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        class="btn btn-sm {{ $item->is_active ? 'btn-danger' : 'btn-success' }}">
                                                        {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>

                                                {{-- EDIT --}}
                                                <button class="btn btn-sm btn-outline-primary btn-edit"
                                                    data-id="{{ $item->id }}" data-year="{{ $item->year }}"
                                                    data-bs-toggle="modal" data-bs-target="#modalEditAY">Edit</button>

                                                {{-- DELETE --}}
                                                <a href="#" class="btn btn-sm btn-outline-danger btn-delete"
                                                    data-id="{{ $item->id }}">Hapus</a>

                                                <form id="formDelete{{ $item->id }}"
                                                    action="{{ route('academic-years.delete', $item->id) }}" method="POST"
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

    @include('admin.academic_year.modal.add')
    @include('admin.academic_year.modal.edit')
@endsection

@push('scripts')
    {{-- DATA TABLE --}}
    <script>
         $(document).ready(function() {
            $('#datatable').DataTable({
                pageLength: 10,
                ordering: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ Tahun Ajaran",
                    zeroRecords: "Tidak ada data ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ Tahun Ajaran",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                document.querySelector('#formEditAY').action = "/academic-years/update/" + id;
                document.querySelector('#editYear').value = this.dataset.year;
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
