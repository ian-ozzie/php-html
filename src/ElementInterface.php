<?php

declare(strict_types=1);

namespace Ozzie\Html;

interface ElementInterface extends ComponentInterface
{
    public function get_tag(): string;

    public function get_control(string $key): bool;

    public function set_control(string $key, bool $val): static;

    /**
     * @param array<string, bool> $controls
     */
    public function set_controls(array $controls): static;

    /**
     * @return array<int, string>
     */
    public function get_classes(): array;

    public function has_class(string $class): bool;

    public function add_class(string $class): static;

    /**
     * @param string|array<int, string> $classes
     */
    public function add_classes(string|array $classes): static;

    /**
     * @param string|array<int, string> $classes
     */
    public function set_classes(string|array $classes): static;

    public function add_attribute(string $key, mixed $val): static;

    /**
     * @param array<string, mixed> $attributes
     */
    public function add_attributes(array $attributes): static;

    public function has_attribute(string $key): bool;

    public function get_attribute(string $key): mixed;

    /**
     * @param array<string, mixed> $attributes
     */
    public function set_attributes(array $attributes): static;
}
