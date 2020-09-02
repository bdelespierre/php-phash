<?php

namespace Tests\Command;

use Bdelespierre\PhpPhash\Command\Compare;
use Bdelespierre\PhpPhash\Command\Generate;
use Bdelespierre\PhpPhash\PHash;
use Intervention\Image\ImageManager;
use Symfony\Component\Console\Application;

trait CreatesApplication
{
    public function createApplication(): Application
    {
        $application = new Application();

        $manager = new ImageManager(['driver' => 'imagick']);
        $phash = new PHash($manager);

        $application->add(new Generate($phash));
        $application->add(new Compare($phash));

        return $application;
    }
}
