<?php

namespace App\Importer\Sanitizer;

interface CleanCSVFieldsInterface {
    /**
     * Returns clean csv fields prepared to validate
     */
    public function cleanCSVRow(array $row): array;
}