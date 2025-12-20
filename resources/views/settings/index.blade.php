@extends('template')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <h4 class="card-title mb-1">Pengaturan Aplikasi</h4>
                    <p class="card-description text-muted mb-4">
                        Atur informasi sekolah & konfigurasi SPP
                    </p>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Sekolah</label>
                            <input type="text" class="form-control shadow-sm" name="school_name"
                                value="{{ $settings['school_name'] ?? '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat Sekolah</label>
                            <textarea class="form-control shadow-sm" rows="3" name="school_address" required>{{ $settings['school_address'] ?? '' }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Telepon Sekolah</label>
                            <input type="text" class="form-control shadow-sm" name="school_phone"
                                value="{{ $settings['school_phone'] ?? '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">No Rekening</label>
                            <input type="text" class="form-control shadow-sm" name="school_account_number"
                                value="{{ $settings['school_account_number'] ?? '' }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary px-4">
                            Simpan Pengaturan
                        </button>

                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
