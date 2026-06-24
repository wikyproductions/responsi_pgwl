<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function index()
    {
        // Agregasi Ringkasan Data Infrastruktur Trafo
        $totalTrafo = DB::table('trafos')->count();
        $trafoNormal = DB::table('trafos')->where('status', 'normal')->count();
        $trafoGangguan = DB::table('trafos')->where('status', 'gangguan')->count();

        // Agregasi Ringkasan Data Laporan Gangguan Warga
        $totalLaporan = DB::table('laporans')->count();
        $laporanPending = DB::table('laporans')->where('status', 'pending')->count();
        $laporanDiproses = DB::table('laporans')->where('status', 'diproses')->count();
        $laporanSelesai = DB::table('laporans')->where('status', 'selesai')->count();

        // Mengambil data statistik pengelompokan jenis gangguan untuk grafik
        $grafikGangguan = DB::table('laporans')
            ->select('kategori_gangguan', DB::raw('count(*) as total'))
            ->groupBy('kategori_gangguan')
            ->get();

        // Mengambil seluruh daftar data untuk ditampilkan ke dalam tabel dashboard
        $daftarTrafo = DB::table('trafos')->orderBy('id', 'desc')->get();
        $daftarLaporan = DB::table('laporans')->orderBy('id', 'desc')->get();

        return view('statistik', compact(
            'totalTrafo', 'trafoNormal', 'trafoGangguan',
            'totalLaporan', 'laporanPending', 'laporanDiproses', 'laporanSelesai',
            'grafikGangguan', 'daftarTrafo', 'daftarLaporan'
        ));
    }
}
