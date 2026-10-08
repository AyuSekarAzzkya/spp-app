@extends('template')

@section('content')
@php
    \Carbon\Carbon::setLocale('id');
@endphp

<div class="page-inner">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('billing.students') }}" class="text-decoration-none" style="color: var(--orange-brand);">Tagihan Siswa</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('billing.index', $payment->student_id) }}" class="text-decoration-none" style="color: var(--orange-brand);">Lembar Tagihan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Rincian Transaksi #{{ $payment->id }}</li>
                </ol>
            </nav>
            <h2 class="page-header-title mb-1">Rincian Transaksi Pembayaran</h2>
            <p class="page-header-subtitle mb-0">Informasi pembayaran yang melunasi lembar tagihan SPP ini.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('billing.index', $payment->student_id) }}" class="btn btn-secondary d-flex align-items-center">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Lembar Tagihan
            </a>
            <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-primary d-flex align-items-center">
                <i class="mdi mdi-shield-search me-1"></i> Buka di Verifikasi Pembayaran
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Rincian Transaksi & Alokasi Tagihan --}}
        <div class="col-12 col-lg-7">
            {{-- Transaksi Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center fw-bold">
                        <i class="mdi mdi-receipt me-2" style="color: var(--orange-brand); font-size: 1.2rem;"></i>
                        Informasi Transaksi Pembayaran
                    </span>
                    @if($payment->status === 'approved')
                        <span class="badge badge-lunas"><i class="mdi mdi-check"></i> Disetujui (Lunas)</span>
                    @elseif($payment->status === 'pending')
                        <span class="badge badge-pending"><i class="mdi mdi-clock-outline"></i> Menunggu Verifikasi</span>
                    @else
                        <span class="badge badge-ditolak"><i class="mdi mdi-close"></i> Ditolak</span>
                    @endif
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Nama Siswa</small>
                            <h5 class="fw-bold text-dark mt-1 mb-0">{{ $payment->student->name }}</h5>
                            <small class="text-muted">NIS: {{ $payment->student->nis }}</small>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Kelas / Rombel</small>
                            <div class="fw-bold text-dark mt-1">{{ $payment->student->class->name ?? '-' }}</div>
                            <small class="text-muted">{{ $payment->student->academicYear->year ?? '-' }}</small>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Tanggal Transaksi</small>
                            <div class="fw-bold text-dark mt-1">{{ \Carbon\Carbon::parse($payment->payment_date ?? $payment->created_at)->translatedFormat('d F Y, H:i') }}</div>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Metode Pembayaran</small>
                            <div class="fw-bold text-dark text-uppercase mt-1">{{ $payment->payment_method ?? 'Transfer Bank' }}</div>
                        </div>
                    </div>

                    @if ($payment->note)
                        <div class="mt-4 p-3 bg-light rounded-3 border">
                            <small class="text-muted fw-bold d-block">Catatan Transaksi:</small>
                            <span class="text-dark small">{{ $payment->note }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Alokasi Tagihan --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center fw-bold">
                        <i class="mdi mdi-format-list-checks me-2" style="color: var(--navy-primary); font-size: 1.2rem;"></i>
                        Rincian Tagihan yang Dilunasi
                    </span>
                    <span class="badge badge-secondary">{{ $payment->details->count() }} Bulan SPP</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Bulan</th>
                                    <th>Tahun</th>
                                    <th>Status Tagihan</th>
                                    <th class="text-end pe-3">Nominal SPP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payment->details as $detail)
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark">
                                            <i class="mdi mdi-calendar-check text-success me-1"></i>
                                            {{ \Carbon\Carbon::create()->month($detail->bill->month)->translatedFormat('F') }}
                                        </td>
                                        <td>{{ $detail->bill->year }}</td>
                                        <td>
                                            @if($detail->bill->status === 'paid')
                                                <span class="badge badge-lunas"><i class="mdi mdi-check"></i> Lunas</span>
                                            @else
                                                <span class="badge badge-unpaid">Belum Lunas</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3 fw-bold text-dark">
                                            Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="3" class="ps-3 text-dark fw-bold">Total Pembayaran:</th>
                                    <th class="text-end pe-3 fw-bold text-navy" style="font-size: 1.15rem;">
                                        Rp {{ number_format($payment->total_amount, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Bukti Transfer Preview --}}
        <div class="col-12 col-lg-5">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center fw-bold">
                        <i class="mdi mdi-image-outline me-2" style="color: var(--navy-primary); font-size: 1.2rem;"></i>
                        Lampiran Bukti Transfer
                    </span>
                    @if($payment->latestProof)
                        <span class="badge badge-navy">v{{ $payment->latestProof->version ?? 1 }}</span>
                    @endif
                </div>
                <div class="card-body p-4 text-center">
                    @if($payment->latestProof)
                        @if($payment->latestProof->fileExists())
                            <div class="proof-container mb-3">
                                <div class="proof-image-wrapper p-2 bg-light rounded-3 border d-inline-block w-100" style="max-height: 420px; overflow: hidden;">
                                    <a href="{{ route('payments.proof.show', $payment->latestProof->id) }}" target="_blank" title="Klik untuk membuka ukuran penuh">
                                        <img src="{{ route('payments.proof.show', $payment->latestProof->id) }}" alt="Bukti Transfer" class="img-fluid rounded shadow-sm" style="max-height: 380px; width: auto; max-width: 100%; object-fit: contain;"
                                             onerror="this.closest('.proof-image-wrapper').style.display='none'; this.closest('.proof-container').querySelector('.proof-fallback').style.display='block';">
                                    </a>
                                </div>
                                <div class="proof-fallback py-4 px-3 bg-light rounded-3 border text-center my-2" style="display: none;">
                                    <i class="mdi mdi-image-broken-variant fs-1 d-block text-secondary mb-2"></i>
                                    <h6 class="fw-bold text-dark mb-1">Bukti pembayaran tidak tersedia</h6>
                                    <p class="text-muted small mb-0">Berkas gambar tidak ditemukan di server.</p>
                                </div>
                            </div>
                            <a href="{{ route('payments.proof.show', $payment->latestProof->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 mb-2">
                                <i class="mdi mdi-open-in-new me-1"></i> Buka Gambar Penuh
                            </a>
                        @else
                            <div class="py-4 px-3 bg-light rounded-3 border text-center mb-3">
                                <i class="mdi mdi-image-broken-variant fs-1 d-block text-secondary mb-2"></i>
                                <h6 class="fw-bold text-dark mb-1">Bukti pembayaran tidak tersedia</h6>
                                <p class="text-muted small mb-0">Berkas fisik bukti transfer tidak dapat ditemukan di penyimpanan.</p>
                            </div>
                        @endif
                        <small class="text-muted d-block">
                            Diunggah pada: {{ \Carbon\Carbon::parse($payment->latestProof->created_at)->translatedFormat('d F Y, H:i') }}
                        </small>
                    @else
                        <div class="py-5 text-muted">
                            <i class="mdi mdi-image-off-outline fs-1 d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">Bukti pembayaran tidak tersedia</h6>
                            <p class="small mb-0">Tidak ada lampiran bukti transfer untuk transaksi ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection