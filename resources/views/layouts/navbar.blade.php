<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        {{-- Expanded Full Brand --}}
        <a class="navbar-brand brand-logo px-3 d-flex align-items-center text-decoration-none" href="#">
            <div class="brand-logo-badge me-2">
                <i class="mdi mdi-shield-check text-white"></i>
            </div>
            <span class="fw-bold fs-5 text-white mb-0" style="letter-spacing: -0.5px;">
                E-SPP <span style="color: var(--orange-brand);">SYSTEM</span>
            </span>
        </a>

        {{-- Collapsed Mini Brand --}}
        <a class="navbar-brand brand-logo-mini d-none align-items-center justify-content-center text-decoration-none" href="#" title="E-SPP System">
            <div class="brand-logo-badge">
                <i class="mdi mdi-shield-check text-white"></i>
            </div>
        </a>
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-center">
        <button class="navbar-toggler navbar-toggler align-self-center border-0 bg-transparent ms-2"
            type="button" data-toggle="minimize" title="Perkecil/Perbesar Menu">
            <i class="mdi mdi-menu fs-4" style="color: var(--navy-primary);"></i>
        </button>

        {{-- Natural Language Command Bar in Topbar --}}
        <div class="d-none d-md-flex align-items-center ms-3 flex-grow-1" style="max-width: 420px;">
            <button type="button" class="spp-command-bar-btn w-100 d-flex align-items-center justify-content-between px-3 py-1 text-start"
                    data-bs-toggle="modal" data-bs-target="#aiAgentModal" id="topbarCommandBarTrigger" title="Buka SPP Assistant (Ctrl+K)">
                <span class="d-flex align-items-center text-truncate text-muted" style="font-size: 0.85rem;">
                    <span class="me-2 fw-bold" style="color: var(--orange-brand);">✦</span>
                    <span class="text-dark fw-medium">Tanya SPP Assistant...</span>
                </span>
                <kbd class="command-kbd-badge ms-2" id="navKbdBadge">Ctrl K</kbd>
            </button>
        </div>

        <ul class="navbar-nav navbar-nav-right ms-auto d-flex align-items-center">
            {{-- Mobile Command Bar Trigger --}}
            <li class="nav-item d-md-none me-2">
                <button type="button" class="btn btn-sm d-flex align-items-center justify-content-center p-2 text-dark border-0 bg-transparent"
                        data-bs-toggle="modal" data-bs-target="#aiAgentModal" title="SPP Assistant (Ctrl+K)">
                    <span class="fs-5 fw-bold" style="color: var(--orange-brand);">✦</span>
                </button>
            </li>


            <li class="nav-item d-none d-lg-block me-3">
                <span class="text-muted small">
                    <i class="mdi mdi-calendar-today me-1" style="color: var(--orange-brand);"></i>
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </li>

            <li class="nav-item nav-profile dropdown border-start ps-3 ms-2">
                <a class="nav-link dropdown-toggle d-flex align-items-center py-0" id="profileDropdown" href="#"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="topbar-user-avatar me-2">
                        <span>{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                    </div>
                    <div class="nav-profile-text d-none d-lg-block text-start">
                        <p class="mb-0 text-dark fw-bold small leading-tight">{{ Auth::user()->name }}</p>
                        <span class="topbar-role-badge">{{ strtoupper(Auth::user()->role ?? 'USER') }}</span>
                    </div>
                </a>

                <div class="dropdown-menu dropdown-menu-end navbar-dropdown border-0 shadow mt-2 rounded-3 py-2"
                    aria-labelledby="profileDropdown">
                    <div class="dropdown-header border-bottom mb-1 pb-2 px-3 text-start">
                        <h6 class="mb-0 text-dark fw-bold">{{ Auth::user()->name }}</h6>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>

                    <div class="dropdown-divider mx-3"></div>

                    <a class="dropdown-item px-3 py-2 text-danger d-flex align-items-center" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="mdi mdi-logout me-2"></i> Keluar
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>

            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center border-0 bg-transparent ms-2"
                type="button" data-toggle="offcanvas" title="Buka Menu Mobile">
                <span class="mdi mdi-menu fs-4" style="color: var(--navy-primary);"></span>
            </button>
        </ul>
    </div>
</nav>
