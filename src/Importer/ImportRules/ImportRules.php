<?php

namespace App\Importer\ImportRules;

class ImportRules {
    public static function minPrice(float $field_price): bool 
    {
        return $field_price < 5;
    }
    
    public static function minStock(int $field_quantity): bool 
    {
        return $field_quantity < 10;
    }

    public static function maxPrice(float $field_price): bool 
    {
        return $field_price > 1000;
    }
}