<div class="modal fade px-2" id="modalEditUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1.25rem;">
            
            <div class="w-100 bg-warning" style="height: 6px;"></div>

            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-pencil-square fs-4 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Edit Profil Pengguna</h5>
                        <p class="text-muted small mb-0">Sesuaikan informasi dasar dan otorisasi</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditUser" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body px-4 py-4">
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary mb-2">Nama Lengkap</label>
                        <div class="input-group input-group-merge border rounded-3 transition-all shadow-sm">
                            <span class="input-group-text border-0 bg-white">
                                <i class="bi bi-person text-muted"></i>
                            </span>
                            <input type="text" id="editName" name="name" 
                                class="form-control border-0 ps-0 shadow-none py-2" 
                                placeholder="Contoh: Ahmad Dhani" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary mb-2">Alamat Email</label>
                        <div class="input-group input-group-merge border rounded-3 transition-all shadow-sm">
                            <span class="input-group-text border-0 bg-white">
                                <i class="bi bi-envelope text-muted"></i>
                            </span>
                            <input type="email" id="editEmail" name="email" 
                                class="form-control border-0 ps-0 shadow-none py-2" 
                                placeholder="name@company.com" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-secondary mb-2">Hak Akses</label>
                            <div class="input-group border rounded-3 transition-all shadow-sm">
                                <span class="input-group-text border-0 bg-white">
                                    <i class="bi bi-shield-check text-muted"></i>
                                </span>
                                <select id="editRole" name="role" class="form-select border-0 ps-0 shadow-none py-2" required>
                                    <option value="admin">Administrator</option>
                                    <option value="petugas">Petugas</option>
                                    <option value="siswa">Siswa</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border border-dashed border-secondary border-opacity-25">
                        <label class="form-label small fw-bold text-dark mb-1">Keamanan (Opsional)</label>
                        <p class="text-muted mb-3" style="font-size: 0.75rem;">Isi kolom di bawah jika ingin mengganti password</p>
                        
                        <div class="input-group border bg-white rounded-3 transition-all">
                            <span class="input-group-text border-0 bg-transparent">
                                <i class="bi bi-lock text-muted"></i>
                            </span>
                            <input type="password" name="password" 
                                class="form-control border-0 ps-0 shadow-none py-2 bg-transparent" 
                                placeholder="Password baru (Min. 8 Karakter)">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4">
                    <div class="d-flex w-100 gap-2">
                        <button type="button" class="btn btn-light rounded-3 fw-bold px-4 py-2 flex-grow-1 border" data-bs-dismiss="modal">
                            Batalkan
                        </button>
                        <button type="submit" class="btn btn-warning rounded-3 fw-bold px-4 py-2 flex-grow-1 shadow-sm text-dark">
                            Perbarui Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>