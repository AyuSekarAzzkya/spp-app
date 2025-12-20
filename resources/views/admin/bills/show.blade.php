@extends('template')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Detail Pembayaran</h3>
        <a href="{{ route('billing.students') }}" class="btn btn-secondary shadow-sm"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Rincian Transaksi</div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Nama Siswa</th>
                            <td>: {{ $payment->student->name }}</td>
                        </tr>
                        <tr>
                            <th>NIS</th>
                            <td>: {{ $payment->student->nis }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Bayar</th>
                            <td>: {{ \Carbon\Carbon::parse($payment->payment_date)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Total Bayar</th>
                            <td>: <span class="fw-bold text-primary">Rp{{ number_format($payment->paid_amount) }}</span></td>
                        </tr>
                        <tr>
                            <th>Status Konfirmasi</th>
                            <td>: 
                                @if($payment->status == 'pending')
                                    <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                                @elseif($payment->status == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        @if($payment->note)
                        <tr>
                            <th>Catatan Admin</th>
                            <td>: <span class="text-danger italic">{{ $payment->note }}</span></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Item Tagihan yang Dibayar</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Bulan Tagihan</th>
                                    <th>Tahun Ajaran</th>
                                    <th class="text-end">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payment->details as $detail)
                                <tr>
                                    <td>{{ \Carbon\Carbon::create()->month($detail->bill->month)->translatedFormat('F') }}</td>
                                    <td>{{ $detail->bill->academicYear->year ?? '-' }}</td>
                                    <td class="text-end">Rp{{ number_format($detail->amount) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">Total</th>
                                    <th class="text-end">Rp{{ number_format($payment->paid_amount) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Bukti Pembayaran</div>
                <div class="card-body text-center">
                    @if($payment->proof_image)
                        <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $payment->proof_image) }}" class="img-fluid rounded shadow-sm border" alt="Bukti Bayar">
                        </a>
                        <p class="small text-muted mt-2">Klik gambar untuk memperbesar</p>
                    @else
                        <div class="py-5 bg-light rounded text-muted">Tidak ada gambar</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection