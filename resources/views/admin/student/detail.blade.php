@extends('template')

@section('content')
<div class="page-inner">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}" class="text-decoration-none" style="color: var(--orange-brand);">Siswa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail: {{ $student->name }}</li>
                </ol>
            </nav>
            <h2 class="page-header-title mb-1">Profil & Informasi Siswa</h2>
            <p class="page-header-subtitle mb-0">Rincian data identitas, rombel, akun login, dan status tagihan SPP siswa.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2">
            <a href="{{ route('students.index') }}" class="btn btn-secondary d-flex align-items-center">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="mdi mdi-pencil me-1"></i> Edit Siswa
            </a>
            <a href="{{ route('billing.index', $student->id) }}" class="btn btn-primary d-flex align-items-center">
                <i class="mdi mdi-receipt me-1"></i> Lembar Tagihan SPP
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Profile Summary Card (Left Column) --}}
        <div class="col-12 col-lg-4">
            <div class="card mb-4 text-center">
                <div class="card-body p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold mx-auto mb-3 shadow-sm" style="width: 84px; height: 84px; font-size: 2.2rem; background: var(--navy-primary);">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>

                    <h4 class="fw-bold text-dark mb-1">{{ $student->name }}</h4>
                    <div class="text-muted small mb-2">NIS: <strong>{{ $student->nis }}</strong> • NISN: <strong>{{ $student->nisn ?? '-' }}</strong></div>

                    <div class="mb-3">
                        @if($student->status === 'active')
                            <span class="badge badge-lunas"><i class="mdi mdi-check-circle-outline"></i> Siswa Aktif</span>
                        @else
                            <span class="badge badge-secondary"><i class="mdi mdi-close-circle-outline"></i> Siswa Nonaktif</span>
                        @endif
                    </div>

                    <hr class="my-3">

                    <div class="text-start small">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Kelas / Rombel:</span>
                            <span class="fw-bold text-dark">{{ $student->class->name ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Tahun Ajaran:</span>
                            <span class="fw-bold text-dark">{{ $student->academicYear->year ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Jenis Kelamin:</span>
                            <span class="fw-bold text-dark">{{ $student->gender === 'L' ? 'Laki-laki' : ($student->gender === 'P' ? 'Perempuan' : '-') }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Terdaftar Pada:</span>
                            <span class="fw-bold text-dark">{{ $student->created_at ? $student->created_at->translatedFormat('d M Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Bill Card --}}
            <div class="card border-0 shadow-sm" style="background-color: #f8fafc; border: 1px solid #e2e8f0 !important;">
                <div class="card-header bg-white border-bottom">
                    <span class="d-flex align-items-center fw-bold text-navy">
                        <i class="mdi mdi-wallet-outline me-2" style="color: var(--orange-brand);"></i>
                        Status Administrasi SPP
                    </span>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-3 bg-white rounded border">
                                <small class="text-muted d-block small">Tagihan Lunas</small>
                                <h5 class="fw-bold text-success mb-0">{{ $student->bills()->where('status', 'paid')->count() }} Bulan</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-white rounded border">
                                <small class="text-muted d-block small">Belum Lunas</small>
                                <h5 class="fw-bold text-danger mb-0">{{ $student->bills()->where('status', 'unpaid')->count() }} Bulan</h5>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('billing.index', $student->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="mdi mdi-clipboard-list-outline me-1"></i> Buka Lembar Tagihan Siswa
                    </a>
                </div>
            </div>
        </div>

        {{-- Detail Information (Right Column) --}}
        <div class="col-12 col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="mdi mdi-card-account-details-outline me-2" style="color: var(--navy-primary); font-size: 1.2rem;"></i>
                    <span class="fw-bold">Informasi Lengkap Siswa & Kontak</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <small class="text-muted d-block">Akun Email Login Siswa</small>
                            <div class="fw-bold text-dark mt-1">
                                <i class="mdi mdi-email-outline me-1 text-primary"></i>
                                {{ $student->user->email ?? 'Belum memiliki akun portal' }}
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <small class="text-muted d-block">Nomor Telepon / WhatsApp</small>
                            <div class="fw-bold text-dark mt-1">
                                <i class="mdi mdi-phone-outline me-1 text-success"></i>
                                {{ $student->phone ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <small class="text-muted d-block mb-1">Alamat Tempat Tinggal</small>
                        <div class="p-3 bg-light rounded-3 border" style="border-left: 4px solid var(--orange-brand) !important;">
                            <span class="text-dark">{{ $student->address ?? 'Alamat belum diisi.' }}</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <small class="text-muted d-block">Tingkat / Jenjang</small>
                            <div class="fw-bold text-dark mt-1">Tingkat {{ $student->class->grade_level ?? '-' }}</div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <small class="text-muted d-block">Program Keahlian / Jurusan</small>
                            <div class="fw-bold text-dark mt-1">{{ $student->class->major ?? 'Umum' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Bills Preview --}}
            <div class="card mb-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center fw-bold">
                        <i class="mdi mdi-history me-2" style="color: var(--orange-brand); font-size: 1.2rem;"></i>
                        Daftar Tagihan SPP Terakhir
                    </span>
                    <a href="{{ route('billing.index', $student->id) }}" class="btn btn-sm btn-outline-secondary">
                        Semua Tagihan <i class="mdi mdi-chevron-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 w-100" id="studentBillsTable">
                            <thead>
                                <tr>
                                    <th class="ps-3">Bulan</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Nominal</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($student->bills()->with('sppRate')->orderBy('year', 'desc')->orderBy('month', 'desc')->take(6)->get() as $bill)
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark">
                                            {{ \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F') }} {{ $bill->year }}
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($bill->due_date)->translatedFormat('d M Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">
                                                Rp {{ number_format($bill->sppRate->amount ?? 0, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($bill->status === 'paid')
                                                <span class="badge badge-lunas"><i class="mdi mdi-check"></i> Lunas</span>
                                            @else
                                                <span class="badge badge-unpaid"><i class="mdi mdi-clock-outline"></i> Belum Lunas</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.bills.show', $bill->id) }}" class="btn btn-sm btn-secondary">
                                                Detail
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
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('#studentBillsTable').length) {
            $('#studentBillsTable').DataTable({
                responsive: true,
                paging: false,
                info: false,
                language: {
                    search: "Cari:",
                    zeroRecords: "Data tidak ditemukan",
                    emptyTable: "Belum ada tagihan untuk siswa ini"
                }
            });
        }
    });
</script>
@endpush
