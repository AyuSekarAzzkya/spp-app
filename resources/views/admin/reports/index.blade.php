@extends('template')

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Laporan Pembayaran</h4>
                <p class="text-muted small mb-0">Kelola laporan harian, bulanan, dan tahunan secara efisien</p>
            </div>
            <div class="bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-4">
                <span class="small fw-bold">Total Pemasukan:</span>
                <span class="h5 fw-bolder mb-0 ms-2">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Berdasarkan Tanggal</label>
                        <input type="date" name="date" class="form-control border-0 bg-light rounded-pill px-3"
                            value="{{ request('date') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Berdasarkan Bulan</label>
                        <select name="month" class="form-select border-0 bg-light rounded-pill px-3">
                            <option value="">-- Pilih Bulan --</option>
                            @php
                                $months = [
                                    'Januari',
                                    'Februari',
                                    'Maret',
                                    'April',
                                    'Mei',
                                    'Juni',
                                    'Juli',
                                    'Agustus',
                                    'September',
                                    'Oktober',
                                    'November',
                                    'Desember',
                                ];
                            @endphp
                            @foreach ($months as $index => $name)
                                <option value="{{ $index + 1 }}" {{ request('month') == $index + 1 ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Berdasarkan Tahun</label>
                        <select name="year" class="form-select border-0 bg-light rounded-pill px-3">
                            @for ($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}"
                                    {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                                    Tahun {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold shadow-sm">
                                <i class="mdi mdi-filter-variant me-1"></i> Filter
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-light rounded-pill w-100 fw-bold border">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="datatable" class="table table-hover align-middle w-100">
                        <thead>
                            <tr class="bg-light">
                                <th class="border-0 px-3 py-3 rounded-start" width="50">No</th>
                                <th class="border-0 py-3">Nama Siswa</th>
                                <th class="border-0 py-3">Tanggal Bayar</th>
                                <th class="border-0 py-3">Status</th>
                                <th class="border-0 py-3 rounded-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="px-3 text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $payment->student->name }}</div>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('d F Y') }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $payment->status == 'success' ? 'success' : 'warning' }} bg-opacity-10 text-{{ $payment->status == 'success' ? 'success' : 'warning' }} rounded-pill px-3">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="fw-bolder text-dark">
                                        Rp {{ number_format($payment->total_amount ?? 0, 0, ',', '.') }}
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
            var table = $('#datatable').DataTable({
                responsive: true,
                dom: '<"d-flex justify-content-between align-items-center mb-4"Bf>rt<"d-flex justify-content-between align-items-center mt-4"ip>',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-success btn-sm rounded-pill px-3 border-0 shadow-sm',
                        text: '<i class="mdi mdi-file-excel me-1"></i> Excel'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm rounded-pill px-3 border-0 shadow-sm ms-2',
                        text: '<i class="mdi mdi-file-pdf-box me-1"></i> PDF'
                    }
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Cari laporan...",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'></i>",
                        next: "<i class='mdi mdi-chevron-right'></i>"
                    }
                }
            });

            $('.dataTables_filter input').addClass('form-control border-0 bg-light rounded-pill px-4 shadow-none');
        });
    </script>
@endpush
