<?php

namespace Bdelespierre\PhpPhash\Command;

use Bdelespierre\PhpPhash\PHash;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Generate extends Command
{
    protected $phash;

    public function __construct(PHash $phash)
    {
        $this->phash = $phash;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('generate')
            ->setDescription('Generates the pHash of given image')
            ->addArgument('file', InputArgument::REQUIRED, 'Pass the file.')
            ->addOption('size', 's', InputOption::VALUE_REQUIRED, 'Sampling size.', 8)
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Output format [hex,bin,ascii].', 'hex');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $size = $input->getOption('size');

        if (! is_readable($file)) {
            $output->writeln("<error>File {$file} not found or unreadable</error>");

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

        if (! in_array($input->getOption('format'), ['hex', 'bin', 'ascii'])) {
            $output->writeln("<error>Invalid format</error>");

            return Command::FAILURE;
        }

        $bits = $this->phash->hash(new \SplFileInfo($file), $size);

        $output->writeln($this->format(
            $bits,
            $input->getOption('format'),
            $input->getOption('size')
        ));

        return Command::SUCCESS;
    }

    protected function format(string $bits, string $format, int $size): string
    {
        switch ($format) {
            default:
            case 'bin':
                return $bits;

            case 'hex':
                return strlen($bits) <= (PHP_INT_SIZE * 8)
                    ? base_convert($bits, 2, 16)
                    : gmp_strval(gmp_init($bits, 2), 16);

            case 'ascii':
                return $this->ascii($bits, $size);
        }
    }

    protected function ascii(string $bits, int $size): string
    {
        return implode("\n", array_map(
            function ($str) {
                return preg_replace('/(1+)/', '<fg=green>$1</>', $str);
            },
            str_split($bits, $size)
        ));
    }
}
