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
                <div class="card-body bg-light text-center">
                    <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank">
                        <img
                            src="{{ asset('storage/' . $payment->proof_image) }}"
                            class="img-fluid rounded shadow-sm"
                            alt="Bukti Pembayaran"
                        >
                    </a>

                    <div class="mt-3">
                        <span class="badge bg-white text-dark border">
                            <i class="bi bi-zoom-in me-1 text-primary"></i>
                            Klik gambar untuk melihat ukuran penuh
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: DETAIL PEMBAYARAN --}}
        <div class="col-lg-5">

            {{-- INFORMASI SISWA --}}
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
                                Rp {{ number_format($payment->paid_amount, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BULAN YANG DIBAYAR --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">
                        Bulan yang Dibayar
                    </h6>

                    <div class="list-group list-group-flush">

                        @foreach ($payment->details as $detail)
                        
                            @php
                                $bulan = \Carbon\Carbon::createFromDate(
                                    $detail->bill->year,
                                    $detail->bill->month,
                                    1
                                )->translatedFormat('F');
                            @endphp

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">
                                        <i class="bi bi-calendar-check text-success me-2"></i>
                                        {{ $bulan }} {{ $detail->bill->year }}
                                    </div>
                                    <small class="text-muted">Tagihan SPP</small>
                                </div>
                                <span class="fw-bold text-primary">
                                    Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            {{-- AKSI --}}
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    {{-- APPROVE --}}
                    <form
                        action="{{ route('admin.payments.approve', $payment->id) }}"
                        method="POST"
                        class="mb-3"
                    >
                        @csrf
                        <button
                            type="submit"
                            class="btn btn-success w-100 fw-bold"
                            onclick="return confirm('Yakin ingin menyetujui pembayaran ini?')"
                        >
                            <i class="bi bi-check-circle me-2"></i>
                            Setujui Pembayaran
                        </button>
                    </form>

                    {{-- REJECT --}}
                    <button
                        class="btn btn-outline-danger w-100 fw-bold"
                        data-bs-toggle="collapse"
                        data-bs-target="#rejectForm"
                    >
                        <i class="bi bi-x-circle me-2"></i>
                        Tolak Pembayaran
                    </button>

                    <div class="collapse mt-3" id="rejectForm">
                        <form
                            action="{{ route('admin.payments.reject', $payment->id) }}"
                            method="POST"
                        >
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Alasan Penolakan
                                </label>
                                <textarea
                                    name="note"
                                    class="form-control"
                                    rows="3"
                                    required
                                    placeholder="Contoh: Bukti tidak jelas, nominal kurang, dll"
                                ></textarea>
                            </div>
                            <button class="btn btn-danger w-100">
                                Konfirmasi Tolak
                            </button>
                        </form>
                    </div>

                </div>
            </div>

            {{-- INFO --}}
            <div class="alert alert-info mt-4 d-flex align-items-center">
                <i class="bi bi-info-circle me-2"></i>
                <small>
                    Setelah pembayaran disetujui, status tagihan otomatis menjadi
                    <strong>Lunas</strong>.
                </small>
            </div>

        </div>
    </div>
</div>
@endsection
