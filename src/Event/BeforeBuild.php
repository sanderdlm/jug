<?php

declare(strict_types=1);

namespace Jug\Event;

use Jug\Domain\Site;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeBuild extends Event
{
    public const NAME =  'before.build';

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
