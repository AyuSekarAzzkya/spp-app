@extends('template')

@section('content')
<div class="container py-5">
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="fw-bold text-dark mb-1">Riwayat Pembayaran SPP</h4>
            <p class="text-muted small mb-0">Pantau seluruh riwayat transaksi Anda secara real-time</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="datatable" style="width: 100%;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0 text-secondary small text-uppercase fw-bold">Tanggal</th>
                            <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">Nominal</th>
                            <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">Status</th>
                            <th class="text-center pe-4 py-3 border-0 text-secondary small text-uppercase fw-bold">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</div>
                                <small class="text-muted small text-capitalize">{{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('l') }}</small>
                            </td>
                            <td class="py-3">
                                <span class="fw-bold text-primary">
                                    Rp {{ number_format($payment->proofs->sum('amount'), 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if ($payment->status === 'pending')
                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2">
                                        Menunggu
                                    </span>
                                @elseif ($payment->status === 'approved')
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-4 py-3">
                                <a href="{{ route('payments.history.show', $payment->id) }}"
                                   class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 fw-bold">
                                    Detail <i class="bi bi-chevron-right small ms-1"></i>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $.fn.dataTable.ext.errMode = 'none';

        $('#datatable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthChange: false,
            columnDefs: [
                { orderable: false, targets: 3 }
            ],
            language: {
                search: "",
                searchPlaceholder: "Cari riwayat...",
                zeroRecords: `
                    <div class="py-5 text-center">
                        <i class="bi bi-inbox fs-1 d-block text-muted mb-2"></i>
                        <span class="text-muted small">Belum ada riwayat pembayaran yang ditemukan.</span>
                    </div>`,
                info: "Menampilkan _TOTAL_ data",
                paginate: {
                    next: "<i class='bi bi-arrow-right'></i>",
                    previous: "<i class='bi bi-arrow-left'></i>"
                }
            },
            initComplete: function() {
                $('.dataTables_filter input').addClass('form-control form-control-sm border-0 bg-light rounded-pill px-3 shadow-none');
                $('.dataTables_filter').addClass('p-3 border-bottom');
            }
        });
    });
</script>
@endpush