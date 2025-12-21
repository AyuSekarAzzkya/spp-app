<nav
    class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row shadow-sm border-bottom border-light bg-white">
    <div
        class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo px-3 d-flex align-items-center text-decoration-none">
            <div class="bg-primary rounded-3 d-flex align-items-center justify-content-center me-2"
                style="width: 32px; height: 32px;">
                <i class="mdi mdi-school text-white fs-5"></i>
            </div>
            <span class="fw-bolder fs-5 text-dark mb-0 tracking-tight" style="letter-spacing: -0.5px;">E-SPP <span
                    class="text-primary text-opacity-75">APPS</span></span>
        </a>

       
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center text-primary border-0 bg-transparent ms-2"
            type="button" data-toggle="minimize">
            <i class="mdi mdi-menu fs-4"></i>
        </button>

        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item d-none d-lg-block full-screen-link me-2">
                <a class="nav-link text-muted">
                    <i class="mdi mdi-fullscreen fs-5" id="fullscreen-button"></i>
                </a>
            </li>

            <li class="nav-item nav-profile dropdown border-start ps-3 ms-2">
                <a class="nav-link dropdown-toggle d-flex align-items-center py-0" id="profileDropdown" href="#"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2 border border-primary border-opacity-25"
                        style="width: 35px; height: 35px;">
                        <span
                            class="text-primary fw-bold small">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    </div>
                    <div class="nav-profile-text d-none d-lg-block text-start">
                        <p class="mb-0 text-dark fw-bold small leading-tight">{{ Auth::user()->name }}</p>
                        <span class="text-muted" style="font-size: 0.7rem;">Siswa Aktif</span>
                    </div>
                </a>

                <div class="dropdown-menu dropdown-menu-end navbar-dropdown border-0 shadow mt-3 rounded-3 py-2"
                    aria-labelledby="profileDropdown">
                    <div class="dropdown-header border-bottom mb-1 pb-2 px-4 text-start">
                        <h6 class="mb-0 text-dark fw-bold">{{ Auth::user()->name }}</h6>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>

                    <div class="dropdown-divider mx-3"></div>

                    <a class="dropdown-item px-4 py-2 text-danger" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="mdi mdi-logout me-2"></i> Keluar
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>

            <button
                class="navbar-toggler navbar-toggler-right d-lg-none align-self-center border-0 bg-transparent text-primary ms-2"
                type="button" data-toggle="offcanvas">
                <span class="mdi mdi-menu fs-4"></span>
            </button>
        </ul>
    </div>
</nav>
