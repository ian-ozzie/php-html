<?php

declare(strict_types=1);

namespace Ozzie\Html;

use Stringable;

interface ComponentInterface extends Stringable
{
    public function add_content(mixed $content): static;

    public function prepend_content(mixed $content): static;

    public function set_content(mixed $content): static;

    public function render(): string;

    /**
     * @param array<string, mixed> $attributes
     */
    public static function element(string $tag, array $attributes = [], mixed $content = null): ElementInterface;

    /**
     * @param array<string, mixed> $attributes
     */
    public function add_element(string $tag, array $attributes = [], mixed $content = null): static;

    /**
     * @param array<string, mixed> $attributes
     */
    public function new_element(string $tag, array $attributes = [], mixed $content = null): ElementInterface;
}
