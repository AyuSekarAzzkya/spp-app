@extends('template')

@section('content')
    <div class="container-fluid py-4">

        {{-- HEADER --}}
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Detail Tunggakan SPP</h4>
            <p class="text-muted mb-0">
                {{ $student->name }} — {{ $student->class->name }}
            </p>
        </div>

        {{-- FILTER TAHUN --}}
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="year" class="form-select">
                    <option value="">Semua Tahun</option>
                    @foreach ($bills->pluck('year')->unique()->sortDesc() as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        {{-- CARD --}}
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- INFO TOTAL --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="alert alert-danger mb-0">
                            <strong>Total Tunggakan:</strong><br>
                            Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- TABLE --}}
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($bills as $bill)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F') }}
                                </td>
                                <td>{{ $bill->year }}</td>
                                <td>{{ \Carbon\Carbon::parse($bill->due_date)->format('d-m-Y') }}</td>
                                <td class="text-end">
                                    Rp {{ number_format($bill->sppRate->amount ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger">Menunggak</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Tidak ada tunggakan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- ACTION --}}
                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('reports.arrears') }}" class="btn btn-secondary">
                        Kembali
                    </a>

                    @php
                        $phone = preg_replace('/[^0-9]/', '', $student->phone ?? '');
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }

                        $tahun = request('year') ? ' tahun ' . request('year') : '';

                        $waMessage = urlencode(
                            "Yth. Orang tua/wali dari {$student->name},\n\n" .
                                "Kami informasikan bahwa terdapat tunggakan SPP{$tahun} sebesar Rp " .
                                number_format($totalTunggakan, 0, ',', '.') .
                                ".\n\nMohon segera ditindaklanjuti.\n\nTerima kasih.",
                        );
                    @endphp

                    @if ($phone)
                        <a href="https://wa.me/{{ $phone }}?text={{ $waMessage }}" target="_blank"
                            class="btn btn-success">
                            Kirim WhatsApp
                        </a>
                    @endif
                </div>

            </div>
        </div>

    </div>
@endsection
