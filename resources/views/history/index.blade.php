@extends('template')

@section('content')
<div class="container mt-4">
    <h4 class="fw-bold mb-3">Riwayat Pembayaran SPP</h4>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah Dibayar</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                                </td>
                                <td>
                                    Rp {{ number_format($payment->proofs->sum('amount'), 0, ',', '.') }}
                                </td>
                                <td>
                                    @if ($payment->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif ($payment->status === 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('payments.history.show', $payment->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada riwayat pembayaran.
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
