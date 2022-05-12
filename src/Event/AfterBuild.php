<?php

declare(strict_types=1);

namespace Jug\Event;

use Jug\Domain\Site;
use Symfony\Contracts\EventDispatcher\Event;

class AfterBuild extends Event
{
    public const NAME =  'after.build';

    public function __construct(
        public readonly Site $site
    ) {
    }
}
