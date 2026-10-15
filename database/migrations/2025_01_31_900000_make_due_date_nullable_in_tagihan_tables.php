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
        // Make due_date nullable in tagihan_kuliahs table
        Schema::table('tagihan_kuliahs', function (Blueprint $table) {
            $table->date('due_date')->nullable()->change();
        });

        // Make due_date nullable in tagihan_kuliah_groups table
        Schema::table('tagihan_kuliah_groups', function (Blueprint $table) {
            $table->date('due_date')->nullable()->change();
        });

        // Update existing indexes to handle NULL values better
        // Drop existing indexes that might conflict with NULL values
        try {
            Schema::table('tagihan_kuliahs', function (Blueprint $table) {
                $table->dropIndex(['due_date', 'overdue_status']);
            });
        } catch (\Exception $e) {
            // Index might not exist, continue
        }

        // Recreate indexes with better NULL handling
        Schema::table('tagihan_kuliahs', function (Blueprint $table) {
            // Index for due date queries (NULL values will be at the end)
            $table->index(['due_date', 'status'], 'idx_due_date_status');
            
            // Index for overdue status with due date
            $table->index(['status', 'due_date', 'overdue_status'], 'idx_status_due_overdue');
            
            // Index for reminder queries
            $table->index(['due_date', 'last_reminder_sent'], 'idx_due_reminder');
        });

        // Add comment to document the change
        DB::statement("ALTER TABLE tagihan_kuliahs MODIFY COLUMN due_date DATE NULL COMMENT 'Tanggal jatuh tempo - NULL untuk tagihan tanpa tempo'");
        DB::statement("ALTER TABLE tagihan_kuliah_groups MODIFY COLUMN due_date DATE NULL COMMENT 'Tanggal jatuh tempo group - NULL untuk group tanpa tempo'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Before making due_date NOT NULL again, we need to handle existing NULL values
        // Set a default date for any NULL due_date records
        $defaultDate = now()->addDays(30)->format('Y-m-d');
        
        DB::table('tagihan_kuliahs')
            ->whereNull('due_date')
            ->update(['due_date' => $defaultDate]);
            
        DB::table('tagihan_kuliah_groups')
            ->whereNull('due_date')
            ->update(['due_date' => $defaultDate]);

        // Drop the new indexes
        Schema::table('tagihan_kuliahs', function (Blueprint $table) {
            $table->dropIndex(['due_date', 'status']);
            $table->dropIndex(['status', 'due_date', 'overdue_status']);
            $table->dropIndex(['due_date', 'last_reminder_sent']);
        });

        // Make due_date NOT NULL again
        Schema::table('tagihan_kuliahs', function (Blueprint $table) {
            $table->date('due_date')->nullable(false)->change();
        });

        Schema::table('tagihan_kuliah_groups', function (Blueprint $table) {
            $table->date('due_date')->nullable(false)->change();
        });

        // Recreate original index if it existed
        try {
            Schema::table('tagihan_kuliahs', function (Blueprint $table) {
                $table->index(['due_date', 'overdue_status'], 'idx_due_overdue');
            });
        } catch (\Exception $e) {
            // Continue if index creation fails
        }

        // Remove comments
        DB::statement("ALTER TABLE tagihan_kuliahs MODIFY COLUMN due_date DATE NOT NULL");
        DB::statement("ALTER TABLE tagihan_kuliah_groups MODIFY COLUMN due_date DATE NOT NULL");
    }
};
