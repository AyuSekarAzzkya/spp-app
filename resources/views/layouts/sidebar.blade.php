<nav class="sidebar sidebar-offcanvas" id="sidebar">
    {{-- 1. SIDEBAR HEADER: Logo & App Title --}}
    <div class="sidebar-header d-flex align-items-center justify-content-between">
        <a class="sidebar-brand d-flex align-items-center text-decoration-none" href="#">
            <div class="brand-logo-badge">
                <i class="mdi mdi-shield-check text-white"></i>
            </div>
            <div class="sidebar-brand-text">
                <span class="brand-title-white">E-SPP</span>
                <span class="brand-title-orange">SYSTEM</span>
            </div>
        </a>
        <button type="button" class="btn btn-link text-white p-0 d-lg-none sidebar-mobile-close-btn" id="sidebarMobileCloseBtn" title="Tutup Menu">
            <i class="mdi mdi-close fs-4"></i>
        </button>
    </div>

    @php
        $role = auth()->check() ? auth()->user()->role : null;
    @endphp

    {{-- 2. SIDEBAR MENU WRAPPER: Scrollable Navigation List --}}
    <div class="sidebar-menu-wrapper">
        <ul class="nav">
            {{-- ============================================================
                 1. MENU KHUSUS ADMIN
                 ============================================================ --}}
            @if ($role == 'admin')
                <li class="sidebar-section-title">Utama</li>
                <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" title="Dashboard">
                        <span class="nav-icon"><i class="mdi mdi-view-dashboard-outline"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-section-title">Master Data</li>
                <li class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}" title="Data Siswa">
                        <span class="nav-icon"><i class="mdi mdi-school"></i></span>
                        <span class="nav-text">Data Siswa</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}" href="{{ route('classes.index') }}" title="Data Kelas">
                        <span class="nav-icon"><i class="mdi mdi-school-outline"></i></span>
                        <span class="nav-text">Data Kelas</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('academic-years.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('academic-years.*') ? 'active' : '' }}" href="{{ route('academic-years.index') }}" title="Tahun Ajaran">
                        <span class="nav-icon"><i class="mdi mdi-calendar-clock-outline"></i></span>
                        <span class="nav-text">Tahun Ajaran</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('spp.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('spp.*') ? 'active' : '' }}" href="{{ route('spp.index') }}" title="Tarif SPP">
                        <span class="nav-icon"><i class="mdi mdi-cash-multiple"></i></span>
                        <span class="nav-text">Tarif SPP</span>
                    </a>
                </li>

                <li class="sidebar-section-title">Transaksi</li>
                <li class="nav-item {{ request()->routeIs('billing.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('billing.*') ? 'active' : '' }}" href="{{ route('billing.students') }}" title="Tagihan Siswa">
                        <span class="nav-icon"><i class="mdi mdi-file-document-outline"></i></span>
                        <span class="nav-text">Tagihan Siswa</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}" title="Verifikasi Pembayaran">
                        <span class="nav-icon"><i class="mdi mdi-credit-card-check-outline"></i></span>
                        <span class="nav-text">Verifikasi Pembayaran</span>
                    </a>
                </li>

                <li class="sidebar-section-title">Laporan</li>
                <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#menu-laporan" 
                       aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}" title="Laporan Keuangan">
                        <span class="nav-icon"><i class="mdi mdi-chart-box-outline"></i></span>
                        <span class="nav-text">Laporan Keuangan</span>
                        <i class="menu-arrow mdi mdi-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="menu-laporan">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}" title="Rekap Pembayaran">
                                    <i class="mdi mdi-account-cash mx-2"></i> Rekap Pembayaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.arrears') ? 'active' : '' }}" href="{{ route('reports.arrears') }}" title="Rekap Tunggakan">
                                    <i class="mdi mdi-receipt mx-2"></i> Rekap Tunggakan
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-section-title">Pengaturan</li>
                <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}" title="Data Pengguna">
                        <span class="nav-icon"><i class="mdi mdi-account-cog-outline"></i></span>
                        <span class="nav-text">Data Pengguna</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#aiAgentModal" title="SPP Assistant (Ctrl+K)">
                        <span class="nav-icon"><i class="mdi mdi-creation-outline" style="color: var(--orange-brand);"></i></span>
                        <span class="nav-text">SPP Assistant</span>
                        <kbd class="sidebar-kbd-badge">Ctrl K</kbd>
                    </a>
                </li>

            {{-- ============================================================
                 2. MENU KHUSUS PETUGAS
                 ============================================================ --}}
            @elseif ($role == 'petugas')
                <li class="sidebar-section-title">Utama</li>
                <li class="nav-item {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}" href="{{ route('petugas.dashboard') }}" title="Dashboard">
                        <span class="nav-icon"><i class="mdi mdi-view-dashboard-outline"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-section-title">Master Data</li>
                <li class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}" title="Data Siswa">
                        <span class="nav-icon"><i class="mdi mdi-school"></i></span>
                        <span class="nav-text">Data Siswa</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}" href="{{ route('classes.index') }}" title="Data Kelas">
                        <span class="nav-icon"><i class="mdi mdi-school-outline"></i></span>
                        <span class="nav-text">Data Kelas</span>
                    </a>
                </li>

                <li class="sidebar-section-title">Transaksi</li>
                <li class="nav-item {{ request()->routeIs('billing.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('billing.*') ? 'active' : '' }}" href="{{ route('billing.students') }}" title="Tagihan Siswa">
                        <span class="nav-icon"><i class="mdi mdi-file-document-outline"></i></span>
                        <span class="nav-text">Tagihan Siswa</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}" title="Verifikasi Pembayaran">
                        <span class="nav-icon"><i class="mdi mdi-credit-card-check-outline"></i></span>
                        <span class="nav-text">Verifikasi Pembayaran</span>
                    </a>
                </li>

                <li class="sidebar-section-title">Laporan</li>
                <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#menu-laporan" 
                       aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}" title="Laporan Keuangan">
                        <span class="nav-icon"><i class="mdi mdi-chart-box-outline"></i></span>
                        <span class="nav-text">Laporan Keuangan</span>
                        <i class="menu-arrow mdi mdi-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="menu-laporan">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}" title="Rekap Pembayaran">
                                    <i class="mdi mdi-account-cash mx-2"></i> Rekap Pembayaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.arrears') ? 'active' : '' }}" href="{{ route('reports.arrears') }}" title="Rekap Tunggakan">
                                    <i class="mdi mdi-account-cash mx-2"></i> Rekap Tunggakan
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-section-title">Pengaturan</li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#aiAgentModal" title="SPP Assistant (Ctrl+K)">
                        <span class="nav-icon"><i class="mdi mdi-creation-outline" style="color: var(--orange-brand);"></i></span>
                        <span class="nav-text">SPP Assistant</span>
                        <kbd class="sidebar-kbd-badge">Ctrl K</kbd>
                    </a>
                </li>

            {{-- ============================================================
                 3. MENU KHUSUS SISWA
                 ============================================================ --}}
            @elseif ($role === 'siswa')
                <li class="sidebar-section-title">Utama</li>
                <li class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}" title="Dashboard">
                        <span class="nav-icon"><i class="mdi mdi-view-dashboard-outline"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-section-title">Transaksi</li>
                <li class="nav-item {{ request()->routeIs('student.payments.create') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('student.payments.create') ? 'active' : '' }}" href="{{ route('student.payments.create') }}" title="Bayar SPP Sekarang">
                        <span class="nav-icon"><i class="mdi mdi-credit-card-plus-outline"></i></span>
                        <span class="nav-text">Bayar SPP Sekarang</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('student.payments.index') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('student.payments.index') ? 'active' : '' }}" href="{{ route('student.payments.index') }}" title="Pembayaran Terbaru">
                        <span class="nav-icon"><i class="mdi mdi-clock-check-outline"></i></span>
                        <span class="nav-text">Pembayaran Terbaru</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('payments.history*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('payments.history*') ? 'active' : '' }}" href="{{ route('payments.history') }}" title="Riwayat Pembayaran">
                        <span class="nav-icon"><i class="mdi mdi-history"></i></span>
                        <span class="nav-text">Riwayat Pembayaran</span>
                    </a>
                </li>

                <li class="sidebar-section-title">Pengaturan</li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#aiAgentModal" title="SPP Assistant (Ctrl+K)">
                        <span class="nav-icon"><i class="mdi mdi-creation-outline" style="color: var(--orange-brand);"></i></span>
                        <span class="nav-text">SPP Assistant</span>
                        <kbd class="sidebar-kbd-badge">Ctrl K</kbd>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    {{-- 3. SIDEBAR FOOTER: Pinned Logout Action --}}
    <div class="sidebar-footer">
        <a class="nav-link sidebar-logout-btn" href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();"
           title="Keluar / Logout">
            <span class="nav-icon"><i class="mdi mdi-logout"></i></span>
            <span class="nav-text">Keluar</span>
        </a>
    </div>

    {{-- Hidden Logout Form for Sidebar --}}
    <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</nav>
