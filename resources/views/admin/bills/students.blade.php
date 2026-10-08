@extends('template')

@section('content')
<div class="page-inner">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="page-header-title mb-1">Manajemen Tagihan SPP Siswa</h2>
            <p class="page-header-subtitle mb-0">Kelola distribusi lembar tagihan bulanan dan generate tagihan otomatis seluruh siswa aktif.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#generateModal">
                <i class="mdi mdi-cogs me-1"></i> Generate Tagihan SPP
            </button>
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

    {{-- Info Cards (Academic Year & SPP Rate) --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card p-3 mb-0">
                <div class="d-flex align-items-center">
                    <div class="stat-icon-wrapper stat-icon-navy me-3">
                        <i class="mdi mdi-calendar-check"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Tahun Ajaran Aktif</small>
                        <h4 class="fw-bold mb-0 text-navy">{{ $activeYear->year }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card p-3 mb-0">
                <div class="d-flex align-items-center">
                    <div class="stat-icon-wrapper stat-icon-orange me-3">
                        <i class="mdi mdi-cash-multiple"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Tarif SPP Standar</small>
                        <h4 class="fw-bold mb-0 text-dark">
                            @if ($sppRates->count())
                                Rp {{ number_format($sppRates->first()->amount, 0, ',', '.') }}
                            @else
                                <span class="text-danger small">Belum diatur</span>
                            @endif
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Students Bill List --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="d-flex align-items-center fw-bold">
                <i class="mdi mdi-account-cash-outline me-2" style="color: var(--navy-primary); font-size: 1.2rem;"></i>
                Daftar Siswa & Lembar Tagihan
            </span>
            <span class="badge badge-secondary">{{ count($students) }} Siswa</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="billsTable">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Tahun Ajaran</th>
                            <th class="text-center" style="width: 140px;">Status Tagihan</th>
                            <th class="text-center" style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $idx => $student)
                            <tr>
                                <td class="text-muted small ps-3">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-2" style="width: 36px; height: 36px; background: var(--navy-primary); font-size: 0.85rem;">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $student->name }}</div>
                                            <small class="text-muted">NIS: {{ $student->nis }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $student->class->name ?? '-' }}</td>
                                <td>{{ $student->academicYear->year ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        @if(($student->unpaid_bills_count ?? 0) > 0)
                                            <span class="badge badge-unpaid">{{ $student->unpaid_bills_count }} Belum Lunas</span>
                                        @else
                                            <span class="badge badge-lunas"><i class="mdi mdi-check"></i> Semua Lunas</span>
                                        @endif
                                        <small class="text-muted">{{ $student->paid_bills_count ?? 0 }} Lunas</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('billing.index', $student->id) }}" class="btn btn-sm btn-primary text-nowrap">
                                        <i class="mdi mdi-clipboard-text-outline me-1"></i> Buka Tagihan
                                    </a>
                                </td>
                            </tr>
                        @empty
                            {{-- DataTables menangani tampilan kosong secara otomatis lewat language setting --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Generate Tagihan --}}
<div class="modal fade" id="generateModal" tabindex="-1" aria-labelledby="generateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center" id="generateModalLabel">
                    <i class="mdi mdi-cogs text-white me-2 fs-5"></i> Generate Tagihan SPP Bulanan
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bills.generateAll') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">
                        Fitur ini akan membuat tagihan SPP untuk seluruh siswa aktif pada bulan dan tahun yang dipilih. Sistem bersifat <strong>idempotent</strong> (tagihan yang sudah ada tidak akan diduplikasi).
                    </p>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark">Bulan Tagihan</label>
                            <select name="month" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark">Tahun</label>
                            <input type="number" name="year" class="form-control" value="{{ now()->year }}" required min="2020" max="2050">
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <small class="text-muted d-block">
                            <i class="mdi mdi-information-outline me-1"></i> Tanggal jatuh tempo otomatis ditetapkan pada tanggal <strong>10</strong> setiap bulannya.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center">
                        <i class="mdi mdi-play me-1"></i> Generate Sekarang
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
        $('#billsTable').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "Cari Siswa:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data siswa tidak ditemukan",
                emptyTable: "Belum ada data siswa terdaftar",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data tersedia",
                paginate: {
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                    next: '<i class="mdi mdi-chevron-right"></i>'
                }
            }
        });
    });
</script>
@endpush
