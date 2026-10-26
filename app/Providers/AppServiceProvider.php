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
        //
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
