<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Pemeliharaan Sistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

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

        /* Ornamen Background Halus (Biar gak polos bgt) */
        .bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.5;
            z-index: -1;
        }

        .content-wrapper {
            text-align: center;
            max-width: 600px;
            padding: 40px 20px;
        }

        /* Ikon Karakter: Perpaduan Robot & Teknologi yang Imut */
        .illustration-container {
            position: relative;
            margin-bottom: 40px;
        }

        .main-icon {
            font-size: 110px;
            color: #0f172a; /* Navy Formal */
            display: inline-block;
            animation: float-soft 3s ease-in-out infinite;
        }

        @keyframes float-soft {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        .gear-addon {
            position: absolute;
            font-size: 40px;
            color: #f59e0b; /* Amber/Gold */
            bottom: 0;
            right: 35%;
            animation: spin-slow 8s linear infinite;
        }

        @keyframes spin-slow {
            100% { transform: rotate(360deg); }
        }

        h2 {
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -1px;
            font-size: 2rem;
        }

        .description {
            color: #64748b;
            font-size: 1.1rem;
            line-height: 1.7;
            margin-top: 15px;
        }

        /* Button Modern & Formal */
        .btn-update {
            background-color: #0f172a;
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 30px;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
        }

        .btn-update:hover {
            background-color: #334155;
            transform: translateY(-2px);
            color: white;
        }

        /* Loading Dots yang Kyut */
        .loading-dots span {
            width: 8px;
            height: 8px;
            margin: 0 4px;
            background-color: #cbd5e1;
            border-radius: 50%;
            display: inline-block;
            animation: dots 1.4s infinite ease-in-out both;
        }

        .loading-dots span:nth-child(1) { animation-delay: -0.32s; }
        .loading-dots span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes dots {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1.0); }
        }
    </style>
</head>

<body>
    <div class="bg-pattern"></div>

    <div class="content-wrapper">
        <div class="illustration-container">
            <div class="main-icon">
                <i class="mdi mdi-robot-happy-outline"></i>
            </div>
            <div class="gear-addon">
                <i class="mdi mdi-cog-outline"></i>
            </div>
        </div>

        <h2>Sistem Sedang Diperbarui</h2>
        
        <p class="description">
            Kami sedang melakukan pemeliharaan rutin untuk meningkatkan performa layanan <strong>E-SPP</strong>. 
            Proses ini tidak akan memakan waktu lama. Mohon tunggu sebentar ya!
        </p>

        <div class="loading-dots my-4">
            <span></span><span></span><span></span>
        </div>

        <div>
            <button onclick="location.reload()" class="btn btn-update">
                <i class="mdi mdi-sync me-2"></i> Segarkan Halaman
            </button>
        </div>

        <div class="mt-5">
            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 2px;">
                © {{ date('Y') }} — Development Team
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>