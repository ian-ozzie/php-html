<?php declare(strict_types = 1);

namespace Ozzie\Html\Element;

use Ozzie\Html\Element;

class Html extends Element {

    public Element $head;

    public Element $title;

    public Element $body;

    public Element $noscript;

    public string $doctype = 'html';

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes=[])
    {
        parent::__construct('html', $attributes);

        // Manually construct and set content, avoids retargeted methods below.
        $this->head = new Element('head', ['_controls' => ['render_empty' => false]]);
        $this->body = new Element('body', []);
        $this->render_content = [$this->head, $this->body];

        $this->title    = $this->head->new_element('title', ['_controls' => ['render_empty' => false]]);
        $this->noscript = $this->body->new_element('noscript', ['_controls' => ['render_empty' => false]]);
    }

    public function add_content(mixed $content, bool $append=true): static
    {
        $this->body->add_content($content, $append);
        return $this;
    }

    public function content_append(mixed $content): static
    {
        $this->body->content_append($content);
        return $this;
    }

    public function content_prepend(mixed $content): static
    {
        $this->body->content_prepend($content);
        return $this;
    }

    public function content_set(mixed $content): static
    {
        $this->body->content_set($content);
        return $this;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function add_element(string $tag, array $attributes=[], mixed $content=null): static
    {
        $this->body->add_element($tag, $attributes, $content);
        return $this;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function new_element(string $tag, array $attributes=[], mixed $content=null): Element
    {
        return $this->body->new_element($tag, $attributes, $content);
    }

    public function render(): string
    {
        return sprintf('<!DOCTYPE %s>%s', $this->doctype, parent::render());
    }

}
