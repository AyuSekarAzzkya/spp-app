@extends('template')

@section('content')
    <div class="container">
        <div class="page-inner">

            <div class="d-flex align-items-left flex-column flex-md-row justify-content-between pt-2 pb-3 mt-3">
                <div>
                    <h3 class="fw-bold mb-1">Daftar Siswa</h3>
                    <span class="text-muted">Kelola data siswa dengan mudah</span>
                </div>
            </div>

            {{-- Alert --}}
            @if (session('success'))
                <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
            @endif

            {{-- Card --}}
            <div class="card shadow-lg border-0 mt-2" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <h4 class="card-title mb-0 fw-semibold">Daftar Siswa</h4>

                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data"
                                class="d-flex gap-2 align-items-center">
                                @csrf
                                <input type="file" name="file" class="form-control" required
                                    accept=".xlsx,.xls,.csv">
                                <button type="submit" class="btn btn-success btn-sm shadow-sm">
                                    <i class="fa fa-upload"></i> Import
                                </button>
                            </form>

                            <a href="{{ route('students.create') }}" class="btn btn-primary shadow-sm btn-sm">
                                <i class="fa fa-plus me-1"></i> Tambah Siswa
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table align-middle" id="datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>NIS</th>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Status</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('students.data') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nis',
                    name: 'nis'
                },
                {
                    data: 'nisn',
                    name: 'nisn'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'gender_text',
                    name: 'gender'
                },
                {
                    data: 'class_name',
                    name: 'studentClass.name'
                },

                {
                    data: 'year_name',
                    name: 'academicYear.year'
                },
                {
                    data: 'status_badge',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            pageLength: 10
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
                        document.getElementById('deleteForm' + id).submit();
                    }
                });
            }
        });
    </script>
@endpush
