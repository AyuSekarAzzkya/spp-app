@extends('template')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                @if ($payment->status === 'rejected')
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-4">
                        <div class="d-flex">
                            <i class="bi bi-exclamation-octagon fs-1 me-3"></i>
                            <div>
                                <h5 class="fw-bold">Pembayaran Ditolak</h5>
                                <p class="mb-0">Mohon maaf, bukti pembayaran Anda sebelumnya tidak dapat kami verifikasi.
                                    Silakan unggah kembali bukti yang valid atau tambahan melalui form di bawah ini.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="fw-bold mb-0">Rincian Pembayaran</h5>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead class="text-secondary small">
                                            <tr>
                                                <th>Item Tagihan</th>
                                                <th class="text-end">Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payment->details as $detail)
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold text-dark">{{ $detail->bill->month }}
                                                            {{ $detail->bill->year }}</div>
                                                        <small class="text-muted">SPP
                                                            {{ $detail->bill->sppRate->name }}</small>
                                                    </td>
                                                    <td class="text-end fw-bold">
                                                        Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th class="ps-3">Total Harus Dibayar</th>
                                                <th class="text-end pe-3 text-primary h5 fw-bold">
                                                    Rp {{ number_format($payment->details->sum('amount'), 0, ',', '.') }}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="fw-bold mb-0">Bukti yang Dikirim</h5>
                            </div>
                            <div class="card-body pt-0">
                                @foreach ($payment->proofs as $proof)
                                    <div class="d-flex align-items-center p-3 mb-2 border rounded-3 bg-light bg-opacity-50">
                                        <i class="bi bi-image text-primary fs-3 me-3"></i>
                                        <div class="flex-grow-1">
                                            <div class="small fw-bold">Rp {{ number_format($proof->amount, 0, ',', '.') }}
                                            </div>
                                            <div class="small text-muted text-truncate" style="max-width: 200px;">
                                                {{ $proof->note ?? 'Tanpa catatan' }}</div>
                                        </div>
                                        <a href="{{ asset('storage/' . $proof->image_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary rounded-pill">Lihat</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        @if ($payment->status === 'rejected')
                            <div class="card border-0 shadow-lg rounded-4 border-top border-danger border-4">
                                <div class="card-body p-4">
                                    <h5 class="fw-bold mb-3 text-danger">Upload Ulang Bukti</h5>

                                    <form action="{{ route('student.payments.upload-proof', $payment->id) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted">Nominal Transfer</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0">Rp</span>
                                                <input type="number" name="amount" class="form-control bg-light border-0"
                                                    placeholder="0" required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted">Foto Bukti Baru</label>
                                            <input type="file" name="proof_image" class="form-control" required
                                                accept="image/*">
                                            <small class="text-muted" style="font-size: 10px;">Format: JPG, PNG, Max:
                                                2MB</small>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label small fw-bold text-muted">Catatan (Opsional)</label>
                                            <textarea name="note" class="form-control bg-light border-0" rows="3"
                                                placeholder="Contoh: Maaf pak, kemarin salah upload foto..."></textarea>
                                        </div>

                                        <button type="submit"
                                            class="btn btn-danger w-100 rounded-pill fw-bold shadow-sm py-2">
                                            Kirim Bukti Tambahan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                                <div class="mb-3">
                                    @if ($payment->status === 'pending')
                                        <i class="bi bi-hourglass-split text-warning display-4"></i>
                                        <h5 class="fw-bold mt-3">Sedang Diverifikasi</h5>
                                        <p class="text-muted small">Pembayaran Anda sedang diperiksa oleh admin. Mohon
                                            menunggu.</p>
                                    @else
                                        <i class="bi bi-check-circle-fill text-success display-4"></i>
                                        <h5 class="fw-bold mt-3">Pembayaran Selesai</h5>
                                        <p class="text-muted small">Terima kasih, tagihan ini sudah lunas terverifikasi.</p>
                                    @endif
                                </div>
                                <a href="{{ route('payments.history') }}"
                                    class="btn btn-light rounded-pill btn-sm border">Kembali ke Riwayat</a>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
