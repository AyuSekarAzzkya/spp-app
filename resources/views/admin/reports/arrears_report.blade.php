@extends('template')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan Tunggakan SPP
            </h4>
            <p class="text-muted mb-0">Ringkasan data siswa yang belum menyelesaikan pembayaran</p>
        </div>
        {{-- Tombol Export dipindah ke pojok kanan atas agar lebih bersih --}}
        <a href="{{ route('reports.export.arrears', request()->all()) }}" 
           class="btn btn-success shadow-sm px-4 rounded-3">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
        </a>
    </div>

    {{-- FILTER CARD --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-body p-3">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Filter Kelas</label>
                    <select name="class_id" class="form-select border-0 bg-light">
                        <option value="">Semua Kelas</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Tahun Ajaran</label>
                    <select name="year" class="form-select border-0 bg-light">
                        <option value="">Semua Tahun</option>
                        @for ($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-4 d-grid gap-2 d-md-flex">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ request()->url() }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle w-100">
                    <thead>
                        <tr class="text-muted text-uppercase small">
                            <th width="50" class="text-center">No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th class="text-center">Durasi Tunggakan</th>
                            <th class="text-center">Status</th>
                            <th width="100" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($arrears as $item)
                            <tr>
                                <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold">{{ $item->student->name }}</div>
                                    <small class="text-muted">NIS: {{ $item->student->nis ?? '-' }}</small>
                                </td>
                                <td>{{ $item->student->class->name }}</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-2">
                                        {{ $item->total_months }} Bulan
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark small fw-medium">
                                        <i class="bi bi-clock-history me-1"></i>Menunggak
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('arrears.detail', $item->student_id) }}" 
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
@endsection

@push('scripts')
<script>
    $(function() {
        $('#datatable').DataTable({
            responsive: true,
            // Hilangkan default button datatables karena kita pakai tombol custom di atas
            dom: '<"d-flex justify-content-between mb-3"lf>rtip',
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
    });
</script>
@endpush