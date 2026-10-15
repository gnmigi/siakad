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
        Schema::create('billing_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama template (e.g., SPP Semester, Praktikum, Wisuda)');
            $table->string('code')->unique()->comment('Kode unik template');
            $table->string('slug')->unique()->comment('URL-friendly slug');
            $table->enum('type', ['semester', 'annual', 'one_time', 'monthly'])
                ->comment('Tipe billing: semester, tahunan, sekali bayar, bulanan');
            
            // Template Configuration
            $table->json('amount_formula')->comment('Formula perhitungan amount berdasarkan kondisi');
            $table->json('auto_generate_rules')->comment('Aturan untuk auto-generate tagihan');
            $table->json('applicable_semesters')->nullable()->comment('Semester yang berlaku [1,2,3,4,5,6,7,8]');
            $table->json('applicable_programs')->nullable()->comment('Program studi yang berlaku');
            $table->json('applicable_classes')->nullable()->comment('Kelas yang berlaku');
            
            // Due Date Configuration
            $table->string('due_date_formula')->comment('Formula untuk menghitung due date');
            $table->integer('grace_period_days')->default(7)->comment('Masa tenggang dalam hari');
            
            // Auto Generation Settings
            $table->boolean('auto_generate')->default(false)->comment('Otomatis generate tagihan');
            $table->enum('generation_trigger', ['semester_start', 'manual', 'scheduled'])
                ->default('manual')->comment('Trigger untuk generate tagihan');
            $table->string('generation_schedule')->nullable()->comment('Cron schedule untuk auto generate');
            
            // Template Status
            $table->enum('status', ['draft', 'active', 'inactive'])->default('draft');
            $table->text('description')->nullable()->comment('Deskripsi template');
            $table->text('notes')->nullable()->comment('Catatan internal');
            
            // Metadata
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'type'], 'idx_status_type');
            $table->index(['auto_generate'], 'idx_auto_generate');
            $table->index(['generation_trigger'], 'idx_generation_trigger');
            
            // Foreign Keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_templates');
    }
};
