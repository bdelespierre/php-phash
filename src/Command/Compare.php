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
    use ValidatesSamplingSize;

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
        try {
            $this->validate($input);
        } catch (\InvalidArgumentException $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");

            return Command::FAILURE;
        }

        $size = $input->getOption('size');
        $hash1 = $this->phash->hash(new \SplFileInfo($input->getArgument('file1')), $size);
        $hash2 = $this->phash->hash(new \SplFileInfo($input->getArgument('file2')), $size);
        $dist = $this->getHammingDistance($hash1, $hash2);
        $sim = 1 - $dist / ($size ** 2);

        $this->display($output, $input->getOption('format'), $dist, $sim);

        return Command::SUCCESS;
    }

    protected function validate(InputInterface $input)
    {
        foreach (['file1', 'file2'] as $arg) {
            if (!is_readable($input->getArgument($arg))) {
                throw new \InvalidArgumentException("File {$input->getArgument($arg)} not found or unreadable");
            }
        }

        $this->validateSamplingSize($input->getOption('size'));

        if (!in_array($input->getOption('format'), ['percent', 'ratio', 'float', 'integer', 'int'])) {
            throw new \InvalidArgumentException('Invalid format');
        }
    }

    protected function getHammingDistance(string $hash1, string $hash2): int
    {
        $size = strlen($hash1);

        for ($dist = 0, $i = 0; $i < $size; ++$i) {
            if ($hash1[$i] != $hash2[$i]) {
                ++$dist;
            }
        }

        return $dist;
    }

    protected function display(OutputInterface $output, string $format, int $distance, float $similarity)
    {
        switch ($format) {
            default:
            case 'percent':
                $output->writeln(round($similarity * 100).'%');
                break;

            case 'ratio':
            case 'float':
                $output->writeln($similarity);
                break;

            case 'integer':
            case 'int':
                $output->writeln($distance);
                break;
        }
    }
}
