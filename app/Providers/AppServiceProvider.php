<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use App\Models\Keuangan\TagihanKuliah;
use App\Observers\TagihanKuliahObserver;
use App\Services\SeoService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register SEO Service
        $this->app->singleton(SeoService::class, function ($app) {
            return new SeoService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force session driver to database to bypass Hostinger file lock lag
        config(['session.driver' => 'database']);

        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
        
        // Register observers for auto-terminal functionality
        TagihanKuliah::observe(TagihanKuliahObserver::class);
        \App\Models\Keuangan\PaymentTransaction::observe(\App\Observers\PaymentTransactionObserver::class);
        
        // Register Morph Map for LogAktivitas
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'mahasiswa' => \App\Models\Mahasantri::class,
            'dosen' => \App\Models\Dosen::class,
            'pendaftar' => \App\Models\PMB\Pendaftar::class,
            'user' => \App\Models\User::class,
        ]);
        
        // Share SEO service with all views
        view()->composer('*', function ($view) {
            $view->with('seo', app(SeoService::class));
        });
    }
}
