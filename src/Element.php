<?php

declare(strict_types=1);

namespace Ozzie\Html;

use InvalidArgumentException;
use Stringable;

class Element extends Component
{
    /**
     * Known void tags
     * ref: https://developer.mozilla.org/en-US/docs/Glossary/Void_element
     *
     * @const array<string>
     */
    protected const array VOID_TAGS = [
        'area',
        'base',
        'br',
        'col',
        'embed',
        'hr',
        'img',
        'input',
        'link',
        'meta',
        'source',
        'track',
        'wbr',
    ];

    /**
     * Element controls
     *
     * @var array<string, bool>
     */
    protected array $controls = [
        'void' => false,
        'render_empty' => true,
    ];

    /**
     * Element attributes
     *
     * @var array<string, mixed>
     */
    protected array $attributes = [];

    /**
     * Element classes
     *
     * @var array<int, string>
     */
    protected array $classes = [];

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        protected readonly string $tag,
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

    public function get_control(string $key): bool
    {
        if (isset($this->controls[$key]) === false) {
            throw new InvalidArgumentException(
                $this::class.'->get_control(): unknown control key "'.$key.'"',
            );
        }

        return $this->controls[$key];
    }

    /**
     * @param array<mixed> $controls
     */
    public function sanitise_controls(array $controls): static
    {
        foreach ($controls as $key => $val) {
            if (is_string($key) === true && is_bool($val) === true && isset($this->controls[$key]) === true) {
                $this->set_control($key, $val);
            }
        }

        return $this;
    }

    public function set_control(string $key, bool $val): static
    {
        if (isset($this->controls[$key]) === false) {
            throw new InvalidArgumentException(
                $this::class.'->set_control(): unknown control key "'.$key.'"',
            );
        }

        $this->controls[$key] = $val;

        return $this;
    }

    /**
     * @param array<string, bool> $controls
     */
    public function set_controls(array $controls): static
    {
        foreach ($controls as $key => $val) {
            if (isset($this->controls[$key]) === false) {
                throw new InvalidArgumentException(
                    $this::class.'->set_controls(): unknown control key "'.$key.'"',
                );
            }

            $this->controls[$key] = $val;
        }

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function get_classes(): array
    {
        return $this->classes;
    }

    public function has_class(string $class): bool
    {
        return in_array($class, $this->classes, true);
    }

    public function add_class(string $class): static
    {
        if (empty($class) === false) {
            $this->classes = array_unique(array_merge($this->classes, [$class]));
        }

        return $this;
    }

    /**
     * @param string|array<int, string> $classes
     */
    public function add_classes(string|array $classes): static
    {
        if (is_string($classes) === true) {
            $classes = explode(' ', $classes);
        }

        $this->classes = array_unique(array_merge($this->classes, array_filter($classes, fn (string $v): bool => $v !== '')));

        return $this;
    }

    public function sanitise_classes(mixed $val): static
    {
        if (is_array($val)) {
            $classes = array_values(array_map(
                fn ($v): string => (is_scalar($v) || $v instanceof Stringable) ? (string) $v : '',
                $val,
            ));

            return $this->add_classes($classes);
        }

        return $this->add_class((is_scalar($val) || $val instanceof Stringable) ? (string) $val : '');
    }

    /**
     * @param string|array<int, string> $classes
     */
    public function set_classes(string|array $classes): static
    {
        if (is_string($classes) === true) {
            $classes = explode(' ', $classes);
        }

        $this->classes = array_unique(array_filter($classes, fn (string $v): bool => $v !== ''));

        return $this;
    }

    public function add_attribute(string $key, mixed $val): static
    {
        if ($key === 'class') {
            return $this->sanitise_classes($val);
        }

        if ($key === '_controls') {
            if (is_array($val) === true) {
                return $this->sanitise_controls($val);
            }

            return $this;
        }

        $this->attributes[$key] = $val;

        return $this;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function add_attributes(array $attributes): static
    {
        foreach ($attributes as $key => $val) {
            $this->add_attribute($key, $val);
        }

        return $this;
    }

    public function has_attribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function get_attribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function set_attributes(array $attributes): static
    {
        $this->attributes = [];
        $this->classes = [];
        $this->add_attributes($attributes);

        return $this;
    }

    public function render(): string
    {
        if ($this->get_control('void') === true) {
            return $this->render_open();
        }

        $content = parent::render();
        if (empty($content) === true && $this->get_control('render_empty') === false) {
            return '';
        }

        return $this->render_open().$content.$this->render_close();
    }

    public function render_open(): string
    {
        $attributes = $this->attributes;
        if (empty($this->classes) === false) {
            $attributes = array_merge($attributes, ['class' => implode(' ', $this->classes)]);
        }

        ksort($attributes);
        $attrs = '';
        foreach ($attributes as $key => $val) {
            if (is_null($val) === true || $val === '') {
                $attrs .= sprintf(' %s', $key);
            } else {
                if (is_scalar($val) === false && $val instanceof Stringable === false) {
                    throw new InvalidArgumentException(
                        $this::class.'->render_open(): attribute "'.$key.'" value ('.gettype($val).') must be scalar or Stringable',
                    );
                }

                $attrs .= sprintf(' %s="%s"', $key, htmlspecialchars((string) $val, ENT_QUOTES));
            }
        }

        return '<'.$this->tag.$attrs.'>';
    }

    public function render_close(): string
    {
        return '</'.$this->tag.'>';
    }
}
