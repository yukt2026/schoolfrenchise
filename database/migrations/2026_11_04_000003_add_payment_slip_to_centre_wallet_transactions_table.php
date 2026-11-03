<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('centre_wallet_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('centre_wallet_transactions', 'payment_slip')) {
                $table->string('payment_slip', 255)->nullable()->after('description');
            }
            if (!Schema::hasColumn('centre_wallet_transactions', 'payment_mode')) {
                $table->string('payment_mode', 50)->default('upi')->after('reference_no');
            }
            if (!Schema::hasColumn('centre_wallet_transactions', 'admin_remarks')) {
                $table->text('admin_remarks')->nullable()->after('payment_slip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('centre_wallet_transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_slip', 'payment_mode', 'admin_remarks']);
        });
    }
};
