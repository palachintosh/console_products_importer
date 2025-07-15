<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Command\Option\CommandOptionsFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Importer\Sanitizer\CleanProductData;
use App\Importer\Validators\CSVRowValidator;
use App\Importer\ImportRules\ImportRules;

use App\Importer\ProductImportController;

#[AsCommand(
    name: 'importer:products_importer',
    description: 'Import products from a CSV file',
)]
class ImporterProductsImporterCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
