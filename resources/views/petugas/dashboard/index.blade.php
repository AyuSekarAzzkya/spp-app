@extends('template')

@section('content')
<div class="page-inner">
    {{-- Header Dashboard --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            @php
                $hour = date('H');
                $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
            @endphp
            <h2 class="page-header-title mb-1">{{ $greeting }}, {{ Auth::user()->name }}!</h2>
            <p class="page-header-subtitle mb-0">Portal Operasional Petugas — Pantau antrean verifikasi, penerimaan setoran harian, dan monitoring tunggakan.</p>
        </div>
        <div class="petugas-action-buttons mt-3 mt-md-0 d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center" style="gap: 12px;">
            <a href="{{ route('admin.payments.index') }}" 
               class="btn btn-primary petugas-header-btn d-inline-flex align-items-center justify-content-center px-3 py-2 fw-semibold text-white shadow-sm"
               style="background-color: var(--orange-brand); border-color: var(--orange-brand); height: 42px; min-height: 42px; border-radius: 8px;">
                <i class="mdi mdi-shield-check me-2 fs-5"></i>
                <span>Verifikasi Pembayaran</span>
                @if($pendingPaymentsCount > 0)
                    <span class="badge bg-white text-dark ms-2 rounded-pill px-2 py-1" style="font-size: 0.75rem;">{{ $pendingPaymentsCount }}</span>
                @endif
            </a>
            <a href="{{ route('billing.students') }}" 
               class="btn btn-outline-secondary petugas-header-btn d-inline-flex align-items-center justify-content-center px-3 py-2 fw-semibold text-dark border shadow-sm"
               style="background-color: #ffffff; border-color: var(--border); color: var(--navy-primary) !important; height: 42px; min-height: 42px; border-radius: 8px;">
                <i class="mdi mdi-account-search-outline me-2 fs-5" style="color: var(--navy-primary);"></i>
                <span>Cek Tagihan Siswa</span>
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- SPP Assistant Command Bar --}}
    <div class="spp-dashboard-command-bar mb-4" role="button" data-bs-toggle="modal" data-bs-target="#aiAgentModal" tabindex="0" title="Buka SPP Assistant (Ctrl+K)">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 text-truncate">
                <span class="spp-command-sparkle">✦</span>
                <span class="spp-command-text">Tanya SPP Assistant, cari siswa, atau cek verifikasi</span>
            </div>
            <kbd class="command-kbd-badge command-bar-kbd ms-2 flex-shrink-0">Ctrl K</kbd>
        </div>
    </div>

    {{-- Pending Warning Banner --}}
    @if($pendingPaymentsCount > 0)
        <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center p-3" style="background-color: #fffbeb; border-left: 4px solid var(--warning) !important; border-radius: 12px;">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <div class="stat-icon-wrapper stat-icon-warning me-3" style="width: 40px; height: 40px; font-size: 1.2rem;">
                    <i class="mdi mdi-bell-ring-outline"></i>
                </div>
                <div>
                    <strong class="text-dark">Perhatian Petugas Loket:</strong>
                    <div class="text-muted small">Terdapat <span class="fw-bold text-dark">{{ $pendingPaymentsCount }} transaksi pembayaran</span> yang menunggu verifikasi bukti transfer Anda.</div>
                </div>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-primary text-nowrap">
                Buka Antrean Verifikasi <i class="mdi mdi-arrow-right ms-1"></i>
            </a>
        </div>
    @endif

    {{-- Main KPI Stat Cards (4 Columns) --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Pending Approval --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Menunggu Verifikasi</div>
                        <div class="stat-value" style="color: var(--warning) !important;">
                            {{ $pendingPaymentsCount }}
                        </div>
                        <div class="small text-muted mt-1">
                            <span class="badge badge-pending">{{ $pendingPaymentsCount }} perlu dicek</span>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-warning">
                        <i class="mdi mdi-clock-alert-outline"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Setoran Hari Ini --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Setoran Hari Ini</div>
                        <div class="stat-value text-success" style="color: var(--success) !important;">
                            Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-1">
                            {{ $todayTransactionsCount }} transaksi diajukan hari ini
                        </div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-success">
                        <i class="mdi mdi-cash-check"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Penerimaan Bulan Ini --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Penerimaan Bulan Ini</div>
                        <div class="stat-value text-primary" style="color: var(--orange-brand) !important;">
                            Rp {{ number_format($thisMonthRevenue, 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-1">
                            Periode: {{ now()->translatedFormat('F Y') }}
                        </div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-orange">
                        <i class="mdi mdi-wallet-outline"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Siswa Menunggak --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Siswa Jatuh Tempo</div>
                        <div class="stat-value text-danger">
                            {{ $arrearsCount }}
                        </div>
                        <div class="small text-muted mt-1">
                            <a href="{{ route('reports.arrears') }}" class="text-decoration-none text-danger fw-semibold">
                                Lihat daftar penunggak <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-danger">
                        <i class="mdi mdi-account-alert-outline"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Metrics Bar --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-3 mb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Total Pembayaran Disetujui</span>
                        <h5 class="fw-bold mb-0 text-navy mt-1">{{ $totalApproved }} Transaksi</h5>
                    </div>
                    <span class="badge badge-lunas"><i class="mdi mdi-check"></i> Approved</span>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: 100%;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 mb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Transaksi Ditolak (Re-upload)</span>
                        <h5 class="fw-bold mb-0 text-danger mt-1">{{ $totalRejected }} Transaksi</h5>
                    </div>
                    <span class="badge badge-ditolak"><i class="mdi mdi-close"></i> Rejected</span>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-danger" style="width: 100%;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 mb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Tanggal Operasional</span>
                        <h5 class="fw-bold mb-0 text-navy mt-1">{{ now()->translatedFormat('l, d F Y') }}</h5>
                    </div>
                    <span class="badge badge-navy"><i class="mdi mdi-calendar"></i> Hari Ini</span>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar" style="width: 100%; background-color: var(--navy-primary);"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section: Antrean Verifikasi Prioritas --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="d-flex align-items-center fw-bold">
                <i class="mdi mdi-clock-check-outline me-2" style="color: var(--orange-brand); font-size: 1.2rem;"></i>
                Antrean Pembayaran Menunggu Verifikasi
            </span>
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-pending">{{ $pendingPaymentsCount }} Antrean</span>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary">
                    Kelola Semua <i class="mdi mdi-chevron-right"></i>
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Waktu Pengajuan</th>
                            <th>Total Nominal</th>
                            <th>Metode / Bukti</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingPayments as $idx => $payment)
                            <tr>
                                <td class="text-muted small ps-3">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-2" style="width: 36px; height: 36px; background-color: var(--navy-primary); font-size: 0.85rem;">
                                            {{ strtoupper(substr($payment->student->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $payment->student->name ?? '-' }}</div>
                                            <small class="text-muted">NIS: {{ $payment->student->nis ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $payment->student->class->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-dark small d-block">{{ \Carbon\Carbon::parse($payment->created_at)->translatedFormat('d M Y, H:i') }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($payment->created_at)->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold" style="color: var(--navy-primary); font-size: 0.95rem;">
                                        Rp {{ number_format($payment->total_amount, 0, ',', '.') }}
                                    </span>
                                    <div class="small text-muted">{{ $payment->details->count() }} bulan SPP</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge badge-navy text-uppercase">{{ $payment->payment_method ?? 'Transfer' }}</span>
                                        @if($payment->latestProof)
                                            <span class="badge badge-secondary">v{{ $payment->latestProof->version ?? 1 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-primary text-nowrap">
                                        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> Verifikasi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="stat-icon-wrapper stat-icon-success mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.8rem;">
                                        <i class="mdi mdi-check-all"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Semua Beres! Tidak Ada Antrean Menunggu</h6>
                                    <p class="text-muted small mb-0">Seluruh bukti transfer siswa telah selesai diverifikasi oleh petugas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Section: Aktivitas Transaksi Terbaru (DataTables) --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="d-flex align-items-center fw-bold">
                <i class="mdi mdi-history me-2" style="color: var(--navy-primary); font-size: 1.2rem;"></i>
                Aktivitas Transaksi Terbaru
            </span>
            <span class="badge badge-secondary">10 Aktivitas Terkini</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="transactionTable" class="table table-hover align-middle mb-0 w-100">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Nominal</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivities as $idx => $item)
                            <tr>
                                <td class="text-muted small ps-3">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->student->name ?? '-' }}</div>
                                    <small class="text-muted">NIS: {{ $item->student->nis ?? '-' }}</small>
                                </td>
                                <td>{{ $item->student->class->name ?? '-' }}</td>
                                <td>
                                    <span class="fw-bold text-dark">
                                        Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-secondary text-uppercase fw-semibold">
                                        <i class="mdi mdi-credit-card-outline me-1"></i>{{ $item->payment_method ?? 'Transfer' }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->status === 'approved')
                                        <span class="badge badge-lunas"><i class="mdi mdi-check"></i> Approved</span>
                                    @elseif($item->status === 'pending')
                                        <span class="badge badge-pending"><i class="mdi mdi-clock-outline"></i> Pending</span>
                                    @else
                                        <span class="badge badge-ditolak"><i class="mdi mdi-close"></i> Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.payments.show', $item->id) }}" class="btn btn-sm btn-outline-secondary">
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#transactionTable').DataTable({
                "pageLength": 10,
                "responsive": true,
                "language": {
                    "search": "Cari Transaksi:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Data transaksi tidak ditemukan",
                    "emptyTable": "Belum ada aktivitas transaksi yang tercatat",
                    "info": "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data",
                    "paginate": {
                        "previous": '<i class="mdi mdi-chevron-left"></i>',
                        "next": '<i class="mdi mdi-chevron-right"></i>'
                    }
                }
            });
        }
    });
</script>
@endpush
