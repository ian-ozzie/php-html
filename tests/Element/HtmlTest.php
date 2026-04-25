<?php

declare(strict_types=1);

use Ozzie\Html\Element\Html;

test('construct_render', function () {
    $element = new Html;
    expect((string) $element)->toBe('<!DOCTYPE html><html><body></body></html>');
});

test('content', function () {
    $element = new Html;
    $element->add_content('Hello World');
    expect((string) $element)->toBe('<!DOCTYPE html><html><body>Hello World</body></html>');
    $element->content_append('!');
    expect((string) $element)->toBe('<!DOCTYPE html><html><body>Hello World!</body></html>');
    $element->content_prepend('!');
    expect((string) $element)->toBe('<!DOCTYPE html><html><body>!Hello World!</body></html>');
    $element->content_set('foo');
    expect((string) $element)->toBe('<!DOCTYPE html><html><body>foo</body></html>');
});

test('add_element', function () {
    $element = new Html;
    $result = $element->add_element('span');
    expect($result)->toBe($element);
    expect((string) $element)->toBe('<!DOCTYPE html><html><body><span></span></body></html>');
});

test('new_element', function () {
    $element = new Html;
    $result = $element->new_element('span');
    expect($result)->not->toBe($element);
    expect((string) $element)->toBe('<!DOCTYPE html><html><body><span></span></body></html>');
});
