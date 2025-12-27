@extends('template')

@section('content')
<div class="container mt-3">
    <div class="page-inner">

        <div class="d-flex align-items-left flex-column flex-md-row justify-content-between pt-2 pb-3">
            <div>
                <h3 class="fw-bold mb-1">Tambah Siswa</h3>
                <span class="text-muted">Isi data siswa dengan lengkap</span>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
        @endif

        {{-- Card Form --}}
        <div class="card shadow-lg border-0 mt-2" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h5 class="card-title fw-semibold mb-0">Form Tambah Siswa</h5>
            </div>

            <div class="card-body px-4 pb-4">
                <form action="{{ route('students.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">NIS</label>
                        <input type="text" name="nis" class="form-control" placeholder="Masukkan NIS" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">NISN</label>
                        <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN (opsional)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama lengkap siswa" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No HP (Opsional)</label>
                        <input type="text" name="phone" class="form-control" placeholder="Masukkan No HP">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat (Opsional)</label>
                        <input type="text" name="address" class="form-control" placeholder="Alamar Siswa">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->major ?? '-' }} ({{ $c->grade_level ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="academic_year_id" class="form-select" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach ($years as $y)
                                <option value="{{ $y->id }}">{{ $y->year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50 py-3">Simpan</button>
                        <a href="{{ route('students.index') }}" class="btn btn-secondary w-50 py-3">Kembali</a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
