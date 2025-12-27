<div class="modal fade px-2" id="modalEditAY" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1.25rem;">

            <div class="w-100 bg-warning" style="height: 6px;"></div>

            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-pencil-square fs-4 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Edit Tahun Ajaran</h5>
                        <p class="text-muted small mb-0">Perbarui periode akademik</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form method="POST" id="formEditAY">
                @csrf
                @method('PUT') {{-- Pastikan gunakan PUT jika route-nya resource/update --}}

                <div class="modal-body px-4 py-4">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary mb-2">Tahun Ajaran</label>
                        <div class="input-group input-group-merge border rounded-3 transition-all shadow-sm">
                            <span class="input-group-text border-0 bg-white">
                                <i class="bi bi-hash text-muted"></i>
                            </span>
                            <input type="text" name="year" id="editYear"
                                class="form-control border-0 ps-0 shadow-none py-2" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4">
                    <div class="d-flex w-100 gap-2">
                        <button type="button" class="btn btn-light rounded-3 fw-bold px-4 py-2 flex-grow-1 border"
                            data-bs-dismiss="modal">
                            Batalkan
                        </button>
                        <button type="submit"
                            class="btn btn-warning rounded-3 fw-bold px-4 py-2 flex-grow-1 shadow-sm text-dark">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
