@extends('template')

@section('content')
    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">Tagihan SPP Siswa</h3>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-start">

                <div>
                    <h5 class="fw-bold mb-2">{{ $student->name }}</h5>

                    <p class="mb-1">
                        NIS:
                        <b>{{ $student->nis }}</b>
                    </p>

                    <p class="mb-1">
                        Kelas:
                        <b>{{ $student->class->name ?? '-' }}</b>
                    </p>

                    <p class="mb-0">
                        Tahun Ajaran Aktif:
                        <b>{{ $activeYear->year }}</b>
                    </p>


                    <span class="badge bg-primary p-2 px-3 fs-6 mt-2">
                        SPP: Rp{{ number_format($sppRate->amount) }}
                    </span>
                </div>

            </div>


        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">
            Riwayat Tagihan SPP
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="bills-table">
                    <thead class="bg-light">
                        <tr class="text-secondary small text-uppercase fw-bold">
                            <th class="ps-4">No</th>
                            <th>Bulan</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-center">Status</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bills as $bill)
                            <tr>
                                <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                                <td class="fw-bold text-dark">
                                    {{ \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F') }}
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($bill->status === 'paid')
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> Lunas
                                        </span>
                                    @else
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">
                                            <i class="fas fa-clock me-1"></i> Belum Lunas
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('admin.bills.show', $bill->id) }}"
                                        class="btn btn-sm btn-primary px-3 shadow-sm fw-bold rounded-pill"
                                        style="font-size: 11px;">
                                        <i class="fas fa-search me-1"></i> LIHAT DETAIL
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
            $('#bills-table').DataTable({
                // Mencegah error 'Unknown parameter' dengan mendefinisikan kolom secara eksplisit
                "columnDefs": [{
                        "orderable": false,
                        "targets": [0, 3, 4]
                    } // Matikan sorting di kolom No, Status, dan Aksi
                ],
                pageLength: 10, // Karena SPP biasanya 12 bulan
                responsive: true,
                language: {
                    search: "",
                    searchPlaceholder: "Cari tagihan...",
                    lengthMenu: "_MENU_ siswa per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ tagihan",
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                },
                drawCallback: function() {
                    // Styling agar senada dengan tabel sebelumnya
                    $('.dataTables_filter input').addClass(
                        'form-control form-control-sm border-0 bg-light shadow-none px-3 rounded-pill'
                    ).css('width', '200px');
                    $('.dataTables_length select').addClass(
                        'form-select form-select-sm border-0 bg-light shadow-none rounded-3');
                    $('.pagination').addClass('pagination-sm mt-3');
                }
            });
        });
    </script>
@endpush
