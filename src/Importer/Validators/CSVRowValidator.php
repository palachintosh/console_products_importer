<?php

namespace App\Importer\Validators;

use App\Importer\Validators\CSVValidatorInterface;
/**
 * Helper validate class. Can be used to validate product data fields
 */
class CSVRowValidator implements CSVValidatorInterface {

    /**
     * Array with validation errors
     */
    protected array $validation_errors = [];

    public function validateCSVRow(array $row): void {
        // Reset errors
        $this->validation_errors = [];

        // Validate product code
        if (!self::isProductCode($row['strProductCode'])) {
            $this->validation_errors[] = 'Invalid product code: '.$row['strProductCode'];
        }

        // Validate product name
        if (!self::isProductName($row['strProductName'])) {
            $this->validation_errors[] = 'Invalid product name: '.$row['strProductName'];
        }

        // Validate product description
        if (!self::isProductDesc($row['strProductDesc'])) {
            $this->validation_errors[] = 'Invalid product description: '.$row['strProductDesc'];
        }

        // Validate product price
        if (!self::isProductPrice($row['decPrice'])) {
            $this->validation_errors[] = 'Invalid product price: '.$row['decPrice'];
        }

        // Validate product quantity
        if (!self::isProductQuantity($row['intProductStock'])) {
            $this->validation_errors[] = 'Invalid product quantity: '.$row['intProductStock'];
        }
    }

    public function isRowValid(array $row): bool {
        $this->validateCSVRow($row);

        return empty($this->validation_errors) ? true : false;
    }

    public function getValidationErrors(): array {
        return array_unique($this->validation_errors);
    }

    // Static methods for validating product fields defined below
    
    public static function isProductCode(string $product_code): bool {
        return !empty($product_code);
    }

    public static function isProductName(string $field_name): bool {
        return !empty($field_name);
    }

    public static function isProductDesc(string $field_desc): bool {
        return !empty($field_desc);
    }

    public static function isProductPrice(string $field_price): bool {
        return is_numeric($field_price) && (float) $field_price >= 0;
    }

    public static function isProductQuantity(string $field_quantity): bool {
        return is_numeric($field_quantity) && (int) $field_quantity >= 0;
    }
}