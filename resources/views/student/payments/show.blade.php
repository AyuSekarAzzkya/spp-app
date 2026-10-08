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
                                <div class="mb-4 text-center proof-item-container">
                                    @if ($proof->fileExists())
                                        <div class="proof-image-wrapper mb-2">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#studentProofModal{{ $proof->id }}" title="Klik untuk memperbesar">
                                                <img src="{{ route('payments.proof.show', $proof->id) }}"
                                                    alt="Bukti Transfer"
                                                    class="img-fluid rounded border shadow-sm" 
                                                    style="max-height: 280px; width: auto; max-width: 100%; object-fit: contain; cursor: zoom-in;"
                                                    onerror="this.closest('.proof-image-wrapper').style.display='none'; this.closest('.proof-item-container').querySelector('.proof-fallback').style.display='block';">
                                            </a>
                                        </div>
                                        <div class="proof-fallback py-4 px-3 bg-light rounded-3 border text-center my-2" style="display: none;">
                                            <i class="mdi mdi-image-broken-variant fs-1 d-block text-secondary mb-2"></i>
                                            <h6 class="fw-bold text-dark mb-1">Bukti pembayaran tidak tersedia</h6>
                                            <p class="text-muted small mb-0">Berkas gambar tidak ditemukan di server penyimpanan.</p>
                                        </div>
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#studentProofModal{{ $proof->id }}">
                                                <i class="mdi mdi-magnify-plus-outline me-1"></i> Buka Gambar
                                            </button>
                                        </div>

                                        <!-- Modal Lightbox Siswa -->
                                        <div class="modal fade" id="studentProofModal{{ $proof->id }}" tabindex="-1" aria-labelledby="studentProofLabel{{ $proof->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                                        <h6 class="modal-title fw-bold text-dark mb-0" id="studentProofLabel{{ $proof->id }}">
                                                            Bukti Pembayaran - Versi {{ $proof->version ?? 1 }}
                                                        </h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center p-2 bg-dark">
                                                        <img src="{{ route('payments.proof.show', $proof->id) }}" alt="Bukti Transfer Penuh" class="img-fluid rounded" style="max-height: 80vh; object-fit: contain;">
                                                    </div>
                                                    <div class="modal-footer py-2 px-3 border-top justify-content-between">
                                                        <span class="text-muted small">Nominal: Rp {{ number_format($proof->amount, 0, ',', '.') }}</span>
                                                        <a href="{{ route('payments.proof.show', $proof->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                                            <i class="mdi mdi-open-in-new me-1"></i> Buka di Tab Baru
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="py-4 px-3 bg-light rounded-3 border text-center mb-2">
                                            <i class="mdi mdi-image-broken-variant fs-1 d-block text-secondary mb-2"></i>
                                            <h6 class="fw-bold text-dark mb-1">Bukti pembayaran tidak tersedia</h6>
                                            <p class="text-muted small mb-0">Berkas gambar tidak ditemukan di penyimpanan.</p>
                                        </div>
                                    @endif

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
                                @if (!$loop->last)
                                    <hr>
                                @endif
                            @empty
                                <div class="py-4 text-center text-muted">
                                    <i class="mdi mdi-image-off-outline fs-1 d-block text-secondary mb-2"></i>
                                    <p class="mb-0">Belum ada bukti pembayaran.</p>
                                </div>
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
