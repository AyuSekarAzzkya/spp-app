@extends('template')

@section('content')
    @php
        \Carbon\Carbon::setLocale('id');
        $totalTransfer = $payment->proofs->sum('amount');
        $totalDialokasikan = $payment->details->sum('amount');
    @endphp

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Detail Pembayaran</h3>
            <a href="{{ route('billing.students') }}" class="btn btn-secondary shadow-sm">
                Kembali
            </a>
        </div>

        <div class="row">

            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Rincian Transaksi</div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
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
                                <td>: {{ $payment->created_at->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Total Transfer</th>
                                <td>
                                    : <span class="fw-bold text-primary">
                                        Rp {{ number_format($totalTransfer, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>:
                                    @if ($payment->status === 'pending')
                                        <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                                    @elseif($payment->status === 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                            </tr>

                            @if ($payment->note)
                                <tr>
                                    <th>Catatan Admin</th>
                                    <td>: <span class="text-danger fst-italic">{{ $payment->note }}</span></td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold">Alokasi Pembayaran ke Tagihan</div>
                    <div class="card-body p-0">

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bulan</th>
                                        <th>Tahun</th>
                                        <th class="text-end">Nominal Dibayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payment->details as $detail)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::create()->month($detail->bill->month)->translatedFormat('F') }}
                                            </td>
                                            <td>{{ $detail->bill->year }}</td>
                                            <td class="text-end">
                                                Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2" class="text-end">Total</th>
                                        <th class="text-end">
                                            Rp {{ number_format($totalDialokasikan, 0, ',', '.') }}
                                        </th>
                                    </tr>

                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold">Bukti Transfer</div>
                    <div class="card-body">

                        @forelse($payment->proofs as $proof)
                            <div class="mb-3 text-center">
                                <a href="{{ asset('storage/' . $proof->image_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $proof->image_path) }}"
                                        class="img-fluid rounded shadow-sm border" alt="Bukti Transfer">
                                </a>
                                <div class="small text-muted mt-1">
                                    Nominal: Rp {{ number_format($proof->amount, 0, ',', '.') }}
                                </div>
                            </div>
                        @empty
                            <div class="py-5 bg-light rounded text-muted text-center">
                                Tidak ada bukti pembayaran
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection