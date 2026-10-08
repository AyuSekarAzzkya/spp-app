@extends('template')

@section('content')
<div class="page-inner">
    {{-- Student Welcome Hero Card --}}
    <div class="card border-0 text-white mb-4 overflow-hidden shadow-sm" style="background: linear-gradient(135deg, var(--navy-primary) 0%, #1e293b 100%); border-radius: 16px;">
        <div class="card-body p-4 p-md-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 text-white shadow-sm" style="width: 52px; height: 52px; background: var(--orange-brand); font-size: 1.3rem;">
                            {{ strtoupper(substr($student->name ?? Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            @php
                                $hour = date('H');
                                $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
                            @endphp
                            <h3 class="fw-bold mb-0 text-white">{{ $greeting }}, {{ $student->name ?? Auth::user()->name }}!</h3>
                            <div class="small text-light opacity-75">Portal Pembayaran SPP Siswa</div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 pt-1">
                        <span class="badge" style="background: rgba(255, 255, 255, 0.12); color: #f8fafc; font-weight: 500;">
                            <i class="mdi mdi-card-account-details-outline me-1"></i> NIS: <strong>{{ $student->nis ?? '-' }}</strong>
                        </span>
                        <span class="badge" style="background: rgba(255, 255, 255, 0.12); color: #f8fafc; font-weight: 500;">
                            <i class="mdi mdi-google-classroom me-1"></i> Kelas: <strong>{{ $student->class->name ?? '-' }}</strong>
                        </span>
                        <span class="badge" style="background: rgba(255, 255, 255, 0.12); color: #f8fafc; font-weight: 500;">
                            <i class="mdi mdi-calendar-range me-1"></i> Tahun Ajaran: <strong>{{ $activeYear->year ?? '-' }}</strong>
                        </span>
                        <span class="badge" style="background: rgba(34, 197, 94, 0.25); color: #86efac; font-weight: 600;">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Siswa Aktif
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    @if($unpaidCount > 0)
                        <a href="{{ route('student.payments.create') }}" class="btn btn-primary btn-lg shadow-sm d-inline-flex align-items-center">
                            <i class="mdi mdi-credit-card-plus-outline me-2"></i> Bayar SPP Sekarang
                        </a>
                    @else
                        <div class="d-inline-flex align-items-center px-3 py-2 rounded-pill" style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3);">
                            <i class="mdi mdi-shield-check text-success me-2" style="font-size: 1.3rem;"></i>
                            <span class="text-white fw-bold small">Seluruh Tagihan Lunas</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Conditional Alert: Rejected Payments (Perlu Tindakan) --}}
    @if($rejectedPayments && $rejectedPayments->count() > 0)
        @foreach($rejectedPayments as $rejected)
            <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center p-3" style="border-left: 4px solid var(--danger) !important; border-radius: 12px; background-color: #fef2f2;">
                <div class="d-flex align-items-start mb-2 mb-md-0">
                    <div class="stat-icon-wrapper stat-icon-danger me-3" style="width: 42px; height: 42px; font-size: 1.3rem;">
                        <i class="mdi mdi-alert-octagon-outline"></i>
                    </div>
                    <div>
                        <strong class="text-danger">Pembayaran Ditolak Petugas:</strong>
                        <div class="text-dark small mt-1">
                            Bukti transfer Anda untuk pembayaran tanggal <b>{{ \Carbon\Carbon::parse($rejected->created_at)->translatedFormat('d M Y') }}</b> ditolak.
                            @if($rejected->rejection_note)
                                <div class="mt-1 p-2 rounded text-danger small bg-white border border-danger-subtle">
                                    <em>Catatan Petugas: "{{ $rejected->rejection_note }}"</em>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <a href="{{ route('student.payments.show', $rejected->id) }}" class="btn btn-sm btn-danger text-nowrap ms-md-3">
                    <i class="mdi mdi-upload me-1"></i> Upload Bukti Perbaikan
                </a>
            </div>
        @endforeach
    @endif

    {{-- Conditional Alert: Pending Payments (Sedang Diproses) --}}
    @if($pendingPaymentsCount > 0)
        <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center p-3" style="background-color: #fffbeb; border-left: 4px solid var(--warning) !important; border-radius: 12px;">
            <div class="stat-icon-wrapper stat-icon-warning me-3" style="width: 40px; height: 40px; font-size: 1.2rem;">
                <i class="mdi mdi-clock-outline"></i>
            </div>
            <div>
                <strong class="text-dark">Menunggu Verifikasi:</strong>
                <div class="text-muted small">
                    Terdapat <b>{{ $pendingPaymentsCount }} transaksi pembayaran</b> Anda yang sedang ditinjau oleh petugas loket. Anda akan melihat status berubah setelah disetujui.
                </div>
            </div>
        </div>
    @endif

    {{-- SPP Assistant Command Bar --}}
    <div class="spp-dashboard-command-bar mb-4" role="button" data-bs-toggle="modal" data-bs-target="#aiAgentModal" tabindex="0" title="Buka SPP Assistant (Ctrl+K)">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 text-truncate">
                <span class="spp-command-sparkle">✦</span>
                <span class="spp-command-text">Tanya SPP Assistant atau cek status tagihan SPP</span>
            </div>
            <kbd class="command-kbd-badge command-bar-kbd ms-2 flex-shrink-0">Ctrl K</kbd>
        </div>
    </div>

    {{-- Modern KPI Stat Cards (4 Columns) --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Sisa Tagihan --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Sisa Tagihan SPP</div>
                        <div class="stat-value {{ $totalUnpaid > 0 ? 'text-danger' : 'text-success' }}">
                            Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-1">
                            @if($unpaidCount > 0)
                                <span class="badge badge-unpaid">{{ $unpaidCount }} Bulan belum lunas</span>
                            @else
                                <span class="badge badge-lunas">Semua lunas</span>
                            @endif
                        </div>
                    </div>
                    <div class="stat-icon-wrapper {{ $totalUnpaid > 0 ? 'stat-icon-danger' : 'stat-icon-success' }}">
                        <i class="mdi {{ $totalUnpaid > 0 ? 'mdi-alert-circle-outline' : 'mdi-check-decagram-outline' }}"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Status Bulan Ini --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Status Bulan Ini</div>
                        <div class="stat-value {{ $isPaidThisMonth ? 'text-success' : 'text-warning' }}" style="{{ $isPaidThisMonth ? '' : 'color: var(--warning) !important;' }}">
                            {{ $isPaidThisMonth ? 'Sudah Lunas' : 'Belum Bayar' }}
                        </div>
                        <div class="small text-muted mt-1">
                            Periode: <strong>{{ now()->translatedFormat('F Y') }}</strong>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper {{ $isPaidThisMonth ? 'stat-icon-success' : 'stat-icon-warning' }}">
                        <i class="mdi {{ $isPaidThisMonth ? 'mdi-calendar-check-outline' : 'mdi-calendar-clock-outline' }}"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Tagihan Telah Dibayar --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Bulan Telah Dibayar</div>
                        <div class="stat-value text-navy">
                            {{ $paidBillsCount }} Bulan
                        </div>
                        <div class="small text-muted mt-1">
                            <span class="badge badge-lunas">Terverifikasi</span>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-navy">
                        <i class="mdi mdi-receipt-text-check-outline"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Pembayaran Disetujui --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Pembayaran Lunas</div>
                        <div class="stat-value text-primary" style="color: var(--orange-brand) !important;">
                            Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-1">
                            <a href="{{ route('payments.history') }}" class="text-decoration-none fw-semibold" style="color: var(--orange-brand);">
                                Lihat riwayat <i class="mdi mdi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-orange">
                        <i class="mdi mdi-wallet-outline"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content: 8 Columns Left / 4 Columns Right --}}
    <div class="row g-4">
        {{-- Left: Unpaid Bills List --}}
        <div class="col-12 col-lg-8">
            <div class="card h-100 mb-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center fw-bold">
                        <i class="mdi mdi-format-list-checks me-2" style="color: var(--orange-brand); font-size: 1.2rem;"></i>
                        Daftar Tagihan SPP Belum Lunas
                    </span>
                    @if($unpaidBills->count() > 0)
                        <a href="{{ route('student.payments.create') }}" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-credit-card-outline me-1"></i> Bayar Sekarang
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($unpaidBills->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3" style="width: 50px;">No</th>
                                        <th>Bulan & Tahun</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Nominal SPP</th>
                                        <th>Status</th>
                                        <th class="text-center" style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unpaidBills as $idx => $bill)
                                        <tr>
                                            <td class="text-muted small ps-3">{{ $idx + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="mdi mdi-calendar-month text-primary me-2" style="font-size: 1.2rem; color: var(--navy-primary) !important;"></i>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F') }} {{ $bill->year }}</div>
                                                        <small class="text-muted">SPP Wajib Bulanan</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $dueDate = \Carbon\Carbon::parse($bill->due_date);
                                                    $isOverdue = $dueDate->isPast();
                                                @endphp
                                                <span class="{{ $isOverdue ? 'text-danger fw-semibold' : 'text-dark' }} small">
                                                    {{ $dueDate->translatedFormat('d M Y') }}
                                                </span>
                                                @if($isOverdue)
                                                    <div class="small text-danger"><i class="mdi mdi-clock-alert"></i> Lewat jatuh tempo</div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark">
                                                    Rp {{ number_format($bill->sppRate->amount ?? 0, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-unpaid">
                                                    <i class="mdi mdi-close-circle-outline"></i> Belum Lunas
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('student.payments.create') }}" class="btn btn-sm btn-outline-primary text-nowrap" title="Pilih di halaman pembayaran">
                                                    Bayar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 bg-light border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                            <span class="text-muted small">Total {{ $unpaidCount }} tagihan bulan perlu diselesaikan.</span>
                            <a href="{{ route('student.payments.create') }}" class="btn btn-primary d-flex align-items-center">
                                <i class="mdi mdi-check-circle me-1"></i> Bayar Sekaligus Multi-Bulan
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="stat-icon-wrapper stat-icon-success mx-auto mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                                <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Luar Biasa! Semua Tagihan Telah Lunas</h5>
                            <p class="text-muted small mb-0">Tidak ada tunggakan SPP pada akun Anda saat ini. Terima kasih atas kedisiplinan Anda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Latest Transaction & Official Payment Account Info --}}
        <div class="col-12 col-lg-4">
            {{-- Latest Transaction Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center fw-bold">
                        <i class="mdi mdi-history me-2" style="color: var(--navy-primary); font-size: 1.1rem;"></i>
                        Transaksi Terakhir
                    </span>
                    <a href="{{ route('payments.history') }}" class="text-decoration-none small fw-semibold" style="color: var(--orange-brand);">
                        Riwayat <i class="mdi mdi-chevron-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($latestPayment)
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                            <div>
                                <small class="text-muted d-block">Status Transaksi</small>
                                @if($latestPayment->status === 'approved')
                                    <span class="badge badge-lunas mt-1"><i class="mdi mdi-check"></i> Disetujui (Lunas)</span>
                                @elseif($latestPayment->status === 'pending')
                                    <span class="badge badge-pending mt-1"><i class="mdi mdi-clock-outline"></i> Menunggu Verifikasi</span>
                                @else
                                    <span class="badge badge-ditolak mt-1"><i class="mdi mdi-close"></i> Ditolak</span>
                                @endif
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Total Nominal</small>
                                <div class="fw-bold text-dark" style="font-size: 1.1rem;">
                                    Rp {{ number_format($latestPayment->total_amount, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 small">
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Tanggal Bayar:</span>
                                <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($latestPayment->payment_date ?? $latestPayment->created_at)->translatedFormat('d M Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Bulan Dibayar:</span>
                                <span class="fw-semibold text-dark">{{ $latestPayment->details->count() }} Bulan</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Metode:</span>
                                <span class="fw-semibold text-uppercase text-dark">{{ $latestPayment->payment_method ?? 'Transfer Bank' }}</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('student.payments.show', $latestPayment->id) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-eye-outline me-1"></i> Lihat Detail Pembayaran
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="mdi mdi-receipt-text-outline fs-2 d-block mb-1"></i>
                            <p class="small mb-0">Belum ada riwayat transaksi pembayaran.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Official School Bank Account Card --}}
            <div class="card border-0 shadow-sm" style="background-color: #f8fafc; border: 1px solid #e2e8f0 !important;">
                <div class="card-header bg-white border-bottom">
                    <span class="d-flex align-items-center fw-bold text-navy">
                        <i class="mdi mdi-bank me-2" style="color: var(--orange-brand); font-size: 1.1rem;"></i>
                        Rekening Resmi Pembayaran
                    </span>
                </div>
                <div class="card-body">
                    <div class="bg-white p-3 rounded-3 border text-center mb-3">
                        <div class="small text-muted text-uppercase fw-bold mb-1">
                            {{ setting('school_bank_name', 'Bank Mandiri') }}
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-2 my-1">
                            <h4 class="fw-bold mb-0 text-navy" id="accountNumberText" style="letter-spacing: 1px;">
                                {{ setting('school_account_number', '12345678910') }}
                            </h4>
                            <button type="button" class="btn btn-sm btn-light border px-2 py-1" onclick="copyAccountNumber()" title="Salin Nomor Rekening">
                                <i class="mdi mdi-content-copy text-primary" id="copyIcon"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block">
                            a.n <strong>{{ setting('school_account_holder', 'Bendahara SPP') }}</strong>
                        </small>
                    </div>

                    <div class="small text-muted">
                        <div class="d-flex align-items-start mb-2">
                            <i class="mdi mdi-information-outline text-primary me-2 mt-1"></i>
                            <span>Transfer via ATM, Mobile Banking, atau Teller ke rekening resmi di atas.</span>
                        </div>
                        <div class="d-flex align-items-start">
                            <i class="mdi mdi-camera-outline text-primary me-2 mt-1"></i>
                            <span>Simpan bukti screenshot / struk transfer lalu lampirkan saat mengisi form pembayaran.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyAccountNumber() {
        const accNumber = document.getElementById('accountNumberText').innerText.trim();
        navigator.clipboard.writeText(accNumber).then(() => {
            const icon = document.getElementById('copyIcon');
            icon.classList.remove('mdi-content-copy');
            icon.classList.add('mdi-check');
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Nomor rekening berhasil disalin!',
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                alert('Nomor rekening berhasil disalin!');
            }

            setTimeout(() => {
                icon.classList.remove('mdi-check');
                icon.classList.add('mdi-content-copy');
            }, 2500);
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }
</script>
@endpush
