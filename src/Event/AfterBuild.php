<?php

namespace Jug\Event;

use Jug\Site;
use Symfony\Contracts\EventDispatcher\Event;

class AfterBuild extends Event
{
    public const NAME =  'after.build';

    protected Site $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function getSite(): Site
    {
        return $this->site;
    }
}
