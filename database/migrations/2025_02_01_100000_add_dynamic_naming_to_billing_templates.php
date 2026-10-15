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
        Schema::table('billing_templates', function (Blueprint $table) {
            // Add dynamic naming configuration
            $table->boolean('use_dynamic_naming')->default(false)
                ->after('auto_generate')
                ->comment('Gunakan penamaan dinamis dengan tahun akademik dan semester');
            
            $table->string('dynamic_naming_pattern')->nullable()
                ->after('use_dynamic_naming')
                ->comment('Pattern untuk dynamic naming: {template_name} - {academic_year} {semester_type}');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_templates', function (Blueprint $table) {
            $table->dropColumn(['use_dynamic_naming', 'dynamic_naming_pattern']);
        });
    }
};
