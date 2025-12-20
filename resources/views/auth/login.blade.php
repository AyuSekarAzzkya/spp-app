<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi SPP</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #6c5ce7, #00b894);
            min-height: 100vh;
        }

        .card {
            border-radius: 16px;
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .card::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #6c5ce7;
        }

        .btn-primary {
            background: #6c5ce7;
            border: none;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #4834d4;
        }

        .alert {
            border-radius: 12px;
        }

        .login-title {
            color: #2d3436;
        }

        .login-subtitle {
            color: #636e72;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh">

            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg p-4">

                    <div class="text-center mb-4">
                        <h3 class="fw-bold login-title">Selamat Datang</h3>
                        <p class="login-subtitle">Masuk untuk mengelola pembayaran SPP</p>
                    </div>

                    {{-- ALERT SUCCESS --}}
                    @if (session('success'))
                        <div class="alert alert-success text-center">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- ERROR LOGIN --}}
                    @if ($errors->any())
                        <div class="alert alert-danger text-center">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.process') }}" method="POST">
                        @csrf

                        {{-- EMAIL --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@example.com"
                                value="{{ old('email') }}" required>
                        </div>

                        {{-- PASSWORD --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="********" required>
                        </div>

                        {{-- BUTTON --}}
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                            Masuk
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Belum punya akun? <a href="#" class="text-primary">Daftar</a>
                        </small>
                    </div>

                </div>

                <div class="text-center mt-4 text-white-50 small">
                    © {{ date('Y') }} Aplikasi SPP
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
