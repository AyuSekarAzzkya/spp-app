@extends('template')

@section('content')
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Laporan Tunggakan SPP</h4>
            <p class="text-muted mb-0">Ringkasan tunggakan per siswa</p>
        </div>

        {{-- FILTER --}}
        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <select name="class_id" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select name="year" class="form-select">
                    <option value="">Semua Tahun</option>
                    @for ($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        {{-- TABLE --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Total Bulan Menunggak</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($arrears as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->student->name }}</td>
                                <td>{{ $item->student->class->name }}</td>
                                <td class="text-center">
                                    <span class="badge bg-danger">
                                        {{ $item->total_months }} Bulan
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark">
                                        Menunggak
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('arrears.detail', $item->student_id) }}"
                                        class="btn btn-sm btn-primary">
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
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#datatable').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: ['excel', 'pdf'],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    zeroRecords: "Tidak ada data tunggakan",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });
        });
    </script>
@endpush
