<div class="modal fade px-2" id="modalAddAY" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="mdi mdi-calendar-plus text-white me-2 fs-5"></i> Tambah Tahun Ajaran
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('academic-years.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Tahun Ajaran</label>
                        <input type="text" name="year" class="form-control" placeholder="Contoh: 2025/2026" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center">
                        <i class="mdi mdi-check-circle me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
