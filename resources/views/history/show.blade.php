@extends('template')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Detail Riwayat Pembayaran</h4>
        <a href="{{ route('payments.history') }}" class="btn btn-secondary btn-sm">
            Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">
                    Tagihan Dibayar
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payment->details as $detail)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::createFromDate(
                                            $detail->bill->year,
                                            $detail->bill->month,
                                            1
                                        )->translatedFormat('F') }}
                                    </td>
                                    <td>{{ $detail->bill->year }}</td>
                                    <td class="text-end">
                                        Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="alert alert-info fw-bold">
                Total Dibayar:
                Rp {{ number_format($payment->proofs->sum('amount'), 0, ',', '.') }}
            </div>

        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    Bukti Transfer
                </div>
                <div class="card-body">
                    @foreach ($payment->proofs as $proof)
                        <div class="mb-3 text-center">
                            <img
                                src="{{ asset('storage/' . $proof->image_path) }}"
                                class="img-fluid rounded border mb-1"
                            >
                            <div class="small text-muted">
                                Rp {{ number_format($proof->amount, 0, ',', '.') }}
                            </div>
                            @if ($proof->note)
                                <div class="small text-danger">
                                    {{ $proof->note }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
