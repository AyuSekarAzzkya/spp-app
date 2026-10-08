<div class="modal fade px-2" id="modalAddUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="mdi mdi-account-plus text-white me-2 fs-5"></i> Tambah User Baru
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Alamat Email</label>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 4 karakter" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Hak Akses / Role</label>
                        <select name="role" class="form-select" required>
                            <option value="" selected disabled>Pilih Role Pengguna</option>
                            <option value="admin">Administrator</option>
                            <option value="petugas">Petugas Sistem</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center">
                        <i class="mdi mdi-check-circle me-1"></i> Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
