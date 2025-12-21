@extends('template')

@section('content')
    @php
        \Carbon\Carbon::setLocale('id');
    @endphp

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Verifikasi Pembayaran</h4>
                <p class="text-muted small mb-0">
                    Periksa bukti transfer dan detail tagihan sebelum approval
                </p>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="row g-4">

            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-semibold">
                        <i class="bi bi-image text-primary me-2"></i>
                        Bukti Transfer Siswa
                    </div>

                    <div class="card-body bg-light">
                        @forelse ($payment->proofs as $proof)
                            <div class="mb-4 text-center">
                                <a href="{{ asset('storage/' . $proof->image_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $proof->image_path) }}"
                                        class="img-fluid rounded shadow-sm mb-2" style="max-height: 320px">
                                </a>

                                <div class="fw-bold text-primary">
                                    Rp {{ number_format($proof->amount, 0, ',', '.') }}
                                </div>

                                @if ($proof->note)
                                    <small class="text-muted d-block">
                                        Catatan siswa: {{ $proof->note }}
                                    </small>
                                @endif

                                <small class="text-muted">
                                    Upload: {{ $proof->created_at->format('d M Y H:i') }}
                                </small>
                            </div>
                            <hr>
                        @empty
                            <p class="text-center text-muted">
                                Belum ada bukti pembayaran.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-5">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">
                            Informasi Pembayar
                        </h6>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <small class="text-muted">Nama Siswa</small>
                                <div class="fw-semibold">
                                    {{ $payment->student->name }}
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted">NISN</small>
                                <div class="fw-semibold">
                                    {{ $payment->student->nisn ?? '-' }}
                                </div>
                            </div>

                            <div class="col-12">
                                <small class="text-muted">Total Transfer</small>
                                <div class="fw-bold text-primary fs-4">
                                    Rp {{ number_format($payment->proofs->sum('amount'), 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">
                            Tagihan Dibayar
                        </h6>

                        <div class="list-group list-group-flush">
                            @php
                                $totalTagihan = 0;
                            @endphp

                            @foreach ($payment->details as $detail)
                                @php
                                    $bulan = \Carbon\Carbon::createFromDate(
                                        $detail->bill->year,
                                        $detail->bill->month,
                                        1,
                                    )->translatedFormat('F');

                                    $totalTagihan += $detail->amount;
                                @endphp

                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold">
                                            <i class="bi bi-calendar-check text-success me-2"></i>
                                            {{ $bulan }} {{ $detail->bill->year }}
                                        </div>
                                        <small class="text-muted">Tagihan SPP</small>
                                    </div>
                                    <span class="fw-bold text-dark">
                                        Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-top pt-3 mt-3">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total Tagihan</span>
                                <span class="fw-bold">
                                    Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mt-1">
                                <span class="fw-bold">Total Dibayar</span>
                                <span class="fw-bold text-primary">
                                    Rp {{ number_format($payment->proofs->sum('amount'), 0, ',', '.') }}
                                </span>
                            </div>

                            @if ($payment->proofs->sum('amount') < $totalTagihan)
                                <div class="alert alert-warning mt-3">
                                    Nominal pembayaran <b>belum mencukupi</b>.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        @if ($payment->status === 'pending')
                            <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST"
                                class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 fw-bold"
                                    onclick="return confirm('Yakin ingin menyetujui pembayaran ini?')"
                                    {{ $payment->proofs->sum('amount') < $totalTagihan ? 'disabled' : '' }}>
                                    <i class="bi bi-check-circle me-2"></i>
                                    Setujui Pembayaran
                                </button>
                            </form>

                            <button class="btn btn-outline-danger w-100 fw-bold" data-bs-toggle="collapse"
                                data-bs-target="#rejectForm">
                                <i class="bi bi-x-circle me-2"></i>
                                Tolak Pembayaran
                            </button>

                            <div class="collapse mt-3" id="rejectForm">
                                <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Alasan Penolakan
                                        </label>
                                        <textarea name="note" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <button class="btn btn-danger w-100">
                                        Konfirmasi Tolak
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="alert {{ $payment->status === 'approved' ? 'alert-success' : 'alert-danger' }}">
                                <strong>Status:</strong>
                                {{ ucfirst($payment->status) }}
                            </div>
                        @endif

                    </div>
                </div>

                <div class="alert alert-info mt-4 d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>
                        Setelah disetujui, seluruh tagihan otomatis menjadi
                        <strong>Lunas</strong>.
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection
