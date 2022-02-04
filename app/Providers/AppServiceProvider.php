<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		Blade::directive('markdown', function ($expression) {
			$markdown = view(
				str_replace('\'', '', $expression)
			)->render();

			$Parsedown = new \Parsedown();
			return $Parsedown->text($markdown);
		});
    }
}
