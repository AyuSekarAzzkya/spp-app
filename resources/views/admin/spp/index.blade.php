@extends('template')

@section('content')
    <div class="container">
        <div class="page-inner">

            <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                <div>
                    <h3 class="fw-bold">Tarif SPP</h3>
                    <span class="text-muted">Atur tarif SPP berdasarkan tahun ajaran</span>
                </div>
            </div>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Table --}}
            <div class="card shadow border-0 mt-5">

                <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 fw-semibold">Daftar Tarif SPP</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdd">
                        <i class="fa fa-plus me-1"></i> Tambah Tarif SPP
                    </button>
                </div>

                <div class="card-body table-responsive">

                    <table class="table align-middle" id="datatable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tahun Ajaran</th>
                                <th>Nominal SPP</th>
                                <th>Keterangan</th>
                                <th width="18%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rates as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->academicYear->year }}</td>
                                    <td>Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                                    <td>{{ $item->description ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">

                                            {{-- EDIT --}}
                                            <button class="btn btn-sm btn-outline-primary btn-edit"
                                                data-id="{{ $item->id }}" data-year="{{ $item->academic_year_id }}"
                                                data-amount="{{ $item->amount }}" data-desc="{{ $item->description }}"
                                                data-bs-toggle="modal" data-bs-target="#modalEdit">
                                                Edit
                                            </button>

                                            {{-- DELETE --}}
                                            <button class="btn btn-sm btn-outline-danger btn-delete"
                                                data-id="{{ $item->id }}">
                                                Hapus
                                            </button>

                                            <form id="formDelete{{ $item->id }}"
                                                action="{{ route('spp.delete', $item->id) }}" method="POST"
                                                class="d-none">
                                                @csrf @method('DELETE')
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>

    {{-- Modal Add --}}
    <div class="modal fade" id="modalAdd" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tarif SPP</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('spp.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Tahun Ajaran</label>
                            <select class="form-control" name="academic_year_id" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year->id }}">{{ $year->year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Nominal SPP</label>
                            <input type="number" class="form-control" name="amount" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Tarif SPP</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="formEdit" method="POST">
                    @csrf @method('PUT')

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="academic_year_id" id="editYear" class="form-control" required>
                                @foreach ($years as $y)
                                    <option value="{{ $y->id }}">
                                        {{ $y->year }} ({{ $y->semester == 1 ? 'Ganjil' : 'Genap' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nominal SPP</label>
                            <input type="number" name="amount" id="editAmount" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" id="editDesc" class="form-control"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary w-100">Update</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                pageLength: 10,
                ordering: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ Data",
                    zeroRecords: "Tidak ada data ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ Data Tarif",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });
        });

        // Fill Edit Modal
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                document.querySelector('#formEdit').action = "/spp-rates/update/" + id;

                document.querySelector('#editYear').value = this.dataset.year;
                document.querySelector('#editAmount').value = this.dataset.amount;
                document.querySelector('#editDesc').value = this.dataset.desc;
            });
        });

        // Delete
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete')) {
                e.preventDefault();
                const id = e.target.closest('.btn-delete').dataset.id;

                Swal.fire({
                    title: "Hapus Data?",
                    text: "Data yang dihapus tidak dapat dikembalikan.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, hapus",
                    cancelButtonText: "Batal",
                }).then(result => {
                    if (result.isConfirmed) {
                        document.getElementById('formDelete' + id).submit();
                    }
                });
            }
        });
    </script>
@endpush
