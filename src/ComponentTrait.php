<?php

declare(strict_types=1);

namespace Ozzie\Html;

use InvalidArgumentException;
use Stringable;

trait ComponentTrait
{
    /**
     * @var array<int, mixed>
     */
    protected array $render_content = [];

    public bool $cache_render = false;

    protected ?string $cached_output = null;

    public function __toString(): string
    {
        return $this->render();
    }

    public function add_content(mixed $content): static
    {
        $this->render_content[] = $content;

        return $this;
    }

    public function prepend_content(mixed $content): static
    {
        array_unshift($this->render_content, $content);

        return $this;
    }

    public function set_content(mixed $content): static
    {
        $this->render_content = is_array($content) === true ? array_values($content) : [$content];

        return $this;
    }

    protected function render_base(): string
    {
        if ($this->cache_render === true) {
            return $this->cached_output ??= $this->render_mixed($this->render_content);
        }

        return $this->render_mixed($this->render_content);
    }

    public function render(): string
    {
        return $this->render_base();
    }

    protected function render_mixed(mixed $var): string
    {
        return match (true) {
            is_null($var) => '',
            is_string($var) => $var,
            is_int($var), is_float($var) => (string) $var,
            is_array($var) => implode('', array_map($this->render_mixed(...), $var)),
            $var instanceof Stringable => (string) $var,
            is_object($var) => throw new InvalidArgumentException(
                $this::class.'->render_mixed($var): $var object ('.$var::class.') must implement Stringable',
            ),
            default => throw new InvalidArgumentException(
                $this::class.'->render_mixed($var): $var type ('.gettype($var).') is unhandled',
            ),
        };
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public static function element(string $tag, array $attributes = [], mixed $content = null): ElementInterface
    {
        return new Element($tag, $attributes, $content);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function add_element(string $tag, array $attributes = [], mixed $content = null): static
    {
        $this->add_content(new Element($tag, $attributes, $content));

        return $this;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function new_element(string $tag, array $attributes = [], mixed $content = null): ElementInterface
    {
        $element = new Element($tag, $attributes, $content);
        $this->add_content($element);

        return $element;
    }
}
