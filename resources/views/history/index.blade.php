@extends('template')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-0">Riwayat Pembayaran SPP</h4>
                <small class="text-muted">Daftar seluruh transaksi pembayaran Anda</small>
            </div>
            <i class="bi bi-receipt fs-3 text-primary"></i>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="datatable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Jumlah Dibayar</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold">
                                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('l') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">
                                            Rp {{ number_format($payment->proofs->sum('amount'), 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($payment->status === 'pending')
                                            <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                                                Menunggu
                                            </span>
                                        @elseif ($payment->status === 'approved')
                                            <span class="badge rounded-pill bg-success px-3 py-2">
                                                Disetujui
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-danger px-3 py-2">
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <a href="{{ route('payments.history.show', $payment->id) }}"
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted mb-2 d-block"></i>
                                        <span class="text-muted">
                                            Belum ada riwayat pembayaran.
                                        </span>
                                    </td>
                                </tr>
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
                pageLength: 5,
                lengthChange: false,
                language: {
                    search: "Cari:",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        next: ">",
                        previous: "<"
                    }
                }
            });
        });
    </script>
@endpush
