<div class="modal fade px-2" id="modalAddUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1.25rem;">

            <div class="w-100 bg-primary" style="height: 6px;"></div>

            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-person-plus fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Tambah User Baru</h5>
                        <p class="text-muted small mb-0">Dafrarkan pengguna baru ke dalam sistem</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-4">

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary mb-2">Nama Lengkap</label>
                        <div class="input-group input-group-merge border rounded-3 transition-all shadow-sm">
                            <span class="input-group-text border-0 bg-white">
                                <i class="bi bi-person text-muted"></i>
                            </span>
                            <input type="text" name="name" class="form-control border-0 ps-0 shadow-none py-2"
                                placeholder="Masukkan nama lengkap" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary mb-2">Alamat Email</label>
                        <div class="input-group input-group-merge border rounded-3 transition-all shadow-sm">
                            <span class="input-group-text border-0 bg-white">
                                <i class="bi bi-envelope text-muted"></i>
                            </span>
                            <input type="email" name="email" class="form-control border-0 ps-0 shadow-none py-2"
                                placeholder="nama@email.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary mb-2">Password</label>
                        <div class="input-group input-group-merge border rounded-3 transition-all shadow-sm">
                            <span class="input-group-text border-0 bg-white">
                                <i class="bi bi-lock text-muted"></i>
                            </span>
                            <input type="password" name="password" class="form-control border-0 ps-0 shadow-none py-2"
                                placeholder="Minimal 8 karakter" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary mb-2">Hak Akses / Role</label>
                        <div class="input-group border rounded-3 transition-all shadow-sm">
                            <span class="input-group-text border-0 bg-white">
                                <i class="bi bi-shield-check text-muted"></i>
                            </span>
                            <select name="role" class="form-select border-0 ps-0 shadow-none py-2" required>
                                <option value="" selected disabled>Pilih Role Pengguna</option>
                                <option value="admin">Administrator</option>
                                <option value="petugas">Petugas Sistem</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 px-4 pb-4">
                    <div class="d-flex w-100 gap-2">
                        <button type="button" class="btn btn-light rounded-3 fw-bold px-4 py-2 flex-grow-1 border"
                            data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit"
                            class="btn btn-primary rounded-3 fw-bold px-4 py-2 flex-grow-1 shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Simpan User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
