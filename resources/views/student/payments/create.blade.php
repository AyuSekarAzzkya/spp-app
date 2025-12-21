@extends('template')

@section('content')
    @php
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    @endphp

    <style>
        :root {
            --primary-blue: #4e73df;
            --soft-blue: #f8faff;
            --border-color: #eaecf0;
        }

        .page-inner {
            background: #fbfcfe;
            min-height: 100vh;
        }

        @media (min-width: 992px) {
            .bill-list-container {
                max-height: 75vh;
                overflow-y: auto;
                padding-right: 15px;
            }

            .sticky-summary {
                position: sticky;
                top: 20px;
            }
        }

        @media (max-width: 767px) {
            .page-inner {
                padding: 15px 10px;
            }

            .bill-card .fw-bold {
                font-size: 0.95rem;
            }

            .text-end.me-4 {
                margin-right: 10px !important;
            }

            .sticky-summary {
                margin-top: 20px;
            }
        }

        .bill-list-container::-webkit-scrollbar {
            width: 4px;
        }

        .bill-list-container::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        .bill-card {
            cursor: pointer;
            transition: all 0.25s ease;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            background: white;
        }

        .bill-card:hover {
            border-color: var(--primary-blue);
            background: var(--soft-blue);
        }

        .bill-check:checked~.bill-card {
            border-color: var(--primary-blue);
            background-color: var(--soft-blue);
            border-width: 2px;
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.1);
        }

        .border-dashed-upload {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 15px;
            background: #fafafa;
        }

        .bill-check {
            display: none;
        }
    </style>

    <div class="container-fluid py-3 py-lg-4">
        <div class="row justify-content-center">
            <div class="col-xl-11">

                <div class="d-flex align-items-center justify-content-between mb-4 px-2">
                    <div>
                        <h2 class="fw-bold text-dark mb-1 fs-3">Pembayaran SPP</h2>
                        <p class="text-muted mb-0 small">Kelola dan bayar tagihan sekolah Anda.</p>
                    </div>
                    <div class="d-none d-sm-block">
                        <span class="badge bg-light text-primary border p-2 px-3 rounded-pill">
                            <i class="fas fa-shield-check me-1"></i> Transaksi Aman
                        </span>
                    </div>
                </div>

                <form action="{{ route('student.payments.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 g-lg-4">

                        {{-- KIRI: LIST TAGIHAN --}}
                        <div class="col-lg-7">
                            <h6 class="fw-bold mb-3 px-2">Daftar Tagihan Tersedia</h6>
                            <div class="bill-list-container">
                                @forelse ($bills as $bill)
                                    @php
                                        // Mengubah angka bulan menjadi nama bulan
                                        $namaBulan = $monthNames[(int) $bill->month] ?? $bill->month;
                                    @endphp
                                    <div class="mb-3 px-1">
                                        <label class="w-100 mb-0">
                                            <input type="checkbox" name="bill_ids[]" class="bill-check"
                                                value="{{ $bill->id }}" data-amount="{{ $bill->sppRate->amount }}">

                                            <div class="card bill-card shadow-sm border-0">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-grow-1">
                                                            <h6 class="fw-bold text-dark mb-0">{{ $namaBulan }}</h6>
                                                            <span class="text-muted small">Tahun {{ $bill->year }}</span>
                                                        </div>
                                                        <div class="text-end me-3 me-md-4">
                                                            <span
                                                                class="fw-bold text-primary">Rp{{ number_format($bill->sppRate->amount, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @empty
                                    <div class="text-center py-5 bg-white rounded-4 border mx-2">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <h5 class="fw-bold">Tagihan Nihil</h5>
                                        <p class="text-muted px-4">Semua pembayaran SPP Anda sudah lunas.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- KANAN: RINGKASAN --}}
                        @if ($bills->count() > 0)
                            <div class="col-lg-5">
                                <div class="sticky-summary">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body p-4">
                                            <h6 class="fw-bold mb-3 text-dark">Ringkasan Pembayaran</h6>

                                            <div id="selectedItemsList" class="mb-4">
                                                <div class="text-center py-3 border rounded-3 border-dashed bg-light">
                                                    <small class="text-muted">Pilih bulan di sebelah kiri</small>
                                                </div>
                                            </div>

                                            <div class="border-top pt-3 mb-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-muted small">Item terpilih:</span>
                                                    <span id="itemCount" class="fw-bold small">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold">Total Bayar:</span>
                                                    <h4 class="text-primary fw-bold mb-0">Rp <span id="totalAmount">0</span>
                                                    </h4>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold">No Rekening:</span>
                                                    <h4 class="text-black fw-bold mb-0">081212537279</h4>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="fw-bold small mb-2">Bukti
                                                    Transfer</label>
                                                <div class="border-dashed-upload">
                                                    <input type="file" name="proof_image"
                                                        class="form-control form-control-sm border-0 bg-transparent"
                                                        required>
                                                </div>
                                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Maks. file
                                                    2MB (JPG/PNG)</small>
                                            </div>

                                            <input type="hidden" name="amount" id="amount">
                                            <button type="submit"
                                                class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm border-0">
                                                Bayar Sekarang
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.bill-check');
            const totalDisplay = document.getElementById('totalAmount');
            const countDisplay = document.getElementById('itemCount');
            const amountInput = document.getElementById('amount');
            const listContainer = document.getElementById('selectedItemsList');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    let total = 0;
                    let count = 0;
                    let htmlList = '';

                    checkboxes.forEach(cb => {
                        if (cb.checked) {
                            const month = cb.closest('label').querySelector('h6').innerText;
                            const price = cb.closest('label').querySelector(
                                '.text-end span').innerText;

                            total += parseInt(cb.dataset.amount);
                            count++;

                            htmlList += `
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small text-muted">${month}</span>
                            <span class="small fw-bold text-dark">${price}</span>
                        </div>
                    `;
                        }
                    });

                    if (count > 0) {
                        listContainer.innerHTML = htmlList;
                    } else {
                        listContainer.innerHTML =
                            '<div class="text-center py-3 border rounded-3 border-dashed bg-light">' +
                            '<small class="text-muted">Pilih bulan di sebelah kiri</small></div>';
                    }

                    totalDisplay.innerText = total.toLocaleString('id-ID');
                    countDisplay.innerText = count + ' Bulan';
                    amountInput.value = total;
                });
            });
        });
    </script>
@endpush
