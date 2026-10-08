@extends('template')

@section('content')
<div class="page-inner">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="page-header-title mb-1">Manajemen Data Siswa</h2>
            <p class="page-header-subtitle mb-0">Kelola direktori siswa, rombongan belajar, tahun ajaran, dan akun portal SPP.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="mdi mdi-file-excel-outline text-success me-1 fs-5"></i> Import Excel
            </button>
            <a href="{{ route('students.create') }}" class="btn btn-primary d-flex align-items-center">
                <i class="mdi mdi-account-plus me-1"></i> Tambah Siswa Baru
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main DataTable Card --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="d-flex align-items-center fw-bold">
                <i class="mdi mdi-account-group me-2" style="color: var(--orange-brand); font-size: 1.2rem;"></i>
                Direktori Siswa Terdaftar
            </span>
            <span class="badge badge-navy">Server-side DataTables</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="datatable">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th>Identitas (NIS/NISN)</th>
                            <th>Nama Lengkap</th>
                            <th class="text-center" style="width: 70px;">JK</th>
                            <th>Kelas</th>
                            <th>Tahun Ajaran</th>
                            <th class="text-center" style="width: 100px;">Status</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Import Excel --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center" id="importModalLabel">
                    <i class="mdi mdi-file-excel text-white me-2 fs-5"></i> Import Data Siswa
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <small class="text-muted d-block fw-bold mb-2">Petunjuk Format Berkas:</small>
                        <ul class="small text-secondary ps-3 mb-0">
                            <li>Format file harus <strong>.xlsx, .xls,</strong> atau <strong>.csv</strong>.</li>
                            <li>Header kolom wajib: <code>name</code>, <code>nis</code>, <code>nisn</code>, <code>gender</code>, <code>class</code>, <code>grade level</code>, <code>major</code>, <code>academic year</code>.</li>
                            <li>Akun portal siswa (email & password) otomatis dibuatkan dari NIS.</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Pilih File Spreadsheet</label>
                        <input type="file" name="file" class="form-control" required accept=".xlsx,.xls,.csv">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center">
                        <i class="mdi mdi-upload me-1"></i> Mulai Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('students.data') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'ps-3 text-muted small'
                },
                {
                    data: 'nis',
                    name: 'nis',
                    render: function(data, type, row) {
                        return `<div>
                            <div class="fw-bold text-dark">${row.nis}</div>
                            <small class="text-muted">NISN: ${row.nisn ? row.nisn : '-'}</small>
                        </div>`;
                    }
                },
                {
                    data: 'name',
                    name: 'name',
                    className: 'fw-bold text-dark'
                },
                {
                    data: 'gender',
                    name: 'gender',
                    className: 'text-center',
                    render: function(d) {
                        const bg = d === 'L' ? 'badge-navy' : 'badge-secondary';
                        return `<span class="badge ${bg}">${d ? d : '-'}</span>`;
                    }
                },
                {
                    data: 'class_name',
                    name: 'class.name'
                },
                {
                    data: 'year_name',
                    name: 'academicYear.year'
                },
                {
                    data: 'status_badge',
                    name: 'status',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    className: 'text-center pe-3',
                    orderable: false,
                    searchable: false
                },
            ],
            language: {
                search: "Cari Siswa:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data siswa tidak ditemukan",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ siswa",
                infoEmpty: "Tidak ada data tersedia",
                paginate: {
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                    next: '<i class="mdi mdi-chevron-right"></i>'
                }
            }
        });

        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            Swal.fire({
                title: "Hapus / Arsipkan Siswa?",
                text: "Jika siswa memiliki riwayat pembayaran, status akan dinonaktifkan (soft delete) untuk melindungi integritas keuangan.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Ya, Lanjutkan",
                cancelButtonText: "Batal",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#deleteForm${id}`).submit();
                }
            });
        });
    });
</script>
@endpush
