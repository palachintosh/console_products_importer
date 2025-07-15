<?php

namespace App\Importer;

use App\Importer\ControllerCore\ImportControllerCore;
use App\Importer\ImportResult\ImportResult;
use App\Importer\Reader\CSVReader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\ProductData;
use App\Importer\Validators\CSVValidatorInterface;
use App\Importer\Sanitizer\CleanCSVFieldsInterface;
use App\Importer\ImportRules\ImportRules;
use App\Importer\Sanitizer\CleanProductData;
use App\Importer\Validators\CSVRowValidator;

/**
 * The target controller class for manage product import from CSV file.
 */
class ProductImportController extends ImportControllerCore {
    /**
     * Fields map between csv file headers and enrtity columns.
     */
    protected const FIELDS_MAP = [
        'Product Code' => 'strProductCode',
        'Product Name' => 'strProductName',
        'Product Description' => 'strProductDesc',
        'Stock' => 'intProductStock',
        'Cost in GBP' => 'decPrice',
        'Discontinued' => 'dmtDiscontinued',
    ];

    public function __construct(
        protected EntityManagerInterface $entity_manager,
        protected ManagerRegistry $manager_registry,
        protected CleanCSVFieldsInterface $csv_cleaner,
        protected CSVValidatorInterface $csv_validator,
        protected ImportRules $import_rules
    ) {
        parent::__construct();
        // Do other things here if needed
        $this->fields_map = static::FIELDS_MAP;
    }


    /**
     * Return the import result object
     */
    public function getResults(): ImportResult {
        return $this->result;
    }


    /**
     * Prepares the row values for database operations.
     * 
     * @param array $row the row to prepare.
     * 
     * @return object|false object 
     */
    protected function createObjectFromRow(array $row): object|false {
        if (!empty($row)) {
            $product_data = new ProductData();
            $product_data->setStrProductCode($row['strProductCode']);
            $product_data->setStrProductName($row['strProductName']);
            $product_data->setStrProductDesc($row['strProductDesc']);
            $product_data->setIntProductStock((int)($row['intProductStock']));
            $product_data->setDecPrice((float)($row['decPrice']));
            $product_data->setStmTimestamp(new \DateTime());
            $product_data->setDmtDiscontinued($row['dmtDiscontinued']);

            return $product_data;
        }

        return false;
    }


    /**
     * Read rows from the CSV file. Returns iterator that can be used to
     * iterate over the file rows
     * 
     * @param CSVReader $csv_reader
     * @return iterable
     */
    protected function readRows(CSVReader $csv_reader): iterable {
        return $csv_reader->getRow();
    }


    protected function save(object $entity_data): bool {
        // define the base saving logic here
        if (empty($entity_data)) {
            return false;
        }

        try {
            // Check if connection is open
            if ($this->entity_manager->isOpen()) {
                // If yes persist the product data
                $this->entity_manager->persist($entity_data);
                // Flush memory after each line
                $this->entity_manager->flush();
            } else {
                throw new \Exception('Entity Manager is closed. Cannot save product data.');
            }
        } catch (\Exception $e) {
            // Clear connection if error on sql level occurs
            $this->entity_manager->clear();
            $this->manager_registry->resetManager();
            $this->entity_manager = $this->manager_registry->getManager();

            // Buil error message
            $this->errors[] = 'Error while import: '.$entity_data->getStrProductCode().': '.$e->getMessage();
            $this->result->addError($e->getMessage());

            return false;
        }        

        return true;
    }

    protected function sanitizeRow(array $row): array
    {
        // Sanitize row using the CSVCleanerInterface
        $clean_row = $this->csv_cleaner->cleanCSVRow($row);

        // Return sanitized row
        return $clean_row;
    }

    protected function validateRow(array $row): array
    {   
        // Validate row using the CSVValidatorInterface
        if ($this->csv_validator->isRowValid($row)) {
            // If row is valid - return it
            return $row;
        } else {
            // Get all errors during validation process for the row 
            // and write them into result object
            $this->result->addMultipleErrors(
                $this->csv_validator->getValidationErrors()
            );
        }

        return [];
    }

    /**
     * Apply import rules
     */
    protected function applyImportRules(array $row): bool 
    {
        // Do NOT import:
        // If cost < 5
        // If quantity < 10
        // If cost > 1000 

        // Do import if:
        // If discontinued = yes but set the dmtDiscontinued to the current date
        
        // All import rules defined in the ImportRules class
        // Extend ImportRules class to add more rules if needed

        $stock = $row['intProductStock'];
        $price = $row['decPrice'];

        $is_rejected = (($this->import_rules::minPrice($price) &&
            $this->import_rules::minStock($stock)) ||
            $this->import_rules::maxPrice($price)
        );

        return $is_rejected ? false : true;
    }
}