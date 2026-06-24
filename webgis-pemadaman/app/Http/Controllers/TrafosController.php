<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trafo;
use Illuminate\Support\Facades\DB;

class TrafosController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'kode_trafo' => 'required|string|unique:trafos,kode_trafo',
            'kapasitas' => 'required|string',
            'wilayah_melayani' => 'required|string',
            'status' => 'required|string',
            'geom' => 'required|string',
        ]);

        $trafo = new Trafo();
        $trafo->kode_trafo = $request->kode_trafo;
        $trafo->kapasitas = $request->kapasitas;
        $trafo->wilayah_melayani = $request->wilayah_melayani;
        $trafo->status = $request->status;
        $trafo->geom = DB::raw("ST_GeomFromText('" . $request->geom . "', 4326)");
        $trafo->save();

        return redirect()->back()->with('success', 'Titik trafo infrastruktur berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:normal,gangguan',
        ]);

        DB::table('trafos')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Status kondisi operasional trafo berhasil diperbarui');
    }
}
