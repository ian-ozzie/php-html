<?php declare(strict_types = 1);

namespace Ozzie\Html;

use InvalidArgumentException;
use Stringable;

class Component implements Stringable {

    /**
     * Component content
     *
     * @var array<int, mixed>
     */
    protected array $render_content = [];

    public function __toString(): string
    {
        return $this->render();
    }

    public function add_content(mixed $content, bool $append=true): static
    {
        $append === true ? $this->content_append($content) : $this->content_prepend($content);
        return $this;
    }

    public function content_append(mixed $content): static
    {
        $this->render_content[] = $content;
        return $this;
    }

    public function content_prepend(mixed $content): static
    {
        array_unshift($this->render_content, $content);
        return $this;
    }

    public function content_set(mixed $content): static
    {
        $this->render_content = is_array($content) === true ? $content : [$content];
        return $this;
    }

    public function render(): string
    {
        return $this->render_mixed($this->render_content);
    }

    public function render_mixed(mixed $var): string
    {
        return match (true) {
            is_null($var) => '',
            is_string($var) => $var,
            is_int($var), is_float($var) => (string) $var,
            is_array($var) => implode('', array_map($this->render_mixed(...), $var)),
            $var instanceof Stringable => (string) $var,
            is_object($var) => throw new InvalidArgumentException(
                $this::class.'->render_mixed($var): $var object ('.$var::class.') must implement Stringable'
            ),
            default => throw new InvalidArgumentException(
                $this::class.'->render_mixed($var): $var type ('.gettype($var).') is unhandled'
            ),
        };
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public static function element(string $tag, array $attributes=[], mixed $content=null): Element
    {
        return new Element($tag, $attributes, $content);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function add_element(string $tag, array $attributes=[], mixed $content=null): static
    {
        $this->add_content(new Element($tag, $attributes, $content));
        return $this;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function new_element(string $tag, array $attributes=[], mixed $content=null): Element
    {
        $element = new Element($tag, $attributes, $content);
        $this->add_content($element);
        return $element;
    }

}
