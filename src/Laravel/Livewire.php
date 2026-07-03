<?php

declare(strict_types=1);

namespace Ozzie\Html\Laravel;

use Illuminate\Contracts\View\View;
use Livewire\Component as LivewireComponent;
use Livewire\Livewire as LivewireFacade;
use Stringable;

abstract class Livewire extends LivewireComponent implements Stringable
{
    protected View|Stringable|null $resolved_view = null;

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
     * Make dynamic text safe to display as HTML content.
     */
    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function render(): View|string
    {
        // Explicitly fetch a fresh view.
        $view = $this->view();
        if ($view instanceof View) {
            return $view;
        }

        // Stash Stringable so it doesn't get rebuilt.
        $this->resolved_view = $view;

        // A constant template compiles once rather than passing dynamic output.
        return '{!! $this->pull_view() !!}';
    }

    protected function pull_view(): View|Stringable
    {
        $view = $this->resolved_view ?? $this->view();
        $this->resolved_view = null;

        return $view;
    }

    abstract public function view(): View|Stringable;
}
