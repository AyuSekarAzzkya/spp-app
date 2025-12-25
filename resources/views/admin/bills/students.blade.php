@extends('template')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <div class="mb-3 mb-md-0">
                                <h3 class="fw-bold mb-1">
                                    <i class="bi bi-receipt-cutoff me-2"></i>Manajemen Tagihan Siswa
                                </h3>
                                <p class="mb-0 opacity-75">Kelola dan generate tagihan SPP bulanan untuk seluruh siswa.</p>
                            </div>
                            <form action="{{ route('bills.generateAll') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-light btn-lg fw-bold shadow-sm px-4">
                                    <i class="bi bi-gear-fill me-2"></i>Generate Tagihan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="bg-info-subtle text-info rounded-circle p-3 me-3">
                            <i class="bi bi-calendar-check fs-3"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.75rem;">TAHUN AJARAN
                                AKTIF</small>
                            <span class="fs-5 fw-bold text-dark">{{ $activeYear->year }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="bg-success-subtle text-success rounded-circle p-3 me-3">
                            <i class="bi bi-tags fs-3"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.75rem;">TARIF SPP
                                STANDAR</small>
                            <span class="fs-5 fw-bold text-dark">
                                @if ($sppRates->count())
                                    Rp{{ number_format($sppRates->first()->amount, 0, ',', '.') }}
                                @else
                                    <span class="text-danger">Belum diatur</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-people-fill text-primary me-2 fs-5"></i>
                    <h5 class="card-title mb-0 fw-bold">Daftar Siswa</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="students-table" class="table table-hover align-middle border-light">
                        <thead class="bg-light">
                            <tr class="text-secondary">
                                <th class="ps-3" width="5%">NO</th>
                                <th>NAMA LENGKAP</th>
                                <th>KELAS</th>
                                <th>NIS</th>
                                <th class="text-center" width="15%">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $s)
                                <tr>
                                    <td class="ps-3 fw-medium text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $s->name }}</div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                                            {{ $s->class->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td><code class="text-dark fw-bold">{{ $s->nis }}</code></td>
                                    <td class="text-center">
                                        <a href="{{ route('billing.index', $s->id) }}"
                                            class="btn btn-outline-primary btn-sm rounded-pill px-3 transition-all hover-shadow">
                                            <i class="bi bi-eye me-1"></i> Lihat Tagihan
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#students-table').DataTable({
                pageLength: 10,
                ordering: true,
                language: {
                    search: "",
                    searchPlaceholder: "Cari nama atau NIS siswa...",
                    lengthMenu: "_MENU_ siswa per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 siswa",
                    paginate: {
                        previous: "<i class='bi bi-chevron-left'></i>",
                        next: "<i class='bi bi-chevron-right'></i>"
                    }
                },
                dom: "<'row mb-3'<'col-md-6'l><'col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
            });

            $('.dataTables_filter input').addClass('form-control shadow-sm border-light-subtle px-3 py-2 w-100');
            $('.dataTables_length select').addClass('form-select shadow-sm border-light-subtle');
        });
    </script>
@endpush
