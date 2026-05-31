<?php

declare(strict_types=1);

namespace Ozzie\Html\Tests\Fixtures\Laravel;

use Ozzie\Html\Laravel\Element;

final class BaseSpan extends Element
{
    public function __construct()
    {
        parent::__construct('span', ['class' => 'base']);
    }
}
