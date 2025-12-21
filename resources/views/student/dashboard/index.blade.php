@extends('template')

@section('content')
    <div class="container-fluid py-4">

        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-primary bg-gradient text-white p-4">
                    <div class="card-body p-0">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="fw-bold mb-1">Halo, {{ Auth::user()->name }}!</h3>
                                <p class="mb-0 opacity-75">Tahun Ajaran Aktif: <b>{{ $activeYear->year ?? '-' }}</b></p>
                            </div>
                            <div class="col-md-4 text-end d-none d-md-block">
                                <i class="bi bi-rocket-takeoff opacity-25" style="font-size: 4rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="rounded-3 bg-danger bg-opacity-10 p-3 me-3">
                            <i class="bi bi-wallet2 text-danger fs-3"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-medium text-uppercase small">Sisa Tagihan</small>
                            <h4 class="fw-bold mb-0">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="rounded-3 bg-warning bg-opacity-10 p-3 me-3">
                            <i class="bi bi-calendar-check text-warning fs-3"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-medium text-uppercase small">Belum Lunas</small>
                            <h4 class="fw-bold mb-0">{{ $unpaidCount }} Bulan</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="rounded-3 bg-success bg-opacity-10 p-3 me-3">
                            <i class="bi bi-shield-check text-success fs-3"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block fw-medium text-uppercase small">Status Bulan Ini</small>
                            <h4 class="fw-bold mb-0 {{ $isPaidThisMonth ? 'text-success' : 'text-danger' }}">
                                {{ $isPaidThisMonth ? 'Sudah Lunas' : 'Belum Bayar' }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div
                        class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Tagihan SPP</h5>
                        <a href="{{ route('student.payments.create') }}"
                            class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-plus-circle me-1"></i> Bayar
                        </a>
                    </div>
                    <div class="card-body p-4">
                        @if ($unpaidBills->count())
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 rounded-start">Bulan</th>
                                            <th class="border-0">Kategori</th>
                                            <th class="border-0 text-end rounded-end">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($unpaidBills->take(5) as $bill)
                                            <tr>
                                                <td>
                                                    <i class="bi bi-calendar3 me-2 text-primary"></i>
                                                    <span
                                                        class="fw-bold">{{ \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F') }}</span>
                                                    <small class="text-muted ms-1">{{ $bill->year }}</small>
                                                </td>
                                                <td><span
                                                        class="badge bg-light text-dark border rounded-pill px-3">Wajib</span>
                                                </td>
                                                <td class="text-end fw-bold">Rp
                                                    {{ number_format($bill->sppRate->amount, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-check-all text-success display-4"></i>
                                <h6 class="fw-bold mt-2">Semua Beres!</h6>
                                <p class="text-muted small">Anda tidak memiliki tunggakan pembayaran.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h6 class="fw-bold mb-0">Transaksi Terakhir</h6>
                    </div>
                    <div class="card-body p-4">
                        @if ($latestPayment)
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-light rounded p-3 me-3">
                                    <i class="bi bi-receipt text-primary fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block small">Status Terakhir:</small>
                                    @php
                                        $statusClass =
                                            [
                                                'approved' => 'text-success',
                                                'pending' => 'text-warning',
                                                'rejected' => 'text-danger',
                                            ][$latestPayment->status] ?? 'text-secondary';
                                    @endphp
                                    <span
                                        class="fw-bold {{ $statusClass }} text-uppercase">{{ $latestPayment->status }}</span>
                                </div>
                            </div>
                            <a href="{{ route('payments.history') }}"
                                class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-bold">
                                Lihat Riwayat <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        @else
                            <div class="text-center py-2">
                                <p class="text-muted small mb-0">Belum ada transaksi.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-light">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-credit-card me-2"></i>Pembayaran</h6>
                        <div class="bg-white p-3 rounded border text-center">
                            <small class="text-muted d-block">Bank BNI</small>
                            <h5 class="fw-bold text-primary mb-0">8827-0021-9922</h5>
                            <small class="text-muted">A/N SMK Bakti Nusantara</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
