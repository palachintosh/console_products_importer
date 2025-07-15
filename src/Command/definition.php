<?php

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Command options definition. Chande this file to add or change defines
 */
const IMPORTER_COMMAND_OPTIONS = 
    [
        'path' => [
            'name' => 'path',
            'shortcut' => 'p',
            'mode' => InputOption::VALUE_REQUIRED,
            'description' => 
                'The full path to the CSV file i. e. /home/user/products.csv',
            'default' => null,
            'suggestedValues' => []
        ],
        'test' => [
            'name' => 'test',
            'shortcut' => 't',
            'mode' => InputOption::VALUE_NONE,
            'description' => 
                'Run program in the test mode. The tes mode has no effect on the database, but can be used to test the import process.',
            'suggestedValues' => []
        ]
    ];