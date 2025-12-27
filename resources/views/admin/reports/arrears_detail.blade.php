@extends('template')

@section('content')
    <div class="container-fluid py-4">
        {{-- BREADCRUMB & TITLE --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('reports.arrears') }}"
                                class="text-decoration-none text-muted">Laporan Tunggakan</a></li>
                        <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">Detail Siswa</li>
                    </ol>
                </nav>
                <h3 class="fw-bold text-dark mb-0">Detail Tunggakan SPP</h3>
            </div>
            <a href="{{ route('reports.arrears') }}" class="btn btn-light border rounded-pill px-3 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="row g-4">
            {{-- SIDEBAR INFO: PROFIL SISWA --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 80px; height: 80px;">
                            <i class="fas fa-user-graduate fa-3x"></i>
                        </div>
                        <h5 class="fw-bold mb-1 text-dark">{{ $student->name }}</h5>
                        <p class="text-muted small mb-3">{{ $student->nis }} — {{ $student->class->name }}</p>

                        <div class="row g-2 text-start mt-2">
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted d-block">Status Tagihan</small>
                                    <span
                                        class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">
                                        <i class="fas fa-exclamation-circle me-1"></i> Menunggak
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 bg-danger text-white rounded-3 shadow-sm">
                                    <small class="opacity-75 d-block">Total Hutang SPP</small>
                                    <h3 class="fw-bold mb-0">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>

                        @php
                            $phone = preg_replace('/[^0-9]/', '', $student->phone ?? '');
                            if (str_starts_with($phone, '0')) {
                                $phone = '62' . substr($phone, 1);
                            }
                            $tahun = request('year') ? ' tahun ' . request('year') : '';
                            $waMessage = urlencode(
                                "Yth. Orang tua/wali dari {$student->name},\n\nKami informasikan bahwa terdapat tunggakan SPP{$tahun} sebesar *Rp " .
                                    number_format($totalTunggakan, 0, ',', '.') .
                                    "*.\n\nMohon segera melakukan pembayaran di loket sekolah. Terima kasih.",
                            );
                        @endphp

                        @if ($phone)
                            <div class="d-grid mt-4">
                                <a href="https://wa.me/{{ $phone }}?text={{ $waMessage }}" target="_blank"
                                    class="btn btn-success btn-lg rounded-pill shadow-sm">
                                    <i class="fab fa-whatsapp me-2"></i> Kirim Notifikasi WA
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- MAIN CONTENT: TABEL TUNGGAKAN --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <h5 class="fw-bold mb-0">Rincian Per Bulan</h5>

                            {{-- FILTER TAHUN --}}
                            <form method="GET" class="d-flex gap-2">
                                <select name="year"
                                    class="form-select form-select-sm border-0 bg-light rounded-pill px-3"
                                    style="width: 150px;">
                                    <option value="">Semua Tahun</option>
                                    @foreach ($bills->pluck('year')->unique()->sortDesc() as $year)
                                        <option value="{{ $year }}"
                                            {{ request('year') == $year ? 'selected' : '' }}>
                                            Tahun {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm rounded-circle shadow-sm">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border-0" id="datatable">
                                <thead class="bg-light">
                                    <tr class="text-secondary small text-uppercase">
                                        <th class="ps-3 border-0">#</th>
                                        <th class="border-0">Bulan</th>
                                        <th class="border-0">Tahun</th>
                                        <th class="border-0 text-end">Nominal</th>
                                        <th class="border-0 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="border-0">
                                    @forelse ($bills as $bill)
                                        <tr>
                                            <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                                            <td><span
                                                    class="fw-bold text-dark">{{ \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F') }}</span>
                                            </td>
                                            <td>{{ $bill->year }}</td>
                                            <td class="text-end fw-semibold">Rp
                                                {{ number_format($bill->sppRate->amount ?? 0, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 px-3 py-2 rounded-pill small">
                                                    Belum Bayar
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-check-circle fa-3x mb-3 text-success opacity-25"></i>
                                                <p class="mb-0">Tidak ada tunggakan ditemukan</p>
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
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "",
                    lengthMenu: "_MENU_ data per halaman",
                    searchPlaceholder: "Cari rincian bulan...",
                    info: "Menampilkan _TOTAL_ bulan menunggak",
                    paginate: {
                        previous: '<i class="fas fa-angle-left"></i>',
                        next: '<i class="fas fa-angle-right"></i>'
                    }
                },
                drawCallback: function() {
                    $('.dataTables_filter input').addClass(
                        'form-control form-control-sm border-0 bg-light shadow-none px-3 rounded-pill'
                    ).css('width', '250px');
                    $('.dataTables_info').addClass('small text-muted mt-3');
                    $('.pagination').addClass('pagination-sm mt-3');
                }
            });
        });
    </script>
@endpush
