<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function trafos()
    {
        $data = DB::table('trafos')->select('id', 'kode_trafo', 'kapasitas', 'wilayah_melayani', 'status', DB::raw('ST_AsGeoJSON(geom) as geojson'))->get();

        $features = [];
        foreach ($data as $row) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => json_decode($row->geojson),
                'properties' => [
                    'id' => $row->id,
                    'kode_trafo' => $row->kode_trafo,
                    'kapasitas' => $row->kapasitas,
                    'wilayah_melayani' => $row->wilayah_melayani,
                    'status' => $row->status,
                ]
            ];
        }
        return response()->json(['type' => 'FeatureCollection', 'features' => $features]);
    }

    public function kecamatans()
    {
        // Pastikan kolom 'namobj' disesuaikan dengan nama kolom kecamatan asli Anda di DBeaver
        $data = DB::table('kecamatans')->select('id', 'namobj', 'status', 'description', DB::raw('ST_AsGeoJSON(geom) as geojson'))->get();

        $features = [];
        foreach ($data as $row) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => json_decode($row->geojson),
                'properties' => [
                    'id' => $row->id,
                    'name' => $row->namobj,
                    'status' => $row->status ?? 'normal',
                    'description' => $row->description ?? 'Tidak ada jadwal pemadaman',
                ]
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    public function laporans()
    {
        $data = DB::table('laporans')->select('id', 'nama_pelapor', 'kategori_gangguan', 'deskripsi', 'status', DB::raw('ST_AsGeoJSON(geom) as geojson'))->get();

        $features = [];
        foreach ($data as $row) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => json_decode($row->geojson),
                'properties' => [
                    'id' => $row->id,
                    'nama_pelapor' => $row->nama_pelapor,
                    'kategori_gangguan' => $row->kategori_gangguan,
                    'deskripsi' => $row->deskripsi,
                    'status' => $row->status,
                ]
            ];
        }

        return response()->json(['type' => 'FeatureCollection', 'features' => $features]);
    }
}
