<?php

namespace App\Providers;


use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Society;

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
        //
        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {
            $moduleName = request()->segment(1);
            if ($moduleName) {
                $view->with('thisModule', $moduleName);
            } else {
                // fallback
                $view->with('thisModule', 'unauthorisedAccess');
            }
        });

        if (Schema::hasTable('societies')) {

            $__societies__ = Society::select('id', 'name')
                ->where('status', 'active')
                ->orderBy('id', 'asc')
                ->get();

            View::share('__societies__', $__societies__);
        }

    }
}
