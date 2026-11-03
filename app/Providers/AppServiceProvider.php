<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Ensure BaconQrCode, DASPRiD Enum & SimpleSoftwareIO are autoloaded cleanly
        spl_autoload_register(function ($class) {
            $base = base_path('vendor');
            if (str_starts_with($class, 'BaconQrCode\\')) {
                $file = $base . '/bacon/bacon-qr-code/src/' . str_replace('\\', '/', substr($class, 12)) . '.php';
                if (file_exists($file)) require_once $file;
            }
            if (str_starts_with($class, 'DASPRiD\\Enum\\')) {
                $file = $base . '/dasprid/enum/src/' . str_replace('\\', '/', substr($class, 13)) . '.php';
                if (file_exists($file)) require_once $file;
            }
            if (str_starts_with($class, 'SimpleSoftwareIO\\QrCode\\')) {
                $file = $base . '/simplesoftwareio/simple-qrcode/src/' . str_replace('\\', '/', substr($class, 24)) . '.php';
                if (file_exists($file)) require_once $file;
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            static $cachedSettings = null;

            if ($cachedSettings === null) {
                try {
                    $dbSettings = Schema::hasTable('settings') ? Setting::getAllKeyValues() : [];
                } catch (\Throwable $e) {
                    $dbSettings = [];
                }

                $defaults = [
                    'institute_name'      => 'TECH ERA COMPUTER',
                    'site_name'           => 'TECH ERA COMPUTER',
                    'institute_tagline'   => 'Building Digital Skills for the Future',
                    'site_tagline'        => 'Building Digital Skills for the Future',
                    'contact_phone'       => '9162384660',
                    'helpline_phone'      => '9162384660',
                    'whatsapp_number'     => '9162384660',
                    'corporate_address'   => 'Sasbahana, Sheikhpura, Bihar - 801105',
                    'corporate_office'    => 'Sasbahana, Sheikhpura, Bihar - 801105',
                    'contact_email'       => '',
                    'official_email'      => '',
                    'website'             => '',
                    'institute_logo'      => 'images/tech-era-logo.png',
                    'receipt_prefix'      => 'TEC-RCP',
                    'certificate_prefix'  => 'TEC/CERT',
                    'marksheet_prefix'    => 'TEC/MS',
                    'currency_symbol'     => '₹',
                    'affiliation_text'    => 'Premier Institute of Computer Technology & Skill Development',
                    'academic_session'    => '2026-2027',
                    'admin_upi_id'        => '9162384660@upi',
                    'admin_upi_name'      => 'TECH ERA COMPUTER',
                    'admin_qr_code'       => null,
                    'admin_bank_name'     => 'State Bank of India',
                    'admin_bank_account_no'=> '38491028472',
                    'admin_bank_ifsc'     => 'SBIN0001234',
                    'admin_bank_holder'   => 'TECH ERA COMPUTER',
                ];

                $merged = array_merge($defaults, $dbSettings);

                $logoPath = $merged['institute_logo'] ?? 'images/tech-era-logo.png';
                if (file_exists(public_path($logoPath))) {
                    $merged['logo_url'] = asset($logoPath);
                } elseif (file_exists(public_path('storage/' . $logoPath))) {
                    $merged['logo_url'] = asset('storage/' . $logoPath);
                } elseif (file_exists(storage_path('app/public/' . $logoPath))) {
                    $merged['logo_url'] = asset('storage/' . $logoPath);
                } else {
                    $merged['logo_url'] = asset('images/tech-era-logo.png');
                }

                $cachedSettings = $merged;
            }

            $view->with('instituteSettings', $cachedSettings);
        });
    }
}
