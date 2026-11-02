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
        // 1. Add commission tracking to admissions table
        if (Schema::hasTable('admissions')) {
            Schema::table('admissions', function (Blueprint $table) {
                if (!Schema::hasColumn('admissions', 'commission_credited')) {
                    $table->boolean('commission_credited')->default(false)->after('status');
                }
                if (!Schema::hasColumn('admissions', 'commission_amount')) {
                    $table->decimal('commission_amount', 10, 2)->nullable()->after('commission_credited');
                }
            });
        }

        // 2. Add centre_id to online_transactions table for wallet recharges
        if (Schema::hasTable('online_transactions')) {
            Schema::table('online_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('online_transactions', 'centre_id')) {
                    $table->foreignId('centre_id')->nullable()->after('admission_id')->constrained('centres')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('admissions')) {
            Schema::table('admissions', function (Blueprint $table) {
                if (Schema::hasColumn('admissions', 'commission_amount')) {
                    $table->dropColumn('commission_amount');
                }
                if (Schema::hasColumn('admissions', 'commission_credited')) {
                    $table->dropColumn('commission_credited');
                }
            });
        }

        if (Schema::hasTable('online_transactions')) {
            Schema::table('online_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('online_transactions', 'centre_id')) {
                    $table->dropForeign(['centre_id']);
                    $table->dropColumn('centre_id');
                }
            });
        }
    }
};
