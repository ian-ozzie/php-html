<?php

declare(strict_types=1);

namespace Ozzie\Html\Laravel;

use Illuminate\Contracts\View\View;
use Livewire\Component as LivewireComponent;
use Livewire\Livewire as LivewireFacade;
use Stringable;

abstract class Livewire extends LivewireComponent implements Stringable
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(
        protected array $params = [],
    ) {}

    abstract public static function alias(): string;

    public function to_livewire(): string
    {
        return LivewireFacade::mount(static::alias(), $this->params);
    }

    /**
     * Mounts the Livewire component letting it handle the rendering, rather than rendering directly.
     */
    public function __toString(): string
    {
        return $this->to_livewire();
    }

    /**
     * Make dynamic content safe to display. Escapes HTML and Blade/Livewire syntax since output is re-compiled through Blade.
     */
    protected function escape(string $value): string
    {
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return str_replace(
            ['@', '{{', '}}', '{!!', '!!}'],
            ['&#64;', '&#123;&#123;', '&#125;&#125;', '&#123;!!', '!!&#125;'],
            $value,
        );
    }

    public function render(): View|string
    {
        $view = $this->view();

        // Avoids casting the View.
        return $view instanceof View ? $view : (string) $view;
    }

    abstract public function view(): View|Stringable;
}
