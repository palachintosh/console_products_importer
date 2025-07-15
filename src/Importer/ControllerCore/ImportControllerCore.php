<?php

namespace App\Importer\ControllerCore;

use App\Importer\ControllerCore\ImportControllerInterface;
use App\Importer\ImportResult\ImportResult;
use App\Importer\Reader\CSVReader;


abstract class ImportControllerCore implements ImportControllerInterface {
    /**
     * Import result object
     */
    protected ImportResult $result;

    // Use to set the actual columns map
    protected array $fields_map = [];

    /**
     * Csv reader object
     */
    protected CSVReader $csv_reader;

    /**
     * Init dry-run mode. False by default
     * @var bool
     */
    public $test_mode = false;

    /**
     * Error messages.
     * It should be only for general import errors.
     * @var array
     */
    public $errors = [];

    /**
     * Warning messages.
     * It should be used for warnings about import process.
     */
    public $warnings = [];

    public $log_path = __DIR__. '/../log/import.log';

    public function __construct() {}
    

    // Init controller
    protected function init(string $csv_path): void {
        $this->result = new ImportResult();
        // Make reader
        $this->csv_reader = new CSVReader($csv_path);
    }

    /**
     * Do import from CSV file to the MySQL database.
     * 
     * @param string $csv_path Path to the CSV file. Must be specified
     * 
     * @return void
     */
    public function doImport(string $csv_path): void {
        $this->init($csv_path);
        $this->resetErrors();
        $this->clearLog();

        // Map csv headers to the entity columns names
        $csv_headers = $this->csv_reader->getCSVHeaders();
        $map_headers = $this->mapHeaders($csv_headers, $this->fields_map);
        // Count headers to validate the csv entries
        $count_headers = count($map_headers);

        foreach ($this->readRows($this->csv_reader) as $row) {
            // Increase proceed rows counter
            $this->result->increaseProceed();

            if (!empty($row)) {
                if (count($row) !== $count_headers) {
                    // ifsomething goes wrong with the row
                    $this->result->increaseIncompleteData();
                    $this->result->increaseFailed();
                    $this->writeLog('Row skipped. Reason: Incomplete row data: ' . json_encode($row));
                    continue;
                } else {
                    // Continue row processing

                    // Do a row mapping to operate real column names
                    $mapped_row = array_combine($map_headers, $row);

                    // Sanitize row values
                    $sanitize_row = $this->sanitizeRow($mapped_row);
                    
                    // Validate row values
                    $validate_row = $this->validateRow($sanitize_row);
                    
                    if (!empty($validate_row) && empty($this->errors)) {
                        $has_passed_rules = $this->applyImportRules($validate_row);
                        if (!$has_passed_rules) {
                            $this->result->increaseRejectedByFilter();
                            $this->result->addError(
                                "Row has not passed import rules: " . json_encode($validate_row)
                            );
                        } else {
                            $create_entity_data = $this->createObjectFromRow($validate_row);
                            if ($create_entity_data !== false) {
                                if ($this->test_mode) {
                                    $this->result->increaseImported();
                                } else {
                                    $save = $this->save($create_entity_data);
                                    if ($save) {
                                        $this->result->increaseImported();
                                    } else {
                                        $this->result->increaseFailed();
                                    }
                                }
                            }
                        }
                    } else {
                        // If row is not valid
                        $this->result->increaseFailed();
                        // Write this line to the log
                        $this->writeLog('Row skipped. Reason: not valid row: ' . json_encode($row));
                    }
                }
            }
        }
    }

    private function writeLog($message) {
        if (!empty($message)) {
            file_put_contents($this->log_path, $message.PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }

    private function clearLog() {
        if (file_exists($this->log_path)) {
            file_put_contents($this->log_path, '');
        }
    }

    abstract protected function readRows(CSVReader $csv_object): iterable;

    /** 
     * Create object from the csv row.
     */
    abstract protected function createObjectFromRow(array $row): object|false;

    /**
     * Save row to the database.
     * 
     * @param  $row The row to save
     * 
     * @return bool Returns true if operation successful
     */
    abstract protected function save(object $entity_data): bool;


    /**
     * Set test mode triger
     * 
     * @param bool $test_mode True if test mode is enabled, false otherwise
     * 
     * @return void
     */
    public function setTestMode(bool $test_mode): void {
        $this->test_mode = $test_mode;
    }

    /**
     * Map csv headers to real column names
     * 
     * @param array $headers Array of csv headers
     * @param array $map The map with corresponding column names
     * 
     * @return array Returns mapped headers
     */
    protected function mapHeaders(array $csv_headers, $map): array {
        if (!empty($csv_headers) && !empty($map)) {
            $mapped_headers = [];

            foreach ($csv_headers as $header) {
                $clean_header = trim($header);

                if ($clean_header && isset($map[$clean_header])) {
                    $mapped_headers[] = $map[$clean_header];
                } else {
                    // If header is not defined 
                    $this->errors[] = "Header '$header' is not defined in the map.";
                }
            }
            
            return $mapped_headers;
        }

        return [];
    }

    /**
     * Sanitize tow values i. e. remove not printable characters, tags, etc
     * Override this method to follow own sanitization rules
     * 
     * @param array $row The csv row in the array format
     * 
     * @return array|false Returns sanitized row
     */
    abstract protected function sanitizeRow(array $row): array;

    /**
     * Validate row entries.
     * Override this method to follow your own validation rules
     * This method should return the validated row array 
     * 
     * @param array $row The csv row in the array format
     * 
     * @return array 
     */
    abstract protected function validateRow(array $row): array;

    /**
     * Applies import rules to fields
     * 
     * @param array $row The csv row in the array format
     * 
     * @return bool True if row has passed all import rules
     */
    protected function applyImportRules(array $row): bool { return true; }

    public function getResults(): ImportResult {
        return $this->result;
    }

    public function resetErrors() {
        $this->errors = [];
        $this->warnings = [];
    }
}