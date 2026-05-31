<?php

declare(strict_types=1);

namespace Ozzie\Html\Tests;

use Illuminate\Support\Facades\Blade;
use Orchestra\Testbench\TestCase;
use Ozzie\Html\Tests\Fixtures\Laravel\BaseSpan;
use Ozzie\Html\Tests\Fixtures\Laravel\Span;
use Ozzie\Html\Tests\Fixtures\Laravel\Text;

abstract class LaravelTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Blade::component(Text::class, 'text');
        Blade::component(Span::class, 'span');
        Blade::component(BaseSpan::class, 'base-span');
    }
}
