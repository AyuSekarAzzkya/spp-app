@extends('template')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <h4 class="fw-bold mb-2">Manajemen Tagihan Siswa</h4>

                    <div class="text-muted">
                        Tahun Ajaran Aktif:
                        <b class="text-dark">{{ $activeYear->year }}</b>
                        <br>
                        Tarif SPP:
                        @if ($sppRates->count())
                            <b class="text-dark">Rp{{ number_format($sppRates->first()->amount) }}</b>
                        @else
                            <b class="text-danger">Belum diatur</b>
                        @endif
                    </div>
                </div>

                <form action="{{ route('bills.generateAll') }}" method="POST">
                    @csrf
                    <button class="btn btn-primary px-4">
                        Generate Tagihan Semua Siswa
                    </button>
                </form>
            </div>
        </div>


        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">
                Daftar Siswa
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table id="students-table" class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>NIS</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($students as $s)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $s->name }}</td>
                                    <td>{{ $s->class->name ?? '-' }}</td>
                                    <td>{{ $s->nis }}</td>
                                    <td>
                                        <a href="{{ route('billing.index', $s->id) }}" class="btn btn-sm btn-primary w-100">
                                            Lihat Tagihan
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
            $('#students-table').DataTable({
                pageLength: 10,
                ordering: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ siswa",
                    zeroRecords: "Tidak ada data ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ siswa",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });
        });
    </script>
@endpush
