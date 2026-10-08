<div class="modal fade px-2" id="modalEditUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="mdi mdi-account-edit text-white me-2 fs-5"></i> Edit Profil Pengguna
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditUser" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Nama Lengkap</label>
                        <input type="text" id="editName" name="name" class="form-control" placeholder="Contoh: Ahmad Dhani" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Alamat Email</label>
                        <input type="email" id="editEmail" name="email" class="form-control" placeholder="name@company.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Hak Akses / Role</label>
                        <select id="editRole" name="role" class="form-select" required>
                            <option value="admin">Administrator</option>
                            <option value="petugas">Petugas</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <label class="form-label small fw-bold text-dark mb-1">Ganti Password (Opsional)</label>
                        <p class="text-muted mb-2 small">Kosongkan jika tidak ingin mengubah password akun.</p>
                        <input type="password" name="password" class="form-control" placeholder="Password baru (Min. 4 Karakter)">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center">
                        <i class="mdi mdi-content-save me-1"></i> Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>