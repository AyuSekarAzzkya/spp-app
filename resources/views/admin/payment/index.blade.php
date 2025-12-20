@extends('template')

@section('content')
    <div class="container">
        <div class="page-inner">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="fw-bold mb-1">Pembayaran SPP</h3>
                    <span class="text-muted">Daftar pembayaran masuk dari siswa</span>
                </div>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table align-middle" id="datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Siswa</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $item->student->name }}</td>
                                        <td>{{ $item->payment_date }}</td>
                                        <td>Rp {{ number_format($item->paid_amount) }}</td>
                                        <td>
                                            @if ($item->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif ($item->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.payments.show', $item->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#datatable').DataTable();
    </script>
@endpush
