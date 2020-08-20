<?php

namespace Bdelespierre\PhpPhash\Command;

use Bdelespierre\PhpPhash\PHash;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Compare extends Command
{
    protected $phash;

    public function __construct(PHash $phash)
    {
        $this->phash = $phash;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('compare')
            ->setDescription('Compare two images and outputs how similar they are')
            ->addArgument('file1', InputArgument::REQUIRED, 'Pass the first file.')
            ->addArgument('file2', InputArgument::REQUIRED, 'Pass the second file.')
            ->addOption('size', 's', InputOption::VALUE_REQUIRED, 'Sampling size.', 8)
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Output format.', 'percent');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file1 = $input->getArgument('file1');
        $file2 = $input->getArgument('file2');
        $size  = $input->getOption('size');

        if (! is_readable($file1)) {
            $output->writeln("<error>File {$file1} not found or unreadable</error>");

            return Command::FAILURE;
        }

        if (! is_readable($file2)) {
            $output->writeln("<error>File {$file2} not found or unreadable</error>");

            return Command::FAILURE;
        }

        if ($size < 8) {
            $output->writeln("<error>Sampling size must be greater or equal to 8</error>");

            return Command::FAILURE;
        }

        if ($size ** 2 > PHP_INT_SIZE * 8 && ! function_exists('gmp_init')) {
            $output->writeln("<error>Sampling size too large: reduce it or install PHP-GMP extension</error>");

            return Command::FAILURE;
        }

        if (! in_array($input->getOption('format'), ['percent', 'ratio', 'float', 'integer', 'int'])) {
            $output->writeln("<error>Invalid format</error>");

            return Command::FAILURE;
        }

        $hash1 = $this->phash->hash(new \SplFileInfo($file1), $size);
        $hash2 = $this->phash->hash(new \SplFileInfo($file2), $size);

        $diff = 0;
        for ($i = 0; $i < $size ** 2; $i++) {
            if ($hash1[$i] != $hash2[$i]) {
                $diff++;
            }
        }

        $sim = 1 - $diff / ($size ** 2);

        switch ($input->getOption('format')) {
            default:
            case 'percent':
                $output->writeln(round($sim * 100) . '%');
                break;

            case 'ratio':
            case 'float':
                $output->writeln($sim);
                break;

            case 'integer':
            case 'int':
                $output->writeln($diff);
                break;
        }

        return Command::SUCCESS;
    }
}
