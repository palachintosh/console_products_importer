<?php

namespace App\Importer;

use App\Importer\ImportResult\ImportResult;
use App\Importer\ProductImportController;
use App\Importer\Sanitizer\CleanProductData;
use App\Importer\Validators\CSVRowValidator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Importer\ImportRules\ImportRules;

use PHPUnit\Framework\TestCase;


class ProductImportControllerTest extends TestCase
{
    private $entityManager;
    private $managerRegistry;
    private $csvCleaner;
    private $csvValidator;
    private $importRules;
    private $controller;
    
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->csvCleaner = $this->createMock(CleanProductData::class);
        $this->csvValidator = $this->createMock(CSVRowValidator::class);
        $this->importRules = $this->createMock(ImportRules::class);
        
        $this->controller = new ProductImportController(
            $this->entityManager,
            $this->managerRegistry,
            $this->csvCleaner,
            $this->csvValidator,
            $this->importRules
        );
    }
    
    public function testFileDoesNotExists()
    {   
        $csv_path = 'non_existent_file.csv';
        $this->expectException(\InvalidArgumentException::class);
        $this->controller->doImport($csv_path);
    }

    public function testEmptyCsv()
    {
        $csv_path = 'tests/fixtures/test_empty_csv.csv';
        $this->expectException(\RuntimeException::class);
        $this->controller->setTestMode(true);
        $this->controller->doImport($csv_path);
    }

    public function testSanitizeRowDirectly(): void
    {
        $testRow = [
            'strProductCode' => 'PRD001 ',
            'strProductName' => 'Test Product</h1>',
            'strProductDesc' => ' Test Description',
            'intProductStock' => '20',
            'decPrice' => '15.99',
            'dmtDiscontinued' => null
        ];

        $expectedCleanRow = [
            'strProductCode' => 'PRD001',
            'strProductName' => 'Test Product',
            'strProductDesc' => 'Test Description',
            'intProductStock' => '20',
            'decPrice' => '15.99',
            'dmtDiscontinued' => null
        ];

        $this->csvCleaner->expects($this->once())
            ->method('cleanCSVRow')
            ->with($testRow)
            ->willReturn($expectedCleanRow);

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('sanitizeRow');
        $method->setAccessible(true);

        $result = $method->invoke($this->controller, $testRow);
        
        $this->assertEquals($expectedCleanRow, $result);
    }

    public function testCreateObjectFromRow(): void
    {
        $row = [
            'strProductCode' => 'PRD001',
            'strProductName' => 'Test Product',
            'strProductDesc' => 'Test Description',
            'intProductStock' => 20,
            'decPrice' => 15.99,
            'dmtDiscontinued' => null
        ];

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('createObjectFromRow');
        $method->setAccessible(true);

        $productData = $method->invoke($this->controller, $row);

        $this->assertInstanceOf(\App\Entity\ProductData::class, $productData);
        $this->assertEquals('PRD001', $productData->getStrProductCode());
        $this->assertEquals('Test Product', $productData->getStrProductName());
        $this->assertEquals('Test Description', $productData->getStrProductDesc());
        $this->assertEquals(20, $productData->getIntProductStock());
        $this->assertEquals(15.99, $productData->getDecPrice());
        $this->assertNull($productData->getDmtDiscontinued());
    }
}