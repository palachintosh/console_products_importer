<?php

namespace App\Importer\ControllerCore;

use App\Importer\ImportResult\ImportResult;

interface ImportControllerInterface
{
    /**
     * Import data from a CSV file
     *
     * @return void 
     */
    public function doImport(string $csv_path): void;

    /**
     * Get the results of the import operation
     *
     * @return ImportResult
     */
    public function getResults(): ImportResult;
}