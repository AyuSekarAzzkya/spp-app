@extends('template')

@section('content')
    <div class="container mt-3">
        <div class="page-inner">

            <div class="d-flex align-items-left flex-column flex-md-row justify-content-between pt-2 pb-3 mb-3">
                <div>
                    <h3 class="fw-bold mb-1">Edit Siswa</h3>
                    <span class="text-muted">Perbarui data siswa dengan mudah</span>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
            @endif

            <div class="card shadow-lg border-0" style="border-radius: 12px;">
                <div class="card-body px-4 py-4">

                    <form action="{{ route('students.update', $student->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" name="nis" class="form-control" value="{{ $student->nis }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" class="form-control" value="{{ $student->nisn }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ $student->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gender (Opsional)</label>
                            <select name="gender" class="form-select" >
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L" {{ $student->gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $student->gender == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No HP (Opsional)</label>
                            <input type="text" name="phone" class="form-control" placeholder="Masukkan No HP" value="{{ $student->phone }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat (Opsional)</label>
                            <input type="text" name="address" class="form-control" placeholder="Alamar Siswa" value="{{ $student->address }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="class_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($classes as $c)
                                    <option value="{{ $c->id }}"
                                        {{ $student->class_id == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} - {{ $c->major ?? '-' }} ({{ $c->grade_level ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="academic_year_id" class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($years as $y)
                                    <option value="{{ $y->id }}"
                                        {{ $student->academic_year_id == $y->id ? 'selected' : '' }}>
                                        {{ $y->year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ $student->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ $student->status == 'inactive' ? 'selected' : '' }}>Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">Update</button>
                            <a href="{{ route('students.index') }}" class="btn btn-secondary flex-fill">Kembali</a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
@endsection
