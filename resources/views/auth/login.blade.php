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
                <div class="bg-white rounded-2 d-flex align-items-center justify-content-center"
                    style="width: 35px; height: 35px;">
                    <i class="fas fa-graduation-cap text-primary"></i>
                </div>
                <span>E-SPP SYSTEM</span>
            </div>

            <div class="visual-content">
                <span class="badge bg-info mb-3 px-3 py-2 rounded-pill">SISTEM KEUANGAN</span>
                <h1>Kelola Pembayaran <br> Jadi Lebih Mudah.</h1>
                <p class="lead opacity-75">Monitoring SPP siswa secara real-time, akurat, dan transparan dalam satu
                    platform.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-check-circle text-info"></i>
                            <span class="small">Laporan Instan</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-check-circle text-info"></i>
                            <span class="small">Keamanan Data</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="login-auth">
            <div class="auth-card">
                <div class="mb-4">
                    <h2 class="fw-bold text-dark">Login</h2>
                    <p class="text-muted small">Gunakan akun Anda.</p>
                </div>

                <form action="{{ route('login.process') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="far fa-user"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="nama@sekolah.sch.id"
                                required autofocus>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <label class="form-label">Password</label>
                            <a href="#" class="text-decoration-none small fw-bold text-info"
                                style="font-size: 0.7rem;">Lupa?</a>
                        </div>
                        <div class="input-group" id="show_hide_password">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            <span class="input-group-text bg-light" style="cursor: pointer;">
                                <i class="fa fa-eye-slash text-muted" aria-hidden="true" style="font-size: 0.8rem;"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label text-muted" for="remember" style="font-size: 0.8rem;">Ingat
                            saya</label>
                    </div>

                    <button type="submit" class="btn btn-login w-100 shadow-sm">
                        LOGIN KE DASHBOARD <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="footer-note">
                    <p>© {{ date('Y') }} E-SPP Management System. <br> Powered by School IT</p>
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
