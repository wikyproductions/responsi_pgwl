<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trafos', function (Blueprint $table) {
            $table->id();
            $table->string('kode_trafo')->unique();
            $table->string('kapasitas'); // Contoh: 200 kVA, 500 kVA
            $table->string('wilayah_melayani');
            $table->string('status')->default('normal'); // normal atau gangguan
            $table->geometry('geom', subtype: 'point', srid: 4326);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trafos');
    }
};
