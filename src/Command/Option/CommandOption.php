<?php

namespace App\Command\Option;
use Symfony\Component\Console\Input\InputOption;

/**
 * CommandOption class extends InputOption 
 * to provide access to the $mode variable that used
 * by CommandOptionsFactory.
 */

class CommandOption {
    /**
     * @param string $name option name.
     * @param ?string $shortcut option  shortcut (-t).
     * @param int $mode require level mode.
     * @param string $description description.
     * @param string|int|bool|array|float|null $default default value.
     * @param array|\Closure $suggestedValues suggested value (showing in command help).
     */
    public function __construct(
        private string $name,
        private ?string $shortcut = null,
        private int $mode = InputOption::VALUE_NONE,
        private string $description = '',
        private string|int|bool|array|float|null $default = null,
        private array|\Closure $suggestedValues = []
    ) {}
    public function getName(): string {
        return $this->name;
    }
    public function getShortcut(): ?string {
        return $this->shortcut;
    }
    public function getDescription(): string {
        return $this->description;
    }
    public function getDefault(): string|int|bool|array|float|null {
        return $this->default;
    }
    public function getSuggestedValues(): array|\Closure {
        return $this->suggestedValues;
    }

    public function getMode(): int {
        return $this->mode;
    }
}