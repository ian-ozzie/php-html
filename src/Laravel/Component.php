<?php

declare(strict_types=1);

namespace Ozzie\Html\Laravel;

use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\Component as IlluminateComponent;
use Ozzie\Html\ComponentTrait;

abstract class Component extends IlluminateComponent
{
    use ComponentTrait {
        render as render_html;
    }

    public function resolveView(): View
    {
        $view = $this->createBladeViewFromString($this->factory(), <<<'BLADE'
{!! $__ozzieHtmlComponent->renderBlade(get_defined_vars())->toHtml() !!}
BLADE);

        return $this->factory()->make($view, [
            '__ozzieHtmlComponent' => $this,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function renderBlade(array $data = []): HtmlString
    {
        $component = clone $this;
        $component->apply_blade_data($data);

        return new HtmlString($component->render_html());
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
