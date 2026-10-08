@extends('template')

@section('content')
<div class="page-inner">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="page-header-title mb-1">Verifikasi Pembayaran SPP</h2>
            <p class="page-header-subtitle mb-0">Tinjau bukti transfer pembayaran, setujui pelunasan tagihan, atau berikan catatan penolakan.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('billing.students') }}" class="btn btn-secondary d-flex align-items-center">
                <i class="mdi mdi-receipt me-1"></i> Tagihan Siswa
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Status Filter Tabs --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.payments.index') }}" class="btn {{ empty($status) ? 'btn-primary' : 'btn-outline-secondary' }} btn-sm d-flex align-items-center">
            Semua Transaksi <span class="badge {{ empty($status) ? 'bg-white text-dark' : 'badge-secondary' }} ms-2">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="btn {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-secondary' }} btn-sm d-flex align-items-center">
            <i class="mdi mdi-clock-outline me-1"></i> Menunggu Verifikasi
            <span class="badge {{ $status === 'pending' ? 'bg-white text-dark' : 'badge-pending' }} ms-2">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'approved']) }}" class="btn {{ $status === 'approved' ? 'btn-primary' : 'btn-outline-secondary' }} btn-sm d-flex align-items-center">
            <i class="mdi mdi-check-circle-outline me-1"></i> Disetujui (Lunas)
            <span class="badge {{ $status === 'approved' ? 'bg-white text-dark' : 'badge-lunas' }} ms-2">{{ $counts['approved'] }}</span>
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'rejected']) }}" class="btn {{ $status === 'rejected' ? 'btn-primary' : 'btn-outline-secondary' }} btn-sm d-flex align-items-center">
            <i class="mdi mdi-close-circle-outline me-1"></i> Ditolak (Perlu Re-upload)
            <span class="badge {{ $status === 'rejected' ? 'bg-white text-dark' : 'badge-ditolak' }} ms-2">{{ $counts['rejected'] }}</span>
        </a>
    </div>

    {{-- Payments DataTable Card --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="d-flex align-items-center fw-bold">
                <i class="mdi mdi-credit-card-check me-2" style="color: var(--navy-primary); font-size: 1.2rem;"></i>
                Daftar Pengajuan Pembayaran
            </span>
            <span class="badge badge-navy">{{ count($payments) }} Data Ditampilkan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="paymentTable">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Tanggal Bayar</th>
                            <th>Tagihan / Nominal</th>
                            <th>Metode / Bukti</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $idx => $item)
                            <tr>
                                <td class="text-muted small ps-3">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-2" style="width: 36px; height: 36px; background: var(--navy-primary); font-size: 0.85rem;">
                                            {{ strtoupper(substr($item->student->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->student->name ?? '-' }}</div>
                                            <small class="text-muted">NIS: {{ $item->student->nis ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $item->student->class->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-dark small d-block">{{ \Carbon\Carbon::parse($item->payment_date ?? $item->created_at)->translatedFormat('d M Y') }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold" style="color: var(--navy-primary); font-size: 0.95rem;">
                                        Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                                    </span>
                                    <div class="small text-muted">{{ $item->details->count() }} Bulan SPP</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge badge-navy text-uppercase">{{ $item->payment_method ?? 'Transfer' }}</span>
                                        @if($item->latestProof)
                                            <span class="badge badge-secondary" title="Bukti Transfer Versi {{ $item->latestProof->version ?? 1 }}">
                                                v{{ $item->latestProof->version ?? 1 }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($item->status == 'pending')
                                        <span class="badge badge-pending"><i class="mdi mdi-clock-outline"></i> Pending</span>
                                    @elseif ($item->status == 'approved')
                                        <span class="badge badge-lunas"><i class="mdi mdi-check"></i> Approved</span>
                                    @else
                                        <span class="badge badge-ditolak"><i class="mdi mdi-close"></i> Rejected</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->status === 'pending')
                                        <a href="{{ route('admin.payments.show', $item->id) }}" class="btn btn-sm btn-primary text-nowrap">
                                            <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> Verifikasi
                                        </a>
                                    @else
                                        <a href="{{ route('admin.payments.show', $item->id) }}" class="btn btn-sm btn-outline-secondary text-nowrap">
                                            <i class="mdi mdi-eye-outline me-1"></i> Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            {{-- DataTables menangani tampilan kosong secara otomatis lewat language setting --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#paymentTable').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "Cari Pembayaran:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data pembayaran tidak ditemukan",
                emptyTable: "Tidak ada transaksi pembayaran pada kategori ini",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data tersedia",
                paginate: {
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                    next: '<i class="mdi mdi-chevron-right"></i>'
                }
            }
        });
    });
</script>
@endpush
