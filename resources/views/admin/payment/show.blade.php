@extends('template')

@section('content')
<div class="page-inner">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}" class="text-decoration-none" style="color: var(--orange-brand);">Pembayaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Verifikasi #{{ $payment->id }}</li>
                </ol>
            </nav>
            <h2 class="page-header-title mb-1">Verifikasi Transaksi Pembayaran</h2>
            <p class="page-header-subtitle mb-0">Periksa bukti transfer dan keabsahan alokasi tagihan sebelum menyetujui transaksi.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary d-flex align-items-center">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Antrean
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left Column: Bukti Transfer & Histori Versi --}}
        <div class="col-12 col-lg-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center fw-bold">
                        <i class="mdi mdi-image-check-outline me-2" style="color: var(--orange-brand); font-size: 1.2rem;"></i>
                        Bukti Transfer Pembayaran
                    </span>
                    @if($payment->latestProof)
                        <span class="badge badge-navy">Versi {{ $payment->latestProof->version ?? 1 }} (Terbaru)</span>
                    @endif
                </div>
                <div class="card-body p-4 text-center">
                    @if($payment->latestProof)
                        @if($payment->latestProof->fileExists())
                            <div class="proof-container mb-3">
                                <div class="proof-image-wrapper p-2 bg-light rounded-3 border d-inline-block w-100" style="max-height: 480px; overflow: hidden;">
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#proofLightboxModal{{ $payment->latestProof->id }}" title="Klik untuk membuka ukuran penuh">
                                        <img src="{{ route('payments.proof.show', $payment->latestProof->id) }}" 
                                             alt="Bukti Transfer" 
                                             class="img-fluid rounded shadow-sm" 
                                             style="max-height: 450px; width: auto; max-width: 100%; object-fit: contain; cursor: zoom-in;"
                                             onerror="this.closest('.proof-image-wrapper').style.display='none'; this.closest('.proof-container').querySelector('.proof-fallback').style.display='block';">
                                    </a>
                                </div>
                                <div class="proof-fallback py-4 px-3 bg-light rounded-3 border text-center my-2" style="display: none;">
                                    <i class="mdi mdi-image-broken-variant fs-1 d-block text-secondary mb-2"></i>
                                    <h6 class="fw-bold text-dark mb-1">Bukti pembayaran tidak tersedia</h6>
                                    <p class="text-muted small mb-0">Berkas fisik bukti transfer tidak dapat ditemukan di server.</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center gap-2 mb-3">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#proofLightboxModal{{ $payment->latestProof->id }}">
                                    <i class="mdi mdi-magnify-plus-outline me-1"></i> Lihat Gambar Penuh
                                </button>
                                <a href="{{ route('payments.proof.show', $payment->latestProof->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="mdi mdi-open-in-new me-1"></i> Buka Tab Baru
                                </a>
                            </div>

                            <!-- Modal Lightbox Bukti Transfer -->
                            <div class="modal fade" id="proofLightboxModal{{ $payment->latestProof->id }}" tabindex="-1" aria-labelledby="proofLightboxLabel{{ $payment->latestProof->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                            <h6 class="modal-title fw-bold text-dark mb-0" id="proofLightboxLabel{{ $payment->latestProof->id }}">
                                                <i class="mdi mdi-image-check-outline me-1" style="color: var(--orange-brand);"></i> Bukti Transfer - Versi {{ $payment->latestProof->version ?? 1 }}
                                            </h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center p-2 bg-dark">
                                            <img src="{{ route('payments.proof.show', $payment->latestProof->id) }}" alt="Bukti Transfer Penuh" class="img-fluid rounded" style="max-height: 80vh; object-fit: contain;">
                                        </div>
                                        <div class="modal-footer py-2 px-3 border-top justify-content-between">
                                            <span class="text-muted small">Nominal: Rp {{ number_format($payment->latestProof->amount ?? $payment->total_amount, 0, ',', '.') }}</span>
                                            <a href="{{ route('payments.proof.show', $payment->latestProof->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="mdi mdi-open-in-new me-1"></i> Buka di Tab Baru
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="py-4 px-3 bg-light rounded-3 border text-center mb-3">
                                <i class="mdi mdi-image-broken-variant fs-1 d-block text-secondary mb-2"></i>
                                <h6 class="fw-bold text-dark mb-1">Bukti pembayaran tidak tersedia</h6>
                                <p class="text-muted small mb-0">Berkas fisik bukti transfer tidak dapat ditemukan di penyimpanan.</p>
                            </div>
                        @endif

                        <div class="text-start bg-light p-3 rounded-3 border">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Nominal Tertera di Bukti:</span>
                                <span class="fw-bold text-dark">
                                    Rp {{ number_format($payment->latestProof->amount ?? $payment->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted small">Waktu Unggah:</span>
                                <span class="fw-semibold text-dark small">
                                    {{ \Carbon\Carbon::parse($payment->latestProof->created_at)->translatedFormat('d F Y, H:i') }}
                                </span>
                            </div>
                            @if($payment->latestProof->note)
                                <div class="pt-2">
                                    <span class="text-muted small d-block">Catatan Siswa:</span>
                                    <em class="text-dark small">"{{ $payment->latestProof->note }}"</em>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="py-5 text-muted">
                            <i class="mdi mdi-image-off-outline fs-1 d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">Bukti pembayaran tidak tersedia</h6>
                            <p class="small mb-0">Tidak ada bukti transfer yang dilampirkan.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Versi Bukti Sebelumnya (Jika ada re-upload) --}}
            @if($payment->proofs->count() > 1)
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <span class="fw-bold small text-muted text-uppercase">
                            <i class="mdi mdi-history me-1"></i> Riwayat Versi Bukti Sebelumnya
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <div class="list-group list-group-flush">
                            @foreach($payment->proofs->skip(1) as $oldProof)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div>
                                        <span class="badge badge-secondary me-2">v{{ $oldProof->version }}</span>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($oldProof->created_at)->translatedFormat('d M Y, H:i') }}</small>
                                        @if($oldProof->note)
                                            <div class="small text-muted mt-1"><em>"{{ $oldProof->note }}"</em></div>
                                        @endif
                                    </div>
                                    <a href="{{ route('payments.proof.show', $oldProof->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        Lihat
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: Informasi Siswa, Alokasi Tagihan & Tindakan --}}
        <div class="col-12 col-lg-6">
            {{-- Info Siswa Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center fw-bold">
                        <i class="mdi mdi-account-circle-outline me-2" style="color: var(--navy-primary); font-size: 1.2rem;"></i>
                        Identitas Siswa Pembayar
                    </span>
                    @if ($payment->status == 'pending')
                        <span class="badge badge-pending"><i class="mdi mdi-clock-outline"></i> Menunggu Verifikasi</span>
                    @elseif ($payment->status == 'approved')
                        <span class="badge badge-lunas"><i class="mdi mdi-check"></i> Disetujui (Lunas)</span>
                    @else
                        <span class="badge badge-ditolak"><i class="mdi mdi-close"></i> Ditolak</span>
                    @endif
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-3" style="width: 48px; height: 48px; background: var(--navy-primary); font-size: 1.1rem;">
                            {{ strtoupper(substr($payment->student->name ?? 'S', 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ $payment->student->name ?? '-' }}</h5>
                            <small class="text-muted">NIS: {{ $payment->student->nis ?? '-' }} • NISN: {{ $payment->student->nisn ?? '-' }}</small>
                        </div>
                    </div>

                    <div class="row g-2 small border-top pt-3">
                        <div class="col-6">
                            <span class="text-muted d-block">Kelas / Rombel:</span>
                            <span class="fw-bold text-dark">{{ $payment->student->class->name ?? '-' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Tahun Ajaran:</span>
                            <span class="fw-bold text-dark">{{ $payment->student->academicYear->year ?? '-' }}</span>
                        </div>
                        <div class="col-6 mt-2">
                            <span class="text-muted d-block">Metode Pembayaran:</span>
                            <span class="fw-bold text-dark text-uppercase">{{ $payment->payment_method ?? 'Transfer Bank' }}</span>
                        </div>
                        <div class="col-6 mt-2">
                            <span class="text-muted d-block">Tanggal Transaksi:</span>
                            <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($payment->payment_date ?? $payment->created_at)->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>

                    @if($payment->status === 'approved' && $payment->verifier)
                        <div class="mt-3 p-3 bg-light rounded-3 border border-success-subtle">
                            <small class="text-success fw-bold d-block"><i class="mdi mdi-shield-check me-1"></i> Diverifikasi Oleh:</small>
                            <span class="text-dark small"><strong>{{ $payment->verifier->name }}</strong> pada {{ \Carbon\Carbon::parse($payment->verified_at)->translatedFormat('d F Y, H:i') }}</span>
                        </div>
                    @endif

                    @if($payment->status === 'rejected')
                        <div class="mt-3 p-3 bg-light rounded-3 border border-danger-subtle">
                            <small class="text-danger fw-bold d-block"><i class="mdi mdi-alert-circle me-1"></i> Alasan Penolakan Petugas:</small>
                            <span class="text-dark small"><em>"{{ $payment->note ?? 'Bukti transfer tidak valid.' }}"</em></span>
                            @if($payment->verifier)
                                <div class="text-muted small mt-1">Ditolak oleh: {{ $payment->verifier->name }} ({{ \Carbon\Carbon::parse($payment->verified_at)->translatedFormat('d M Y, H:i') }})</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Alokasi Tagihan Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center fw-bold">
                        <i class="mdi mdi-format-list-checks me-2" style="color: var(--orange-brand); font-size: 1.2rem;"></i>
                        Alokasi Tagihan yang Dibayarkan
                    </span>
                    <span class="badge badge-secondary">{{ $payment->details->count() }} Bulan SPP</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Bulan Tagihan</th>
                                    <th>Tahun</th>
                                    <th>Status Tagihan</th>
                                    <th class="text-end pe-3">Nominal</th>
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
                                                <span class="badge badge-lunas">Lunas</span>
                                            @else
                                                <span class="badge badge-pending">Menunggu Approve</span>
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
                                    <th colspan="3" class="ps-3 text-dark fw-bold">Total Pembayaran Diperlukan:</th>
                                    <th class="text-end pe-3 fw-bold text-navy" style="font-size: 1.15rem;">
                                        Rp {{ number_format($payment->total_amount, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tindakan Verifikasi Petugas --}}
            @if ($payment->status === 'pending')
                <div class="card border-0 shadow-sm" style="border: 1px solid var(--border) !important;">
                    <div class="card-header bg-white border-bottom">
                        <span class="fw-bold text-navy">
                            <i class="mdi mdi-check-decagram text-success me-1"></i> Eksekusi Verifikasi Transaksi
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-sm-row gap-3">
                            {{-- Form Approve --}}
                            <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST" id="approveForm" class="flex-fill">
                                @csrf
                                <button type="button" class="btn btn-success w-100 d-flex align-items-center justify-content-center py-2" id="btnApprove">
                                    <i class="mdi mdi-check-circle-outline me-2 fs-5"></i> Setujui (Lunas)
                                </button>
                            </form>

                            {{-- Button Trigger Reject Modal --}}
                            <button type="button" class="btn btn-danger flex-fill d-flex align-items-center justify-content-center py-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="mdi mdi-close-circle-outline me-2 fs-5"></i> Tolak Pembayaran
                            </button>
                        </div>
                        <small class="text-muted d-block mt-3 text-center">
                            <i class="mdi mdi-information-outline me-1"></i> Menyetujui akan mengubah status tagihan menjadi <strong>Lunas</strong> dan mencatat transaksi ke audit log.
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Tolak Pembayaran --}}
@if ($payment->status === 'pending')
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center" id="rejectModalLabel">
                        <i class="mdi mdi-alert-circle text-warning me-2 fs-5"></i> Tolak Pembayaran Siswa
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning small border-0 mb-3" style="border-radius: 8px;">
                            Siswa akan menerima pemberitahuan dan dapat mengunggah kembali bukti transfer yang valid.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="note" class="form-control" rows="4" placeholder="Contoh: Bukti transfer buram/tidak terbaca, nominal kurang, atau nomor rekening tujuan tidak sesuai." required></textarea>
                            <small class="text-muted">Jelaskan alasan secara spesifik agar siswa dapat memperbaikinya.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="mdi mdi-close-circle-outline me-1"></i> Konfirmasi Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnApprove = document.getElementById('btnApprove');
        if (btnApprove) {
            btnApprove.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Persetujuan Pembayaran',
                    text: "Apakah bukti transfer dan nominal transaksi sudah sesuai? Seluruh tagihan terkait akan otomatis ditandai sebagai LUNAS.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Setujui Sekarang',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('approveForm').submit();
                    }
                });
            });
        }
    });
</script>
@endpush
