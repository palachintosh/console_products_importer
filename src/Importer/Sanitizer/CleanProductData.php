<?php

namespace App\Importer\Sanitizer;

use App\Importer\Sanitizer\CleanCSVFieldsInterface;
use Datetime;

/**
 * Sanitizer class for product data.
 */
class CleanProductData implements CleanCSVFieldsInterface {
    /**
     * Sanitize CSV fields. Remember that this function modify the row values
     */
    public function cleanCSVRow(array $row): array {
        $row['strProductCode'] = self::cleanProductCode($row['strProductCode']);
        $row['strProductName'] = self::cleanName($row['strProductName']);
        $row['strProductDesc'] = self::cleanDesc($row['strProductDesc']);
        $row['intProductStock'] = self::cleanQuantity($row['intProductStock']);
        $row['dmtDiscontinued'] = self::cleanDiscontinued($row['dmtDiscontinued']);

        return $row;
    }
    
    public static function cleanProductCode(string $field): string {
        return trim(strip_tags($field));
    }

    public static function cleanName(string $field): string {
        // Trim whitespaces and remove tags
        return trim(strip_tags($field));
    }

    public static function cleanDesc(string $desc): string {
        // Trim whitespaces
        return trim($desc);
    }

    public static function cleanQuantity(string $quantity): int {
        return is_numeric($quantity) ? (int) $quantity : 0;
    }

    public static function cleanDiscontinued(string $discount): DateTime|null {
        // If 'yes' set the value as current date
        if (trim(strtolower($discount)) == 'yes') {
            return new DateTime();
        }
        
        return null;
    }
}