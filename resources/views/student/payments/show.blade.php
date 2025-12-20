@extends('template')

@section('content')
    <div class="container">
        <div class="page-inner">

            <div class="mb-4">
                <h3 class="fw-bold mb-1">Detail Pembayaran</h3>
                <span class="text-muted">Informasi pembayaran SPP</span>
            </div>

            <div class="row">

                {{-- Bukti --}}
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-white fw-semibold">
                            Bukti Transfer
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ asset('storage/' . $payment->proof_image) }}" class="img-fluid rounded"
                                style="max-height:400px">
                        </div>
                    </div>
                </div>

                {{-- Detail --}}
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-white fw-semibold">
                            Detail Pembayaran
                        </div>
                        <div class="card-body">

                            <p><b>Tanggal:</b> {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</p>
                            <p><b>Jumlah:</b> Rp {{ number_format($payment->paid_amount) }}</p>
                            <p>
                                <b>Status:</b>
                                @if ($payment->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif ($payment->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </p>

                            @if ($payment->note)
                                <div class="alert alert-danger mt-3">
                                    <b>Catatan Admin:</b><br>
                                    {{ $payment->note }}
                                </div>
                            @endif

                            <hr>

                            <h6 class="fw-semibold">Tagihan Dibayar</h6>
                            <ul class="mb-0">
                                @foreach ($payment->details as $detail)
                                    <li>
                                        {{ $detail->bill->month }}
                                        {{ $detail->bill->year }}
                                        — Rp {{ number_format($detail->amount) }}
                                    </li>
                                @endforeach
                            </ul>

                        </div>
                    </div>
                </div>

            </div>

            <a href="{{ route('student.payments.index') }}" class="btn btn-secondary mt-2">
                Kembali
            </a>

        </div>
    </div>
@endsection
