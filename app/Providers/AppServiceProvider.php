<?php

namespace App\Providers;

use App\Events\UserSaved;
use App\Models\User;
use App\Services\UserService;
use App\Services\UserServiceInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::macro('softDeletes', function ($prefix, $controller) {
            Route::prefix($prefix)
                ->as($prefix . '.')
                ->controller($controller)
                ->group(function () {
                    Route::get('trashed', 'trashed')->name('trashed');
                    Route::patch('{id}/restore', 'restore')->name('restore');
                    Route::delete('{id}/delete', 'delete')->name('delete');
                });
        });
    }
}
