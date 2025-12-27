<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | E-SPP System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="main-wrapper">
        <section class="login-visual">
            <div class="brand-logo">
                <i class="fas fa-graduation-cap"></i>
                <span>E-SPP <span style="color: var(--orange-brand)">SYSTEM</span></span>
            </div>

            <div class="visual-content">
                <span class="badge mb-3 px-3 py-2 rounded-pill"
                    style="background: var(--orange-brand); font-weight: 800; font-size: 0.7rem; letter-spacing: 1px;">V3.0
                    CORE SYSTEM</span>
                <h1>Finance <br> <span>Simplified.</span></h1>
                <p class="lead">Solusi modern untuk manajemen keuangan sekolah yang transparan, aman, dan otomatis.
                </p>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-white bg-opacity-10 d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-shield-alt text-warning"></i>
                            </div>
                            <span class="small fw-bold">Encrypted Data</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-white bg-opacity-10 d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-bolt text-warning"></i>
                            </div>
                            <span class="small fw-bold">Instant Approval</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="login-auth">
            <div class="auth-card">
                <div class="mb-5">
                    <h2 class="text-dark">Selamat Datang</h2>
                    <p class="text-muted">Silakan masuk ke portal manajemen Anda.</p>
                </div>

                <form action="{{ route('login.process') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="far fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="admin@sekolah.sch.id"
                                required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-end">
                            <label class="form-label">Kata Sandi</label>
                            <a href="#" class="text-decoration-none small fw-bold"
                                style="font-size: 0.75rem; color: var(--orange-brand); margin-bottom: 8px;">Lupa
                                Password?</a>
                        </div>
                        <div class="input-group" id="show_hide_password">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            <span class="input-group-text bg-light border-start-0" style="cursor: pointer;">
                                <i class="fa fa-eye-slash text-muted" aria-hidden="true" style="font-size: 0.8rem;"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label text-muted" for="remember"
                            style="font-size: 0.85rem; font-weight: 500;">Ingat sesi saya</label>
                    </div>

                    <button type="submit" class="btn btn-login w-100 shadow-sm uppercase">
                        MASUK KE SISTEM <i class="fas fa-sign-in-alt ms-2"></i>
                    </button>
                </form>

                <div class="footer-note">
                    <p>© {{ date('Y') }} <strong>E-SPP SYSTEM</strong>. <br> High-Performance School Infrastructure.
                    </p>
                </div>
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#show_hide_password span").on('click', function(event) {
                event.preventDefault();
                let input = $('#show_hide_password input');
                let icon = $('#show_hide_password i');
                if (input.attr("type") == "text") {
                    input.attr('type', 'password');
                    icon.addClass("fa-eye-slash").removeClass("fa-eye");
                } else {
                    input.attr('type', 'text');
                    icon.removeClass("fa-eye-slash").addClass("fa-eye");
                }
            });
        });
    </script>
</body>

</html>
