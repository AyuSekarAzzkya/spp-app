@extends('template')

@section('content')
    <div class="container mt-4">

        {{-- TITLE --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">Tagihan SPP Siswa</h3>
        </div>

        {{-- CARD: INFORMASI SISWA --}}
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

    {{-- CARD: DAFTAR TAGIHAN --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">
            Riwayat Tagihan SPP
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle" id="bills-table">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Bulan</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($bills as $bill)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F') }}</td>

                                <td>{{ \Carbon\Carbon::parse($bill->due_date)->format('d-m-Y') }}</td>

                                <td>
                                    @if ($bill->status === 'paid')
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-danger">Belum Lunas</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('admin.bills.show', $bill->id) }}"
                                        class="btn btn-sm btn-primary w-100 shadow-sm">
                                         Detail
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Belum ada tagihan untuk siswa ini.
                                </td>
                            </tr>
                        @endforelse
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
                pageLength: 10,
                ordering: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ Data",
                    zeroRecords: "Tidak ada data ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ Data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });
        });
    </script>
@endpush
