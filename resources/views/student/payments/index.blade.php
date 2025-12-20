@extends('template')

@section('content')
    <div class="container">
        <div class="page-inner">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Riwayat Pembayaran</h3>
                    <span class="text-muted">Daftar pembayaran SPP Anda</span>
                </div>

                <a href="{{ route('student.payments.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fa fa-plus me-1"></i> Bayar SPP
                </a>
            </div>


            @if (session('success'))
                <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif

            <div class="card shadow border-0">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->payment_date)->format('d M Y') }}</td>
                                        <td>Rp {{ number_format($item->paid_amount) }}</td>
                                        <td>
                                            @if ($item->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif ($item->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('student.payments.show', $item->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Belum ada pembayaran
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
