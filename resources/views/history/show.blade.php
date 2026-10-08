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
                    @forelse ($payment->proofs as $proof)
                        <div class="mb-3 text-center proof-item-container">
                            @if ($proof->fileExists())
                                <div class="proof-image-wrapper mb-2">
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#historyProofModal{{ $proof->id }}" title="Klik untuk memperbesar">
                                        <img
                                            src="{{ route('payments.proof.show', $proof->id) }}"
                                            alt="Bukti Transfer"
                                            class="img-fluid rounded border mb-1 shadow-sm"
                                            style="max-height: 240px; width: auto; max-width: 100%; object-fit: contain; cursor: zoom-in;"
                                            onerror="this.closest('.proof-image-wrapper').style.display='none'; this.closest('.proof-item-container').querySelector('.proof-fallback').style.display='block';"
                                        >
                                    </a>
                                </div>
                                <div class="proof-fallback py-3 px-2 bg-light rounded border text-center my-2" style="display: none;">
                                    <i class="mdi mdi-image-broken-variant fs-2 d-block text-secondary mb-1"></i>
                                    <span class="small fw-bold text-dark d-block">Bukti pembayaran tidak tersedia</span>
                                    <small class="text-muted">Berkas tidak ditemukan</small>
                                </div>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#historyProofModal{{ $proof->id }}">
                                        <i class="mdi mdi-magnify-plus-outline me-1"></i> Buka Gambar
                                    </button>
                                </div>

                                <!-- Modal Lightbox Riwayat -->
                                <div class="modal fade" id="historyProofModal{{ $proof->id }}" tabindex="-1" aria-labelledby="historyProofLabel{{ $proof->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                                <h6 class="modal-title fw-bold text-dark mb-0" id="historyProofLabel{{ $proof->id }}">
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
                                <div class="py-3 px-2 bg-light rounded border text-center mb-2">
                                    <i class="mdi mdi-image-broken-variant fs-2 d-block text-secondary mb-1"></i>
                                    <span class="small fw-bold text-dark d-block">Bukti pembayaran tidak tersedia</span>
                                    <small class="text-muted">Berkas tidak ditemukan</small>
                                </div>
                            @endif

                            <div class="small fw-semibold text-dark">
                                Rp {{ number_format($proof->amount, 0, ',', '.') }}
                            </div>
                            @if ($proof->note)
                                <div class="small text-muted fst-italic">
                                    "{{ $proof->note }}"
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-4 text-center text-muted">
                            <i class="mdi mdi-image-off-outline fs-1 d-block text-secondary mb-2"></i>
                            <span class="small">Belum ada bukti pembayaran.</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
