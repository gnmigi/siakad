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
        Schema::create('bobot_nilai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('component_key')->unique(); // tugas, quiz, uts, uas
            $table->string('component_name'); // Nama yang ditampilkan (bisa diubah admin)
            $table->decimal('weight', 5, 2); // Bobot dalam persen (0.00 - 100.00)
            $table->string('color_class')->default('text-primary'); // CSS class untuk warna
            $table->integer('order')->default(0); // Urutan tampilan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default values
        DB::table('bobot_nilai_settings')->insert([
            [
                'component_key' => 'tugas',
                'component_name' => 'Tugas',
                'weight' => 20.00,
                'color_class' => 'text-primary',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'component_key' => 'quiz',
                'component_name' => 'Quiz',
                'weight' => 20.00,
                'color_class' => 'text-info',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'component_key' => 'uts',
                'component_name' => 'UTS',
                'weight' => 30.00,
                'color_class' => 'text-warning',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'component_key' => 'uas',
                'component_name' => 'UAS',
                'weight' => 30.00,
                'color_class' => 'text-success',
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bobot_nilai_settings');
    }
};
