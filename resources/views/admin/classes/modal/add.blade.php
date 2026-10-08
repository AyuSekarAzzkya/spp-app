<div class="modal fade px-2" id="modalAddClass" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="mdi mdi-google-classroom text-white me-2 fs-5"></i> Tambah Kelas Baru
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('classes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Nama Kelas</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: X RPL 1" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-dark">Jurusan</label>
                            <input type="text" name="major" class="form-control" placeholder="RPL / AKL / MPLB" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-dark">Tingkat</label>
                            <input type="text" name="grade_level" class="form-control" placeholder="X / XI / XII" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center">
                        <i class="mdi mdi-check-circle me-1"></i> Simpan Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>