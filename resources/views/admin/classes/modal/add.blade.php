<div class="modal fade px-2" id="modalAddClass" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1.25rem;">
            
            <div class="w-100 bg-primary" style="height: 6px;"></div>

            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-door-open fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Tambah Kelas Baru</h5>
                        <p class="text-muted small mb-0">Manajemen data rombongan belajar</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('classes.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-4">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary mb-2">Nama Kelas</label>
                        <div class="input-group input-group-merge border rounded-3 transition-all shadow-sm">
                            <span class="input-group-text border-0 bg-white">
                                <i class="bi bi-card-text text-muted"></i>
                            </span>
                            <input type="text" name="name" 
                                class="form-control border-0 ps-0 shadow-none py-2" 
                                placeholder="Contoh: X RPL 1" required>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-4">
                            <label class="form-label small fw-bold text-secondary mb-2">Jurusan</label>
                            <div class="input-group input-group-merge border rounded-3 transition-all shadow-sm">
                                <span class="input-group-text border-0 bg-white">
                                    <i class="bi bi-gear text-muted"></i>
                                </span>
                                <input type="text" name="major" 
                                    class="form-control border-0 ps-0 shadow-none py-2" 
                                    placeholder="RPL / AKL" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label small fw-bold text-secondary mb-2">Tingkat</label>
                            <div class="input-group input-group-merge border rounded-3 transition-all shadow-sm">
                                <span class="input-group-text border-0 bg-white">
                                    <i class="bi bi-layers text-muted"></i>
                                </span>
                                <input type="text" name="grade_level" 
                                    class="form-control border-0 ps-0 shadow-none py-2" 
                                    placeholder="X / XI / XII" required>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 px-4 pb-4">
                    <div class="d-flex w-100 gap-2">
                        <button type="button" class="btn btn-light rounded-3 fw-bold px-4 py-2 flex-grow-1 border" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4 py-2 flex-grow-1 shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Simpan Kelas
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>