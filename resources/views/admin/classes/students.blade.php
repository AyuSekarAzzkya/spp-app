@extends('template')

@section('content')
    <div class="container">
        <div class="page-inner">

            {{-- PAGE TITLE --}}
            <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Siswa Kelas {{ $class->name }}</h3>
                    <span class="text-muted">Daftar lengkap siswa dalam kelas ini</span>
                </div>
                <a href="{{ route('classes.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="fa fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            {{-- CARD --}}
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="card-title fw-semibold mb-0">Daftar Siswa</h4>

                    {{-- Total siswa --}}
                    <span class="badge bg-primary p-2 px-3 fs-6">
                        Total: {{ $students->count() }}
                    </span>
                </div>

                <div class="card-body px-4 pb-4">

                    <div class="table-responsive">
                        <table class="table align-middle" id="datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>NIS</th>
                                    <th>NISN</th>
                                    <th>Jenis Kelamin</th>
                                    <th>No HP</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($students as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->nis }}</td>
                                        <td>{{ $item->nisn }}</td>
                                        <td>{{ $item->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                        <td>{{ $item->phone ?? '-' }}</td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Tidak ada siswa dalam kelas ini.
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
@endsection

@push('scripts')
    <script>
        $('#datatable').DataTable({
            pageLength: 10,
            ordering: true,
        });
    </script>
@endpush
