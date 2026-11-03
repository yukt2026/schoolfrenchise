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
        Schema::table('certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificates', 'verification_token')) {
                $table->string('verification_token', 64)->nullable()->unique()->after('certificate_no');
            }
            if (!Schema::hasColumn('certificates', 'serial_no')) {
                $table->string('serial_no', 60)->nullable()->after('certificate_no');
            }
        });

        Schema::table('marksheets', function (Blueprint $table) {
            if (!Schema::hasColumn('marksheets', 'verification_token')) {
                $table->string('verification_token', 64)->nullable()->unique()->after('marksheet_no');
            }
            if (!Schema::hasColumn('marksheets', 'serial_no')) {
                $table->string('serial_no', 60)->nullable()->after('marksheet_no');
            }
        });

        // Populate existing rows with tokens
        $certs = DB::table('certificates')->get();
        foreach ($certs as $cert) {
            $token = 'CERT-' . strtoupper(Str::random(10)) . '-' . sprintf('%04d', $cert->id);
            $serial = sprintf('%s%04d', date('y', strtotime($cert->issue_date ?? now())), $cert->id + 1340);
            DB::table('certificates')->where('id', $cert->id)->update([
                'verification_token' => $cert->verification_token ?: $token,
                'serial_no'          => $cert->serial_no ?: $serial,
            ]);
        }

        $marks = DB::table('marksheets')->get();
        foreach ($marks as $mark) {
            $token = 'MS-' . strtoupper(Str::random(10)) . '-' . sprintf('%04d', $mark->id);
            $serial = sprintf('%s%04d', date('y', strtotime($mark->issue_date ?? now())), $mark->id + 1340);
            DB::table('marksheets')->where('id', $mark->id)->update([
                'verification_token' => $mark->verification_token ?: $token,
                'serial_no'          => $mark->serial_no ?: $serial,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            if (Schema::hasColumn('certificates', 'verification_token')) {
                $table->dropColumn('verification_token');
            }
            if (Schema::hasColumn('certificates', 'serial_no')) {
                $table->dropColumn('serial_no');
            }
        });

        Schema::table('marksheets', function (Blueprint $table) {
            if (Schema::hasColumn('marksheets', 'verification_token')) {
                $table->dropColumn('verification_token');
            }
            if (Schema::hasColumn('marksheets', 'serial_no')) {
                $table->dropColumn('serial_no');
            }
        });
    }
};
