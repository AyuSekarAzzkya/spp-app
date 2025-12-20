<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        <!-- PROFILE -->
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('template/dist/assets/images/faces/face1.jpg') }}" alt="profile" />
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ auth()->user()->name }}</span>
                    <span class="text-secondary text-small">{{ auth()->user()->role }}</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>

        @php
            $role = auth()->check() ? auth()->user()->role : null;
        @endphp

        {{-- ADMIN & PETUGAS MENU --}}
        @if (in_array($role, ['admin', 'petugas']))
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <span class="menu-title">Dashboard</span>
                    <i class="mdi mdi-home menu-icon"></i>
                </a>
            </li>

            <hr>

            <!-- Data Master -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('students.index') }}">
                    <span class="menu-title">Data Siswa</span>
                    <i class="mdi mdi-school menu-icon"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('classes.index') }}">
                    <span class="menu-title">Data Kelas</span>
                    <i class="mdi mdi-google-classroom menu-icon"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('academic-years.index') }}">
                    <span class="menu-title">Tahun Ajaran</span>
                    <i class="mdi mdi-calendar-clock menu-icon"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('spp.index') }}">
                    <span class="menu-title">Nominal SPP</span>
                    <i class="mdi mdi-cash-multiple menu-icon"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('billing.students') }}">
                    <span class="menu-title">Tagihan Siswa</span>
                    <i class="mdi mdi-receipt menu-icon"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.payments.index') }}">
                    <span class="menu-title">Transaksi Pembayaran</span>
                    <i class="mdi mdi-credit-card menu-icon"></i>
                </a>
            </li>
        @endif

        {{-- SISWA MENU --}}
        @if ($role === 'siswa')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('student.dashboard') }}">
                    <span class="menu-title">Dashboard</span>
                    <i class="mdi mdi-home menu-icon"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('student.payments.index') }}">
                    <span class="menu-title">Bayar SPP</span>
                    <i class="mdi mdi-credit-card menu-icon"></i>
                </a>
            </li>
        @endif

        <!-- COMMON MENU -->
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span class="menu-title">Riwayat Pembayaran</span>
                <i class="mdi mdi-history menu-icon"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#menu-laporan" aria-expanded="false"
                aria-controls="menu-laporan">
                <span class="menu-title">Laporan</span>
                <i class="mdi mdi-file-chart menu-icon"></i>
            </a>
            <div class="collapse" id="menu-laporan">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="#">Laporan Harian</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Laporan Bulanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Laporan Tunggakan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Laporan Per Kelas</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('settings.index') }}">
                <span class="menu-title">Pengaturan</span>
                <i class="mdi mdi-cog menu-icon"></i>
            </a>
        </li>

    </ul>
</nav>
