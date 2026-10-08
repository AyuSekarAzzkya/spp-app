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
            <p class="page-header-subtitle mb-0">Ringkasan performa administrasi dan keuangan SPP sekolah hari ini.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <form action="{{ route('bills.generateAll') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin meng-generate tagihan bulan ini untuk seluruh siswa aktif?');">
                @csrf
                <button type="submit" class="btn btn-navy d-flex align-items-center">
                    <i class="mdi mdi-cogs me-1"></i> Generate Tagihan
                </button>
            </form>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-primary d-flex align-items-center">
                <i class="mdi mdi-shield-check me-1"></i> Verifikasi Pembayaran
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

    {{-- SPP Assistant Command Bar (Option B) --}}
    <div class="spp-dashboard-command-bar mb-4" role="button" data-bs-toggle="modal" data-bs-target="#aiAgentModal" tabindex="0" title="Buka SPP Assistant (Ctrl+K)">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 text-truncate">
                <span class="spp-command-sparkle">✦</span>
                <span class="spp-command-text">Tanya SPP Assistant, cari data, atau buat laporan</span>
            </div>
            <kbd class="command-kbd-badge command-bar-kbd ms-2 flex-shrink-0">Ctrl K</kbd>
        </div>
    </div>

    {{-- Main KPI Stat Cards --}}
    <div class="row g-3 mb-4 dashboard-row">
        {{-- Card 1: Pemasukan Bulan Ini --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card dashboard-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Pemasukan Bulan Ini</div>
                        <div class="stat-value text-primary" style="color: var(--orange-brand) !important;">
                            Rp {{ number_format($totalRevenueMonth, 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-1">
                            Total: Rp {{ number_format($totalRevenueAll, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-orange">
                        <i class="mdi mdi-cash-multiple"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Tunggakan --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card dashboard-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Tunggakan SPP</div>
                        <div class="stat-value text-danger">
                            Rp {{ number_format($totalArrears, 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-1">
                            <span class="badge badge-unpaid">{{ $unpaidBillsCount }} tagihan belum lunas</span>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-danger">
                        <i class="mdi mdi-alert-circle-outline"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Siswa Terdaftar --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card dashboard-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Siswa Terdaftar</div>
                        <div class="stat-value">{{ $totalStudents }}</div>
                        <div class="small text-muted mt-1">
                            <span class="text-success fw-bold">{{ $activeStudents }} aktif</span> di {{ $totalClasses }} kelas
                        </div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-navy">
                        <i class="mdi mdi-school"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Pending Approval --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card dashboard-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Menunggu Verifikasi</div>
                        <div class="stat-value" style="color: var(--warning) !important;">
                            {{ $pendingApprovals }}
                        </div>
                        <div class="small text-muted mt-1">
                            <a href="{{ route('admin.payments.index') }}" class="text-decoration-none fw-semibold" style="color: var(--orange-brand);">
                                Periksa antrean <i class="mdi mdi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-warning">
                        <i class="mdi mdi-clock-alert-outline"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Metric Bar --}}
    <div class="row g-3 mb-4 dashboard-row">
        <div class="col-md-4">
            <div class="card dashboard-card p-3 mb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Rasio Tagihan Lunas</span>
                        <h5 class="fw-bold mb-0 text-navy mt-1">
                            {{ $totalBills > 0 ? round(($paidBillsCount / $totalBills) * 100, 1) : 0 }}%
                        </h5>
                    </div>
                    <span class="badge badge-lunas">{{ $paidBillsCount }} dari {{ $totalBills }} Tagihan</span>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar" style="width: {{ $totalBills > 0 ? ($paidBillsCount / $totalBills) * 100 : 0 }}%; background-color: var(--success);"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card dashboard-card p-3 mb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Total Pembayaran Berhasil</span>
                        <h5 class="fw-bold mb-0 text-navy mt-1">{{ $totalPayments - $pendingApprovals - $rejectedPaymentsCount }} Transaksi</h5>
                    </div>
                    <span class="badge badge-lunas">Approved</span>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar" style="width: 100%; background-color: var(--orange-brand);"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card dashboard-card p-3 mb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Transaksi Ditolak (Reject)</span>
                        <h5 class="fw-bold mb-0 text-danger mt-1">{{ $rejectedPaymentsCount }} Transaksi</h5>
                    </div>
                    <span class="badge badge-ditolak">Perlu Re-upload</span>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-danger" style="width: {{ $totalPayments > 0 ? ($rejectedPaymentsCount / $totalPayments) * 100 : 0 }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="row g-4 mb-4 dashboard-row">
        {{-- Revenue Chart (6 Months) --}}
        <div class="col-12 col-lg-8">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center fw-semibold">
                        <i class="mdi mdi-chart-line me-2" style="color: var(--orange-brand);"></i> Tren Penerimaan SPP (6 Bulan Terakhir)
                    </span>
                    <span class="badge badge-navy">Data Terverifikasi</span>
                </div>
                <div class="card-body">
                    @php
                        $hasRevenue = count($monthlyRevenue) > 0 && collect($monthlyRevenue)->sum('total') > 0;
                    @endphp
                    @if($hasRevenue)
                        <div class="dashboard-chart-wrapper">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    @else
                        <div class="dashboard-chart-wrapper d-flex align-items-center justify-content-center">
                            <div class="chart-empty-state">
                                <div class="empty-icon"><i class="mdi mdi-chart-line"></i></div>
                                <p>Belum ada data pembayaran</p>
                                <span>Data akan muncul setelah transaksi tersedia.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Class Distribution Doughnut --}}
        <div class="col-12 col-lg-4">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center fw-semibold">
                        <i class="mdi mdi-chart-donut me-2" style="color: var(--navy-primary);"></i> Komposisi Siswa per Kelas
                    </span>
                    <span class="badge badge-secondary">{{ $totalClasses }} Kelas</span>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    @php
                        $hasClassData = count($classDistribution) > 0 && collect($classDistribution)->sum('students_count') > 0;
                    @endphp
                    @if($hasClassData)
                        <div class="dashboard-chart-wrapper">
                            <canvas id="classChart"></canvas>
                        </div>
                        <p class="text-muted small text-center mt-3 mb-0">
                            <i class="mdi mdi-information-outline me-1"></i> Persebaran siswa aktif per rombongan belajar.
                        </p>
                    @else
                        <div class="dashboard-chart-wrapper d-flex align-items-center justify-content-center">
                            <div class="chart-empty-state">
                                <div class="empty-icon"><i class="mdi mdi-chart-donut"></i></div>
                                <p>Belum ada data siswa di kelas</p>
                                <span>Data akan muncul setelah siswa dimasukkan ke rombongan belajar.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Payments Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="d-flex align-items-center">
                <i class="mdi mdi-history me-2" style="color: var(--navy-primary);"></i> Transaksi Pembayaran Terkini
            </span>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary">
                Lihat Semua Pembayaran <i class="mdi mdi-chevron-right"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="recentPaymentsTable">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Tanggal</th>
                            <th>Total Tagihan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $idx => $payment)
                            <tr>
                                <td class="text-muted small ps-3">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $payment->student->name ?? '-' }}</div>
                                    <small class="text-muted">NIS: {{ $payment->student->nis ?? '-' }}</small>
                                </td>
                                <td>{{ $payment->student->class->name ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="fw-bold" style="color: var(--navy-primary);">
                                        Rp {{ number_format($payment->total_amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    @if($payment->status === 'approved')
                                        <span class="badge badge-lunas"><i class="mdi mdi-check"></i> Approved</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="badge badge-pending"><i class="mdi mdi-clock-outline"></i> Pending</span>
                                    @else
                                        <span class="badge badge-ditolak"><i class="mdi mdi-close"></i> Rejected</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-secondary">
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
        // Line Chart: Revenue Trend (Orange Brand with subtle fill)
        const revenueData = {!! json_encode($monthlyRevenue) !!};
        const ctxRev = document.getElementById('revenueChart');
        if (ctxRev && revenueData.length > 0) {
            const ctx = ctxRev.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 280);
            gradient.addColorStop(0, 'rgba(248, 123, 27, 0.28)');
            gradient.addColorStop(1, 'rgba(248, 123, 27, 0.01)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: revenueData.map(item => item.label),
                    datasets: [{
                        label: 'Pemasukan (Rp)',
                        data: revenueData.map(item => item.total),
                        borderColor: '#F87B1B',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#F87B1B',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.35,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Pemasukan: Rp ' + Number(context.raw).toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                color: '#64748b',
                                callback: val => 'Rp ' + Number(val).toLocaleString('id-ID')
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b' }
                        }
                    }
                }
            });
        }

        // Doughnut Chart: Class Distribution (Navy & Complementary Palette)
        const classData = {!! json_encode($classDistribution) !!};
        const ctxClass = document.getElementById('classChart');
        if (ctxClass && classData.length > 0) {
            new Chart(ctxClass.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: classData.map(c => c.name),
                    datasets: [{
                        data: classData.map(c => c.students_count ?? 0),
                        backgroundColor: [
                            '#0F172A', '#F87B1B', '#16a34a', '#2563eb', 
                            '#334155', '#475569', '#f59e0b', '#64748b'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                color: '#334155',
                                font: { size: 11 }
                            }
                        }
                    }
                }
            });
        }

        // Initialize DataTables on Recent Payments Table
        if (typeof $.fn.DataTable !== 'undefined' && $('#recentPaymentsTable').length) {
            $('#recentPaymentsTable').DataTable({
                responsive: true,
                pageLength: 5,
                lengthMenu: [5, 10, 25],
                language: {
                    search: "Cari Transaksi:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data pembayaran tidak ditemukan",
                    emptyTable: "Belum ada transaksi pembayaran yang tercatat",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data transaksi",
                    paginate: {
                        previous: '<i class="mdi mdi-chevron-left"></i>',
                        next: '<i class="mdi mdi-chevron-right"></i>'
                    }
                }
            });
        }
    });
</script>
@endpush
