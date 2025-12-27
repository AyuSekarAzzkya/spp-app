@extends('template')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="mdi mdi-card-clock-outline text-warning me-2"></i>Status Pembayaran
                </h4>
                <p class="text-muted small">Pantau verifikasi transaksi terbaru Anda di sini</p>
            </div>
            <a href="{{ route('student.payments.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="mdi mdi-plus-thick me-1"></i>Bayar SPP
            </a>
        </div>

        @php $latest = $payments->first(); @endphp

        @if ($latest)
            <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-4 bg-primary d-flex align-items-center justify-content-center p-4">
                            <div class="text-center text-white">
                                <i class="mdi mdi-bell-ring-outline fs-1 opacity-50"></i>
                                <h6 class="mt-2 text-uppercase fw-bold small">Update Terkini</h6>
                                @if ($latest->status === 'pending')
                                    <span class="badge rounded-pill bg-white text-primary px-3 py-2 shadow-sm">
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($latest->status === 'approved')
                                    <span class="badge rounded-pill bg-white text-success px-3 py-2 shadow-sm">
                                        Berhasil Disetujui
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-white text-danger px-3 py-2 shadow-sm">
                                        Perlu Perbaikan
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-8 p-4 bg-white">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="text-muted small d-block">Tanggal Kirim:</span>
                                    <h5 class="fw-bold mb-0">
                                        {{ \Carbon\Carbon::parse($latest->payment_date)->translatedFormat('d F Y') }}
                                    </h5>
                                </div>
                                <h4 class="fw-bold text-primary mb-0">
                                    Rp {{ number_format($latest->proofs->sum('amount'), 0, ',', '.') }}
                                </h4>
                            </div>
                            <p class="text-secondary small mb-4">
                                Mencakup Tagihan:
                                <span class="fw-semibold text-dark">
                                    @foreach ($latest->details as $d)
                                        {{ \Carbon\Carbon::createFromDate(null, $d->bill->month, 1)->translatedFormat('F') }}
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </span>
                            </p>
                            <a href="{{ route('student.payments.show', $latest->id) }}"
                                class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">
                                Detail Transaksi <i class="mdi mdi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="mdi mdi-format-list-bulleted me-2 text-primary"></i>Aktivitas Terakhir
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th class="text-center pe-4">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold">
                                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</div>
                                        </td>
                                        <td><span class="fw-semibold">Rp
                                                {{ number_format($payment->proofs->sum('amount'), 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            @if ($payment->status === 'pending')
                                                <i class="mdi mdi-circle text-warning small me-1"></i>Pending
                                            @elseif ($payment->status === 'approved')
                                                <i class="mdi mdi-circle text-success small me-1"></i>Sukses
                                            @else
                                                <i class="mdi mdi-circle text-danger small me-1"></i>Ditolak
                                            @endif
                                        </td>
                                        <td class="text-center pe-4">
                                            <a href="{{ route('student.payments.show', $payment->id) }}"
                                                class="text-primary fs-5">
                                                <i class="mdi mdi-eye-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3 text-center border-top">
                    <a href="{{ route('payments.history') }}"
                        class="btn btn-link text-primary fw-bold text-decoration-none small">
                        Lihat Seluruh Riwayat Pembayaran <i class="mdi mdi-chevron-double-right ms-1"></i>
                    </a>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-4 py-5 text-center bg-white">
                <div class="card-body">
                    <i class="mdi mdi-cash-register fs-1 text-light d-block mb-3"></i>
                    <h5 class="fw-bold">Belum Ada Transaksi</h5>
                    <p class="text-muted small">Segera lakukan pembayaran SPP agar tagihan tidak menumpuk.</p>
                    <a href="{{ route('student.payments.create') }}" class="btn btn-primary rounded-pill px-4">Bayar
                        Sekarang</a>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>
@endpush
