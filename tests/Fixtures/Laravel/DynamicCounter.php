<?php

declare(strict_types=1);

namespace Ozzie\Html\Tests\Fixtures\Laravel;

use Illuminate\Contracts\View\View;
use Ozzie\Html\Element;
use Ozzie\Html\Laravel\Livewire;
use Stringable;

final class DynamicCounter extends Livewire
{
    public int $count = 0;

    public string $label = 'Count';

    public static function alias(): string
    {
        return 'dynamic-counter';
    }

    public function increment(): void
    {
        $this->count++;
    }

    public function mount(string $label = 'Count'): void
    {
        $this->label = $label;
    }

    public function view(): View|Stringable
    {
        return new Element('button', [
            'type' => 'button',
            'wire:click' => 'increment',
        ], $this->escape($this->label).': '.$this->escape((string) $this->count));
    }
}
