<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Terjadi Kesalahan Sistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
            color: #1e293b;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(#f1f5f9 1px, transparent 1px);
            background-size: 30px 30px;
            z-index: -1;
        }

        .content-wrapper {
            text-align: center;
            max-width: 600px;
            padding: 40px 20px;
        }

        .illustration-container {
            position: relative;
            margin-bottom: 40px;
        }

        /* Ikon Utama Robot Sedih/Error yang Imut */
        .main-icon {
            font-size: 110px;
            color: #334155;
            /* Navy Gray */
            display: inline-block;
            animation: shake 4s ease-in-out infinite;
        }

        @keyframes shake {

            0%,
            100% {
                transform: rotate(0deg);
            }

            10%,
            30%,
            50% {
                transform: rotate(-5deg);
            }

            20%,
            40%,
            60% {
                transform: rotate(5deg);
            }

            70% {
                transform: rotate(0deg);
            }
        }

        .warning-cloud {
            position: absolute;
            font-size: 45px;
            color: #f43f5e;
            /* Coral/Red Soft */
            top: -10px;
            right: 32%;
            animation: float-alt 3s ease-in-out infinite;
        }

        @keyframes float-alt {

            0%,
            100% {
                transform: translateY(0px);
                opacity: 0.8;
            }

            50% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }

        h2 {
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -1px;
            font-size: 2.2rem;
        }

        .error-code {
            color: #94a3b8;
            font-weight: 600;
            letter-spacing: 2px;
            margin-bottom: 10px;
            display: block;
        }

        .description {
            color: #64748b;
            font-size: 1.1rem;
            line-height: 1.7;
            margin-top: 15px;
        }

        .btn-group-custom {
            margin-top: 35px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn-retry {
            background-color: #0f172a;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
        }

        .btn-retry:hover {
            background-color: #334155;
            transform: translateY(-2px);
            color: white;
        }

        .btn-back {
            background-color: #f1f5f9;
            color: #475569;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }
    </style>
</head>

<body>
    <div class="bg-pattern"></div>

    <div class="content-wrapper">
        <div class="illustration-container">
            <div class="main-icon">
                <i class="mdi mdi-robot-dead-outline"></i>
            </div>
            <div class="warning-cloud">
                <i class="mdi mdi-alert-circle-outline"></i>
            </div>
        </div>

        <span class="error-code">ERROR 500</span>
        <h2>Waduh, Sistem Lagi Bingung!</h2>

        <p class="description">
            Sepertinya ada sedikit kendala teknis di dalam server kami.
            Tim kami sudah otomatis mendapatkan laporan ini. Silakan coba muat ulang halaman.
        </p>

        <div class="btn-group-custom">
            <button onclick="location.reload()" class="btn btn-retry">
                <i class="mdi mdi-refresh me-2"></i> Muat Ulang
            </button>
            <a href="/" class="btn btn-back">
                <i class="mdi mdi-home-outline me-2"></i> Ke Beranda
            </a>
        </div>

        <div class="mt-5">
            <p class="small text-muted mb-0">© {{ date('Y') }} — E-SPP APPS</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
