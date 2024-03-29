<?php

namespace App\Console\Migrate;

use Illuminate\Support\Collection;

class Migrator extends \Illuminate\Database\Migrations\Migrator
{
    /**
     * Get all migration files
     *
     * @param string|array  $paths
     * @param bool $reverse
     * @return array
     */
    public function getMigrationFiles($paths)
    {
	    $delim='/';
	    if(strpos(base_path(), "\\")>0){
				$delim='\\';
	    }

        $defaultPath = base_path() . $delim.'database'.$delim.'migrations';

        // add modules paths only if no path given or given path is equal to
        // default Laravel path. Otherwise it means user wants to run migration
        // from custom location and we don't want to add other modules in that
        // case


        if (($paths && $paths[0] == $defaultPath) || ($paths && $paths[1] == $defaultPath) || !$paths) {
            // get modules list
            $modules = glob(base_path() . $delim. 'app'.$delim.'Modules'.$delim.'*'.$delim);

            // add to paths all migration directories from modules
            foreach ($modules as $module) {

                $paths[] = $module . $delim.'Database'.$delim.'Migrations'.$delim;
            }
        }

        return $this->getAllMigrationFiles($paths);
    }

    /**
     * Get all of the migration files in a given path.
     *
     * @param  string|array  $paths
     * @return array
     */
    public function getAllMigrationFiles($paths)
    {
        $files = Collection::make($paths)->flatMap(function ($path) {
            return $this->files->glob($path . '/*_*.php');
        })->filter()->sortBy(function ($file) {
            return $this->getMigrationName($file);
        })->values()->keyBy(function ($file) {
            return $this->getMigrationName($file);
        });

        $files = $files->all();

        return $files;
    }
}
