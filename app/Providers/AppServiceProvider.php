<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use App\View\Composers\SidebarComposer;

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
        // Register view composer for sidebar
        View::composer(
            'components.layouts.managers.sidebar',
            SidebarComposer::class
        );

        // Override filepond scripts directive to use relative paths to avoid cross-host CORS
        // This ensures the assets are requested from the same origin as the current page
        Blade::directive('filepondScripts', function () {
            // Prefer published assets if present
            $scriptPath = file_exists(public_path('vendor/livewire-filepond/filepond.js'))
                ? '/vendor/livewire-filepond/filepond.js'
                : '/_filepond/scripts';

            $stylePath = file_exists(public_path('vendor/livewire-filepond/filepond.css'))
                ? '/vendor/livewire-filepond/filepond.css'
                : '/_filepond/styles';

            return "<link rel=\"stylesheet\" href=\"{$stylePath}\">" .
                   "<script type=\"module\" src=\"{$scriptPath}\" data-navigate-once defer data-navigate-track></script>";
        });
    }
}
