<?php

namespace App\Modules\Importer\Console\Commands;

use Illuminate\Console\Command;
use function dd;

class ImporterConsoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
		protected $signature = 'create:ImportWorkOrder {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
	    $out = new \Symfony\Component\Console\Output\ConsoleOutput();

	    $out->writeln(  \App\Modules\Importer\Http\Сontrollers\ImporterController::getfileConsole($this->argument('path')));

    }
}
