@extends('template')

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4 gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    <i class="bi bi-wallet2 me-2 text-primary"></i>Laporan Pembayaran
                </h4>
                <p class="text-muted small mb-0">Pantau riwayat transaksi harian, bulanan, dan tahunan</p>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-4 shadow-sm">
                    <span class="small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Total Pemasukan</span>
                    <span class="h5 fw-bolder mb-0">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <a href="{{ route('reports.export.payments', request()->all()) }}"
                    class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-file-earmark-excel me-2"></i>Export
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Tanggal Spesifik</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light rounded-start-pill"><i
                                    class="bi bi-calendar-event"></i></span>
                            <input type="date" name="date" class="form-control border-0 bg-light rounded-end-pill"
                                value="{{ request('date') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Bulan</label>
                        <select name="month" class="form-select border-0 bg-light rounded-pill px-3">
                            <option value="">-- Semua Bulan --</option>
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
                        <label class="form-label small fw-bold text-muted">Tahun</label>
                        <select name="year" class="form-select border-0 bg-light rounded-pill px-3">
                            @for ($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}"
                                    {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                                    Tahun {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill flex-grow-1 fw-bold shadow-sm">
                                <i class="bi bi-search me-1"></i> Cari
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-light rounded-pill border px-3"
                                title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
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
                                <th class="border-0 py-3 text-center">Status</th>
                                <th class="border-0 py-3 rounded-end text-end px-3">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="px-3 text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $payment->student->name }}</div>
                                        <div class="small text-muted">Siswa Aktif</div>
                                    </td>
                                    <td class="text-muted">
                                        {{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="text-center">
                                        @if ($payment->status == 'success' || $payment->status == 'paid')
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                                <i class="bi bi-check-circle-fill me-1"></i> Sukses
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">
                                                <i class="bi bi-info-circle-fill me-1"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="fw-bolder text-dark text-end px-3">
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
            $('#datatable').DataTable({
                responsive: true,
                dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rtip',
                language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari siswa...",
                lengthMenu: "_MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "<i class='bi bi-chevron-left'></i>",
                    next: "<i class='bi bi-chevron-right'></i>"
                }
            }
            });

            $('.dataTables_filter input').addClass('form-control border-0 bg-light rounded-pill px-4 shadow-none');
        });
    </script>
@endpush
