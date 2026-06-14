<?php

declare(strict_types=1);

namespace Ozzie\Html\Tests;

use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase;
use Ozzie\Html\Tests\Fixtures\Laravel\BaseSpan;
use Ozzie\Html\Tests\Fixtures\Laravel\DynamicCounter;
use Ozzie\Html\Tests\Fixtures\Laravel\Span;
use Ozzie\Html\Tests\Fixtures\Laravel\Text;

abstract class LaravelTestCase extends TestCase
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        Blade::component(Text::class, 'text');
        Blade::component(Span::class, 'span');
        Blade::component(BaseSpan::class, 'base-span');
        Livewire::component(DynamicCounter::alias(), DynamicCounter::class);
    }
}
