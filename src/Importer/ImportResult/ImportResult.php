<?php

namespace App\Importer\ImportResult;

/**
 * Easy access to import results
 */
class ImportResult {
    private int $total_proceed = 0;
    private int $total_failed = 0;
    private int $total_imported = 0;
    private int $total_rejected_by_filter = 0;
    private int $total_incomplete_data = 0;
    private array $import_errors = [];

    
    public function getTotalProceed(): int {
        return $this->total_proceed;
    }
    public function getTotalFailed(): int {
        return $this->total_failed;
    }
    public function getTotalImported(): int {
        return $this->total_imported;
    }
    
    public function getTotalRejectedByFilter(): int {
        return $this->total_rejected_by_filter;
    }

    public function getTotalIncompleteData(): int {
        return $this->total_incomplete_data;
    }

    public function increaseProceed(): void {
        $this->total_proceed++;
    }

    public function increaseFailed(): void {
        $this->total_failed++;
    }

    public function increaseImported(): void {
        $this->total_imported++;
    }

    public function increaseRejectedByFilter(): void {
        $this->total_rejected_by_filter++;
    }

    public function increaseIncompleteData(): void {
        $this->total_incomplete_data++;
    }

    public function addError(string $error): void {
        $this->import_errors[] = $error;
    }

    public function addMultipleErrors(array $errors): void {
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }

    public function getImportErrors(): array {
        return $this->import_errors;
    }

    public function getSummary(string $import_log_path=''): string {
        $summary = 'Import Summary:'.PHP_EOL;
        $summary .= 'Total Proceed: '.$this->getTotalProceed().PHP_EOL;
        $summary .= 'Total Imported: '.$this->getTotalImported().PHP_EOL;
        $summary .= 'Total Failed: '.$this->getTotalFailed().PHP_EOL;
        $summary .= 'Total Rejected by Filter: '.$this->getTotalRejectedByFilter().PHP_EOL;
        $summary .= 'Total Incomplete Data: '.$this->getTotalIncompleteData().PHP_EOL;

        if (!empty($this->import_errors)) {
            $summary .= 'Errors:'.PHP_EOL.implode(PHP_EOL, $this->import_errors);
        }

        if (!empty($import_log_path)) {
            if (file_exists($import_log_path)) {
                $summary .= PHP_EOL . '<error>Invalid rows:<error> ' .PHP_EOL;
                // Read log line  by line and add to output
                $log_file = new \SplFileObject($import_log_path, 'r');
                while (!$log_file->eof()) {
                    $line = $log_file->fgets();
                    if (!empty($line)) {
                        $summary .= $line; // Line already contains a newline char
                    }
                }
                unset($log_file);
            }
        }

        return $summary;
    }

    public function reset(): void {
        $this->total_proceed = 0;
        $this->total_failed = 0;
        $this->total_imported = 0;
        $this->total_rejected_by_filter = 0;
        $this->total_incomplete_data = 0;
        $this->import_errors = [];
    }
}