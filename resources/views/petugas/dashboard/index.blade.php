@extends('template')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        .card {
            border-radius: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .icon-shape {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .bg-gradient-warning {
            background: linear-gradient(45deg, #f6ad55, #ed8936);
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, #fc8181, #f56565);
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #68d391, #48bb78);
        }

        /* Styling DataTables agar menyatu dengan desain */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0;
            margin-left: 5px;
        }

        div.dataTables_wrapper div.dataTables_info {
            padding-top: 1.5em;
            padding-left: 1.5em;
            color: #6c757d;
            font-size: 0.85rem;
        }

        div.dataTables_wrapper div.dataTables_paginate {
            padding: 1.5em;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #edf2f7;
        }

        .badge-soft-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-soft-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-soft-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-soft-secondary {
            background-color: #f3f4f6;
            color: #374151;
        }
    </style>

    <div class="container-fluid py-4">
        {{-- HEADER --}}
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h3 class="fw-bold text-dark mb-1">Dashboard Petugas</h3>
                <p class="text-muted mb-0">Selamat datang kembali! Berikut ringkasan aktivitas hari ini.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="d-inline-flex align-items-center bg-white border rounded-pill px-3 py-2 shadow-sm">
                    <i class="mdi mdi-calendar-range text-primary me-2"></i>
                    <span class="fw-semibold small">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </div>

        {{-- ROW WIDGET UTAMA --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p class="text-muted fw-medium small mb-1">Pending Approval</p>
                                <h2 class="fw-bold mb-0">{{ $pendingPaymentsCount }}</h2>
                                <span class="text-warning small fw-semibold"><i class="mdi mdi-clock-check"></i> Butuh
                                    Verifikasi</span>
                            </div>
                            <div class="icon-shape bg-gradient-warning text-white shadow-warning">
                                <i class="mdi mdi-clock-outline mdi-24px"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p class="text-muted fw-medium small mb-1">Siswa Menunggak</p>
                                <h2 class="fw-bold mb-0">{{ $arrearsCount }}</h2>
                                <span class="text-danger small fw-semibold"><i class="mdi mdi-alert-circle"></i> Jatuh
                                    Tempo</span>
                            </div>
                            <div class="icon-shape bg-gradient-danger text-white shadow-danger">
                                <i class="mdi mdi-account-off mdi-24px"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p class="text-muted fw-medium small mb-1">Total Setoran Hari Ini</p>
                                <h2 class="fw-bold mb-0">Rp {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}</h2>
                                <span class="text-success small fw-semibold"><i class="mdi mdi-trending-up"></i> Dana
                                    Masuk</span>
                            </div>
                            <div class="icon-shape bg-gradient-success text-white shadow-success">
                                <i class="mdi mdi-cash-multiple mdi-24px"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @if ($pendingPaymentsCount > 0)
                    <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center"
                        style="border-radius: 12px;">
                        <i class="mdi mdi-bell-ring mdi-24px me-3 text-warning"></i>
                        <div>
                            <strong>Perhatian:</strong> Ada {{ $pendingPaymentsCount }} transaksi menunggu persetujuan. <a
                                href="{{ route('admin.payments.index') }}" class="alert-link">Verifikasi Sekarang</a>.
                        </div>
                    </div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-4 px-4">
                        <h5 class="fw-bold mb-0">Aktivitas Transaksi Terbaru</h5>
                    </div>
                    <div class="card-body p-0 pb-3">
                        <div class="table-responsive">
                            <table id="transactionTable" class="table table-hover align-middle mb-0 w-100">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th class="ps-4">Siswa</th>
                                        <th>Nominal</th>
                                        <th>Metode</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentActivities as $item)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-soft-primary rounded-circle p-2 me-3 text-primary fw-bold d-flex align-items-center justify-content-center"
                                                        style="width: 38px; height: 38px;">
                                                        {{ substr($item->student->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $item->student->name }}</div>
                                                        <div class="small text-muted">
                                                            {{ $item->student->class->name ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-bold text-dark">
                                                Rp {{ number_format($item->proofs->sum('amount'), 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <span class="small fw-medium text-secondary">
                                                    <i
                                                        class="mdi mdi-credit-card-outline me-1"></i>{{ strtoupper($item->payment_method ?? 'Cash') }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass =
                                                        [
                                                            'pending' => 'badge-soft-warning',
                                                            'approved' => 'badge-soft-success',
                                                            'rejected' => 'badge-soft-danger',
                                                        ][$item->status] ?? 'badge-soft-secondary';
                                                @endphp
                                                <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('admin.payments.show', $item->id) }}"
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3">
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
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#transactionTable').DataTable({
                "pageLength": 10,
                "language": {
                    "search": "Cari Transaksi:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data tersedia",
                    "paginate": {
                        "previous": '<i class="mdi mdi-chevron-left"></i>',
                        "next": '<i class="mdi mdi-chevron-right"></i>'
                    }
                },
                "dom": '<"d-flex justify-content-between align-items-center p-4"<"fw-bold"f><"small"l>>rtip'
            });
        });
    </script>
@endpush
