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
    use ValidatesSamplingSize;

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
        try {
            $this->validate($input);
        } catch (\InvalidArgumentException $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");

            return Command::FAILURE;
        }

        $bits = $this->phash->hash(
            new \SplFileInfo($input->getArgument('file')),
            $input->getOption('size')
        );

        $this->display($output, $input->getOption('format'), $bits, $input->getOption('size'));

        return Command::SUCCESS;
    }

    protected function validate(InputInterface $input)
    {
        if (!is_readable($input->getArgument('file'))) {
            throw new \InvalidArgumentException("File {$input->getArgument('file')} not found or unreadable");
        }

        $this->validateSamplingSize($input->getOption('size'));

        if (!in_array($input->getOption('format'), ['hex', 'bin', 'ascii'])) {
            throw new \InvalidArgumentException('Invalid format');
        }
    }

    protected function display(OutputInterface $output, string $format, string $bits, int $size)
    {
        switch ($format) {
            default:
            case 'bin':
                $output->writeln($bits);
                break;

            case 'hex':
                $output->writeln(
                    strlen($bits) <= (PHP_INT_SIZE * 8)
                        ? base_convert($bits, 2, 16)
                        : gmp_strval(gmp_init($bits, 2), 16)
                );
                break;

            case 'ascii':
                $output->writeln(
                    implode("\n", array_map(
                        function ($str) {
                            return preg_replace('/(1+)/', '<fg=green>$1</>', $str);
                        },
                        str_split($bits, $size)
                    ))
                );
                break;
        }
    }
}
