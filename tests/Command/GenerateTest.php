<?php

namespace Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateCommandTest extends TestCase
{
    use CreatesApplication;

    /**
     * @dataProvider executeDataProvider
     */
    public function testExecute(array $arguments, string $hash)
    {
        $application = $this->createApplication();

        $command = $application->find('generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute($arguments);

        $output = $commandTester->getDisplay();

        $this->assertEquals($hash, rtrim($output));
    }

    public function executeDataProvider()
    {
        return [
            'red dress 1.1' => [
                'arguments' => [
                    'file' => __DIR__.'/../images/NKIE-WD294_V1.jpg',
                    '--size' => 8,
                    '--format' => 'hex',
                ],
                'hash' => 'ffffef0001900000',
            ],
            'red dress 1.2' => [
                'arguments' => [
                    'file' => __DIR__.'/../images/NKIE-WD294_V2.jpg',
                    '--size' => 16,
                    '--format' => 'bin',
                ],
                'hash' => '1111111111111111111111111111111111111111111111111111111111111111111111111111111111100000111111111000000000000011000000000000000000000000000000001000000000000111111111111101111111111111111111111111111111111111111111111111111111111111111111111111111111111111',
            ],
            'red dress 1.3' => [
                'arguments' => [
                    'file' => __DIR__.'/../images/NKIE-WD294_V3.jpg',
                    '--size' => 32,
                    '--format' => 'ascii',
                ],
                'hash' => "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111100000000011111111111111111\n".
                    "11111000000000000011111111111111\n".
                    "11111000000000000000011111111111\n".
                    "10000000000000000000000000000101\n".
                    "00000000000000000000000000000000\n".
                    "00000000000000000000000000111000\n".
                    "00000000000000000000000101110000\n".
                    "10011000000000000000000000000000\n".
                    "11111000000000000000000000011111\n".
                    "11111000000000000000011111111111\n".
                    "11111000000000000011111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    "11111111111111111111111111111111\n".
                    '11111111111111111111111111111111',
            ],
        ];
    }

    /**
     * @dataProvider executeCommandFailsDataProvider
     */
    public function testExecuteFails(array $arguments, string $reason)
    {
        $application = $this->createApplication();

        $command = $application->find('generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute($arguments);

        $output = $commandTester->getDisplay();
        $exit = $commandTester->getStatusCode();

        $this->assertEquals($reason, rtrim($output));
        $this->assertEquals(Command::FAILURE, $exit);
    }

    public function executeCommandFailsDataProvider()
    {
        return [
            'fails when given an invalid file' => [
                'arguments' => [
                    'file' => 'INVALID',
                ],
                'reason' => 'File INVALID not found or unreadable',
            ],

            'fails when given a size smaller than 8' => [
                'arguments' => [
                    'file' => realpath(__DIR__.'/../images/NKIE-WD294_V1.jpg'),
                    '--size' => 4,
                ],
                'reason' => 'Sampling size must be greater or equal to 8',
            ],

            'fails when given an invalid format' => [
                'arguments' => [
                    'file' => realpath(__DIR__.'/../images/NKIE-WD294_V1.jpg'),
                    '--format' => 'INVALID',
                ],
                'reason' => 'Invalid format',
            ],
        ];
    }
}
