@extends('template') {{-- Sesuaikan dengan nama layout Anda --}}

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-0">Dashboard Petugas</h3>
                <p class="text-muted">Selamat bekerja! Pantau aktivitas pembayaran hari ini.</p>
            </div>
            <div class="text-end">
                <span class="badge bg-primary px-3 py-2">{{ now()->format('d M Y') }}</span>
            </div>
        </div>

        {{-- ROW WIDGET UTAMA --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0 bg-warning text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 45px; height: 45px;">
                                <i class="mdi mdi-clock-check mdi-24px"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted small mb-0">Pending Approval</h6>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0">{{ $pendingPaymentsCount }}</h2>
                        <p class="text-muted small mb-0">Butuh konfirmasi segera</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 45px; height: 45px;">
                                <i class="mdi mdi-account-alert mdi-24px"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted small mb-0">Siswa Menunggak</h6>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0">{{ $arrearsCount }}</h2>
                        <p class="text-muted small mb-0">Lewat jatuh tempo</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0 bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 45px; height: 45px;">
                                <i class="mdi mdi-account-group mdi-24px"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted small mb-0">Siswa Dilayani</h6>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0">{{ $todayTransactionsCount }}</h2>
                        <p class="text-muted small mb-0">Total pelayanan hari ini</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-white"
                    style="border-radius: 15px; background: linear-gradient(45deg, #4e73df, #224abe);">
                    <div class="card-body text-center">
                        <h6 class="small text-uppercase opacity-75 mb-2">Kas Masuk Hari Ini</h6>
                        <h3 class="fw-bold mb-0">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- LIST AKTIVITAS TERBARU --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h5 class="fw-bold mb-0">Aktivitas Transaksi Terbaru</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Siswa</th>
                                        <th>Nominal</th>
                                        <th>Metode</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentActivities as $item)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">{{ $item->student->name }}</div>
                                                <div class="small text-muted">{{ $item->student->class->name ?? '-' }}</div>
                                            </td>
                                            <td class="fw-bold">
                                                {{-- Mengambil amount dari relasi paymentProof --}}
                                                Rp {{ number_format($item->paymentProof->amount ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td>{{ strtoupper($item->payment_method ?? 'Cash') }}</td>
                                            <td>
                                                @if ($item->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($item->status == 'success')
                                                    <span class="badge bg-success">Success</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="#"
                                                    class="btn btn-sm btn-outline-primary rounded-pill">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi
                                                masuk.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INFO PANEL --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="mdi mdi-lightbulb-on text-warning me-2"></i>Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-primary text-start px-3 py-2">
                                <i class="mdi mdi-cash-multiple me-2"></i> Input Bayar Loket
                            </a>
                            <a href="#" class="btn btn-outline-secondary text-start px-3 py-2">
                                <i class="mdi mdi-magnify me-2"></i> Cari Data Siswa
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ALERT PENTING --}}
                @if ($pendingPaymentsCount > 0)
                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center" role="alert"
                        style="border-radius: 15px;">
                        <i class="mdi mdi-alert-circle mdi-24px me-3"></i>
                        <div>
                            Ada <strong>{{ $pendingPaymentsCount }}</strong> pembayaran menunggu konfirmasi Anda.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
