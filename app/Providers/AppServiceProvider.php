<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Setting;
use App\Policies\AuditLogPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\SettingPolicy;
use App\Support\PlatformSettings;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PlatformSettings::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(AuditLog::class, AuditLogPolicy::class);
        Gate::policy(Organization::class, OrganizationPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);

        View::composer('*', function ($view): void {
            $platformSettings = Schema::hasTable('settings')
                ? app(PlatformSettings::class)->all()
                : app(PlatformSettings::class)->defaults();

            $view->with('platformSettings', $platformSettings);
        });
    }
}
