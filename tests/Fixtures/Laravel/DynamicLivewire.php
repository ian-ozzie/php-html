<?php

declare(strict_types=1);

namespace Ozzie\Html\Tests\Fixtures\Laravel;

use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Ozzie\Html\Laravel\Livewire;
use Stringable;

final class DynamicLivewire extends Livewire
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(
        array $params = [],
        private View|Stringable|null $provided_view = null,
    ) {
        parent::__construct($params);
    }

    public static function alias(): string
    {
        return 'dynamic-livewire';
    }

    public function escaped(string $value): string
    {
        return $this->escape($value);
    }

    public function view(): View|Stringable
    {
        return $this->provided_view ?? new HtmlString('<div>default</div>');
    }
}
