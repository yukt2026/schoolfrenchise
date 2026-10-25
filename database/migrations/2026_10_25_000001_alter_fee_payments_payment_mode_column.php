<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `fee_payments` MODIFY `payment_mode` VARCHAR(50) NOT NULL DEFAULT 'online'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `fee_payments` MODIFY `payment_mode` ENUM('cash', 'upi', 'online', 'cheque', 'bank_transfer') NOT NULL DEFAULT 'online'");
    }
};
