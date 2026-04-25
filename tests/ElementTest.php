<?php

declare(strict_types=1);

use Ozzie\Html\Element;

test('construct', function () {
    $element = new Element('span');
    expect((string) $element)->toBe('<span></span>');
});

test('element_with_attribute', function () {
    $element = new Element('span', ['hello' => 'world']);
    expect((string) $element)->toBe('<span hello="world"></span>');
});

test('construct_with_content', function () {
    $element = new Element('span', content: 'foo');
    expect((string) $element)->toBe('<span>foo</span>');
});

test('construct_with_attribute_and_content', function () {
    $element = new Element('span', ['hello' => 'world'], 'foo');
    expect((string) $element)->toBe('<span hello="world">foo</span>');
});

test('get_control', function () {
    $element = new Element('span');
    expect($element->get_control('void'))->toBe(false);
    expect($element->get_control('render_empty'))->toBe(true);
});

test('get_control_unknown_key_throws', function () {
    $element = new Element('span');
    expect(fn () => $element->get_control('unknown'))->toThrow(InvalidArgumentException::class);
});

test('get_control_with_constructor', function () {
    $element = new Element('span', ['_controls' => ['void' => true]]);
    expect($element->get_control('void'))->toBe(true);
    expect($element->get_control('render_empty'))->toBe(true);
});

test('set_control', function () {
    $element = new Element('span');
    $element->set_control('void', true);
    expect($element->get_control('void'))->toBe(true);
    expect($element->get_control('render_empty'))->toBe(true);
});

test('set_controls', function () {
    $element = new Element('span');
    $element->set_controls(['void' => true, 'render_empty' => false]);
    expect($element->get_control('void'))->toBe(true);
    expect($element->get_control('render_empty'))->toBe(false);
});

test('set_control_unknown_key_throws', function () {
    $element = new Element('span');
    expect(fn () => $element->set_control('unknown', true))->toThrow(InvalidArgumentException::class);
});

test('set_controls_with_unknown_key_throws', function () {
    $element = new Element('span');
    expect(fn () => $element->set_controls(['void' => true, 'unknown_key' => true]))->toThrow(InvalidArgumentException::class);
});

test('get_classes', function () {
    $element = new Element('span', ['class' => 'foo']);
    $element->add_class('bar');
    expect($element->get_classes())->toBe(['foo', 'bar']);
});

test('has_class', function () {
    $element = new Element('span', ['class' => 'foo']);
    expect($element->has_class('foo'))->toBeTrue();
    expect($element->has_class('bar'))->toBeFalse();
});

test('add_class', function () {
    $element = new Element('span');
    $element->add_class('foo');
    expect((string) $element)->toBe('<span class="foo"></span>');
});

test('add_class_with_constructor', function () {
    $element = new Element('span', ['class' => 'foo']);
    expect((string) $element)->toBe('<span class="foo"></span>');
});

test('add_classes', function () {
    $element = new Element('span');
    $element->add_classes(['foo', 'bar']);
    expect((string) $element)->toBe('<span class="foo bar"></span>');
});

test('add_classes_with_string', function () {
    $element = new Element('span');
    $element->add_classes('foo bar');
    expect((string) $element)->toBe('<span class="foo bar"></span>');
});

test('add_classes_with_constructor', function () {
    $element = new Element('span', ['class' => ['foo', 'bar']]);
    expect((string) $element)->toBe('<span class="foo bar"></span>');
});

test('set_classes', function () {
    $element = new Element('span', ['class' => 'baz']);
    $element->set_classes(['foo', 'bar']);
    expect((string) $element)->toBe('<span class="foo bar"></span>');
});

test('set_classes_with_string', function () {
    $element = new Element('span', ['class' => 'baz']);
    $element->set_classes('foo bar');
    expect((string) $element)->toBe('<span class="foo bar"></span>');
});

test('set_classes_deduplicates', function () {
    $element = new Element('span');
    $element->set_classes(['foo', 'bar', 'foo']);
    expect((string) $element)->toBe('<span class="foo bar"></span>');
});

test('set_classes_filters_empty_strings', function () {
    $element = new Element('span');
    $element->set_classes(['foo', '', 'bar']);
    expect((string) $element)->toBe('<span class="foo bar"></span>');
});

test('add_classes_filters_empty_strings', function () {
    $element = new Element('span');
    $element->add_classes(['foo', '', 'bar']);
    expect((string) $element)->toBe('<span class="foo bar"></span>');
});

test('add_attribute', function () {
    $element = new Element('span');
    $element->add_attribute('hello', 'world');
    expect((string) $element)->toBe('<span hello="world"></span>');
});

test('add_attribute_null_empty', function () {
    $element = new Element('span');
    $element->add_attribute('hello', null);
    $element->add_attribute('world', '');
    expect((string) $element)->toBe('<span hello world></span>');
});

test('add_attributes', function () {
    $element = new Element('span');
    $element->add_attributes(['foo' => '', 'hello' => 'world']);
    expect((string) $element)->toBe('<span foo hello="world"></span>');
});

test('add_attributes_order', function () {
    $element = new Element('span');
    $element->add_attributes(['hello' => 'world', 'foo' => '']);
    expect((string) $element)->toBe('<span foo hello="world"></span>');
});

test('add_attribute_controls_non_array', function () {
    $element = new Element('span');
    $result = $element->add_attribute('_controls', 'invalid');
    expect($result)->toBe($element);
    expect((string) $element)->toBe('<span></span>');
});

test('get_attributes', function () {
    $element = new Element('span');
    $element->add_attributes(['foo' => '', 'hello' => 'world']);
    expect($element->get_attribute('foo'))->toBe('');
    expect($element->get_attribute('hello'))->toBe('world');

    expect($element->get_attribute('unknown'))->toBeNull();
});

test('set_attributes', function () {
    $element = new Element('span', ['foo' => 'bar']);
    expect((string) $element)->toBe('<span foo="bar"></span>');
    $element->set_attributes(['hello' => 'world']);
    expect((string) $element)->toBe('<span hello="world"></span>');
});

test('set_attributes_resets_classes', function () {
    $element = new Element('span', ['class' => 'foo']);
    $element->set_attributes(['class' => 'bar']);
    expect((string) $element)->toBe('<span class="bar"></span>');
});

test('render', function () {
    $element = new Element('span');
    expect($element->render())->toBe('<span></span>');
});

test('render_auto_void', function () {
    $element = new Element('br');
    expect($element->render())->toBe('<br>');
});

test('render_manual_void', function () {
    $element = new Element('span', ['_controls' => ['void' => true]]);
    expect($element->render())->toBe('<span>');
});

test('render_when_empty', function () {
    $element = new Element('span');
    expect($element->render())->toBe('<span></span>');
    $element->set_control('render_empty', false);
    expect($element->render())->toBe('');
});

test('render_open', function () {
    $element = new Element('span');
    expect($element->render_open())->toBe('<span>');
    $element->add_class('test');
    expect($element->render_open())->toBe('<span class="test">');
    $element->add_attribute('hello', 'world');
    expect($element->render_open())->toBe('<span class="test" hello="world">');
    $element->add_attribute('foo', '');
    expect($element->render_open())->toBe('<span class="test" foo hello="world">');
});

test('render_close', function () {
    $element = new Element('span');
    expect($element->render_close())->toBe('</span>');
});

test('render_open_non_stringable_attribute_throws', function () {
    $element = new Element('span');
    $element->add_attribute('foo', new stdClass);
    expect(fn () => $element->render_open())->toThrow(InvalidArgumentException::class);
});
