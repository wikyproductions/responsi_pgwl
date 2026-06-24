<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebGIS Pemadaman DIY - Pusat Kontrol</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Geist:wght@100..900&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

    <style>
        :root {
            --bg-base: #051424;
            --bg-surface: #122131;
            --bg-surface-high: #1c2b3c;
            --bg-surface-highest: #273647;
            --border-color: rgba(135, 146, 154, 0.2);
            --border-light: rgba(135, 146, 154, 0.1);

            --text-main: #f1f7ff;
            --text-muted: #94a3b8;

            --color-primary: #38bdf8;
            --color-primary-dim: #004965;
            --color-danger: #ffb4ab;
            --color-danger-dim: #93000a;
            --color-warning: #ffc176;
            --color-warning-dim: #613b00;
            --color-success: #81c995;
            --map-tile-filter: none;
        }

        body.light-mode {
            --bg-base: #f8fafc;
            --bg-surface: #ffffff;
            --bg-surface-high: #f1f5f9;
            --bg-surface-highest: #e2e8f0;
            --border-color: rgba(15, 23, 42, 0.1);
            --border-light: rgba(15, 23, 42, 0.05);

            --text-main: #0f172a;
            --text-muted: #475569;

            --color-primary: #0284c7;
            --color-primary-dim: #e0f2fe;
            --color-danger: #dc2626;
            --color-danger-dim: #fee2e2;
            --color-warning: #d97706;
            --color-warning-dim: #fef3c7;
            --color-success: #16a34a;
            --map-tile-filter: invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .font-mono {
            font-family: 'Geist', monospace;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
            line-height: 1;
        }

        .saas-navbar {
            font-family: 'Poppins', sans-serif;
            background: rgba(12, 33, 49, 0.8) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            height: 64px;
            z-index: 1050;
        }

        body.light-mode .saas-navbar {
            background: rgba(255, 255, 255, 0.85) !important;
        }

        .brand-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--color-primary) !important;
            letter-spacing: -0.02em;
        }

        .nav-icon-btn {
            color: var(--text-muted) !important;
            background: transparent !important;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .nav-icon-btn:hover {
            background: var(--bg-surface-highest) !important;
            color: var(--text-main) !important;
        }

        .profile-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 12px 4px 4px;
            background: var(--bg-surface-high);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            color: var(--text-main) !important;
            transition: all 0.2s;
        }

        .profile-chip:hover {
            background: var(--bg-surface-highest);
        }

        .profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--color-primary-dim);
            color: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .saas-sidebar {
            width: 320px;
            background-color: var(--bg-surface);
            border-right: 1px solid var(--border-color);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: var(--bg-surface-high);
            color: var(--text-main);
        }

        .sidebar-link.active .material-symbols-outlined {
            color: var(--color-primary);
        }

        .scrollable-sidebar-content {
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .scrollable-sidebar-content::-webkit-scrollbar {
            display: none;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .pulse-danger {
            background-color: #ff4d4d;
            box-shadow: 0 0 0 rgba(255, 77, 77, 0.4);
            animation: pulse-danger-anim 2s infinite;
        }

        .pulse-warning {
            background-color: #ffad33;
            box-shadow: 0 0 0 rgba(255, 173, 51, 0.4);
            animation: pulse-warning-anim 2s infinite;
        }

        @keyframes pulse-danger-anim {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 77, 77, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(255, 77, 77, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 77, 77, 0);
            }
        }

        @keyframes pulse-warning-anim {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 173, 51, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(255, 173, 51, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 173, 51, 0);
            }
        }

        .dropdown-menu-custom {
            background-color: var(--bg-surface-high) !important;
            border: 1px solid var(--border-color) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
        }

        .dropdown-menu-custom .dropdown-item {
            color: var(--text-main);
            font-size: 14px;
        }

        .dropdown-menu-custom .dropdown-item:hover {
            background-color: var(--bg-surface-highest);
            color: var(--color-primary);
        }

        .notif-menu-panel {
            width: 340px;
            max-height: 450px;
            overflow-y: auto;
            padding: 0;
            border-radius: 12px;
        }

        .notif-header-box {
            background: rgba(0, 0, 0, 0.15);
            border-bottom: 1px solid var(--border-color);
        }

        .notif-list-item {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-light);
            transition: background 0.2s ease;
            cursor: pointer;
        }

        .notif-list-item:hover {
            background: var(--bg-surface-highest);
        }

        .notif-list-item:last-child {
            border-bottom: none;
        }

        .modal-content {
            background-color: var(--bg-surface) !important;
            color: var(--text-main) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 12px;
        }

        .modal-header,
        .modal-footer {
            border-color: var(--border-color) !important;
        }

        .form-control,
        .form-select {
            background-color: var(--bg-surface-high) !important;
            border-color: var(--border-color) !important;
            color: var(--text-main) !important;
        }

        .form-control[readonly] {
            background-color: var(--bg-surface-highest) !important;
            color: var(--text-main) !important;
            font-weight: 600;
            opacity: 0.85;
        }

        .form-control::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.7;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--color-primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.2) !important;
        }

        .toast-custom {
            background-color: var(--bg-surface-high) !important;
            border: 1px solid var(--border-color) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
        }
    </style>
    @yield('styles')
</head>

<body>

    <header class="saas-navbar w-100 d-flex justify-content-between align-items-center px-4">
        <div class="d-flex align-items-center gap-3">
            <span class="material-symbols-outlined text-primary fs-3">bolt</span>
            <a class="text-decoration-none brand-title" href="/map">WebGIS Pemadaman DIY</a>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button class="nav-icon-btn" id="themeToggleBtn" title="Ganti Tema Visual">
                <span class="material-symbols-outlined" id="themeIcon">dark_mode</span>
            </button>

            <div class="dropdown">
                <button class="nav-icon-btn position-relative" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false" title="Notifikasi Grid">
                    <span class="material-symbols-outlined">notifications</span>
                    <span
                        class="position-absolute top-0 end-0 mt-1 me-1 w-2 h-2 bg-danger rounded-circle shadow"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-custom notif-menu-panel shadow-lg">
                    <div class="notif-header-box p-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-white fs-14">Notifikasi Pusat Kontrol</span>
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0.5 fs-10 fw-bold">3
                            BARU</span>
                    </div>
                    <div class="notif-scroll-area">
                        <div class="notif-list-item d-flex gap-3">
                            <div class="text-warning mt-1"><i class="fa-solid fa-circle-exclamation fs-5"></i></div>
                            <div>
                                <h6 class="text-white fs-13 mb-0 fw-semibold">Aduan Gangguan Baru</h6>
                                <p class="text-muted fs-11 mb-1">Tiket masuk dari user1: Padam Tanpa Jadwal di wilayah
                                    DIY.</p>
                                <small class="text-muted fs-10 font-mono">Baru Saja</small>
                            </div>
                        </div>
                        <div class="notif-list-item d-flex gap-3">
                            <div class="text-danger mt-1"><i class="fa-brands fa-simplybuilt fs-5"></i></div>
                            <div>
                                <h6 class="text-white fs-13 mb-0 fw-semibold">Anomali Trafo Kritis</h6>
                                <p class="text-muted fs-11 mb-1">Unit TRF-SLM-001 mendeteksi pembebanan puncak di luar
                                    batas normal.</p>
                                <small class="text-muted fs-10 font-mono">5 mnt lalu</small>
                            </div>
                        </div>
                        <div class="notif-list-item d-flex gap-3">
                            <div class="text-info mt-1"><span class="material-symbols-outlined fs-5">schedule</span>
                            </div>
                            <div>
                                <h6 class="text-white fs-13 mb-0 fw-semibold">Jadwal Pemadaman Aktif</h6>
                                <p class="text-muted fs-11 mb-1">Kelurahan Sendangsari resmi memasuki durasi
                                    pemeliharaan berkala.</p>
                                <small class="text-muted fs-10 font-mono">12 mnt lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button class="nav-icon-btn" data-bs-toggle="modal" data-bs-target="#tentangAplikasiModal"
                title="Tentang WebGIS Pemadaman DIY">
                <span class="material-symbols-outlined">info</span>
            </button>

            <div style="width: 1px; height: 24px; background: var(--border-color); margin: 0 8px;"></div>

            <a class="nav-link text-muted fw-medium me-3" style="font-size: 14px; color: var(--text-muted) !important;"
                href="{{ route('statistik') }}">Analitik Data</a>

            @auth
                <div class="dropdown">
                    <button class="btn border-0 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-chip">
                            <div class="profile-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <span class="fw-medium text-start"
                                style="font-size: 13px; line-height: 1.2; color: var(--text-main);">
                                {{ auth()->user()->name }}<br>
                                <span class="text-muted d-block"
                                    style="font-size: 10px; color: var(--text-muted) !important;">{{ auth()->user()->role }}</span>
                            </span>
                            <span class="material-symbols-outlined text-muted fs-6 ms-1"
                                style="color: var(--text-muted) !important;">expand_more</span>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom mt-2">
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                href="{{ route('profile.edit') }}">
                                <span class="material-symbols-outlined fs-5">manage_accounts</span> Pengaturan Profil
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider border-secondary opacity-25">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit"
                                    class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                    <span class="material-symbols-outlined fs-5">logout</span> Keluar Sistem
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </header>

    <main class="flex-grow-1 d-flex overflow-hidden" style="height: calc(100vh - 64px);">
        <aside class="saas-sidebar p-3">
            <div class="d-flex align-items-center gap-2 mb-4 px-2">
                <div class="profile-avatar bg-primary text-white fw-bold">SO</div>
                <div>
                    <h6 class="mb-0" style="font-size: 13px; font-weight: 600; color: var(--text-main);">Pusat
                        Kontrol</h6>
                    <small class="text-muted d-block"
                        style="font-size: 11px; color: var(--text-muted) !important;">Sistem Regional DIY</small>
                </div>
            </div>

            <div class="mb-3">
                <a href="/map" class="sidebar-link active mb-1">
                    <span class="material-symbols-outlined">map</span>
                    <span>Tampilan Peta</span>
                </a>
                <a href="{{ route('statistik') }}" class="sidebar-link mb-1">
                    <span class="material-symbols-outlined">bar_chart</span>
                    <span>Statistik Wilayah</span>
                </a>
            </div>

            <hr class="my-2 opacity-25" style="color: var(--text-muted)">

            <div class="scrollable-sidebar-content pt-2">
                @yield('sidebar-stats')
            </div>
        </aside>

        <section class="flex-grow-1 h-100 position-relative overflow-hidden">
            @yield('content')
        </section>
    </main>

    <div class="modal fade" id="tentangAplikasiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-poppins fw-bold text-primary fs-16">Tentang WebGIS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        style="filter: var(--map-tile-filter);"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="mb-4 rounded-4 overflow-hidden border border-outline-variant bg-slate-900/10 p-2">
                        <img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=600&auto=format&fit=crop"
                            alt="Pusat Jaringan Yogyakarta Kelistrikan" class="img-fluid rounded-3 opacity-90">
                    </div>
                    <h6 class="fw-bold mb-2 fs-15 font-poppins" style="color: var(--text-main);">Latar Belakang Pembuatan WebGIS</h6>
                    <p class="fs-12 mb-0 text-muted" style="line-height: 1.6; text-align: justify;">
                        Pemadaman listrik bergilir yang melanda Pulau Jawa sepanjang Juni 2026, termasuk wilayah
                        Kabupaten Sleman dan Kota Yogyakarta, banyak dikeluhkan warga karena durasi mati listrik yang
                        cukup panjang turut mengganggu aktivitas rumah tangga, perkantoran, hingga UMKM, sementara
                        informasi jadwal dan wilayah terdampak masih tersebar tidak menentu lewat media sosial dan grup
                        percakapan warga. Minimnya akses informasi yang cepat dan berbasis lokasi inilah yang
                        melatarbelakangi pengembangan WebGIS Pemadaman DIY, sebuah sistem informasi geografis yang
                        memetakan kondisi kelistrikan secara real-time hingga tingkat kelurahan/desa, sekaligus menjadi
                        sarana bagi masyarakat untuk memantau area terdampak dan melaporkan langsung gangguan yang
                        mereka alami.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000;">
        @if (session('success'))
            <div class="toast toast-custom" role="alert" aria-live="assertive" aria-atomic="true"
                data-bs-delay="4000">
                <div class="toast-header bg-transparent border-0 pb-0 pt-2"
                    style="color: var(--text-main) !important;">
                    <span class="material-symbols-outlined text-success me-2">check_circle</span>
                    <strong class="me-auto" style="color: var(--color-primary) !important;">Notifikasi Sistem</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"
                        style="filter: var(--map-tile-filter);"></button>
                </div>
                <div class="toast-body pt-1 pb-2" style="color: var(--text-main) !important; font-weight: 500;">
                    {{ session('success') }}
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@terraformer/wkt"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastElements = [].slice.call(document.querySelectorAll('.toast'))
            const toastList = toastElements.map(el => new bootstrap.Toast(el))
            toastList.forEach(toast => toast.show())

            const themeToggleBtn = document.getElementById('themeToggleBtn')
            const themeIcon = document.getElementById('themeIcon')
            const currentTheme = localStorage.getItem('theme') || 'dark'

            if (currentTheme === 'light') {
                document.body.classList.add('light-mode')
                themeIcon.textContent = 'light_mode'
            }

            themeToggleBtn.addEventListener('click', function() {
                document.body.classList.toggle('light-mode')
                let theme = 'dark'
                if (document.body.classList.contains('light-mode')) {
                    theme = 'light'
                    themeIcon.textContent = 'light_mode'
                } else {
                    themeIcon.textContent = 'dark_mode'
                }
                localStorage.setItem('theme', theme)
            })
        })
    </script>
    @yield('scripts')
</body>

</html>
