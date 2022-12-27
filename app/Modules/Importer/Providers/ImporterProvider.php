<?php

namespace App\Modules\Importer\Providers;

use Illuminate\Support\ServiceProvider;

class ImporterProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
	public function boot()
	{
		$this->loadViewsFrom(realpath(__DIR__.'/../Views'), 'Importer');
		$this->commands([
			\App\Modules\Importer\Console\Commands\ImporterConsoleCommand::class
		]);
	}
}
