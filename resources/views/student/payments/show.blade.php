@extends('template')

@section('content')
    <div class="container">
        <div class="page-inner">

            <div class="mb-4">
                <h3 class="fw-bold mb-1">Detail Pembayaran</h3>
                <span class="text-muted">Informasi pembayaran SPP</span>
            </div>

            <div class="row">

                {{-- BUKTI TRANSFER --}}
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-white fw-semibold">
                            Bukti Transfer
                        </div>
                        <div class="card-body">

                            @forelse ($payment->proofs as $proof)
                                <div class="mb-4 text-center">
                                    <img src="{{ asset('storage/' . $proof->image_path) }}"
                                        class="img-fluid rounded shadow-sm mb-2" style="max-height: 300px">

                                    <div class="fw-bold">
                                        Rp {{ number_format($proof->amount, 0, ',', '.') }}
                                    </div>

                                    @if ($proof->note)
                                        <small class="text-muted d-block">
                                            Catatan: {{ $proof->note }}
                                        </small>
                                    @endif

                                    <small class="text-muted">
                                        Upload: {{ $proof->created_at->format('d M Y H:i') }}
                                    </small>
                                </div>
                                <hr>
                            @empty
                                <p class="text-muted text-center">
                                    Belum ada bukti pembayaran.
                                </p>
                            @endforelse

                        </div>
                    </div>
                </div>

                {{-- DETAIL PEMBAYARAN --}}
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-white fw-semibold">
                            Detail Pembayaran
                        </div>
                        <div class="card-body">

                            <p>
                                <b>Tanggal:</b>
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                            </p>

                            <p>
                                <b>Total Transfer:</b>
                                Rp {{ number_format($payment->proofs->sum('amount'), 0, ',', '.') }}
                            </p>

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
                                        — Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                    </li>
                                @endforeach
                            </ul>

                        </div>
                    </div>

                    {{-- FORM UPLOAD ULANG --}}
                    @if (in_array($payment->status, ['pending', 'rejected']))
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white fw-semibold">
                                Upload Bukti Tambahan
                            </div>
                            <div class="card-body">
                                <form action="{{ route('student.payments.upload-proof', $payment->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label">Nominal Transfer</label>
                                        <input type="number" name="amount" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Bukti Transfer</label>
                                        <input type="file" name="proof_image" class="form-control" required>
                                    </div>

                                    <button class="btn btn-primary w-100">
                                        Upload Bukti Tambahan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <a href="{{ route('student.payments.index') }}" class="btn btn-secondary mt-3">
                Kembali
            </a>

        </div>
    </div>
@endsection
