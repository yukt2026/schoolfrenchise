<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'verification_token')) {
                $table->string('verification_token', 64)->nullable()->unique()->after('status');
            }
            if (!Schema::hasColumn('students', 'id_card_status')) {
                $table->enum('id_card_status', ['valid', 'expired', 'revoked', 'invalid'])->default('valid')->after('verification_token');
            }
            if (!Schema::hasColumn('students', 'id_card_issued_at')) {
                $table->timestamp('id_card_issued_at')->nullable()->after('id_card_status');
            }
            if (!Schema::hasColumn('students', 'id_card_expires_at')) {
                $table->date('id_card_expires_at')->nullable()->after('id_card_issued_at');
            }
        });

        // Generate unique verification token for any existing students
        $existingStudents = DB::table('students')->whereNull('verification_token')->get(['id', 'registration_no']);
        foreach ($existingStudents as $student) {
            $token = 'STU-' . strtoupper(Str::random(10)) . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT);
            DB::table('students')->where('id', $student->id)->update([
                'verification_token' => $token,
                'id_card_status'     => 'valid',
                'id_card_issued_at'  => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('students', 'id_card_expires_at')) $columns[] = 'id_card_expires_at';
            if (Schema::hasColumn('students', 'id_card_issued_at')) $columns[] = 'id_card_issued_at';
            if (Schema::hasColumn('students', 'id_card_status')) $columns[] = 'id_card_status';
            if (Schema::hasColumn('students', 'verification_token')) $columns[] = 'verification_token';

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
