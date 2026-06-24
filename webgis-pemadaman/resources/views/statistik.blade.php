<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>WebGIS Pemadaman DIY - Analitik & Statistik</title>

    <!-- Fonts and Icons -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;600;700&family=Geist:wght@500;600&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Tailwind Config Mapped to CSS Variables -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "background": "var(--bg-base)",
                        "surface": "var(--bg-surface)",
                        "surface-container": "var(--bg-surface)",
                        "surface-container-low": "var(--bg-surface-low)",
                        "surface-container-high": "var(--bg-surface-high)",
                        "surface-container-highest": "var(--bg-surface-highest)",
                        "on-surface": "var(--text-main)",
                        "on-surface-variant": "var(--text-muted)",
                        "outline-variant": "var(--border-color)",
                        "primary": "var(--color-primary)",
                        "error": "var(--color-danger)",
                        "tertiary": "var(--color-warning)",
                        "primary-container": "var(--color-primary)"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "stack-md": "16px",
                        "stack-lg": "24px",
                        "sidebar-width": "320px",
                        "stack-sm": "8px",
                        "margin-container": "24px",
                        "gutter": "16px"
                    },
                    "fontFamily": {
                        "sans": ["Inter", "sans-serif"],
                        "poppins": ["Poppins", "sans-serif"],
                        "mono-data": ["Geist", "monospace"]
                    }
                }
            }
        }
    </script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* TEMA VARIABEL SINKRON DENGAN MAP BLADE */
        :root {
            --bg-base: #051424;
            --bg-surface: #122131;
            --bg-surface-low: #0d1c2d;
            --bg-surface-high: #1c2b3c;
            --bg-surface-highest: #273647;
            --border-color: rgba(135, 146, 154, 0.2);
            --text-main: #f1f7ff;
            --text-muted: #94a3b8;
            --color-primary: #38bdf8;
            --color-danger: #ffb4ab;
            --color-warning: #ffc176;
        }

        html:not(.dark) {
            --bg-base: #f8fafc;
            --bg-surface: #ffffff;
            --bg-surface-low: #f1f5f9;
            --bg-surface-high: #e2e8f0;
            --bg-surface-highest: #cbd5e1;
            --border-color: rgba(15, 23, 42, 0.1);
            --text-main: #0f172a;
            --text-muted: #475569;
            --color-primary: #0284c7;
            --color-danger: #dc2626;
            --color-warning: #d97706;
        }

        .glass-panel {
            background: var(--bg-surface-high);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .metric-ring-container {
            position: relative;
            width: 72px;
            height: 72px;
        }

        .metric-ring-svg {
            transform: rotate(-90deg);
            width: 100%;
            height: 100%;
        }

        .metric-ring-bg {
            fill: none;
            stroke: var(--bg-surface-low);
            stroke-width: 6;
        }

        .metric-ring-progress {
            fill: none;
            stroke: var(--ring-color, var(--color-primary));
            stroke-width: 6;
            stroke-linecap: round;
            stroke-dasharray: 226;
            stroke-dashoffset: var(--dash-offset, 0);
            transition: stroke-dashoffset 1s ease-in-out;
        }

        .metric-ring-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body
    class="bg-background text-on-surface antialiased overflow-x-hidden min-h-screen flex transition-colors duration-300">

    <!-- SIDEBAR UTAMA KIRI -->
    <nav
        class="hidden md:flex flex-col fixed left-0 top-0 h-full w-sidebar-width bg-surface-container border-r border-outline-variant shadow-xl z-40 pt-4">
        <div class="flex items-center gap-2 mb-6 px-4">
            <div
                class="w-8 h-8 rounded-full bg-primary text-background font-bold flex items-center justify-center text-sm font-poppins">
                SO</div>
            <div>
                <h6 class="font-semibold text-[13px] leading-tight">Pusat Kontrol</h6>
                <small class="text-on-surface-variant text-[11px] block">Sistem Regional DIY</small>
            </div>
        </div>

        <div class="flex-grow py-2 flex flex-col gap-1">
            <a class="text-on-surface-variant px-4 py-3 flex items-center gap-3 hover:bg-surface-container-highest transition-all duration-150 mx-2 rounded-lg text-[14px] font-medium"
                href="/map">
                <span class="material-symbols-outlined">map</span>
                <span>Tampilan Peta</span>
            </a>
            <a class="bg-surface-container-highest text-primary px-4 py-3 flex items-center gap-3 transition-all duration-150 mx-2 rounded-lg text-[14px] font-semibold"
                href="#">
                <span class="material-symbols-outlined">bar_chart</span>
                <span>Statistik Wilayah</span>
            </a>
        </div>
    </nav>

    <!-- AREA KONTEN UTAMA KANAN -->
    <div class="flex-1 flex flex-col min-h-screen md:ml-sidebar-width w-full relative">

        <!-- TOP NAVBAR FONT POPPINS -->
        <header
            class="bg-surface/80 backdrop-blur-xl border-b border-outline-variant flex justify-between items-center h-16 px-margin-container w-full sticky top-0 z-50 font-poppins transition-colors duration-300">
            <div class="flex items-center gap-4">
                <h1 class="text-[20px] font-bold text-primary tracking-tight">WebGIS Pemadaman DIY</h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1 border-r border-outline-variant pr-4 h-8">
                    <!-- Tombol Interaktif Switcher Tema -->
                    <button
                        class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-colors"
                        id="themeToggleBtn" title="Ganti Tema Visual">
                        <span class="material-symbols-outlined" id="themeIcon">dark_mode</span>
                    </button>

                    <!-- Dropdown Kontrol Notifikasi Sistem -->
                    <div class="relative inline-block text-left">
                        <button
                            class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-colors relative"
                            id="notifDropdownBtn">
                            <span class="material-symbols-outlined">notifications</span>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-circle shadow"></span>
                        </button>

                        <div class="hidden absolute right-0 mt-2 w-80 bg-surface border border-outline-variant rounded-xl shadow-xl overflow-hidden z-50"
                            id="notifDropdownPanel">
                            <div
                                class="p-3 bg-slate-900/10 border-b border-outline-variant d-flex justify-between items-center">
                                <span class="font-bold text-[14px]">Notifikasi Pusat Kontrol</span>
                                <span class="bg-rose-500/20 text-error text-[10px] font-bold px-2 py-0.5 rounded-full">3
                                    BARU</span>
                            </div>
                            <div class="max-h-80 overflow-y-auto divide-y divide-outline-variant/30">
                                <div
                                    class="p-3 flex gap-3 hover:bg-surface-container-high cursor-pointer transition-colors">
                                    <div class="text-tertiary mt-0.5"><i
                                            class="fa-solid fa-circle-exclamation text-[16px]"></i></div>
                                    <div>
                                        <h6 class="text-[13px] font-semibold mb-0">Aduan Gangguan Baru</h6>
                                        <p class="text-on-surface-variant text-[11px] mb-0.5">Tiket masuk dari user1:
                                            Padam Tanpa Jadwal di DIY.</p>
                                        <small class="text-on-surface-variant text-[10px] font-mono">Baru Saja</small>
                                    </div>
                                </div>
                                <div
                                    class="p-3 flex gap-3 hover:bg-surface-container-high cursor-pointer transition-colors">
                                    <div class="text-error mt-0.5"><i class="fa-brands fa-simplybuilt text-[16px]"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-[13px] font-semibold mb-0">Anomali Trafo Kritis</h6>
                                        <p class="text-on-surface-variant text-[11px] mb-0.5">Unit TRF-SLM-001
                                            mendeteksi pembebanan puncak.</p>
                                        <small class="text-on-surface-variant text-[10px] font-mono">5 mnt lalu</small>
                                    </div>
                                </div>
                                <div
                                    class="p-3 flex gap-3 hover:bg-surface-container-high cursor-pointer transition-colors">
                                    <div class="text-primary mt-0.5"><span
                                            class="material-symbols-outlined text-[16px]">schedule</span></div>
                                    <div>
                                        <h6 class="text-[13px] font-semibold mb-0">Jadwal Pemadaman Aktif</h6>
                                        <p class="text-on-surface-variant text-[11px] mb-0.5">Kelurahan Sendangsari
                                            memasuki durasi pemeliharaan.</p>
                                        <small class="text-on-surface-variant text-[10px] font-mono">12 mnt lalu</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button
                        class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-colors"><span
                            class="material-symbols-outlined">settings</span></button>
                </div>
                <div
                    class="flex items-center gap-2 bg-surface-container-high px-3 py-1.5 border border-outline-variant rounded-full">
                    <div
                        class="w-8 h-8 rounded-full bg-primary/20 text-primary flex items-center justify-center font-bold text-sm">
                        AD</div>
                    <span class="font-medium text-primary text-[13px] hidden sm:block">admin1</span>
                </div>
            </div>
        </header>

        <!-- DASHBOARD KONTEN -->
        <main class="flex-1 p-margin-container overflow-y-auto relative">
            <div
                class="absolute top-0 left-1/4 w-1/2 h-96 bg-primary/5 rounded-full blur-[120px] pointer-events-none -z-10">
            </div>

            <header class="mb-stack-lg">
                <h2 class="text-headline-lg font-bold text-on-surface flex items-center gap-3">
                    <span class="material-symbols-outlined text-[32px] text-primary">pie_chart</span>
                    <span>Dasbor Analitik & Statistik</span>
                </h2>
            </header>

            <!-- KUMPULAN KARTU METRIK DINAMIS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter mb-stack-lg">
                <div
                    class="glass-panel rounded-xl p-4 flex items-center justify-between group hover:bg-surface-container-high/40 transition-colors">
                    <div>
                        <p class="text-[11px] font-mono-data uppercase tracking-wider text-on-surface-variant mb-1">
                            TOTAL ASET TRAFO</p>
                        <div class="flex items-baseline gap-1">
                            <span id="metric_total_trafo" class="text-[32px] font-bold text-on-surface">0</span>
                            <span class="text-on-surface text-[14px] font-medium">Unit</span>
                        </div>
                        <p id="metric_sub_trafo_normal"
                            class="text-[12px] text-emerald-500 flex items-center gap-1 mt-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> 0 Normal
                        </p>
                    </div>
                    <div class="metric-ring-container" style="--ring-color: var(--color-primary); --dash-offset: 0;">
                        <svg class="metric-ring-svg" viewbox="0 0 80 80">
                            <circle class="metric-ring-bg" cx="40" cy="40" r="36"></circle>
                            <circle class="metric-ring-progress" id="ring_total_trafo" cx="40" cy="40"
                                r="36"></circle>
                        </svg>
                        <div class="metric-ring-icon text-primary">
                            <i class="fa-brands fa-simplybuilt text-[20px]"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="glass-panel rounded-xl p-4 flex items-center justify-between group hover:bg-surface-container-high/40 transition-colors">
                    <div>
                        <p class="text-[11px] font-mono-data uppercase tracking-wider text-on-surface-variant mb-1">
                            TRAFO BERMASALAH</p>
                        <div class="flex items-baseline gap-1">
                            <span id="metric_trafo_gangguan" class="text-[32px] font-bold text-error">0</span>
                            <span class="text-error text-[14px] font-medium">Unit</span>
                        </div>
                        <p class="text-[12px] text-on-surface-variant mt-1">Butuh pemeliharaan segera</p>
                    </div>
                    <div class="metric-ring-container" style="--ring-color: var(--color-danger); --dash-offset: 226;">
                        <svg class="metric-ring-svg" viewbox="0 0 80 80">
                            <circle class="metric-ring-bg" cx="40" cy="40" r="36"></circle>
                            <circle class="metric-ring-progress" id="ring_trafo_gangguan" cx="40"
                                cy="40" r="36"></circle>
                        </svg>
                        <div class="metric-ring-icon text-error">
                            <i class="fa-brands fa-simplybuilt text-[20px]"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="glass-panel rounded-xl p-4 flex items-center justify-between group hover:bg-surface-container-high/40 transition-colors">
                    <div>
                        <p class="text-[11px] font-mono-data uppercase tracking-wider text-on-surface-variant mb-1">
                            TOTAL ADUAN WARGA</p>
                        <div class="flex items-baseline gap-1">
                            <span id="metric_total_laporan" class="text-[32px] font-bold text-tertiary">0</span>
                            <span class="text-tertiary text-[14px] font-medium">Berkas</span>
                        </div>
                        <p id="metric_sub_laporan_pending"
                            class="text-[12px] text-tertiary flex items-center gap-1 mt-1">
                            <span class="material-symbols-outlined text-[14px]">schedule</span> 0 Antrean
                        </p>
                    </div>
                    <div class="metric-ring-container"
                        style="--ring-color: var(--color-warning); --dash-offset: 226;">
                        <svg class="metric-ring-svg" viewbox="0 0 80 80">
                            <circle class="metric-ring-bg" cx="40" cy="40" r="36"></circle>
                            <circle class="metric-ring-progress" id="ring_total_laporan" cx="40"
                                cy="40" r="36"></circle>
                        </svg>
                        <div class="metric-ring-icon text-tertiary">
                            <i class="fa-solid fa-circle-exclamation text-[20px]"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="glass-panel rounded-xl p-4 flex items-center justify-between group hover:bg-surface-container-high/40 transition-colors">
                    <div>
                        <p class="text-[11px] font-mono-data uppercase tracking-wider text-on-surface-variant mb-1">
                            ADUAN SELESAI</p>
                        <div class="flex items-baseline gap-1">
                            <span id="metric_laporan_selesai" class="text-[32px] font-bold text-emerald-500">0</span>
                            <span class="text-emerald-500 text-[14px] font-medium">Kasus</span>
                        </div>
                        <p id="metric_sub_laporan_proses"
                            class="text-[12px] text-on-surface-variant flex items-center gap-1 mt-1">
                            <span class="material-symbols-outlined text-[14px] text-sky-400">sync</span> 0 Perbaikan
                        </p>
                    </div>
                    <div class="metric-ring-container" style="--ring-color: #81c995; --dash-offset: 226;">
                        <svg class="metric-ring-svg" viewbox="0 0 80 80">
                            <circle class="metric-ring-bg" cx="40" cy="40" r="36"></circle>
                            <circle class="metric-ring-progress" id="ring_laporan_selesai" cx="40"
                                cy="40" r="36"></circle>
                        </svg>
                        <div class="metric-ring-icon text-emerald-500">
                            <span class="material-symbols-outlined text-[24px]">check_circle</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AREA GRAFIK TENGAH -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter mb-stack-lg">
                <div class="glass-panel rounded-xl p-4 lg:col-span-2 flex flex-col h-[360px]">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-[16px] font-semibold text-on-surface">Tren Kategori Gangguan Masyarakat</h3>
                    </div>
                    <div class="flex-1 relative w-full h-full">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
                <div class="glass-panel rounded-xl p-4 flex flex-col h-[360px]">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-[16px] font-semibold text-on-surface">Proporsi Kondisi Tipe Trafo</h3>
                    </div>
                    <div class="flex-1 relative w-full h-full flex items-center justify-center">
                        <div class="relative w-[220px] h-[220px]">
                            <canvas id="healthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL REAL DATA -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-gutter">
                <div class="glass-panel rounded-xl flex flex-col overflow-hidden">
                    <div
                        class="p-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low/30">
                        <h3 class="font-semibold text-on-surface flex items-center gap-2 text-[15px]">
                            <i class="fa-brands fa-simplybuilt text-primary"></i>
                            <span>Informasi Trafo (trafos)</span>
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-[13px]">
                            <thead>
                                <tr
                                    class="font-mono-data text-on-surface-variant border-b border-outline-variant bg-slate-500/10">
                                    <th class="p-3 font-semibold">kode_trafo</th>
                                    <th class="p-3 font-semibold">kapasitas</th>
                                    <th class="p-3 font-semibold">wilayah_melayani</th>
                                    <th class="p-3 font-semibold">status</th>
                                </tr>
                            </thead>
                            <tbody id="trafoTableBody" class="font-mono-data text-on-surface">
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-on-surface-variant">Memuat data
                                        trafo...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="glass-panel rounded-xl flex flex-col overflow-hidden">
                    <div
                        class="p-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low/30">
                        <h3 class="font-semibold text-on-surface flex items-center gap-2 text-[15px]">
                            <i class="fa-solid fa-circle-exclamation text-tertiary"></i>
                            <span>Daftar Aduan (laporans)</span>
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-[13px]">
                            <thead>
                                <tr
                                    class="font-mono-data text-on-surface-variant border-b border-outline-variant bg-slate-500/10">
                                    <th class="p-3 font-semibold">nama_pelapor</th>
                                    <th class="p-3 font-semibold">kategori_gangguan</th>
                                    <th class="p-3 font-semibold">deskripsi</th>
                                    <th class="p-3 font-semibold">status</th>
                                </tr>
                            </thead>
                            <tbody id="laporanTableBody" class="text-on-surface">
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-on-surface-variant">Memuat data
                                        aduan...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // LOGIKA INTERAKTIF DROPDOWN PANEL NOTIFIKASI
        const notifDropdownBtn = document.getElementById('notifDropdownBtn');
        const notifDropdownPanel = document.getElementById('notifDropdownPanel');
        if (notifDropdownBtn && notifDropdownPanel) {
            notifDropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notifDropdownPanel.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!notifDropdownPanel.contains(e.target)) {
                    notifDropdownPanel.classList.add('hidden');
                }
            });
        }

        // CONTROL ENGINE FOR THEME SWITCHING SINKRONISASI LUAR BIASA
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');
        let currentTheme = localStorage.getItem('theme') || 'dark';

        function applyTheme(theme) {
            if (theme === 'light') {
                document.documentElement.classList.remove('dark');
                themeIcon.textContent = 'light_mode';
            } else {
                document.documentElement.classList.add('dark');
                themeIcon.textContent = 'dark_mode';
            }
            // Update Chart grid lines dynamically if loaded
            if (window.trendChartInstance && window.healthChartInstance) {
                const textCol = theme === 'light' ? '#475569' : '#87929a';
                window.trendChartInstance.options.scales.y.grid.color = theme === 'light' ? 'rgba(0,0,0,0.05)' :
                    'rgba(255,255,255,0.05)';
                window.trendChartInstance.options.scales.y.ticks.color = textCol;
                window.trendChartInstance.options.scales.x.ticks.color = textCol;
                window.healthChartInstance.options.plugins.legend.labels.color = textCol;
                window.trendChartInstance.update();
                window.healthChartInstance.update();
            }
        }

        applyTheme(currentTheme);

        themeToggleBtn.addEventListener('click', function() {
            currentTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            localStorage.setItem('theme', currentTheme);
            applyTheme(currentTheme);
        });

        function updateSVGProgressRing(circleId, count, total) {
            const circle = document.getElementById(circleId);
            if (!circle) return;
            const radius = 36;
            const circumference = 2 * Math.PI * radius;
            if (total === 0) {
                circle.style.strokeDashoffset = circumference;
                return;
            }
            circle.style.strokeDashoffset = circumference - ((count / total) * circumference);
        }

        function loadRealDashboardData() {
            fetch("{{ route('api.trafos') }}").then(r => r.json()).then(data => {
                const trafoTableBody = document.getElementById('trafoTableBody');
                trafoTableBody.innerHTML = '';
                const features = data.features ? data.features : data;
                let totalTrafo = features.length,
                    countNormal = 0,
                    countGangguan = 0;

                if (totalTrafo === 0) {
                    trafoTableBody.innerHTML =
                        `<tr><td colspan="4" class="p-4 text-center text-on-surface-variant">Tidak ada data trafo tersedia</td></tr>`;
                }

                features.forEach(item => {
                    let props = item.properties ? item.properties : item;
                    if (props.status === 'normal') countNormal++;
                    else countGangguan++;

                    let statusBadge = props.status === 'normal' ?
                        `<span class="inline-flex items-center gap-1 bg-emerald-500/10 text-emerald-500 px-2 py-0.5 rounded text-[11px] font-semibold border border-emerald-500/20">normal</span>` :
                        `<span class="inline-flex items-center gap-1 bg-rose-500/10 text-rose-500 px-2 py-0.5 rounded text-[11px] font-semibold border border-rose-500/20">gangguan</span>`;

                    trafoTableBody.innerHTML += `
                        <tr class="hover:bg-primary/5 transition-colors border-b border-outline-variant/30">
                            <td class="p-3 text-primary font-bold">${props.kode_trafo}</td>
                            <td class="p-3 text-on-surface-variant">${props.kapasitas || '250 kVA'}</td>
                            <td class="p-3">${props.wilayah_melayani || 'Wilayah DIY'}</td>
                            <td class="p-3">${statusBadge}</td>
                        </tr>
                    `;
                });

                document.getElementById('metric_total_trafo').textContent = totalTrafo;
                document.getElementById('metric_trafo_gangguan').textContent = countGangguan;
                document.getElementById('metric_sub_trafo_normal').innerHTML =
                    `<span class="w-2 h-2 rounded-full bg-emerald-500 inline-block align-middle me-1"></span> ${countNormal} Normal`;

                updateSVGProgressRing('ring_total_trafo', totalTrafo, totalTrafo);
                updateSVGProgressRing('ring_trafo_gangguan', countGangguan, totalTrafo);

                if (window.healthChartInstance) {
                    window.healthChartInstance.data.datasets[0].data = [countNormal, countGangguan];
                    window.healthChartInstance.update();
                }
            });

            fetch("{{ route('api.laporans') }}").then(r => r.json()).then(data => {
                const laporanTableBody = document.getElementById('laporanTableBody');
                laporanTableBody.innerHTML = '';
                const features = data.features ? data.features : data;
                let totalLaporan = features.length,
                    countPending = 0,
                    countProses = 0,
                    countSelesai = 0;

                if (totalLaporan === 0) {
                    laporanTableBody.innerHTML =
                        `<tr><td colspan="4" class="p-4 text-center text-on-surface-variant">Tidak ada laporan aduan masyarakat</td></tr>`;
                }

                features.forEach(item => {
                    let props = item.properties ? item.properties : item;
                    let stat = props.status.toLowerCase();
                    let badgeClass = "bg-amber-500/10 text-amber-500 border-amber-500/20";
                    if (stat === 'pending') countPending++;
                    if (stat === 'diproses') {
                        countProses++;
                        badgeClass = "bg-sky-500/10 text-primary border-primary/20";
                    }
                    if (stat === 'selesai') {
                        countSelesai++;
                        badgeClass = "bg-emerald-500/10 text-emerald-500 border-emerald-500/20";
                    }

                    laporanTableBody.innerHTML += `
                        <tr class="hover:bg-primary/5 transition-colors border-b border-outline-variant/30">
                            <td class="p-3 font-mono-data text-on-surface-variant">${props.nama_pelapor}</td>
                            <td class="p-3 font-semibold">${props.kategori_gangguan}</td>
                            <td class="p-3 text-on-surface-variant">${props.deskripsi || '-'}</td>
                            <td class="p-3"><span class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold border ${badgeClass}">${props.status}</span></td>
                        </tr>
                    `;
                });

                document.getElementById('metric_total_laporan').textContent = totalLaporan;
                document.getElementById('metric_laporan_selesai').textContent = countSelesai;
                document.getElementById('metric_sub_laporan_pending').innerHTML =
                    `<span class="material-symbols-outlined text-[14px]">schedule</span> ${countPending} Antrean`;
                document.getElementById('metric_sub_laporan_proses').innerHTML =
                    `<span class="material-symbols-outlined text-[14px] text-sky-400">sync</span> ${countProses} Perbaikan`;

                updateSVGProgressRing('ring_total_laporan', totalLaporan, totalLaporan);
                updateSVGProgressRing('ring_laporan_selesai', countSelesai, totalLaporan);
            });
        }

        // INITIALIZE CHART COMPONENT
        const currentTxtColor = localStorage.getItem('theme') === 'light' ? '#475569' : '#87929a';
        Chart.defaults.color = currentTxtColor;
        Chart.defaults.font.family = 'Inter, sans-serif';

        window.healthChartInstance = new Chart(document.getElementById('healthChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Normal', 'Gangguan'],
                datasets: [{
                    data: [0, 0],
                    backgroundColor: ['#38bdf8', '#ffb4ab'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            font: {
                                size: 11,
                                family: 'Geist'
                            },
                            color: currentTxtColor
                        }
                    }
                }
            }
        });

        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        const barGradient = ctxTrend.createLinearGradient(0, 0, 0, 300);
        barGradient.addColorStop(0, 'rgba(56, 189, 248, 0.8)');
        barGradient.addColorStop(1, 'rgba(56, 189, 248, 0.1)');

        window.trendChartInstance = new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Laporan Masuk',
                    data: [65, 59, 80, 45, 56, 95, 40],
                    backgroundColor: barGradient,
                    borderColor: '#38bdf8',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: localStorage.getItem('theme') === 'light' ? 'rgba(0,0,0,0.05)' :
                                'rgba(255,255,255,0.05)'
                        },
                        ticks: {
                            color: currentTxtColor
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: currentTxtColor
                        }
                    }
                }
            }
        });

        document.addEventListener('DOMContentLoaded', loadRealDashboardData);
    </script>
</body>

</html>
