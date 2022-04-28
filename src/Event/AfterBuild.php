<?php

namespace Jug\Event;

use Jug\Generator;
use Symfony\Contracts\EventDispatcher\Event;

class AfterBuild extends Event
{
    public const NAME =  'after.build';

    protected Generator $site;

    public function __construct(Generator $site)
    {
        $this->site = $site;
    }

    public function getSite(): Generator
    {
        return $this->site;
    }
}
