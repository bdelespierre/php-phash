<?php

namespace Bdelespierre\PhpPhash\Command;

trait ValidatesSamplingSize
{
    public function validateSamplingSize(int $size)
    {
        if ($size < 8) {
            throw new \InvalidArgumentException("Sampling size must be greater or equal to 8");
        }

        if ($size ** 2 > PHP_INT_SIZE * 8 && ! function_exists('gmp_init')) {
            throw new \InvalidArgumentException("Sampling size too large: reduce it or install PHP-GMP extension");
        }
    }
}
