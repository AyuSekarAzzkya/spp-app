@extends('template')

@section('content')
    <div class="container py-4">
        <div class="page-inner">
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h3 class="fw-bold text-dark mb-1">Manajemen Siswa</h3>
                    <p class="text-secondary small mb-0">Total data terkelola dalam sistem</p>
                </div>
                <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-2 mt-3 mt-md-0">

                    <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data"
                        class="d-none d-lg-block">
                        @csrf
                        <div
                            class="input-group input-group-sm border rounded-3 bg-white shadow-sm px-1 py-1 align-items-center">
                            <span class="input-group-text border-0 bg-transparent text-success pe-1">
                                <i class="fas fa-file-excel"></i>
                            </span>
                            <input type="file" name="file" class="form-control border-0 bg-transparent shadow-none"
                                style="width: 140px; font-size: 11px;" required accept=".xlsx,.xls,.csv">
                            <button type="submit" class="btn btn-success rounded-3 px-3 fw-bold border-0"
                                style="font-size: 11px;">
                                IMPORT
                            </button>
                        </div>
                    </form>

                    {{-- Mobile Import Button --}}
                    <button type="button"
                        class="btn btn-outline-success border-2 d-lg-none shadow-sm fw-bold px-3 rounded-3"
                        data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-excel me-1"></i> Import
                    </button>

                    {{-- Tambah Siswa --}}
                    <a href="{{ route('students.create') }}"
                        class="btn btn-primary px-4 fw-bold shadow-sm d-flex align-items-center gap-2 border-0 rounded-3"
                        style="padding-top: 9px; padding-bottom: 9px;">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Siswa</span>
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="datatable">
                            <thead class="bg-light">
                                <tr class="text-secondary small text-uppercase fw-bold">
                                    <th class="ps-4 py-3 border-0" width="5%">#</th>
                                    <th class="border-0">Identitas</th>
                                    <th class="border-0">Nama Lengkap</th>
                                    <th class="border-0 text-center">JK</th>
                                    <th class="border-0">Kelas</th>
                                    <th class="border-0">TA</th>
                                    <th class="border-0 text-center">Status</th>
                                    <th class="border-0 text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-dark small border-top-0">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Import --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="importModalLabel">
                        <i class="fas fa-file-excel text-success me-2"></i> Import Data Siswa
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="p-3 bg-light rounded-3 mb-3">
                            <small class="text-muted d-block mb-2">Petunjuk:</small>
                            <ul class="small text-secondary ps-3 mb-0">
                                <li>Format file harus <strong>.xlsx, .xls,</strong> atau <strong>.csv</strong></li>
                                <li>Pastikan header kolom sesuai dengan template sistem.</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label class="form-label small fw-bold">Pilih File Excel</label>
                            <input type="file" name="file" class="form-control form-control-lg rounded-3 fs-6"
                                required accept=".xlsx,.xls,.csv">
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 d-grid">
                        <button type="submit" class="btn btn-success btn-lg rounded-3 fw-bold shadow-sm">
                            <i class="fas fa-upload me-2"></i> Upload & Import
                        </button>
                        <button type="button" class="btn btn-link text-secondary text-decoration-none btn-sm mt-1"
                            data-bs-dismiss="modal">
                            Batal
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
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'ps-4'
                    },
                    {
                        data: null,
                        render: d =>
                            `<div><div class="fw-bold text-dark">${d.nis}</div><div class="text-muted" style="font-size:10px">NISN: ${d.nisn}</div></div>`
                    },
                    {
                        data: 'name',
                        name: 'name',
                        className: 'fw-semibold text-dark'
                    },
                    {
                        data: 'gender',
                        className: 'text-center',
                        render: d =>
                            `<span class="badge bg-light text-secondary border fw-normal px-2">${d}</span>`
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
                        className: 'text-center pe-4'
                    },
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Cari data...",
                    lengthMenu: "_MENU_ siswa per halaman",
                    paginate: {
                        previous: '<i class="fas fa-angle-left"></i>',
                        next: '<i class="fas fa-angle-right"></i>'
                    }
                },
                drawCallback: function() {
                    $('.dataTables_filter input').addClass(
                        'form-control form-control-sm border-0 bg-light shadow-none px-3 rounded-pill'
                    ).css('width', '200px');
                    $('.dataTables_length select').addClass(
                        'form-select form-select-sm border-0 bg-light shadow-none rounded-3');
                    $('.dataTables_paginate').addClass('mt-3 mb-3 me-3 small');
                    $('.dataTables_info').addClass('mt-3 mb-3 ms-4 small text-muted');
                    $('.pagination').addClass('pagination-sm');
                }
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                Swal.fire({
                    title: "Hapus Data?",
                    text: "Tindakan ini permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545",
                    cancelButtonColor: "#adb5bd",
                    confirmButtonText: "Ya, Hapus",
                    cancelButtonText: "Batal",
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-danger px-4 mx-2',
                        cancelButton: 'btn btn-light px-4 mx-2'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(`#deleteForm${id}`).submit();
                    }
                });
            });
        });
    </script>
@endpush
