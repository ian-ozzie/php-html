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

test('head_renders_when_populated', function () {
    $element = new Html;
    $element->head->add_content('<meta charset="utf-8">');
    expect((string) $element)->toBe('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body></body></html>');
});

test('title_renders_when_populated', function () {
    $element = new Html;
    $element->title->add_content('My Page');
    expect((string) $element)->toBe('<!DOCTYPE html><html><head><title>My Page</title></head><body></body></html>');
});

test('noscript_renders_when_populated', function () {
    $element = new Html;
    $element->noscript->add_content('Please enable JavaScript.');
    expect((string) $element)->toBe('<!DOCTYPE html><html><body><noscript>Please enable JavaScript.</noscript></body></html>');
});

test('doctype_customisation', function () {
    $element = new Html;
    $element->doctype = 'html PUBLIC "-//W3C//DTD HTML 4.01//EN"';
    expect((string) $element)->toStartWith('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">');
});
