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
        // Check if unique constraints exist before trying to drop them
        try {
            Schema::table('billing_templates', function (Blueprint $table) {
                $table->dropUnique(['code']);
            });
        } catch (Exception $e) {
            // Index doesn't exist, continue
        }
        
        try {
            Schema::table('billing_templates', function (Blueprint $table) {
                $table->dropUnique(['slug']);
            });
        } catch (Exception $e) {
            // Index doesn't exist, continue
        }

        // For MariaDB/MySQL, we'll create a composite unique index that includes deleted_at
        // This allows the same code/slug to exist multiple times if deleted_at is not null
        // but only one record can have code/slug with deleted_at = null
        DB::statement('CREATE UNIQUE INDEX billing_templates_code_deleted_unique ON billing_templates (code, deleted_at)');
        DB::statement('CREATE UNIQUE INDEX billing_templates_slug_deleted_unique ON billing_templates (slug, deleted_at)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the composite unique indexes
        DB::statement('DROP INDEX IF EXISTS billing_templates_code_deleted_unique ON billing_templates');
        DB::statement('DROP INDEX IF EXISTS billing_templates_slug_deleted_unique ON billing_templates');

        // Restore original unique constraints
        Schema::table('billing_templates', function (Blueprint $table) {
            $table->unique('code');
            $table->unique('slug');
        });
    }
};
