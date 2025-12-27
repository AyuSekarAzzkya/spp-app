<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-SPP SYSTEM - Transparansi Keuangan Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        .bg-navy {
            background-color: #0F172A;
        }

        .bg-orange-brand {
            background-color: #F87B1B;
        }

        .text-orange-brand {
            color: #F87B1B;
        }

        .hero-gradient {
            background: radial-gradient(circle at 0% 0%, #1e293b 0%, #0f172a 100%);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(2deg);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                opacity: 0.5;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        .glow-effect {
            animation: pulse-glow 4s ease-in-out infinite;
        }

        @keyframes scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .scroll-container {
            display: flex;
            width: 200%;
            animation: scroll 20s linear infinite;
        }

        .nav-glass {
            background: rgba(15, 23, 42, 0.9);
            /* Navy dengan transparansi */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(248, 123, 27, 0.2);
            /* Garis orange tipis di bawah */
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .text-gradient {
            background: linear-gradient(to right, #F87B1B, #ffbc85);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-card {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .feature-card:hover {
            transform: translateY(-15px) scale(1.02);
            border-color: #F87B1B;
            box-shadow: 0 25px 50px -12px rgba(248, 123, 27, 0.2);
        }

        ::selection {
            background: #F87B1B;
            color: white;
        }
    </style>
</head>

<body class="bg-white">

    <nav class="nav-glass fixed w-full z-50 bg-navy/80 backdrop-blur-xl py-4 border-b border-white/5">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center space-x-3 group cursor-pointer">
                <div
                    class="w-11 h-11 bg-orange-brand rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/40 group-hover:rotate-12 transition-transform">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>
                <span class="text-white font-extrabold text-xl tracking-tighter uppercase leading-none">
                    E-SPP <br><span class="text-orange-brand text-sm tracking-[0.2em]">SYSTEM</span>
                </span>
            </div>
            <div class="hidden md:flex space-x-8 text-white/70 font-bold text-[10px] uppercase tracking-[0.2em]">
                <a href="#home" class="hover:text-orange-brand transition">Home</a>
                <a href="#workflow" class="hover:text-orange-brand transition">Alur Kerja</a>
                <a href="#portal" class="hover:text-orange-brand transition">Portal</a>
                <a href="#tentang" class="hover:text-orange-brand transition">Tentang</a>
            </div>
            <a href="{{ route('login') }}"
                class="bg-white text-navy px-6 py-2 rounded-full font-black hover:bg-orange-brand hover:text-blue transition-all text-xs tracking-widest">LOGIN</a>
        </div>
    </nav>

    <section class="hero-gradient min-h-screen pt-40 pb-20 flex items-center text-white relative overflow-hidden"
        id="home">
        <div class="absolute top-20 left-10 w-64 h-64 bg-orange-brand/10 blur-[120px] glow-effect"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-blue-600/10 blur-[150px] glow-effect"></div>

        <div class="container mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center relative z-10">
            <div data-aos="fade-right" data-aos-duration="1200">
                <div
                    class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 mb-8">
                    <span class="w-2 h-2 bg-orange-brand rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black tracking-[0.3em] text-gray-300 uppercase">Modern Finance
                        Solution</span>
                </div>
                <h1 class="text-5xl md:text-7xl font-black mb-8 leading-[1.1] tracking-tight">
                    Kelola SPP <br> <span class="text-gradient uppercase italic">Tanpa Ribet.</span>
                </h1>
                <p class="text-gray-400 text-lg leading-relaxed mb-10 max-w-lg">
                    Platform administrasi keuangan sekolah modern. Kirim bukti bayar cukup lewat foto, verifikasi instan
                    oleh petugas.
                </p>
                <div class="flex flex-col sm:flex-row gap-5">
                    <a href="#workflow"
                        class="bg-orange-brand px-10 py-5 rounded-2xl font-black text-center hover:shadow-[0_0_30px_rgba(248,123,27,0.5)] transition-all">LIHAT
                        CARA KERJA</a>
                    <a href="#portal"
                        class="glass px-10 py-5 rounded-2xl font-black text-center hover:bg-white/10 transition-all border border-white/20">AKSES
                        PORTAL</a>
                </div>
            </div>

            <div class="relative" data-aos="fade-left" data-aos-duration="1200">
                <img src="images/dashboard-preview.png" alt="E-SPP Preview"
                    class="relative z-10 w-full animate-float drop-shadow-[0_50px_50px_rgba(0,0,0,0.5)]">
                <div class="absolute -bottom-6 -left-6 glass p-6 rounded-3xl z-20 animate-float"
                    style="animation-delay: 1s">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-12 h-12 bg-green-500 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/40">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Status Terakhir</p>
                            <p class="font-black text-white italic tracking-tighter">Pembayaran Disetujui</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="bg-orange-brand py-6 overflow-hidden">
        <div class="scroll-container">
            <div
                class="flex space-x-24 items-center text-white font-black uppercase tracking-[0.4em] text-[10px] whitespace-nowrap">
                <span><i class="fas fa-camera mr-2"></i> Upload Bukti Foto</span>
                <span><i class="fas fa-shield-halved mr-2"></i> Verifikasi Admin</span>
                <span><i class="fas fa-clock mr-2"></i> Real-time Approval</span>
                <span><i class="fas fa-file-invoice mr-2"></i> Riwayat Digital</span>
                <span><i class="fas fa-camera mr-2"></i> Upload Bukti Foto</span>
                <span><i class="fas fa-shield-halved mr-2"></i> Verifikasi Admin</span>
            </div>
        </div>
    </div>

    <section id="workflow" class="py-32 bg-slate-50 relative">
        <div class="container mx-auto px-6">
            <div class="max-w-3xl mx-auto text-center mb-24" data-aos="fade-up">
                <h2 class="text-navy font-black text-4xl md:text-5xl mb-6 uppercase tracking-tight italic">Sistem
                    Approval <span class="text-orange-brand font-black">Digital</span></h2>
                <p class="text-gray-500 font-semibold tracking-wide">Proses pembayaran yang transparan dan terekam
                    dengan baik dalam 3 tahap utama.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-12 relative">
                <div class="bg-white p-12 rounded-[3rem] feature-card border border-slate-100 relative overflow-hidden"
                    data-aos="fade-up">
                    <div class="absolute top-4 right-8 text-slate-100 text-8xl font-black">01</div>
                    <div
                        class="w-16 h-16 bg-navy rounded-2xl flex items-center justify-center mb-10 relative z-10 shadow-xl shadow-navy/20">
                        <i class="fas fa-upload text-white text-2xl"></i>
                    </div>
                    <h4 class="font-black text-xl text-navy mb-4 uppercase relative z-10 tracking-tight">Kirim Bukti
                    </h4>
                    <p class="text-gray-500 leading-relaxed relative z-10">Siswa melakukan pembayaran via transfer bank
                        dan mengunggah foto bukti bayar ke portal.</p>
                </div>

                <div class="bg-white p-12 rounded-[3rem] feature-card border border-slate-100 relative overflow-hidden"
                    data-aos="fade-up" data-aos-delay="200">
                    <div class="absolute top-4 right-8 text-slate-100 text-8xl font-black">02</div>
                    <div
                        class="w-16 h-16 bg-orange-brand rounded-2xl flex items-center justify-center mb-10 relative z-10 shadow-xl shadow-orange-500/20">
                        <i class="fas fa-user-check text-white text-2xl"></i>
                    </div>
                    <h4 class="font-black text-xl text-navy mb-4 uppercase relative z-10 tracking-tight">Verifikasi</h4>
                    <p class="text-gray-500 leading-relaxed relative z-10">Petugas memeriksa kecocokan data transaksi
                        dengan bukti yang diunggah oleh siswa.</p>
                </div>

                <div class="bg-white p-12 rounded-[3rem] feature-card border border-slate-100 relative overflow-hidden"
                    data-aos="fade-up" data-aos-delay="400">
                    <div class="absolute top-4 right-8 text-slate-100 text-8xl font-black">03</div>
                    <div
                        class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center mb-10 relative z-10 shadow-xl shadow-green-500/20">
                        <i class="fas fa-check-double text-white text-2xl"></i>
                    </div>
                    <h4 class="font-black text-xl text-navy mb-4 uppercase relative z-10 tracking-tight">Validasi</h4>
                    <p class="text-gray-500 leading-relaxed relative z-10">Status berubah menjadi "Berhasil", dan
                        tagihan siswa otomatis terpotong secara sistem.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="statistik" class="py-32 bg-navy relative">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-16">
                <div class="text-center" data-aos="zoom-in">
                    <div class="text-white text-7xl font-black mb-4 tracking-tighter italic">LIVE</div>
                    <div class="h-1.5 w-12 bg-orange-brand mx-auto rounded-full mb-6"></div>
                    <p class="text-white/40 font-black uppercase text-xs tracking-[0.4em]">Monitoring Sistem</p>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="200">
                    <div class="text-orange-brand text-7xl font-black mb-4 tracking-tighter">SECURE</div>
                    <div class="h-1.5 w-12 bg-white mx-auto rounded-full mb-6"></div>
                    <p class="text-white/40 font-black uppercase text-xs tracking-[0.4em]">Enkripsi Bukti Bayar</p>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="400">
                    <div class="text-white text-7xl font-black mb-4 tracking-tighter italic">V3.0</div>
                    <div class="h-1.5 w-12 bg-orange-brand mx-auto rounded-full mb-6"></div>
                    <p class="text-white/40 font-black uppercase text-xs tracking-[0.4em]">Core Technology</p>
                </div>
            </div>
        </div>
    </section>

    <section id="tentang" class="py-32 bg-white overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-20 items-center">

                <div class="relative" data-aos="fade-right">
                    <div class="absolute -inset-4 bg-orange-brand/10 rounded-[4rem] rotate-3"></div>
                    <div class="relative bg-navy rounded-[3.5rem] p-10 overflow-hidden shadow-2xl">
                        <i
                            class="fas fa-shield-check text-[15rem] absolute -bottom-10 -right-10 text-white/5 rotate-12"></i>
                        <h4 class="text-orange-brand font-black text-xs uppercase tracking-[0.4em] mb-6">Misi Kami</h4>
                        <p class="text-white text-2xl font-bold leading-relaxed relative z-10">
                            "Membangun ekosistem keuangan sekolah yang <span class="text-orange-brand italic">terbuka,
                                jujur, dan terukur</span> melalui teknologi verifikasi digital."
                        </p>
                    </div>
                </div>

                <div data-aos="fade-left">
                    <h2 class="text-navy font-black text-4xl md:text-5xl uppercase italic mb-8 leading-tight">
                        Kenapa Harus <br> <span class="text-orange-brand">E-SPP SYSTEM?</span>
                    </h2>
                    <div class="space-y-8">
                        <div class="flex gap-6">
                            <div
                                class="flex-none w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-navy group-hover:bg-orange-brand transition-all">
                                <i class="fas fa-fingerprint text-xl text-orange-brand"></i>
                            </div>
                            <div>
                                <h5 class="font-black text-navy uppercase tracking-tight mb-2">Keamanan Data Terjamin
                                </h5>
                                <p class="text-slate-500 text-sm leading-relaxed">Setiap bukti bayar yang diunggah
                                    dienkripsi dan disimpan secara permanen sebagai arsip digital yang sah.</p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div
                                class="flex-none w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-navy">
                                <i class="fas fa-bolt text-xl text-orange-brand"></i>
                            </div>
                            <div>
                                <h5 class="font-black text-navy uppercase tracking-tight mb-2">Proses Approval Kilat
                                </h5>
                                <p class="text-slate-500 text-sm leading-relaxed">Petugas mendapatkan notifikasi
                                    langsung saat ada unggahan baru, mempercepat proses verifikasi tanpa harus tatap
                                    muka.</p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div
                                class="flex-none w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-navy">
                                <i class="fas fa-chart-pie text-xl text-orange-brand"></i>
                            </div>
                            <div>
                                <h5 class="font-black text-navy uppercase tracking-tight mb-2">Laporan Real-Time</h5>
                                <p class="text-slate-500 text-sm leading-relaxed">Admin dapat melihat statistik
                                    keuangan detik ini juga, memudahkan perencanaan anggaran sekolah ke depannya.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <hr class="border-slate-360">

    <section id="portal" class="py-32 bg-white relative overflow-hidden">
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-24">
                <h2 class="text-navy font-black text-5xl uppercase italic mb-4">Akses Sistem</h2>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Pilih akun untuk melanjutkan</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <div class="group bg-slate-50 p-12 rounded-[4rem] border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-navy/10 transition-all duration-700"
                    data-aos="fade-up">
                    <i
                        class="fas fa-user-shield text-4xl text-navy group-hover:text-orange-brand mb-10 block transition-colors"></i>
                    <h3 class="text-2xl font-black text-navy mb-4 uppercase tracking-tight">
                        Admin</h3>
                    <p class="text-slate-500 mb-10">Kelola master data, periode biaya, dan
                        kendali penuh sistem.</p>
                    <a href="{{ route('login') }}"
                        class="w-full py-5 bg-navy group-hover:bg-orange-brand text-white text-center rounded-3xl font-black block transition-all uppercase tracking-widest text-xs">Login
                        Admin</a>
                </div>

                <div class="group bg-slate-50 p-12 rounded-[4rem] border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-navy/10 transition-all duration-700"
                    data-aos="fade-up" data-aos-delay="200">
                    <i
                        class="fas fa-user-gear text-4xl text-navy group-hover:text-orange-brand mb-10 block transition-colors"></i>
                    <h3 class="text-2xl font-black text-navy mb-4 uppercase tracking-tight">
                        Petugas</h3>
                    <p class="text-slate-500 mb-10">Verifikasi bukti bayar siswa dan kelola
                        transaksi harian loket.</p>
                    <a href="{{ route('login') }}"
                        class="w-full py-5 bg-navy group-hover:bg-orange-brand text-white text-center rounded-3xl font-black block transition-all uppercase tracking-widest text-xs">Login
                        Petugas</a>
                </div>

                <div class="group bg-slate-50 p-12 rounded-[4rem] border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-navy/10 transition-all duration-700"
                    data-aos="fade-up" data-aos-delay="400">
                    <i
                        class="fas fa-user-graduate text-4xl text-navy group-hover:text-orange-brand mb-10 block transition-colors"></i>
                    <h3 class="text-2xl font-black text-navy mb-4 uppercase tracking-tight">
                        Siswa</h3>
                    <p class="text-slate-500 mb-10">Unggah bukti bayar, cek status, dan
                        riwayat tagihan kamu.</p>
                    <a href="{{ route('login') }}"
                        class="w-full py-5 bg-navy group-hover:bg-orange-brand text-white text-center rounded-3xl font-black block transition-all uppercase tracking-widest text-xs">Login
                        Siswa</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-navy text-white pt-32 pb-12 relative overflow-hidden">
        <div class="absolute inset-0 opacity-5">
            <i class="fas fa-graduation-cap text-[40rem] absolute -bottom-40 -right-40 rotate-12"></i>
        </div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="grid lg:grid-cols-4 gap-16 mb-24">
                <div class="col-span-2">
                    <div class="flex items-center space-x-4 mb-10">
                        <div
                            class="w-14 h-14 bg-orange-brand rounded-2xl flex items-center justify-center shadow-2xl shadow-orange-500/30">
                            <i class="fas fa-graduation-cap text-white text-2xl"></i>
                        </div>
                        <span class="text-3xl font-black uppercase tracking-tighter">E-SPP <span
                                class="text-orange-brand">SYSTEM</span></span>
                    </div>
                    <p class="text-slate-400 max-w-sm text-lg leading-relaxed mb-10 italic">
                        "Mendukung digitalisasi administrasi sekolah dengan sistem verifikasi bukti bayar yang praktis
                        dan aman."
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-2xl text-slate-500 hover:text-orange-brand transition"><i
                                class="fab fa-instagram"></i></a>
                        <a href="#" class="text-2xl text-slate-500 hover:text-orange-brand transition"><i
                                class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-2xl text-slate-500 hover:text-orange-brand transition"><i
                                class="fab fa-github"></i></a>
                    </div>
                </div>

                <div>
                    <h5 class="font-black mb-8 uppercase text-orange-brand tracking-[0.3em] text-xs">Navigasi</h5>
                    <ul class="space-y-4 font-bold text-slate-400 text-sm">
                        <li><a href="#workflow" class="hover:text-white transition">Cara Kerja</a></li>
                        <li><a href="#portal" class="hover:text-white transition">Portal Masuk</a></li>
                        <li><a href="#" class="hover:text-white transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-white transition">Syarat & Ketentuan</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="font-black mb-8 uppercase text-orange-brand tracking-[0.3em] text-xs">Dukungan</h5>
                    <p class="text-slate-400 font-bold text-sm mb-4 italic">Bantuan teknis & integrasi:</p>
                    <a href="mailto:admin@espp-system.com"
                        class="text-white font-black text-sm block mb-2 underline decoration-orange-brand">support@espp-system.com</a>
                </div>
            </div>

            <div class="pt-12 border-t border-white/5 flex flex-col md:row justify-between items-center gap-6">
                <p class="text-slate-600 font-black text-[10px] uppercase tracking-[0.5em]">© 2025 E-SPP SYSTEM • SMK
                    UNGGULAN INDONESIA</p>
                <div class="flex space-x-8">
                    <span class="text-slate-600 font-black text-[10px] uppercase tracking-[0.2em]">Crafted for
                        Excellence</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: false,
            duration: 1000,
            offset: 100,
            easing: 'ease-in-out-cubic'
        });
    </script>
</body>

</html>
