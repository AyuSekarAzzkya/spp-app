@extends('template')

@section('content')
<div class="page-inner">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}" class="text-decoration-none" style="color: var(--orange-brand);">Siswa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Siswa</li>
                </ol>
            </nav>
            <h2 class="page-header-title mb-1">Tambah Siswa Baru</h2>
            <p class="page-header-subtitle mb-0">Lengkapi formulir identitas siswa untuk mendaftarkan akun ke sistem SPP.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('students.index') }}" class="btn btn-secondary d-flex align-items-center">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <strong class="d-block mb-1"><i class="mdi mdi-alert-circle me-1"></i> Terdapat kesalahan input formulir:</strong>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="card mb-4">
        <div class="card-header">
            <span class="d-flex align-items-center fw-bold">
                <i class="mdi mdi-account-edit me-2" style="color: var(--orange-brand); font-size: 1.2rem;"></i>
                Formulir Identitas & Akademik Siswa
            </span>
        </div>
        <div class="card-body">
            <form action="{{ route('students.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Nomor Induk Siswa (NIS) <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis') }}" placeholder="Contoh: 12108543" required>
                        <small class="text-muted">NIS juga digunakan sebagai kata sandi login default akun siswa.</small>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Nomor Induk Siswa Nasional (NISN)</label>
                        <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn') }}" placeholder="Contoh: 0054321987 (Opsional)">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label fw-semibold text-dark">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan nama lengkap siswa" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Jenis Kelamin</label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                            <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Rombongan Belajar (Kelas) <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} {{ $c->major ? '— ' . $c->major : '' }} (Tingkat {{ $c->grade_level ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Tahun Ajaran Masuk <span class="text-danger">*</span></label>
                        <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach ($years as $y)
                                <option value="{{ $y->id }}" {{ old('academic_year_id') == $y->id || $y->is_active ? 'selected' : '' }}>
                                    {{ $y->year }} {{ $y->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Nomor Handphone / WhatsApp</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Contoh: 081234567890">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Alamat Tempat Tinggal</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="Alamat lengkap domisili">
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('students.index') }}" class="btn btn-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 d-flex align-items-center">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Data Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
