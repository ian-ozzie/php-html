<?php

declare(strict_types=1);

namespace Ozzie\Html\Laravel;

use Illuminate\View\ComponentAttributeBag;
use InvalidArgumentException;
use Ozzie\Html\ElementInterface;
use Ozzie\Html\ElementTrait;
use Stringable;

abstract class Element extends Component implements ElementInterface
{
    use ElementTrait {
        render as render_html;
        prepare_attributes as prepare_html_attributes;
    }

    protected ?ComponentAttributeBag $blade_attributes = null;

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        public readonly string $tag,
        array $attributes = [],
        mixed $content = null,
    ) {
        if (in_array($this->tag, static::VOID_TAGS) === true) {
            $this->controls['void'] = true;
        }

        $this->set_attributes($attributes);
        if (isset($content) === true) {
            $this->add_content($content);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function apply_blade_data(array $data): void
    {
        if (($data['attributes'] ?? null) instanceof ComponentAttributeBag) {
            $this->blade_attributes = $data['attributes'];
            $classes = $this->blade_attributes->get('class');
            if (is_array($classes) === true) {
                $classes = $this->normalise_blade_classes($classes);
            }

            $this->sanitise_classes($classes);
        }

        parent::apply_blade_data($data);
    }

    /**
     * @param array<mixed> $classes
     */
    private function normalise_blade_classes(array $classes): string
    {
        $normalised = [];

        foreach ($classes as $key => $value) {
            if (is_string($key) === true) {
                if ((bool) $value === true) {
                    $normalised[] = $key;
                }

                continue;
            }

            if ((is_scalar($value) === true && is_bool($value) === false) || $value instanceof Stringable) {
                $normalised[] = (string) $value;
            }
        }

        return implode(' ', $normalised);
    }

    /**
     * @return array<string, string|true>
     */
    protected function prepare_attributes(): array
    {
        $attributes = $this->prepare_html_attributes();
        if ($this->blade_attributes === null) {
            return $attributes;
        }

        foreach ($this->blade_attributes->except('class') as $key => $value) {
            if (is_string($key) === false) {
                throw new InvalidArgumentException(
                    $this::class.'->prepare_attributes(): attribute name ('.gettype($key).') must be a string',
                );
            }

            $sanitised = $this->sanitise_attribute($key, $value);
            if ($sanitised === null) {
                continue;
            }

            $attributes[$key] = $sanitised;
        }

        return $attributes;
    }
}
