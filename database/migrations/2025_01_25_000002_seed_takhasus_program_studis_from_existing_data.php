<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get unique takhasus values from mahasiswas table grouped by program studi
        $takhasusData = DB::table('mahasiswas')
            ->select('prodi_id', 'takhasus')
            ->whereNotNull('takhasus')
            ->where('takhasus', '!=', '')
            ->whereNotNull('prodi_id')
            ->distinct()
            ->get();

        // Group by program studi and create takhasus options
        $takhasusByProdi = [];
        foreach ($takhasusData as $data) {
            if (!isset($takhasusByProdi[$data->prodi_id])) {
                $takhasusByProdi[$data->prodi_id] = [];
            }
            
            // Split takhasus by comma if multiple values exist
            $takhasusNames = array_map('trim', explode(',', $data->takhasus));
            
            foreach ($takhasusNames as $takhasusName) {
                if (!empty($takhasusName) && !in_array($takhasusName, $takhasusByProdi[$data->prodi_id])) {
                    $takhasusByProdi[$data->prodi_id][] = $takhasusName;
                }
            }
        }

        // Create takhasus options for each program studi
        foreach ($takhasusByProdi as $prodiId => $takhasusNames) {
            foreach ($takhasusNames as $takhasusName) {
                // Check if this takhasus already exists for this program studi
                $exists = DB::table('takhasus_program_studis')
                    ->where('program_studi_id', $prodiId)
                    ->where('name', $takhasusName)
                    ->exists();
                
                if (!$exists) {
                    DB::table('takhasus_program_studis')->insert([
                        'program_studi_id' => $prodiId,
                        'name' => $takhasusName,
                        'description' => 'Migrated from existing mahasiswa takhasus data',
                        'status' => 'Aktif',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Update mahasiswas table to link existing takhasus to new takhasus_id
        $mahasiswas = DB::table('mahasiswas')
            ->whereNotNull('takhasus')
            ->where('takhasus', '!=', '')
            ->whereNotNull('prodi_id')
            ->get();

        foreach ($mahasiswas as $mahasiswa) {
            // Find matching takhasus option for this mahasiswa's program studi
            $takhasusOption = DB::table('takhasus_program_studis')
                ->where('program_studi_id', $mahasiswa->prodi_id)
                ->where('name', $mahasiswa->takhasus)
                ->first();

            if ($takhasusOption) {
                DB::table('mahasiswas')
                    ->where('id', $mahasiswa->id)
                    ->update(['takhasus_id' => $takhasusOption->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear takhasus_id from mahasiswas first to avoid foreign key constraint issues
        DB::table('mahasiswas')->update(['takhasus_id' => null]);
        
        // Delete all migrated takhasus data (use delete instead of truncate to avoid foreign key issues)
        DB::table('takhasus_program_studis')->delete();
    }
};
