<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fff;
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

        .main-icon {
            font-size: 110px;
            color: #0f172a;
            display: inline-block;
            animation: lock-shake 4s ease-in-out infinite;
        }

        @keyframes lock-shake {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .shield-badge {
            position: absolute;
            font-size: 40px;
            color: #10b981;
            top: 0;
            right: 33%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.5;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.5;
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

        .btn-navy {
            background-color: #0f172a;
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 12px;
            font-weight: 600;
            transition: 0.3s;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
            text-decoration: none;
            display: inline-block;
        }

        .btn-navy:hover {
            background-color: #334155;
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>

<body>
    <div class="bg-pattern"></div>
    <div class="content-wrapper">
        <div class="position-relative mb-4">
            <div class="main-icon"><i class="mdi mdi-shield-lock-outline"></i></div>
            <div class="shield-badge"><i class="mdi mdi-check-decagram-outline"></i></div>
        </div>
        <span class="error-code">ERROR 403</span>
        <h2>Eits, Gak Bisa Masuk!</h2>
        <p class="text-muted fs-5 mt-3">Hanya user dengan akses khusus yang boleh masuk ke sini. Kamu mungkin salah
            pintu.</p>
        <div class="mt-5">
            <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-4 me-2">Kembali</a>
            <a href="/" class="btn btn-navy"><i class="mdi mdi-view-dashboard-outline me-2"></i> Ke Dashboard</a>
        </div>
    </div>
</body>

</html>
