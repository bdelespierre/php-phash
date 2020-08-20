<?php

namespace Tests\Command;

use Bdelespierre\PhpPhash\Command\Compare;
use Bdelespierre\PhpPhash\Command\Generate;
use Bdelespierre\PhpPhash\PHash;
use Intervention\Image\ImageManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\PHashTest;

class CompareCommandTest extends TestCase
{
    use CreatesApplication;

    /**
     * @dataProvider executeDataProvider
     */
    public function testExecute(array $argments, string $similarity)
    {
        $application = $this->createApplication();

        $command = $application->find('compare');
        $commandTester = new CommandTester($command);
        $commandTester->execute($argments);

        $output = $commandTester->getDisplay();
        $this->assertEquals(
            $similarity,
            rtrim($output)
        );
    }

    public function executeDataProvider()
    {
        return [
            'front' => [
                'arguments' => [
                    'file1' => __DIR__ . "/../images/NKIE-WD294_V1.jpg",
                    'file2' => __DIR__ . "/../images/SPDW-WD215_V1.jpg",
                    '--size' => 8,
                    '--format' => "percent",
                ],
                'similarity' => "98%",
            ],
            'side' => [
                'arguments' => [
                    'file1' => __DIR__ . "/../images/NKIE-WD294_V2.jpg",
                    'file2' => __DIR__ . "/../images/SPDW-WD215_V2.jpg",
                    '--size' => 16,
                    '--format' => "float",
                ],
                'similarity' => "0.953125",
            ],
            'rear' => [
                'arguments' => [
                    'file1' => __DIR__ . "/../images/NKIE-WD294_V3.jpg",
                    'file2' => __DIR__ . "/../images/SPDW-WD215_V3.jpg",
                    '--size' => 32,
                    '--format' => "integer",
                ],
                'similarity' => "39",
            ],
        ];
    }

    /**
     * @dataProvider executeFailsDataProvider
     */
    public function testExecuteFails(array $argments, string $reason)
    {
        $application = $this->createApplication();

        $command = $application->find('compare');
        $commandTester = new CommandTester($command);
        $commandTester->execute($argments);

        $output = $commandTester->getDisplay();
        $exit = $commandTester->getStatusCode();

        $this->assertEquals($reason, rtrim($output));
        $this->assertEquals(Command::FAILURE, $exit);
    }

    public function executeFailsDataProvider()
    {
        return [
            'fails when file1 is unreadable' => [
                'arguments' => [
                    'file1' => "INVALID",
                    'file2' => __DIR__ . "/../images/SPDW-WD215_V1.jpg",
                    '--size' => 8,
                    '--format' => "percent",
                ],
                'reason' => "File INVALID not found or unreadable",
            ],

            'fails when file2 is unreadable' => [
                'arguments' => [
                    'file1' => __DIR__ . "/../images/NKIE-WD294_V1.jpg",
                    'file2' => "INVALID",
                    '--size' => 8,
                    '--format' => "percent",
                ],
                'reason' => "File INVALID not found or unreadable",
            ],

            'fails when size is smaller than 8' => [
                'arguments' => [
                    'file1' => __DIR__ . "/../images/NKIE-WD294_V1.jpg",
                    'file2' => __DIR__ . "/../images/SPDW-WD215_V1.jpg",
                    '--size' => 4,
                    '--format' => "percent",
                ],
                'reason' => "Sampling size must be greater or equal to 8",
            ],

            'fails when format is invalid' => [
                'arguments' => [
                    'file1' => __DIR__ . "/../images/NKIE-WD294_V1.jpg",
                    'file2' => __DIR__ . "/../images/SPDW-WD215_V1.jpg",
                    '--size' => 8,
                    '--format' => "INVALID",
                ],
                'reason' => "Invalid format",
            ],
        ];
    }
}
