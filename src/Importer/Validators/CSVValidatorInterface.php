<?php

namespace App\Importer\Validators;

/**
 * Interface for CSV row validations.
 */
interface CSVValidatorInterface {
    /**
     * Validate a single csv row passed as array
     * 
     * @param array $row The csv row
     */
    public function validateCSVRow(array $row): void;

    /**
     * Check if row valid 
     */
    public function isRowValid(array $row): bool;

    /**
     * Returns an array wit validation errors
     */
    public function getValidationErrors(): array;

}