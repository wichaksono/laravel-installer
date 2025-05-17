<?php

namespace Modules\NeonWebId;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\NeonWebId\Console\Commands\GitPullCommand;
use Modules\NeonWebId\Http\Controllers\InstallerController;
use Modules\NeonWebId\Http\Middleware\CheckInstallation;
use Modules\NeonWebId\Services\GitService;

/**
 * NeonWebId Module Class
 * @created 2025-05-17 07:16:17
 * @author wichaksono
 */
class NeonWebId extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Git Service
        $this->app->singleton(GitService::class);

        // Register config
        $this->mergeConfigFrom(
            __DIR__ . '/Config/git.php', 'neonwebid.git'
        );

        // Merge git configuration from services
        config([
            'neonwebid.git.schedule.enabled' => config('services.git.auto_pull.enabled'),
            'neonwebid.git.schedule.frequency' => config('services.git.auto_pull.frequency'),
            'neonwebid.git.schedule.cron' => config('services.git.auto_pull.cron'),
        ]);
    }


    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                GitPullCommand::class,
            ]);
        }

        // Register config for publishing
        $this->publishes([
            __DIR__ . '/Config/git.php' => config_path('neonwebid/git.php'),
        ], 'neonwebid-config');
    }

    /**
     * Register Git Puller
     */
    public static function gitPuller(): void
    {
        // Schedule git pull if enabled
        if (config('neonwebid.git.schedule.enabled')) {
            $frequency = config('neonwebid.git.schedule.frequency');
            $cron      = config('neonwebid.git.schedule.cron');

            app()->make(Schedule::class)
                ->command('git:pull')
                ->when(function () use ($frequency, $cron) {
                    return match ($frequency) {
                        'hourly' => true,
                        'daily' => true,
                        'custom' => $cron,
                        default => false
                    };
                });
        }
    }

    /**
     * Register the installer routes and middleware
     *
     * @return void
     */
    public static function installer(): void
    {
        // Register a views path
        $viewsPath = __DIR__ . '/Resources/views';
        View::addNamespace('neonwebid', $viewsPath);

        // Register middleware
        app('router')->aliasMiddleware('check.installation', CheckInstallation::class);

        // Add middleware to web group
        app('router')->pushMiddlewareToGroup('web', CheckInstallation::class);

        // Register installer routes
        Route::middleware(['web'])->group(function () {
            Route::prefix('install')->group(function () {
                Route::get('/', [InstallerController::class, 'index'])->name('installer.index');
                Route::get('/requirements', [InstallerController::class, 'requirements'])
                    ->name('installer.requirements');
                Route::get('/configuration', [InstallerController::class, 'configuration'])
                    ->name('installer.configuration');
                Route::post('/setup', [InstallerController::class, 'setup'])->name('installer.setup');
            });
        });
    }

    /**
     * Check if application is installed
     *
     * @return bool
     */
    public static function isInstalled(): bool
    {
        return file_exists(storage_path('installed'));
    }
}
