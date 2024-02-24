<?php

namespace App;

use App\Trait\TimeZoneTrait;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Twig\Environment;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    
    use TimeZoneTrait;

    // décallage horaire
    public function __construct(string $environment, bool $debug)
    {
        $this->changeTimeZone("Europe/Paris");

        parent::__construct($environment, $debug);
    }
}
