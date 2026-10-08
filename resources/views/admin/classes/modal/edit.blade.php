<div class="modal fade px-2" id="modalEditClass" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="mdi mdi-pencil-box-outline text-white me-2 fs-5"></i> Edit Data Kelas
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" id="formEditClass">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Nama Kelas</label>
                        <input type="text" id="editName" name="name" class="form-control" placeholder="Contoh: X RPL 1" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-dark">Jurusan</label>
                            <input type="text" id="editMajor" name="major" class="form-control" placeholder="RPL / AKL">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-dark">Tingkat</label>
                            <input type="text" id="editLevel" name="grade_level" class="form-control" placeholder="X / XI / XII">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center">
                        <i class="mdi mdi-content-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>