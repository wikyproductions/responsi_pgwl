<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laporan;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelapor' => 'required|string',
            'kategori_gangguan' => 'required|string',
            'deskripsi' => 'required|string',
            'geom' => 'required|string',
        ]);

        $laporan = new Laporan();
        $laporan->nama_pelapor = $request->nama_pelapor;
        $laporan->kategori_gangguan = $request->kategori_gangguan;
        $laporan->deskripsi = $request->deskripsi;
        $laporan->geom = DB::raw("ST_GeomFromText('" . $request->geom . "', 4326)");
        $laporan->save();

        return redirect()->back()->with('success', 'Laporan gangguan Anda berhasil dikirim ke sistem');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,diproses,selesai',
        ]);

        DB::table('laporans')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Status perkembangan aduan warga berhasil diperbarui');
    }
}
