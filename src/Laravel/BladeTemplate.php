<?php

declare(strict_types=1);

namespace Ozzie\Html\Laravel;

use Illuminate\Support\Facades\Blade as BladeFacade;
use Stringable;

/**
 * Explicitly marks content as a Blade template to be compiled at render time.
 *
 * Templates should be static to avoid constant recompilation.
 */
final class BladeTemplate implements Stringable
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        protected string $template,
        protected array $data = [],
        protected bool $delete_cached_view = false,
    ) {}

    public function render(): string
    {
        return BladeFacade::render($this->template, $this->data, deleteCachedView: $this->delete_cached_view);
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
