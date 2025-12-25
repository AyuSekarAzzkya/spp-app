<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile" style="border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 10px;">
            <a href="#" class="nav-link">
                <div class="nav-profile-image d-flex align-items-center justify-content-center bg-gradient-primary rounded-circle"
                    style="width: 40px; height: 40px;">
                    <i class="mdi mdi-account text-white" style="font-size: 20px;"></i>
                </div>
                <div class="nav-profile-text d-flex flex-column ms-3">
                    <span class="font-weight-bold mb-1" style="letter-spacing: 0.5px;">{{ auth()->user()->name }}</span>
                    <span class="text-secondary text-small">{{ strtoupper(auth()->user()->role) }}</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>

        @php
            $role = auth()->check() ? auth()->user()->role : null;
        @endphp

        @if ($role == 'admin')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <span class="menu-title">Dashboard</span>
                    <i class="mdi mdi-view-dashboard menu-icon text-primary"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <span class="menu-title">Data Akun</span>
                    <i class="mdi mdi-account-group menu-icon text-danger"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('academic-years.index') }}">
                    <span class="menu-title">Tahun Ajaran</span>
                    <i class="mdi mdi-calendar-clock menu-icon text-warning"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('spp.index') }}">
                    <span class="menu-title">Nominal SPP</span>
                    <i class="mdi mdi-cash-multiple menu-icon text-primary"></i>
                </a>
            </li>
        @endif

        @if ($role == 'petugas')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('petugas.dashboard') }}">
                    <span class="menu-title">Dashboard</span>
                    <i class="mdi mdi-view-dashboard menu-icon text-primary"></i>
                </a>
            </li>
        @endif

        @if (in_array($role, ['admin', 'petugas']))

            <li class="nav-item">
                <a class="nav-link" href="{{ route('students.index') }}">
                    <span class="menu-title">Data Siswa</span>
                    <i class="mdi mdi-school menu-icon text-info"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('classes.index') }}">
                    <span class="menu-title">Data Kelas</span>
                    <i class="mdi mdi-google-classroom menu-icon text-success"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('billing.students') }}">
                    <span class="menu-title">Tagihan Siswa</span>
                    <i class="mdi mdi-file-document-outline menu-icon text-secondary"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.payments.index') }}">
                    <span class="menu-title">Transaksi Pembayaran</span>
                    <i class="mdi mdi-credit-card-check menu-icon text-success"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#menu-laporan">
                    <span class="menu-title">Laporan</span>
                    <i class="mdi mdi-file-chart menu-icon text-warning"></i>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="menu-laporan">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.index') }}">Bulanan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.arrears') }}">Tunggakan</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif


        @if ($role === 'siswa')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('student.dashboard') }}">
                    <span class="menu-title">Dashboard</span>
                    <i class="mdi mdi-home menu-icon text-primary"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('student.payments.create') }}">
                    <span class="menu-title">Bayar SPP</span>
                    <i class="mdi mdi-credit-card menu-icon text-success"></i>
                </a>
            </li>
        @endif

        @if (in_array($role, ['admin', 'petugas', 'siswa']))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('payments.history') }}">
                    <span class="menu-title">Riwayat Pembayaran</span>
                    <i class="mdi mdi-history menu-icon text-info"></i>
                </a>
            </li>
        @endif

        <li class="nav-item">
            <div style="border-top: 1px solid rgba(255,255,255,0.1); margin: 10px 20px;"></div>
        </li>
    </ul>
</nav>
