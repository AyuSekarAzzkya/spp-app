@extends('template')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width: 50px; height: 50px;">
                        <i class="bi bi-person-badge text-white fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">
                            @php
                                $hour = date('H');
                                $greeting =
                                    $hour < 12
                                        ? 'Selamat Pagi'
                                        : ($hour < 15
                                            ? 'Selamat Siang'
                                            : ($hour < 18
                                                ? 'Selamat Sore'
                                                : 'Selamat Malam'));
                            @endphp
                            {{ $greeting }}, {{ Auth::user()->name }}!
                        </h3>
                        <p class="text-muted mb-0">Selamat datang di sistem manajemen SPP. Berikut ringkasan performa sekolah
                            hari ini.</p>
                    </div>
                </div>
                <hr class="text-muted opacity-25">
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm custom-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-shape bg-primary-subtle text-primary rounded-pill px-3 py-2">
                                <i class="bi bi-wallet2 fs-5"></i>
                            </div>
                            <span
                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Bulan
                                Ini</span>
                        </div>
                        <p class="text-muted small fw-medium mb-1 uppercase text-uppercase tracking-wider">Total Pendapatan
                        </p>
                        <h3 class="fw-bold mb-0">Rp{{ number_format($totalRevenueMonth, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm custom-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-shape bg-danger-subtle text-danger rounded-pill px-3 py-2">
                                <i class="bi bi-shield-exclamation fs-5"></i>
                            </div>
                            <span class="text-danger small fw-bold">Peringatan</span>
                        </div>
                        <p class="text-muted small fw-medium mb-1 text-uppercase tracking-wider">Total Tunggakan</p>
                        <h3 class="fw-bold mb-0">Rp{{ number_format($totalArrears, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm custom-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-shape bg-success-subtle text-success rounded-pill px-3 py-2">
                                <i class="bi bi-mortarboard fs-5"></i>
                            </div>
                        </div>
                        <p class="text-muted small fw-medium mb-1 text-uppercase tracking-wider">Siswa Terdaftar</p>
                        <h3 class="fw-bold mb-0">{{ $totalStudents }} <span class="fs-6 fw-normal text-muted">Siswa</span>
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm custom-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-shape bg-warning-subtle text-warning rounded-pill px-3 py-2">
                                <i class="bi bi-patch-check fs-5"></i>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning text-dark rounded-pill">{{ $pendingApprovals }} Task</span>
                            </div>
                        </div>
                        <p class="text-muted small fw-medium mb-1 text-uppercase tracking-wider">Konfirmasi Pembayaran</p>
                        <h3 class="fw-bold mb-0 text-warning-emphasis">Pending</h3>
                    </div>
                    <div class="card-footer bg-transparent border-0 px-4 pb-4">
                        <a href="{{ route('admin.payments.index') }}"
                            class="btn btn-outline-warning btn-sm w-100 rounded-pill fw-bold">Periksa Semua <i
                                class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div
                        class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-activity text-primary me-2"></i>Grafik Pendapatan
                        </h5>
                        <button class="btn btn-light btn-sm text-muted rounded-circle"><i
                                class="bi bi-three-dots-vertical"></i></button>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <canvas id="revenueChart" height="320"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-graph-up text-success me-2"></i>Diagram Kelas
                        </h5>
                    </div>
                    <div class="card-body px-4 pb-4 d-flex flex-column justify-content-center">
                        <canvas id="classChart"></canvas>
                        <div class="mt-4 small text-muted text-center">
                            <i class="bi bi-info-circle me-1"></i> Data diperbarui secara real-time berdasarkan pendaftaran
                            siswa aktif.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .08) !important;
        }

        .tracking-wider {
            letter-spacing: 0.05em;
        }

        .bg-primary-subtle {
            background-color: #e7f1ff !important;
        }

        .bg-danger-subtle {
            background-color: #ffeef0 !important;
        }

        .bg-success-subtle {
            background-color: #e6fcf5 !important;
        }

        .bg-warning-subtle {
            background-color: #fff9db !important;
        }

        canvas {
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.02));
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const revenueData = {!! json_encode($monthlyRevenue) !!};
        const classData = {!! json_encode($classDistribution) !!};

        // ===== LINE CHART =====
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(13,110,253,0.25)');
        gradient.addColorStop(1, 'rgba(13,110,253,0)');

        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: revenueData.map(item => item.label),
                datasets: [{
                    data: revenueData.map(item => item.total),
                    borderColor: '#0d6efd',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Rp ' + value.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });

        // ===== DOUGHNUT CHART =====
        new Chart(document.getElementById('classChart'), {
            type: 'doughnut',
            data: {
                labels: classData.map(item => item.class?.name ?? 'N/A'),
                datasets: [{
                    data: classData.map(item => item.total),
                    backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#fd7e14', '#6610f2', '#0dcaf0'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    </script>
@endpush
