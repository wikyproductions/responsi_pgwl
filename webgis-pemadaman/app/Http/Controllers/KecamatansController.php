<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KecamatansController extends Controller
{
    /**
     * Memproses pembaruan status dan deskripsi pemadaman kelistrikan kecamatan
     */
    public function update(Request $request, $id)
    {
        // Validasi data kiriman dari formulir modal map.blade
        $request->validate([
            'status' => 'required|string|in:normal,terjadwal,berlangsung',
            'description' => 'required|string',
        ]);

        // Memperbarui status dan deskripsi kecamatan di database
        DB::table('kecamatans')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'description' => $request->description,
            ]);

        // Mengembalikan halaman ke tampilan peta dengan membawa pesan sukses
        return redirect()->back()->with('success', 'Status kelistrikan wilayah berhasil diperbarui');
    }
}
