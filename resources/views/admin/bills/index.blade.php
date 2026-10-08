@extends('template')

@section('content')
<div class="page-inner">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('billing.students') }}" class="text-decoration-none" style="color: var(--orange-brand);">Tagihan Siswa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $student->name }}</li>
                </ol>
            </nav>
            <h2 class="page-header-title mb-1">Lembar Tagihan SPP Siswa</h2>
            <p class="page-header-subtitle mb-0">Rincian status pembayaran seluruh bulan dalam tahun ajaran berjalan.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('billing.students') }}" class="btn btn-secondary d-flex align-items-center">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar Siswa
            </a>
            <a href="{{ route('students.detail', $student->id) }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="mdi mdi-account-eye-outline me-1"></i> Profil Siswa
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

    {{-- Student Summary Card --}}
    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-3 shadow-sm" style="width: 56px; height: 56px; background: var(--navy-primary); font-size: 1.4rem;">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1">{{ $student->name }}</h4>
                        <div class="text-muted small">
                            NIS: <strong>{{ $student->nis }}</strong> • Kelas: <strong>{{ $student->class->name ?? '-' }}</strong> • Tahun Ajaran: <strong>{{ $activeYear->year ?? '-' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <div class="p-2 px-3 bg-light rounded-3 border text-center">
                        <small class="text-muted d-block small">Tarif SPP Bulanan</small>
                        <span class="fw-bold text-navy">Rp {{ number_format($sppRate->amount ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-2 px-3 bg-light rounded-3 border text-center">
                        <small class="text-muted d-block small">Sudah Lunas</small>
                        <span class="fw-bold text-success">{{ $bills->where('status', 'paid')->count() }} Bulan</span>
                    </div>
                    <div class="p-2 px-3 bg-light rounded-3 border text-center">
                        <small class="text-muted d-block small">Belum Lunas</small>
                        <span class="fw-bold text-danger">{{ $bills->where('status', 'unpaid')->count() }} Bulan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bills Table Card --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="d-flex align-items-center fw-bold">
                <i class="mdi mdi-calendar-month-outline me-2" style="color: var(--orange-brand); font-size: 1.2rem;"></i>
                Lembar Tagihan SPP Bulanan
            </span>
            <span class="badge badge-secondary">{{ $bills->count() }} Tagihan Terdaftar</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="datatable">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th>Bulan & Tahun</th>
                            <th>Jatuh Tempo</th>
                            <th>Nominal Tarif</th>
                            <th class="text-center" style="width: 140px;">Status Tagihan</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bills as $idx => $bill)
                            <tr>
                                <td class="text-muted small ps-3">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F') }} {{ $bill->year }}
                                    </div>
                                    <small class="text-muted">SPP Wajib</small>
                                </td>
                                <td>
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($bill->due_date);
                                        $isOverdue = $bill->status === 'unpaid' && $dueDate->isPast();
                                    @endphp
                                    <span class="{{ $isOverdue ? 'text-danger fw-semibold' : 'text-dark' }} small">
                                        {{ $dueDate->translatedFormat('d M Y') }}
                                    </span>
                                    @if($isOverdue)
                                        <div class="small text-danger"><i class="mdi mdi-alert-circle"></i> Terlewat tempo</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">
                                        Rp {{ number_format($bill->sppRate->amount ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($bill->status === 'paid')
                                        <span class="badge badge-lunas">
                                            <i class="mdi mdi-check"></i> Lunas
                                        </span>
                                    @else
                                        <span class="badge badge-unpaid">
                                            <i class="mdi mdi-clock-outline"></i> Belum Lunas
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($bill->status === 'paid')
                                        <a href="{{ route('admin.bills.show', $bill->id) }}" class="btn btn-sm btn-outline-secondary text-nowrap">
                                            <i class="mdi mdi-receipt me-1"></i> Rincian Bayar
                                        </a>
                                    @else
                                        <span class="text-muted small">Menunggu Pembayaran</span>
                                    @endif
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#datatable').DataTable({
            responsive: true,
            pageLength: 12,
            language: {
                search: "Cari Tagihan:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data tagihan tidak ditemukan",
                emptyTable: "Belum ada lembar tagihan untuk siswa ini",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data tagihan",
                paginate: {
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                    next: '<i class="mdi mdi-chevron-right"></i>'
                }
            }
        });
    });
</script>
@endpush
