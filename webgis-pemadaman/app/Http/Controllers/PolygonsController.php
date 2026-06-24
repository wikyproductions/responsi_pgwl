<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PolygonsController extends Controller
{
    public function store(Request $request)
    {
        // Validasi kelayakan data inputan
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'geom' => 'required|string',
        ]);

        // Menyimpan data ke tabel polygons database PostgreSQL tanpa bergantung pada model
        DB::table('polygons')->insert([
            'name' => $request->name,
            'description' => $request->description,
            // Mengonversi teks WKT menjadi objek spasial PostGIS dengan SRID 4326
            'geom' => DB::raw("ST_GeomFromText('" . $request->geom . "', 4326)"),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Kembali ke halaman peta dengan membawa status sukses
        return redirect()->back()->with('success', 'Zona pemadaman listrik berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $polygon = \App\Models\Polygon::findOrFail($id);
        $polygon->name = $request->name;
        $polygon->description = $request->description;
        $polygon->save();

        return redirect()->back()->with('success', 'Zona pemadaman berhasil diperbarui');
    }

    public function destroy($id)
    {
        $polygon = \App\Models\Polygon::findOrFail($id);
        $polygon->delete();

        return redirect()->back()->with('success', 'Zona pemadaman berhasil dihapus');
    }
}
