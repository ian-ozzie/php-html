<?php

declare(strict_types=1);

namespace Ozzie\Html\Laravel;

use Closure;
use Illuminate\Support\HtmlString;
use Illuminate\View\Component as IlluminateComponent;
use Ozzie\Html\ComponentTrait;

abstract class Component extends IlluminateComponent
{
    use ComponentTrait {
        render as render_html;
    }

    public function resolveView(): Closure
    {
        return function (array $data = []): HtmlString {
            /** @var array<string, mixed> $data */
            $component = clone $this;
            $component->apply_blade_data($data);

            return new HtmlString($component->render_html());
        };
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function apply_blade_data(array $data): void
    {
        if (isset($data['slot']) === true) {
            $this->add_content($data['slot']);
        }
    }
}
