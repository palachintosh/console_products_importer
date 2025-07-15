<?php

namespace App\Importer\Reader;

use \SplFileObject;


/**
 * Reader helper class for reading CSV files.
 * Provides interface for reading and validate CSV files.
 */
class CSVReader {
    /**
     * SplFileObject instace of CSV
     */
    protected SplFileObject $csv_object;
    /**
     * Store CSV headers in the variable
     */
    protected array $headers = [];

    /**
     * Constructor
     *
     * @param string $file_path The full path to the CSV file
     * @param string $delimiter The delimiter used in CSV
     * @param string $encoding The encoding of the CSV file
     * @param string $enclosure The enclosure character used in the CSV
     * @param string $escape The escape character used in the CSV
     * 
     * @throws \InvalidArgumentException if the file does not exist or some parameters are invalid
     */
    public function __construct(
        private string $file_path, 
        private string $delimiter = ',',
        private string $enclosure = '"',
        private string $escape = '\\'
        ) {
        $this->setFilePath($file_path);
        $this->setDelimiter($delimiter);
        $this->setEnclosure($enclosure);
        $this->setEscape($escape);

        $this->createFileObject();
    }
    
    private function setFilePath(string $file_path): void {
        if (!file_exists($file_path)) {
            throw new \InvalidArgumentException(
                'Looks like the file path ' . $file_path . ' does not exist.
                Did you provide the correct path?');
        }
        $this->file_path = $file_path;
    }

    public function setFileObject(SplFileObject $csv_object): void {
        if (!$csv_object instanceof SplFileObject) {
            throw new \InvalidArgumentException('Invalid SplFileObject instance provided.');
        }
        $this->csv_object = $csv_object;
    }

    private function setDelimiter(string $delimiter): void {
        if (empty($delimiter)) {
            throw new \InvalidArgumentException('Delimiter cannot be empty.');
        }
        $this->delimiter = $delimiter;
    }

    private function setEnclosure(string $enclosure): void {
        if (empty($enclosure)) {
            throw new \InvalidArgumentException('Enclosure cannot be empty.');
        }
        $this->enclosure = $enclosure;
    }

    private function setEscape(string $escape): void {
        if (empty($escape)) {
            throw new \InvalidArgumentException('Escape character cannot be empty.');
        }
        $this->escape = $escape;
    }

    /**
     * Opens the CSV file and returns pointer on the file as SplFileObject instance.
     *
     * @return SplFileObject
     */
    private function createFileObject() {
        $csv_object = new SplFileObject($this->file_path);
        $csv_object->setFlags(
            SplFileObject::READ_CSV | 
            SplFileObject::SKIP_EMPTY |
            SplFileObject::READ_AHEAD);
        $csv_object->setCsvControl(
            $this->delimiter,
            $this->enclosure,
            $this->escape);
        
        $this->setFileObject($csv_object);
        $this->setCSVHeaders();
    }


    /**
     * Read CSV file by line. returns iterator of lines.
     * 
     * @return iterable
     */
    public function getRow(): iterable {
        // Read CSV file row by row
        while (!$this->csv_object->eof()) {
            $row = $this->csv_object->fgetcsv();
            
            yield $row;
        }
    }    

    /**
     * Return the csv headers.
     * 
     */
    public function getCSVHeaders(): array {
        return $this->headers;
    }

    /**
     * Put the csv headers to the class variable
     */
    protected function setCSVHeaders(): void {
        $headers = $this->csv_object->fgetcsv();

        if (!$headers || !is_array($headers)) {
            throw new \RuntimeException('CSV file is empty or not readable.');
        }

        $this->headers = $headers;
    }
    
}