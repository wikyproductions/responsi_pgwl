@extends('template')

@section('styles')
    <style>
        .map-container {
            height: 100%;
            width: 100%;
            position: relative;
        }

        .sidebar-content {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .saas-card {
            background: var(--bg-surface-high);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 12px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .saas-card:hover {
            background: var(--bg-surface-highest);
            border-color: var(--color-primary);
            transform: translateY(-2px);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .badge-status {
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-family: 'Geist', monospace;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .badge-mati {
            background: var(--color-danger-dim);
            color: var(--color-danger);
            border: 1px solid rgba(255, 180, 171, 0.3);
        }

        .badge-rencana {
            background: var(--color-warning-dim);
            color: var(--color-warning);
            border: 1px solid rgba(255, 193, 118, 0.3);
        }

        .dot-pulse {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 1.5s infinite;
        }

        .bg-pulse-danger {
            background-color: var(--color-danger);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 180, 171, 0.7);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(255, 180, 171, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 180, 171, 0);
            }
        }

        .c-feeder {
            font-size: 11px;
            color: var(--text-muted);
            font-family: 'Geist', monospace;
        }

        .c-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-main);
            margin: 0 0 6px 0;
        }

        .c-time {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            font-size: 12px;
            margin-bottom: 8px;
            font-family: 'Geist', monospace;
        }

        .c-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin: 0;
            padding-top: 8px;
            border-top: 1px solid var(--border-light);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #map {
            width: 100%;
            height: 100%;
            z-index: 1;
            filter: none !important;
        }

        .leaflet-tile {
            filter: var(--map-tile-filter);
            transition: filter 0.3s;
        }

        /* PERBAIKAN RE-ALOKASI POSISI TOMBOL ZOOM */
        .leaflet-top.leaflet-right {
            margin-top: 72px !important;
        }

        /* WIDGET KOTAK PENCARIAN KELURAHAN */
        .search-box-wrapper {
            position: absolute;
            top: 16px;
            left: 16px;
            z-index: 900;
            width: 300px;
        }

        .widget-search {
            background: var(--bg-surface);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .widget-search input {
            background: transparent;
            border: none;
            color: var(--text-main);
            font-size: 13px;
            width: 100%;
            outline: none;
            margin-left: 8px;
        }

        .search-suggestions-box {
            background: var(--bg-surface);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-top: 4px;
            max-height: 220px;
            overflow-y: auto;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .suggest-item {
            padding: 8px 12px;
            font-size: 13px;
            color: var(--text-main);
            cursor: pointer;
            transition: background 0.15s;
        }

        .suggest-item:hover {
            background: var(--bg-surface-highest);
            color: var(--color-primary);
        }

        /* PERBAIKAN WIDGET FAB GANGGUAN LISTRIK (MENGHILANGKAN TEXT-UPPERCASE BUG) */
        .widget-fab {
            position: absolute;
            bottom: 24px;
            left: 16px;
            z-index: 900;
            background: var(--color-primary);
            color: #00354a;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(56, 189, 248, 0.3);
            transition: all 0.2s;
            cursor: pointer;
            text-transform: none !important;
            /* Mencegah pemaksaan kapitalisasi teks ikon */
        }

        .widget-fab:hover {
            transform: scale(1.05);
        }

        /* WIDGET KONTROL LAPISAN */
        .widget-layer-control {
            position: absolute;
            top: 16px;
            right: 16px;
            z-index: 900;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }

        .btn-layer-toggle {
            background: var(--bg-surface);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-layer-toggle:hover {
            background: var(--bg-surface-high);
            color: var(--color-primary);
        }

        .layer-panel-box {
            background: var(--bg-surface);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 14px;
            width: 210px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        /* WIDGET LEGENDA */
        .widget-legend {
            position: absolute;
            bottom: 24px;
            right: 16px;
            z-index: 900;
            background: var(--bg-surface);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 240px;
            max-height: 380px;
            overflow-y: auto;
        }

        .wl-title {
            font-size: 11px;
            font-family: 'Geist', monospace;
            font-weight: 700;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-light);
            padding-bottom: 6px;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .wl-section-title {
            font-size: 10px;
            font-weight: 700;
            color: var(--color-primary);
            margin-top: 10px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .wl-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .wl-color {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .wl-icon-wrap {
            width: 16px;
            display: flex;
            justify-content: center;
            flex-shrink: 0;
        }

        .leaflet-popup-content-wrapper,
        .leaflet-popup-tip {
            background: var(--bg-surface-high) !important;
            color: var(--text-main) !important;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4) !important;
            padding: 0 !important;
            overflow: hidden;
        }

        .leaflet-popup-content {
            margin: 0 !important;
            width: 260px !important;
        }

        .leaflet-container a.leaflet-popup-close-button {
            color: var(--text-muted);
            top: 8px;
            right: 8px;
        }

        .p-accent-danger {
            height: 4px;
            width: 100%;
            background: var(--color-danger);
        }

        .p-accent-warning {
            height: 4px;
            width: 100%;
            background: var(--color-warning);
        }

        .p-accent-success {
            height: 4px;
            width: 100%;
            background: var(--color-success);
        }

        .p-body {
            padding: 16px;
        }

        .p-label {
            font-size: 10px;
            font-family: 'Geist', monospace;
            color: var(--text-muted);
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: block;
        }

        .p-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-main);
            margin: 0 0 12px 0;
        }

        .p-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            background: var(--bg-surface-highest);
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .pg-col {
            display: flex;
            flex-direction: column;
        }

        .pg-lbl {
            font-size: 10px;
            color: var(--text-muted);
        }

        .pg-val {
            font-size: 12px;
            font-family: 'Geist', monospace;
            font-weight: 600;
            color: var(--text-main);
        }

        .p-desc {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.4;
            margin-bottom: 12px;
            border-top: 1px solid var(--border-light);
            padding-top: 8px;
        }

        .p-btn {
            width: 100%;
            background: transparent;
            border: 1px solid var(--color-primary);
            color: var(--color-primary);
            padding: 6px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .p-btn:hover {
            background: var(--color-primary-dim);
        }

        .custom-trafo-icon {
            background: none !important;
            border: none !important;
        }
    </style>
@endsection

@section('sidebar-stats')
    <div class="d-flex justify-content-between align-items-center mb-3 px-1">
        <h3 class="m-0" style="font-size: 14px; font-weight: 600;">Statistik Wilayah DIY</h3>
        <span class="material-symbols-outlined text-muted" style="font-size: 18px; cursor: pointer;">filter_list</span>
    </div>

    <div class="sidebar-content" id="scheduleList">
        <div class="text-center py-4" id="emptyScheduleText" style="color: var(--text-muted);">
            <span class="material-symbols-outlined d-block mb-2"
                style="font-size: 32px; color: var(--border-color);">cloud_sun</span>
            <small>Aman, tidak ada wilayah padam</small>
        </div>
    </div>
@endsection

@section('content')
    <div class="map-container">
        <div id="map"></div>

        <div class="search-box-wrapper">
            <div class="widget-search">
                <span class="material-symbols-outlined text-muted" style="font-size: 18px;">search</span>
                <input type="text" id="kecamatanSearchInput" placeholder="Cari nama kelurahan...">
            </div>
            <div class="search-suggestions-box d-none" id="searchSuggestionsContainer"></div>
        </div>

        <div class="widget-layer-control">
            <button class="btn-layer-toggle" id="btnToggleLayers" title="Pengaturan Lapisan Peta">
                <span class="material-symbols-outlined">layers</span>
            </button>
            <div class="layer-panel-box d-none" id="layerControlPanel">
                <h6 class="text-white text-[11px] font-bold uppercase tracking-wider mb-3 font-mono">Visibilitas Layer</h6>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="chkLayerKelurahan" checked>
                    <label class="form-check-label text-muted text-[12px] cursor-pointer" for="chkLayerKelurahan">Batas
                        Kelurahan</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="chkLayerTrafo" checked>
                    <label class="form-check-label text-muted text-[12px] cursor-pointer" for="chkLayerTrafo">Infrastruktur
                        Trafo</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="chkLayerLaporan" checked>
                    <label class="form-check-label text-muted text-[12px] cursor-pointer" for="chkLayerLaporan">Aduan
                        Masyarakat</label>
                </div>
            </div>
        </div>

        <div class="widget-legend">
            <div class="wl-title">LEGENDA KANVAS SPASIAL</div>

            <div class="wl-section-title">Status Kelurahan</div>
            <div class="wl-item">
                <span class="wl-color" style="background: #81c995"></span>
                <span>Normal, Penormalan</span>
            </div>
            <div class="wl-item">
                <span class="wl-color" style="background: #ffc176"></span>
                <span>Padam, Terjadwal</span>
            </div>
            <div class="wl-item">
                <span class="wl-color" style="background: #ffb4ab"></span>
                <span>Padam, Berlangsung</span>
            </div>

            <div class="wl-section-title">Infrastruktur PLN</div>
            <div class="wl-item">
                <div class="wl-icon-wrap"><i class="fa-brands fa-simplybuilt" style="color: #81c995; font-size: 13px;"></i>
                </div>
                <span>Trafo Beroperasi Normal</span>
            </div>
            <div class="wl-item">
                <div class="wl-icon-wrap"><i class="fa-brands fa-simplybuilt" style="color: #ffb4ab; font-size: 13px;"></i>
                </div>
                <span>Trafo Mengalami Gangguan</span>
            </div>

            <div class="wl-section-title">Aduan Gangguan</div>
            <div class="wl-item">
                <div class="wl-icon-wrap"><i class="fa-solid fa-circle-exclamation"
                        style="color: #ffc176; font-size: 13px;"></i></div>
                <span>Aduan Masuk (Pending)</span>
            </div>
            <div class="wl-item">
                <div class="wl-icon-wrap"><i class="fa-solid fa-circle-exclamation"
                        style="color: #38bdf8; font-size: 13px;"></i></div>
                <span>Aduan Penanganan (Diproses)</span>
            </div>
            <div class="wl-item">
                <div class="wl-icon-wrap"><i class="fa-solid fa-circle-exclamation"
                        style="color: #81c995; font-size: 13px;"></i></div>
                <span>Aduan Selesai Ditangani</span>
            </div>
        </div>

        @guest
            <button class="widget-fab" id="btnTriggerLapor"
                onclick="alert('Silakan login atau gunakan fitur Lapor Publik (jika tersedia) untuk melaporkan gangguan.')">
                <span class="material-symbols-outlined">campaign</span> Lapor Gangguan
            </button>
        @else
            @if (auth()->user()->role === 'user')
                <button class="widget-fab" id="btnTriggerLapor">
                    <span class="material-symbols-outlined">campaign</span> Lapor Gangguan (Klik Peta)
                </button>
            @endif
        @endguest
    </div>

    <div class="modal fade" id="laporanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: var(--color-danger);"><span
                            class="material-symbols-outlined me-2">campaign</span> Lapor Gangguan Listrik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                </div>
                <form action="{{ route('laporan.store') }}" method="POST" id="laporanForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="geom" id="laporan_geom_wkt">
                        <div class="mb-3">
                            <label class="form-label text-muted d-block mb-1" style="font-size: 12px;">Nama Pelapor
                                (nama_pelapor)</label>
                            <input type="text" class="form-control" name="nama_pelapor"
                                value="{{ auth()->user()->name ?? '' }}" required readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted d-block mb-1" style="font-size: 12px;">Kategori Gangguan
                                (kategori_gangguan)</label>
                            <select class="form-select" name="kategori_gangguan" required>
                                <option value="Kabel Putus">Kabel Putus / Terkelupas</option>
                                <option value="Pohon Tumbang">Pohon Tumbang Menimpa Jaringan</option>
                                <option value="Tiang Miring">Tiang Listrik Miring / Roboh</option>
                                <option value="Padam Tanpa Jadwal">Mati Lampu Sepihak (Tanpa Jadwal)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted d-block mb-1" style="font-size: 12px;">Deskripsi Kejadian
                                Kronologi (deskripsi)</label>
                            <textarea class="form-control" name="deskripsi" rows="3" required
                                placeholder="Ceritakan detail lokasi atau kondisi di lapangan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="submit" class="btn w-100"
                            style="background: var(--color-danger); color: white; font-weight: 500;">Kirim Laporan
                            Spasial</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editKecamatanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-info">Update Status Wilayah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        style="filter: invert(1);"></button>
                </div>
                <form action="" method="POST" id="editKecamatanForm">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted d-block mb-1" style="font-size: 12px">Nama Kelurahan /
                                Desa (namobj)</label>
                            <input type="text" class="form-control" id="display_kecamatan_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted d-block mb-1" style="font-size: 12px">Status Pengaliran
                                Listrik (status)</label>
                            <select class="form-select" name="status" id="edit_kecamatan_status">
                                <option value="normal">Normal</option>
                                <option value="terjadwal">Terjadwal</option>
                                <option value="berlangsung">Berlangsung</option>
                            </select>
                        </div>

                        <input type="hidden" name="description" id="edit_kecamatan_description">

                        <div id="wrapper_otomatisasi_deskripsi" class="d-none">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1" style="font-size: 12px">Jam Mulai</label>
                                    <input type="time" class="form-control" id="auto_jam_mulai" value="09:00">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1" style="font-size: 12px">Jam Selesai</label>
                                    <input type="time" class="form-control" id="auto_jam_selesai" value="12:00">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted mb-1" style="font-size: 12px">Alasan
                                    Operasional</label>
                                <select class="form-select" id="auto_alasan_pemadaman">
                                    <option value="Pemeliharaan Jaringan JTM">Pemeliharaan Jaringan JTM</option>
                                    <option value="Pemangkasan Pohon Ruang Bebas">Pemangkasan Pohon Ruang Bebas</option>
                                    <option value="Perbaikan Gardu Distribusi">Perbaikan Gardu Distribusi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary w-100">Simpan Status Wilayah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editTrafoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-info">Update Trafo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        style="filter: invert(1);"></button>
                </div>
                <form action="" method="POST" id="editTrafoForm">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted d-block mb-1" style="font-size: 12px">Kode Infrastruktur
                                Trafo (kode_trafo)</label>
                            <input type="text" class="form-control" id="edit_display_trafo_kode" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted d-block mb-1" style="font-size: 12px">Kondisi Operasional
                                Alat (status)</label>
                            <select class="form-select" name="status" id="edit_trafo_status">
                                <option value="normal">Normal</option>
                                <option value="gangguan">Gangguan</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editLaporanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Konfirmasi Aduan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        style="filter: invert(1);"></button>
                </div>
                <form action="" method="POST" id="editLaporanForm">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted d-block mb-1" style="font-size: 12px">Nama Warga Pelapor
                                (nama_pelapor)</label>
                            <input type="text" class="form-control" id="edit_display_laporan_pelapor" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted d-block mb-1" style="font-size: 12px">Status Tindak Lanjut
                                Keluhan (status)</label>
                            <select class="form-select" name="status" id="edit_laporan_status">
                                <option value="pending">Pending</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn w-100"
                            style="background: var(--color-danger); color: white;">Update Progres Aduan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const map = L.map('map', {
            zoomControl: false
        }).setView([-7.7956, 110.3695], 10);

        L.control.zoom({
            position: 'topright'
        }).addTo(map);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        const kecamatanGroup = L.layerGroup().addTo(map);
        const trafoGroup = L.layerGroup().addTo(map);
        const laporanGroup = L.layerGroup().addTo(map);
        const kecamatanLayersCache = {};
        const globalKecamatanList = [];
        const isAdmin = @json(auth()->check() && auth()->user()->role === 'admin');

        const btnToggleLayers = document.getElementById('btnToggleLayers');
        const layerControlPanel = document.getElementById('layerControlPanel');
        if (btnToggleLayers && layerControlPanel) {
            btnToggleLayers.addEventListener('click', function() {
                layerControlPanel.classList.toggle('d-none');
            });
        }

        document.getElementById('chkLayerKelurahan').addEventListener('change', function(e) {
            if (e.target.checked) map.addLayer(kecamatanGroup);
            else map.removeLayer(kecamatanGroup);
        });

        document.getElementById('chkLayerTrafo').addEventListener('change', function(e) {
            if (e.target.checked) map.addLayer(trafoGroup);
            else map.removeLayer(trafoGroup);
        });

        document.getElementById('chkLayerLaporan').addEventListener('change', function(e) {
            if (e.target.checked) map.addLayer(laporanGroup);
            else map.removeLayer(laporanGroup);
        });

        let reportingMode = false;
        const btnTrigger = document.getElementById('btnTriggerLapor');
        if (btnTrigger) {
            btnTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                reportingMode = !reportingMode;
                if (reportingMode) {
                    // SINKRONISASI PENANDA JAVASCRIPT: MENGGUNAKAN IKON FONTAWESOME SEHINGGA BEBAS BUG SALAH CETAK TEKS
                    btnTrigger.innerHTML = '<i class="fa-solid fa-crosshairs me-1"></i> Klik Titik di Peta';
                    btnTrigger.style.background = 'var(--color-warning)';
                    map.getContainer().style.cursor = 'crosshair';
                } else {
                    btnTrigger.innerHTML = '<span class="material-symbols-outlined">campaign</span> Lapor Gangguan';
                    btnTrigger.style.background = 'var(--color-primary)';
                    map.getContainer().style.cursor = '';
                }
            });
        }

        map.on('click', function(e) {
            if (reportingMode) {
                document.getElementById('laporan_geom_wkt').value = `POINT(${e.latlng.lng} ${e.latlng.lat})`;
                new bootstrap.Modal(document.getElementById('laporanModal')).show();
                if (btnTrigger) {
                    reportingMode = false;
                    btnTrigger.innerHTML = '<span class="material-symbols-outlined">campaign</span> Lapor Gangguan';
                    btnTrigger.style.background = 'var(--color-primary)';
                    map.getContainer().style.cursor = '';
                }
            }
        });

        function getKecamatanColor(status) {
            return status === 'berlangsung' ? "#ffb4ab" : (status === 'terjadwal' ? "#ffc176" : "#81c995");
        }

        fetch("{{ route('api.kecamatans') }}").then(r => r.json()).then(data => {
            const scheduleList = document.getElementById('scheduleList');
            let hasSchedules = false;

            let kecamatanLayer = L.geoJSON(data, {
                style: feature => ({
                    color: "#555555",
                    weight: 1,
                    fillColor: getKecamatanColor(feature.properties.status),
                    fillOpacity: 0.2
                }),
                onEachFeature: function(feature, layer) {
                    let props = feature.properties;
                    kecamatanLayersCache[props.id] = layer;

                    globalKecamatanList.push({
                        id: props.id,
                        name: props.name
                    });

                    let isMati = props.status === 'berlangsung';

                    if (props.status === 'berlangsung' || props.status === 'terjadwal') {
                        if (!hasSchedules) {
                            document.getElementById('emptyScheduleText').remove();
                            hasSchedules = true;
                        }

                        let badgeClass = isMati ? 'badge-mati' : 'badge-rencana';
                        let pulseHtml = isMati ? '<span class="dot-pulse bg-pulse-danger"></span>' : '';
                        let statusText = isMati ? 'Mati Lampu' : 'Rencana';
                        let rDesc = props.description ? props.description.split('|') : [];
                        let timeString = rDesc.length > 1 ? rDesc[1].trim() : "Waktu Kustom";
                        let descString = rDesc.length > 3 ? rDesc[3].trim() : (props.description ||
                            "Pemeliharaan");

                        let cardHTML = `
                            <div class="saas-card" onclick="focusToKecamatan(${props.id})">
                                <div class="card-top">
                                    <span class="badge-status ${badgeClass}">${pulseHtml} ${statusText}</span>
                                    <span class="c-feeder">FDR-YK-0${props.id}</span>
                                </div>
                                <h4 class="c-title">Kel. ${props.name}</h4>
                                <div class="c-time"><span class="material-symbols-outlined" style="font-size:14px;">schedule</span> ${timeString}</div>
                                <p class="c-desc">${descString}</p>
                            </div>
                        `;
                        scheduleList.innerHTML += cardHTML;
                    }

                    let accentClass = isMati ? 'p-accent-danger' : (props.status === 'terjadwal' ?
                        'p-accent-warning' : 'p-accent-success');
                    let valColor = isMati ? 'var(--color-danger)' : (props.status === 'terjadwal' ?
                        'var(--color-warning)' : 'var(--color-success)');

                    let popupContent = `
                        <div class="${accentClass}"></div>
                        <div class="p-body">
                            <span class="p-label">INFO WILAYAH</span>
                            <h5 class="p-title">Kelurahan ${props.name}</h5>
                            <div class="p-grid">
                                <div class="pg-col"><span class="pg-lbl">Status</span><span class="pg-val" style="color:${valColor}">${props.status.toUpperCase()}</span></div>
                            </div>
                            <p class="p-desc">${props.description || 'Kondisi Kelistrikan Normal'}</p>
                            ${isAdmin ? `<button class="p-btn" onclick="triggerEditKecamatan(${props.id}, '${props.name}', '${props.status}', '${(props.description || '').replace(/'/g, "\\'")}')">Ubah Status Wilayah</button>` : ''}
                        </div>
                    `;
                    layer.bindPopup(popupContent);
                    layer.on('mouseover', e => {
                        e.target.setStyle({
                            color: "var(--color-primary)",
                            weight: 2,
                            fillOpacity: 0.4
                        });
                        e.target.bringToFront();
                    });
                    layer.on('mouseout', e => kecamatanLayer.resetStyle(e.target));
                }
            }).addTo(kecamatanGroup);
        });

        const kecamatanSearchInput = document.getElementById('kecamatanSearchInput');
        const searchSuggestionsContainer = document.getElementById('searchSuggestionsContainer');

        if (kecamatanSearchInput && searchSuggestionsContainer) {
            kecamatanSearchInput.addEventListener('input', function() {
                const textQuery = this.value.toLowerCase().trim();
                searchSuggestionsContainer.innerHTML = '';

                if (textQuery.length < 2) {
                    searchSuggestionsContainer.classList.add('d-none');
                    return;
                }

                const matchesResult = globalKecamatanList.filter(item => item.name.toLowerCase().includes(
                    textQuery));

                if (matchesResult.length === 0) {
                    searchSuggestionsContainer.innerHTML =
                        `<div class="text-muted p-2 text-center" style="font-size: 12px">Wilayah tidak ditemukan</div>`;
                    searchSuggestionsContainer.classList.remove('d-none');
                    return;
                }

                matchesResult.forEach(item => {
                    const rowSuggest = document.createElement('div');
                    rowSuggest.className = 'suggest-item';
                    rowSuggest.textContent = `Kelurahan ${item.name}`;
                    rowSuggest.addEventListener('click', function() {
                        focusToKecamatan(item.id);
                        kecamatanSearchInput.value = item.name;
                        searchSuggestionsContainer.classList.add('d-none');
                    });
                    searchSuggestionsContainer.appendChild(rowSuggest);
                });
                searchSuggestionsContainer.classList.remove('d-none');
            });

            document.addEventListener('click', function(event) {
                if (!event.target.closest('#kecamatanSearchInput') && !event.target.closest(
                        '#searchSuggestionsContainer')) {
                    searchSuggestionsContainer.classList.add('d-none');
                }
            });
        }

        function focusToKecamatan(id) {
            const layer = kecamatanLayersCache[id];
            if (layer) {
                map.fitBounds(layer.getBounds(), {
                    padding: [50, 50],
                    maxZoom: 13
                });
                layer.openPopup();
            }
        }

        fetch("{{ route('api.trafos') }}").then(r => r.json()).then(data => {
            L.geoJSON(data, {
                pointToLayer: (f, ll) => L.marker(ll, {
                    icon: L.divIcon({
                        html: `<i class="fa-brands fa-simplybuilt" style="font-size:24px; color:${f.properties.status==='normal'?'#81c995':'#ffb4ab'}; filter: drop-shadow(0 0 4px black);"></i>`,
                        className: 'custom-trafo-icon',
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    })
                }),
                onEachFeature: function(feature, layer) {
                    let props = feature.properties;
                    let isNormal = props.status === 'normal';
                    let popupContent = `
                        <div class="${isNormal ? 'p-accent-success' : 'p-accent-danger'}"></div>
                        <div class="p-body">
                            <span class="p-label">ASET TRAFO PLN</span>
                            <h5 class="p-title">${props.kode_trafo}</h5>
                            <div class="p-grid">
                                <div class="pg-col"><span class="pg-lbl">Daya</span><span class="pg-val">${props.kapasitas}</span></div>
                                <div class="pg-col"><span class="pg-lbl">Kondisi</span><span class="pg-val" style="color:${isNormal?'var(--color-success)':'var(--color-danger)'}">${props.status.toUpperCase()}</span></div>
                            </div>
                            <p class="p-desc">Melayani: ${props.wilayah_melayani}</p>
                            ${isAdmin ? `<button class="p-btn" onclick="triggerEditTrafo(${props.id}, '${props.kode_trafo}', '${props.status}')">Perbarui Alat</button>` : ''}
                        </div>
                    `;
                    layer.bindPopup(popupContent);
                }
            }).addTo(trafoGroup);
        });

        fetch("{{ route('api.laporans') }}").then(r => r.json()).then(data => {
            L.geoJSON(data, {
                pointToLayer: (f, ll) => L.marker(ll, {
                    icon: L.divIcon({
                        html: `<i class="fa-solid fa-circle-exclamation" style="font-size:24px; color:${f.properties.status==='pending'?'#ffc176':(f.properties.status==='selesai'?'#81c995':'#38bdf8')}; filter: drop-shadow(0 0 4px black);"></i>`,
                        className: 'custom-trafo-icon',
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    })
                }),
                onEachFeature: function(feature, layer) {
                    let props = feature.properties;
                    let stat = props.status.toLowerCase();
                    let accentPopup = stat === 'pending' ? 'p-accent-warning' : (stat === 'selesai' ?
                        'p-accent-success' : 'p-accent-danger');
                    let valColor = stat === 'pending' ? 'var(--color-warning)' : (stat === 'selesai' ?
                        'var(--color-success)' : 'var(--color-primary)');

                    let popupContent = `
                        <div class="${accentPopup}"></div>
                        <div class="p-body">
                            <span class="p-label">ADUAN MASYARAKAT</span>
                            <h5 class="p-title">${props.kategori_gangguan}</h5>
                            <div class="p-grid">
                                <div class="pg-col"><span class="pg-lbl">Pelapor</span><span class="pg-val">${props.nama_pelapor}</span></div>
                                <div class="pg-col"><span class="pg-lbl">Status</span><span class="pg-val" style="color:${valColor}">${props.status.toUpperCase()}</span></div>
                            </div>
                            <p class="p-desc">${props.deskripsi}</p>
                            ${isAdmin ? `<button class="p-btn" onclick="triggerEditLaporan(${props.id}, '${props.nama_pelapor}', '${props.status}')">Konfirmasi Aksi</button>` : ''}
                        </div>
                    `;
                    layer.bindPopup(popupContent);
                }
            }).addTo(laporanGroup);
        });

        const statusSelect = document.getElementById('edit_kecamatan_status');
        const autoFieldsWrapper = document.getElementById('wrapper_otomatisasi_deskripsi');
        const hiddenDescriptionInput = document.getElementById('edit_kecamatan_description');

        function generateAutomatedDescription() {
            const currentStatus = statusSelect.value;
            if (currentStatus === 'normal') {
                autoFieldsWrapper.classList.add('d-none');
                hiddenDescriptionInput.value = "Normal | - | - | Jaringan Kelistrikan Optimal Wilayah Terkait";
            } else {
                autoFieldsWrapper.classList.remove('d-none');
                const tStart = document.getElementById('auto_jam_mulai').value;
                const tEnd = document.getElementById('auto_jam_selesai').value;
                const reason = document.getElementById('auto_alasan_pemadaman').value;
                const statusLabel = currentStatus === 'berlangsung' ? 'Berlangsung' : 'Terjadwal';

                hiddenDescriptionInput.value = `${statusLabel} | ${tStart} - ${tEnd} | - | ${reason}`;
            }
        }

        statusSelect.addEventListener('change', generateAutomatedDescription);
        document.getElementById('auto_jam_mulai').addEventListener('input', generateAutomatedDescription);
        document.getElementById('auto_jam_selesai').addEventListener('input', generateAutomatedDescription);
        document.getElementById('auto_alasan_pemadaman').addEventListener('change', generateAutomatedDescription);

        function triggerEditKecamatan(id, name, status, desc) {
            document.getElementById('display_kecamatan_name').value = name;
            statusSelect.value = status;

            if (status !== 'normal' && desc.includes('|')) {
                let parts = desc.split('|');
                if (parts.length > 1) {
                    let times = parts[1].split('-');
                    if (times.length > 1) {
                        document.getElementById('auto_jam_mulai').value = times[0].trim();
                        document.getElementById('auto_jam_selesai').value = times[1].trim();
                    }
                }
                if (parts.length > 3) {
                    document.getElementById('auto_alasan_pemadaman').value = parts[3].trim();
                }
            }

            document.getElementById('editKecamatanForm').action = `/kecamatan/update/${id}`;
            generateAutomatedDescription();
            new bootstrap.Modal(document.getElementById('editKecamatanModal')).show();
        }

        function triggerEditTrafo(id, kode, status) {
            document.getElementById('edit_display_trafo_kode').value = kode;
            document.getElementById('edit_trafo_status').value = status;
            document.getElementById('editTrafoForm').action = `/trafo/update/${id}`;
            new bootstrap.Modal(document.getElementById('editTrafoModal')).show();
        }

        function triggerEditLaporan(id, pelapor, status) {
            document.getElementById('edit_display_laporan_pelapor').value = pelapor;
            document.getElementById('edit_laporan_status').value = status;
            document.getElementById('editLaporanForm').action = `/laporan/update/${id}`;
            new bootstrap.Modal(document.getElementById('editLaporanModal')).show();
        }
    </script>
@endsection
