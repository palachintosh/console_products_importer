<?php

namespace App\Command\Option;

use App\Command\Option\CommandOption;

/**
 * This is the command options factory class.
 * We can use it to get all defined options.
 */
class CommandOptionsFactory {
    private $available_options = [];

    public function __construct(array $available_options = []) {
        $this->available_options = $available_options;
    }
    
    /**
     * Returns all available options for the command.
     *
     * @return CommandOption[]
     */
    public function getOptions(): array
    {
        $options = [];

        foreach($this->available_options as $option) {
            $options[] = new CommandOption(
                $option['name'],
                $option['shortcut'],
                $option['mode'],
                $option['description'],
                $option['default'] ?? null,
                $option['suggestedValues']);
        }
        return $options;
    }

    public function getOptionsNames(): array
    {
        $options_names = [];
        foreach ($this->getOptions() as $option) {
            $options_names[] = $option->getName();
        }
        return $options_names;
    }
}