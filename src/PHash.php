<?php

namespace Bdelespierre\PhpPhash;

use Intervention\Image\ImageManager;

class PHash
{
    protected $manager;

    public function __construct(ImageManager $manager)
    {
        $this->manager = $manager;
    }

    public function hash(\SplFileInfo $file, int $size = 8): string
    {
        $image = $this->manager->make($file)
            ->resize($size, $size)
            ->greyscale();

        $sum = 0;
        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $sum += $image->pickColor($x, $y, 'array')[0];
            }
        }

        $mean = $sum / ($size ** 2);
        $bits = "";
        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $bits .= $image->pickColor($x, $y, 'array')[0] > $mean ? 1 : 0;
            }
        }

        return $bits;
    }
}
