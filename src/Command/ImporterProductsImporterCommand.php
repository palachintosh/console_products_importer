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
    public function __construct(
        private EntityManagerInterface $entity_manager,
        private ManagerRegistry $manager_registry,
        )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Import products from a CSV file an write to the MySQL database directly');
        
        // Build options for command
        $available_options = [];

        // Build main options
        if (IMPORTER_COMMAND_OPTIONS !== null) {
            $available_options = IMPORTER_COMMAND_OPTIONS;
        }

        $options_factory = new CommandOptionsFactory($available_options);
        $allowed_options = $options_factory->getOptions();

        foreach ($allowed_options as $option) {
            $this->addOption(
                $option->getName(),
                $option->getShortcut(),
                $option->getMode(),
                $option->getDescription(),
                $option->getDefault()
            );
        }

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Extract path from the input options
        $csv_path = $input->getOption('path');
        // If path is empty - stop procedure. We cannot work with nothing
        if (!$csv_path) {
            $io->error('You must provide a path to the CSV file using --path option. See --help for more.');
            return Command::FAILURE;
        }

        // We have defined static allowed filters in the definition.php file
        // so use it to build only allowed filters using FilterFactory
        // $allowed_filters = OPTION_FILTERS;
        // $user_filters = [];
        // $filters = new FilterFactory($user_filters);
        $test_mode = $input->getOption('test') ? true : false;
        $product_controller = new ProductImportController(
            $this->entity_manager,
            $this->manager_registry,
            new CleanProductData(), // Pass the sanitizer for controller
            new CSVRowValidator(), // Pass the validator for controller
            new ImportRules()
        );

        $product_controller->setTestMode($test_mode);
        
        // Start import
        $io->writeln('<info>Starting import process..</info>');
        $product_controller->doImport($csv_path);

        // Show the import reults
        $io->writeln($product_controller->getResults()->getSummary($product_controller->log_path));

        return Command::SUCCESS;
    }
}
