@extends('template')

@section('content')
    <div class="container">
        <div class="page-inner">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Detail Siswa</h3>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('students.index') }}">Siswa</a>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ $student->name }}
                            </li>
                        </ol>
                    </nav>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Kembali
                    </a>
                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-edit me-1"></i> Edit
                    </a>
                </div>
            </div>

            <div class="row g-3">

                {{-- IDENTITAS SINGKAT --}}
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 text-center">
                        <div class="card-body">

                            <div class="mb-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto"
                                    style="width:100px;height:100px;font-size:40px;">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                            </div>

                            <h5 class="fw-bold mb-1">{{ $student->name }}</h5>
                            <div class="text-muted small mb-2">
                                NIS {{ $student->nis }} • NISN {{ $student->nisn }}
                            </div>

                            <span
                                class="badge 
                            {{ $student->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($student->status) }}
                            </span>

                            <hr>

                            <ul class="list-group list-group-flush text-start small">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Gender</span>
                                    <strong>
                                        {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Kelas</span>
                                    <strong>{{ $student->class->name ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Tahun Ajaran</span>
                                    <strong>{{ $student->academicYear->year ?? '-' }}</strong>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>

                {{-- DETAIL INFORMASI --}}
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-semibold">
                            Informasi Lengkap
                        </div>
                        <div class="card-body">

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <div class="text-muted small">Email Login</div>
                                    <div class="fw-semibold">
                                        {{ $student->user->email ?? '-' }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="text-muted small">Nomor Telepon</div>
                                    <div class="fw-semibold">
                                        {{ $student->phone ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="text-muted small">Terdaftar</div>
                                    <div class="fw-semibold">
                                        {{ $student->created_at->format('d M Y') }}
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <small class="text-muted d-block mb-1">Alamat Lengkap</small>
                                    <div class="p-3 bg-light rounded" style="border-left: 4px solid #4e73df;">
                                        {{ !blank($student->address) ? $student->address : 'Alamat belum diisi.' }}
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3">
                                    <div class="text-muted small">Terakhir Update</div>
                                    <div class="fw-semibold">
                                        {{ $student->updated_at->format('d M Y H:i') }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
