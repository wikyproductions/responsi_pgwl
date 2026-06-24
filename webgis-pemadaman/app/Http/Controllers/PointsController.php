<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Point;
use Illuminate\Support\Facades\DB;

class PointsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'geom' => 'required|string',
        ]);

        $point = new Point();
        $point->name = $request->name;
        $point->description = $request->description;

        // Mengonversi teks WKT menjadi objek titik spasial dengan SRID 4326
        $point->geom = DB::raw("ST_GeomFromText('" . $request->geom . "', 4326)");
        $point->save();

        return redirect()->back()->with('success', 'Titik gardu listrik berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $point = \App\Models\Point::findOrFail($id);
        $point->name = $request->name;
        $point->description = $request->description;
        $point->save();

        return redirect()->back()->with('success', 'Data gardu berhasil diperbarui');
    }

    public function destroy($id)
    {
        $point = \App\Models\Point::findOrFail($id);
        $point->delete();

        return redirect()->back()->with('success', 'Data gardu berhasil dihapus');
    }
}
