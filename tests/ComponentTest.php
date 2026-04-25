<?php

declare(strict_types=1);

use Ozzie\Html\Component;
use Ozzie\Html\Element;

test('render', function () {
    $component = new Component;
    expect($component->render())->toBe('');
});

test('to_string', function () {
    $component = new Component;
    expect((string) $component)->toBe('');
});

test('add_content', function () {
    $component = new Component;
    $component
        ->add_content('foo')
        ->add_content('bar');

    expect((string) $component)->toBe('foobar');
});

test('add_content_with_append', function () {
    $component = new Component;
    $component
        ->add_content('foo')
        ->add_content('bar', true);

    expect((string) $component)->toBe('foobar');
});

test('add_content_with_prepend', function () {
    $component = new Component;
    $component
        ->add_content('foo')
        ->add_content('bar', false);

    expect((string) $component)->toBe('barfoo');
});

test('content_append', function () {
    $component = new Component;
    $component
        ->add_content('foo')
        ->content_append('bar');

    expect((string) $component)->toBe('foobar');
});

test('content_prepend', function () {
    $component = new Component;
    $component
        ->add_content('foo')
        ->content_prepend('bar');

    expect((string) $component)->toBe('barfoo');
});

test('content_set', function () {
    $component = new Component;
    $component
        ->add_content('foo')
        ->content_set('bar');

    expect((string) $component)->toBe('bar');
});

test('content_set_array', function () {
    $component = new Component;
    $component->content_set(['foo', 'bar', 'baz']);
    expect((string) $component)->toBe('foobarbaz');
});

test('render_mixed_null', function () {
    $component = new Component;
    expect($component->render_mixed(null))->toBe('');
});

test('render_mixed_string', function () {
    $component = new Component;
    expect($component->render_mixed('foo'))->toBe('foo');
});

test('render_mixed_int', function () {
    $component = new Component;
    expect($component->render_mixed(42))->toBe('42');
});

test('render_mixed_float', function () {
    $component = new Component;
    expect($component->render_mixed(3.14))->toBe('3.14');
});

test('render_mixed_array', function () {
    $component = new Component;
    expect($component->render_mixed(['foo', 'bar', 'baz']))->toBe('foobarbaz');
});

test('render_mixed_object', function () {
    $component = new Component;
    expect(fn () => $component->render_mixed(new stdClass))->toThrow(InvalidArgumentException::class);
});

test('render_mixed_object_stringable', function () {
    $component = new Component;
    $stringable = new class implements Stringable
    {
        public function __toString(): string
        {
            return 'foo';
        }
    };
    expect($component->render_mixed($stringable))->toBe('foo');
});

test('render_mixed_bool_throws', function () {
    $component = new Component;
    expect(fn () => $component->render_mixed(true))->toThrow(InvalidArgumentException::class);
});

test('element', function () {
    $element = Component::Element('foo');
    expect($element)->toBeInstanceOf(Element::class);
});

test('add_element', function () {
    $component = new Component;
    $result = $component->add_element('foo');
    expect($result)->toBe($component);
    expect((string) $component)->toBe((string) new Element('foo'));
});

test('new_element', function () {
    $component = new Component;
    $result = $component->new_element('foo');
    expect($result)->toBeInstanceOf(Element::class);
    expect((string) $component)->toBe((string) new Element('foo'));
});

test('chaining_functions', function () {
    $component = new Component;

    expect($component->add_content('foo'))->toBe($component);
    expect($component->content_append('foo'))->toBe($component);
    expect($component->content_prepend('foo'))->toBe($component);
    expect($component->content_set('foo'))->toBe($component);

    expect($component->add_element('foo'))->toBe($component);
});
