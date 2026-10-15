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
        Schema::create('pmb_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['file', 'image', 'document'])->default('file');
            $table->string('allowed_extensions')->default('jpg,jpeg,png,pdf');
            $table->integer('max_size_mb')->default(5);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Tabel untuk menyimpan upload dokumen pendaftar
        Schema::create('pmb_requirement_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftar_id')->constrained('pendaftars')->onDelete('cascade');
            $table->foreignId('requirement_id')->constrained('pmb_requirements')->onDelete('cascade');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('file_size');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('uploaded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmb_requirement_uploads');
        Schema::dropIfExists('pmb_requirements');
    }
};
